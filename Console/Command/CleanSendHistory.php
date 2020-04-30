<?php
/**
 * User: xiaxixiang @email:1635055310@qq.com
 * Date: 2019/8/21
 * Time: 11:50
 * @description
 */

namespace Samary\Plan\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CleanSendHistory extends Command
{
    private $_state;
    protected $cleanSendHistory;

    public function __construct(
        \Magento\Framework\App\State $state,
        \Samary\Plan\Cron\CleanSendHistory $cleanSendHistory
    )
    {
        $this->_state = $state;
        $this->cleanSendHistory = $cleanSendHistory;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('longqi:send-email:cleanSendHistory');
        $this->setDescription('Clean Send Email History');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        echo "loading...";
        $this->_state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
        $this->cleanSendHistory->run();
    }
}