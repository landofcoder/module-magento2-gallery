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

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.5', '<')) {
            $installer = $setup;
            $installer->startSetup();

            // Create table lof_gallery_category_store
            $setup->getConnection()->dropTable($setup->getTable('lof_gallery_category_store'));
            $table = $installer->getConnection()->newTable(
                $installer->getTable('lof_gallery_category_store')
                )->addColumn(
                'category_id',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'primary' => true],
                'Category Id'
                )->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store ID'
                )->addIndex(
                $installer->getIdxName('lof_gallery_category_store', ['category_id']),
                ['category_id']
                )->addForeignKey(
                $installer->getFkName('lof_gallery_category_store', 'category_id', 'lof_gallery_banner_category', 'category_id'),
                'category_id',
                $installer->getTable('lof_gallery_banner_category'),
                'category_id',
                Table::ACTION_CASCADE
                )->setComment(
                'Category Store Table'
                )->addForeignKey(
                $installer->getFkName('lof_gallery_category_store', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
                )->setComment(
                'Gallery Store'
                );
                $installer->getConnection()->createTable($table);

                
            // Create table lof_gallery_banner_product 
                $installer = $setup;
                $installer->startSetup();
                $setup->getConnection()->dropTable($setup->getTable('lof_gallery_category_product'));
                $setup->getConnection()->dropTable($setup->getTable('lof_gallery_banner_product'));
                $table = $installer->getConnection()->newTable(
                    $installer->getTable('lof_gallery_banner_product')
                    )->addColumn(
                    'banner_id',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'primary' => true],
                    'Banner Category Id'
                    )->addColumn(
                    'entity_id',
                    Table::TYPE_INTEGER,
                    null,
                    [ 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Entity id'
                    )->addColumn(
                    'position',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Position'
                    )->addIndex(
                    $installer->getIdxName('lof_gallery_banner_product', ['banner_id']),
                    ['banner_id']
                    )->addForeignKey(
                    $installer->getFkName('lof_gallery_banner_product', 'banner_id', 'lof_gallery_banner', 'banner_id'),
                    'banner_id',
                    $installer->getTable('lof_gallery_banner'),
                    'banner_id',
                    Table::ACTION_CASCADE
                    )->setComment(
                    'Category Product Table'
                    )->addForeignKey(
                    $installer->getFkName('lof_gallery_banner_product', 'entity_id', 'catalog_product_entity', 'entity_id'),
                    'entity_id',
                    $installer->getTable('catalog_product_entity'),
                    'entity_id',
                    Table::ACTION_CASCADE
                    );
                    $installer->getConnection()->createTable($table);



                    //Add url key
                    $installer = $setup;
                    $installer->startSetup();
                    $tableItems = $installer->getTable('lof_gallery_banner');

                    $installer->getConnection()->addColumn(
                        $tableItems,
                        'identifier',
                        [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 255,
                        'nullable' => false,
                        'comment' => 'Identifier'
                        ]
                        );
                    $installer->getConnection()->addColumn(
                        $tableItems,
                        'link',
                        [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 255,
                        'nullable' => false,
                        'comment' => 'Link image'
                        ]
                        );
                    $installer->getConnection()->addColumn(
                        $tableItems,
                        'video_type',
                        [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 
                        'nullable' => false,
                        'comment' => 'Video type'
                        ]
                        );
                    $installer->getConnection()->addColumn(
                        $tableItems,
                        'video_id',
                        [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 255,
                        'nullable' => false,
                        'comment' => 'Video Id'
                        ]
                        );
                    $installer->getConnection()->addColumn(
                        $tableItems,
                        'show_image',
                        [
                        'default' => 'image_file',
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 255,
                        'nullable' => false,
                        'comment' => 'Show Image'
                        ]
                        );


                    //Add url key
                    $installer = $setup;
                    $installer->startSetup();
                    $tableItems = $installer->getTable('lof_gallery_banner_category');

                    $installer->getConnection()->addColumn(
                        $tableItems,
                        'identifier',
                        [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 255,
                        'nullable' => false,
                        'comment' => 'Identifier'
                        ]
                        );

                    $installer->endSetup();
                }    
                if (version_compare($context->getVersion(), '1.0.6', '<')) {
                    //Add url key
                    $installer = $setup;
                    $installer->startSetup();

                    $tableItems = $installer->getTable('lof_gallery_banner');

                    $installer->getConnection()->addColumn(
                        $tableItems,
                        'open_link',
                        [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 255,
                        'nullable' => false,
                        'comment' => 'Open Custom Link'
                        ]
                        );

                    $tableItems = $installer->getTable('lof_gallery_banner_category');

                    $installer->getConnection()->addColumn(
                        $tableItems,
                        'page_layout',
                        [
                        'type' => Table::TYPE_TEXT,
                        'length' => 255,
                        'nullable' => false,
                        'comment' => 'Page Layout'
                        ]
                    );
                    $installer->getConnection()->addColumn(
                        $tableItems,
                        'layout_type',
                        [
                        'type' => Table::TYPE_TEXT,
                        'length' => 255,
                        'nullable' => false,
                        'comment' => 'Layout Type'
                        ]
                    );
                    $installer->getConnection()->addColumn(
                        $tableItems,
                        'is_active',
                        [
                        'type' => Table::TYPE_SMALLINT,
                        'length' => 4,
                        'nullable' => false,
                        'comment' => 'Active'
                        ]
                    );
                    $installer->getConnection()->addColumn(
                        $tableItems,
                        'items_per_page',
                        [
                        'type' => Table::TYPE_INTEGER,
                        'length' => 11,
                        'nullable' => false,
                        'comment' => 'Items Per Page'
                        ]
                    );
                    $installer->getConnection()->addColumn(
                        $tableItems,
                        'lg_column',
                        [
                        'type' => Table::TYPE_INTEGER,
                        'length' => 11,
                        'nullable' => false,
                        'comment' => 'Large Desktop (≥1200px)'
                        ]
                    );
                    $installer->getConnection()->addColumn(
                        $tableItems,
                        'md_column',
                        [
                        'type' => Table::TYPE_INTEGER,
                        'length' => 11,
                        'nullable' => false,
                        'comment' => 'Medium devices Desktops (≥992px)'
                        ]
                    );
                    $installer->getConnection()->addColumn(
                        $tableItems,
                        'sm_column',
                        [
                        'type' => Table::TYPE_INTEGER,
                        'length' => 11,
                        'nullable' => false,
                        'comment' => 'Small devices Tablets (≥768px)'
                        ]
                    );
                    $installer->getConnection()->addColumn(
                        $tableItems,
                        'xs_column',
                        [
                        'type' => Table::TYPE_INTEGER,
                        'length' => 11,
                        'nullable' => false,
                        'comment' => 'Phone < 768px'
                        ]
                    );
                    $installer->getConnection()->addColumn(
                        $tableItems,
                        'cat_position',
                        [
                        'type' => Table::TYPE_INTEGER,
                        'length' => 11,
                        'nullable' => false,
                        'comment' => 'Cat Position'
                        ]
                    );
                    $installer->getConnection()->addColumn(
                        $tableItems,
                        'parent_id',
                        [
                        'type' => Table::TYPE_INTEGER,
                        'length' => 11,
                        'nullable' => false,
                        'comment' => 'Parent Id'
                        ]
                    );
                    $installer->getConnection()->addColumn(
                        $tableItems,
                        'page_title',
                        [
                        'type' => Table::TYPE_TEXT,
                        'length' => 255,
                        'nullable' => false,
                        'comment' => 'Meta Title'
                        ]
                    );
                    $installer->getConnection()->addColumn(
                        $tableItems,
                        'lightbox',
                        [
                        'type' => Table::TYPE_TEXT,
                        'length' => 255,
                        'nullable' => false,
                        'comment' => 'Lightbox'
                        ]
                    );
                    $installer->getConnection()->addColumn(
                        $tableItems,
                        'layout_update_xml',
                        [
                        'type' => Table::TYPE_TEXT,
                        'length' => '2M',
                        'nullable' => false,
                        'comment' => 'Layout Update Xml'
                        ]
                    );
                    $installer->getConnection()->addColumn(
                        $tableItems,
                        'show_toptoolbar',
                        [
                        'type' => Table::TYPE_SMALLINT,
                        'length' => 4,
                        'nullable' => false,
                        'comment' => 'Show Top Toolbar'
                        ]
                    );
                    $installer->getConnection()->addColumn(
                        $tableItems,
                        'show_bottomtoolbar',
                        [
                        'type' => Table::TYPE_SMALLINT,
                        'length' => 4,
                        'nullable' => false,
                        'comment' => 'Show Bottom Toolbar'
                        ]
                    );
                    $installer->getConnection()->addColumn(
                        $tableItems,
                        'description',
                        [
                        'type' => Table::TYPE_TEXT,
                        'length' => '2M',
                        'nullable' => false,
                        'comment' => 'Description'
                        ]
                    );

                    $installer->endSetup();
                }
            }
}
