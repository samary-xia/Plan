<?php
/**
 * User: xiaxixiang @email:1635055310@qq.com
 * Date: 2019/8/20
 * Time: 16:39
 * @description
 */

namespace Samary\Plan\Helper;

use Samary\Plan\Model\SendHistory as SendHistory;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Quote\Model\ResourceModel\Quote\Collection;
use Magento\Quote\Model\ResourceModel\Quote\Item\Collection as itemCollection;
use Magento\Store\Model\StoreManagerInterface;

class NotCheckout extends AbstractHelper
{
    const XML_PATH_EMAIL_SENDER_FORM = 'contact/email/sender_email_identity';
    const XML_PATH_EMAIL_SENDER_TO = 'trans_email/ident_general/email';
    const XML_PATH_STORE_NAME = 'general/store_information/name';
    const XML_PATH_STORE_PHONE = 'general/store_information/phone';

    private $_collection;
    private $_dateTime;
    protected $scopeConfig;
    private $transportBuilder;
    private $inlineTranslation;
    private $storeManager;
    private $sendHistory;
    private $itemCollection;

    public function __construct
    (
        Collection $collection,
        DateTime $dateTime,
        ScopeConfigInterface $scopeConfig,
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation,
        StoreManagerInterface $storeManager = null,
        SendHistory $sendHistory,
        itemCollection $itemCollection
    )
    {
        $this->_collection = $collection;
        $this->_dateTime = $dateTime;
        $this->scopeConfig = $scopeConfig;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->storeManager = $storeManager ?: ObjectManager::getInstance()->get(StoreManagerInterface::class);
        $this->sendHistory = $sendHistory;
        $this->itemCollection = $itemCollection;
    }

    /**
     * @description 查询活跃状态有邮箱的未支付购物车，进行邮件发送
     */
    public function start()
    {
        $start = $this->_dateTime->gmtDate("Y-m-d H:i:s", strtotime("-2 day", $this->_dateTime->timestamp()));
        $end = $this->_dateTime->gmtDate();
        $success = $failure = 0;
        $collections = $this->_collection
            ->addFieldToSelect(array('customer_email', 'created_at'))
            ->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('customer_email', array('neq' => ''))
            ->addFieldToFilter('updated_at', array('from' => "$start", 'to' => "$end"));

        //获取店铺信息配置
        $store = $this->storeConfig();
        //储存日志
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/sendEmail.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        foreach ($collections as $collection) {
            $entity_id = $collection->getEntityId();
            $customer_email = $collection->getCustomerEmail();
            $sendHistory = $this->sendHistory->loadQuoteId($entity_id);
            if ($sendHistory->getQuoteId()) {
                continue;
            }

            $items = $this->itemCollection
                // ->addFieldToSelect(array('name', 'qty','base_row_total'))
                ->addFieldToFilter('quote_id', $entity_id);
            $items->getSelect()->joinInner(array('ev' => 'catalog_product_entity_varchar'), 'ev.entity_id=main_table.product_id AND ev.attribute_id=126 AND ev.store_id=0', array('url' => 'ev.value'));
            $items->getSelect()->joinInner(array('im' => 'catalog_product_entity_varchar'), 'im.entity_id=main_table.product_id AND im.attribute_id=133 AND ev.store_id=0', array('image' => 'im.value'));


            if ($this->sendEmail($customer_email, $items, $store)) {
                $logger->info("$entity_id Send Success");
                $success++;
            } else {
                $logger->info("$entity_id Send Failure");
                $failure++;
            }
            $this->sendHistory->setQuoteId($entity_id)->save();
        }
        echo "\nSend Success:$success\nSend Failure:$failure\n";
    }

    /**
     * @param object $item
     * @return bool
     * @throws \Magento\Framework\Exception\MailException
     * $description 发送邮件方法
     */
    protected function sendEmail($customer_email, $items, $store)
    {
        $this->inlineTranslation->suspend();
        try {
            $transport = $this->transportBuilder
                ->setTemplateIdentifier('10')//这里设置邮件模版
                ->setTemplateOptions(
                    [
                        'area' => Area::AREA_FRONTEND,
                        'store' => $this->storeManager->getStore()->getId()
                    ]
                )
                ->setTemplateVars(['data' => $items, 'store' => $this->storeManager->getStore(), 'store_email' => $store->addresses, 'store_phone' => $store->store_phone, 'store_url' => $store->store_url])
                ->setFrom($this->scopeConfig->getValue(self::XML_PATH_EMAIL_SENDER_FORM, $store->storeScope))
                ->addTo($customer_email)
                ->setReplyTo($store->addresses, $store->store_name)
                ->getTransport();
            $transport->sendMessage();
            return true;
        } catch (LocalizedException $e) {
            return false;
        } finally {
            $this->inlineTranslation->resume();
        }
    }

    /**
     * @return \stdClass
     * @description 店铺信息配置
     */
    public function storeConfig()
    {
        $store = new \stdClass;
        $store->storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $store->store_name = $this->scopeConfig->getValue(self::XML_PATH_STORE_NAME,  $store->storeScope);
        $store->store_phone = $this->scopeConfig->getValue(self::XML_PATH_STORE_PHONE,  $store->storeScope);
        $store->addresses = $this->scopeConfig->getValue(self::XML_PATH_EMAIL_SENDER_TO,  $store->storeScope);
        $store->store_url = $this->storeManager->getStore()->getUrl();
        return $store;

    }
}