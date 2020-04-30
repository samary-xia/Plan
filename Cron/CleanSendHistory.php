<?php
/**
 * User: xiaxixiang @email:1635055310@qq.com
 * Date: 2019/8/21
 * Time: 10:31
 * @description
 */

namespace Samary\Plan\Cron;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Stdlib\DateTime\DateTime;

class CleanSendHistory
{
    private $resourceConnection;
    protected $_dateTime;

    public function __construct
    (
        ResourceConnection $resourceConnection,
        DateTime $dateTime
    )
    {
        $this->resourceConnection = $resourceConnection;
        $this->_dateTime = $dateTime;
    }

    public function run()
    {
        $time = $this->_dateTime->gmtDate("Y-m-d H:i:s", strtotime("-30 day", $this->_dateTime->timestamp()));
        $this->resourceConnection->getConnection()->delete($this->resourceConnection->getTableName('send_history'), ["created_at<'$time'"]);
    }
}