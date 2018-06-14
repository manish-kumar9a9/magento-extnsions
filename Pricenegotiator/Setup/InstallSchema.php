<?php
/**
 * Copyright © 2015 Ipragmatech. All rights reserved.
 */

namespace Ipragmatech\Pricenegotiator\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
	
        $installer = $setup;

        $installer->startSetup();

		/**
         * Create table 'pricenegotiator_negotiator'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('pricenegotiator_negotiator')
        )
		->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'auto_increment' => true, 'nullable' => false, 'primary' => true],
            'pricenegotiator_negotiator'
        )
		->addColumn(
            'product_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'product_id'
        )
        ->addColumn(
        		'product_sku',
        		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        		null,
        		['nullable' => false],
        		'product_sku'
        )
		->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'customer_id'
        )
		->addColumn(
            'customer_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'customer_name'
        )
        ->addColumn(
        		'customer_email',
        		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        		'64k',
        		[],
        		'customer_email'
        		)
		->addColumn(
            'customer_price',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'customer_price'
        )
		->addColumn(
            'customer_message',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'customer_message'
        )
		->addColumn(
            'original_price',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'original_price'
        )
		->addColumn(
            'owner_price',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'owner_price'
        )
		->addColumn(
            'owner_msg',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'owner_msg'
        )
		->addColumn(
            'product_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'product_name'
        )
		->addColumn(
            'replied_status',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false,  'default' => 0],
            'replied_status'
        )
		->addColumn(
            'coupan_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'coupan_code'
        )
		->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'created_at'
        )
		->addColumn(
            'replied_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
            'replied_at'
        )
        ->addColumn(
        		'query_status',
        		\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        		null,
        		['nullable' => false, 'default' => 1],
        		'1-active, 2-expired'
        )
		/*{{CedAddTableColumn}}}*/
		
		
        ->setComment(
            'Ipragmatech Pricenegotiator pricenegotiator_negotiator'
        );
		
		$installer->getConnection()->createTable($table);
		/*{{CedAddTable}}*/

        $installer->endSetup();

    }
}
