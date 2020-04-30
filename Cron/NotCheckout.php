<?php
/**
 * User: xiaxixiang @email:1635055310@qq.com
 * Date: 2019/8/16
 * Time: 16:30
 * @description
 */
namespace Samary\Plan\Cron;

class NotCheckout{

    private $_helper;

    public function __construct
    (
        \Samary\Plan\Helper\NotCheckout $notCheckout
    )
    {
        $this->_helper = $notCheckout;
    }

    public function run()
    {
        $this->_helper->start();
    }
}