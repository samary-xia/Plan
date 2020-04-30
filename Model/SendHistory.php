<?php
/**
 * User: xiaxixiang @email:1635055310@qq.com
 * Date: 2019/8/21
 * Time: 9:31
 * @description
 */
namespace Samary\Plan\Model;
use Magento\Framework\Model\AbstractExtensibleModel;

class SendHistory extends AbstractExtensibleModel
{
    protected function _construct()
    {
        $this->_init(\Samary\Plan\Model\ResourceModel\SendHistory::class);
    }

    /**
     * @param $quote_id
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * @description 按照quote_id字段筛选
     */
    public function loadQuoteId($quote_id)
    {
        $this->_getResource()->loadQuoteId($this, $quote_id);
        $this->_afterLoad();
        return $this;
    }
}