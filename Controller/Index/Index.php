<?php
/**
 * User: xiaxixiang @email:1635055310@qq.com
 * Date: 2019/8/19
 * Time: 16:37
 * @description
 */

namespace Samary\Plan\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResourceConnection;
class Index extends Action
{
    protected $_helper;
    protected $_sendHistory;
    protected $resourceConnection;
    public function __construct
    (
        Context $context,
        \Samary\Plan\Helper\NotCheckout $notCheckout,
        \Samary\Plan\Model\SendHistory $sendHistory,
        ResourceConnection $resourceConnection
    )
    {
        parent::__construct($context);
        $this->_helper = $notCheckout;
        $this->_sendHistory = $sendHistory;
        $this->resourceConnection = $resourceConnection;
    }

    public function execute()
    {
        $this->resourceConnection->getConnection()->delete('send_history',["created_at<'2019-08-22'"]);
        //$this->_helper->start();
    }
}