<?php
namespace Comwrap\ElasticsuiteBlog\Setup;

use \Magento\Framework\Setup\InstallSchemaInterface;
use \Magento\Framework\Setup\ModuleContextInterface;
use \Magento\Framework\Setup\SchemaSetupInterface;
use \Magento\Eav\Setup\EavSetup;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * Installs DB schema for the module
     *
     * @param SchemaSetupInterface   $setup   The setup interface
     * @param ModuleContextInterface $context The module Context
     *
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $connection = $setup->getConnection();
        $table      = $setup->getTable('magefan_blog_post');

        // Append a column 'is_searchable' into the db.
        $connection->addColumn(
            $table,
            'is_searchable',
            [
                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                'nullable' => false,
                'default'  => '0',
                'comment'  => 'If post is searchable',
            ]
        );

        $setup->endSetup();
    }
}
