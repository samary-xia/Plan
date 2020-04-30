<?php
/**
 * User: xiaxixiang @email:1635055310@qq.com
 * Date: 2019/8/16
 * Time: 16:43
 * @description
 */
namespace Samary\Plan\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class NotCheckout extends Command
{
    protected $_helper;
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\State $state,
        \Magento\Framework\ObjectManager\ConfigLoaderInterface $configLoader,
        \Samary\Plan\Helper\NotCheckout $notCheckout
    ){
        $state->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND);
        $objectManager->configure($configLoader->load('frontend'));
        $this->_helper = $notCheckout;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('longqi:send-email:notCheckout');
        $this->setDescription('Send Email To Not Checkout');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        echo "loading...";
        $this->_helper->start();
    }
}