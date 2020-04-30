<?php
/**
 * User: xiaxixiang @email:1635055310@qq.com
 * Date: 2019/8/21
 * Time: 9:32
 * @description
 */

namespace Samary\Plan\Model\ResourceModel\SendHistory;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\VersionControl\Collection
{
    protected function _construct()
    {
        $this->_init(\Samary\Plan\Model\SendHistory::class, \Samary\Plan\Model\ResourceModel\SendHistory::class);
    }
}