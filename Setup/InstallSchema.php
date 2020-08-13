<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_Gallery
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\Gallery\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * Install table
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();
        /**
         * Create table 'lof_gallery_banner_category'
         */
        $setup->getConnection()->dropTable($setup->getTable('lof_gallery_banner_category'));
        $table = $installer->getConnection()->newTable(
            $installer->getTable('lof_gallery_banner_category')
        )->addColumn(
            'category_id',
            Table::TYPE_SMALLINT,
            null,
            ['identity' => true,'nullable' => false, 'primary' => true],
            'Banner Category Id'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Banner Category Name'
        )->addColumn(
            'is_active',
            Table::TYPE_SMALLINT,
            1,
            ['nullable' => false, 'default' => '1'],
            'Active'
        )->addColumn(
            'page_title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Meta Title'
        )->addColumn(
            'meta_keywords',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            [],
            'Meta Keywords'
        )->addColumn(
            'meta_description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            [],
            'Meta Description'
        )->setComment(
            'Banner Category Table'
        );
        $installer->getConnection()->createTable($table);



        /* Add table lof_gallery_banner */
        $setup->getConnection()->dropTable($setup->getTable('lof_gallery_banner'));
        $table = $installer->getConnection()->newTable(
            $installer->getTable('lof_gallery_banner')
        )->addColumn(
            'banner_id',
            Table::TYPE_SMALLINT,
            6,
            ['identity' => true,'nullable' => false, 'primary' => true],
            'Banner ID'
        )->addColumn(
            'title',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'title banner'
        )->addColumn(
            'label',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'label'
        )->addColumn(
            'description',
            Table::TYPE_TEXT,
            '2M',
            ['nullable' => false],
            'description'
        )->addColumn(
            'file',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'file'
        )->addColumn(
            'created_at',
            Table::TYPE_DATE,
            null,
            ['nullable' => true],
            'Created'
        )->addColumn(
            'is_active',
            Table::TYPE_SMALLINT,
            1,
            ['nullable' => false, 'default' => '1'],
            'Active'
        )->addColumn(
            'page_title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Meta Title'
        )->addColumn(
            'meta_keywords',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            [],
            'Meta Keywords'
        )->addColumn(
            'meta_description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            [],
            'Meta Description'
        )->setComment(
            'Banner Table'
        );
        $installer->getConnection()->createTable($table);


        /**
         * Create table 'lof_gallery'
         */
        $setup->getConnection()->dropTable($setup->getTable('lof_gallery'));
        $table = $installer->getConnection()->newTable(
            $installer->getTable('lof_gallery')
        )->addColumn(
            'category_id',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'primary' => true],
            'Banner Category Id'
        )->addColumn(
            'banner_id',
            Table::TYPE_SMALLINT,
            6,
            ['nullable' => false, 'primary' => true],
            'banner id'
        )->addColumn(
            'position',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Position'
        )->addIndex(
            $installer->getIdxName('lof_gallery', ['category_id']),
            ['category_id']
        )->addForeignKey(
            $installer->getFkName('lof_gallery_banner_category_banner', 'banner_id', 'lof_gallery_banner', 'banner_id'),
            'banner_id',
            $installer->getTable('lof_gallery_banner'),
            'banner_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('lof_gallery_banner_category_category', 'category_id', 'lof_gallery_banner_category', 'category_id'),
            'category_id',
            $installer->getTable('lof_gallery_banner_category'),
            'category_id',
            Table::ACTION_CASCADE
        )->setComment(
            'Gallery Table'
        );
        $installer->getConnection()->createTable($table);


        $installer->endSetup();
    }
}