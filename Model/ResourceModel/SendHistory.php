<?php
/**
 * User: xiaxixiang @email:1635055310@qq.com
 * Date: 2019/8/21
 * Time: 9:31
 * @description
 */
namespace Samary\Plan\Model\ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;

class SendHistory extends AbstractDb
{

    protected function _construct()
    {
        $this->_init('send_history', 'id');
    }

    /**
     * @param $sendHistory
     * @param $quoteId
     * @return $this
     */
    public function loadQuoteId($sendHistory, $quoteId)
    {
        $connection = $this->getConnection();
        $select = $this->_getLoadSelect(
            'quote_id',
            $quoteId,
            $sendHistory
        )->order(
            'created_at ' . \Magento\Framework\DB\Select::SQL_DESC
        )->limit(
            1
        );

        $data = $connection->fetchRow($select);

        if ($data) {
            $sendHistory->setData($data);
        }

        $this->_afterLoad($sendHistory);

        return $this;
    }
}