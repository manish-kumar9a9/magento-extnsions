<?php
/**
 * Copyright © 2015 Ipragmatech. All rights reserved.
 */

namespace Ipragmatech\Bannerblock\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;



class InstallData implements InstallDataInterface
{

	/**
	 * Customer setup factory
	 *
	 * @var \Magento\Customer\Setup\CustomerSetupFactory
	 */
	private $eavSetupFactory;

	/**
	 * Init
	 *
	 * @param \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
	 */
	public function __construct( \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory)
	{
		$this->eavSetupFactory = $eavSetupFactory;
	}

	/**
	 * Installs DB schema for a module
	 *
	 * @param ModuleDataSetupInterface $setup
	 * @param ModuleContextInterface $context
	 * @return void
	 */
	public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
	{
		/** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

		$eavSetup->addAttribute(
				\Magento\Catalog\Model\Product::ENTITY,
				'is_feature',
				[
						'group' => 'Product Details',
						'type'     => 'int',
						'backend'  => '',
						'frontend' => '',
						'label'    => 'Is Feature',
						'input'    => 'select',
						'source'   => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
						'visible'  => true,
						'default'  => '0',
						'frontend' => '',
						'unique'   => false,
						'note'     => '',
						'required' => false,
                	    'sort_order' => '',
                	    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                	    'used_in_product_listing' => true,
                	    'visible_on_front' => true
				]
		);

	}
}
