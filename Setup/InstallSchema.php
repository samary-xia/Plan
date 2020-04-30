<?php
/**
 * User: xiaxixiang @email:1635055310@qq.com
 * Date: 2019/8/21
 * Time: 9:06
 * @description
 */

namespace Samary\Plan\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $table = $installer->getConnection()
            ->newTable($installer->getTable('send_history'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'null',
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                '主键ID'
            )
            ->addColumn(
                'quote_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Quote Id'
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created At'
            )->addIndex(
                $installer->getIdxName('send_history', ['quote_id']),
                ['quote_id']
            )->addIndex(
                $installer->getIdxName('send_history',['created_at']),
                ['created_at']
            )->setComment(
                '发送邮件记录表'
            );
            $installer->getConnection()->createTable($table);
            $installer->endSetup();
    }
}