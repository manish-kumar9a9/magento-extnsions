<?php
/**
 * Copyright © 2015 Ipragmatech. All rights reserved.
 */

namespace Meggalife\Auth\Setup;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetup;
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
	private $customerSetupFactory;

	/**
	 * Init
	 *
	 * @param \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
	 */
	public function __construct(\Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory)
	{
		$this->customerSetupFactory = $customerSetupFactory;
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
		$setup->startSetup();

		/** @var CustomerSetup $customerSetup */
		$customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

		$customerSetup->addAttribute(
				Customer::ENTITY,
				'user_name',
				[
						"type"     => "varchar",
						"backend"  => "",
						"label"    => "User Name",
						"input"    => "text",
						"source"   => "",
						"visible"  => true,
						"required" => true,
						"default" => "",
						"frontend" => "",
						"unique"     => "",
						"note"       => "",
                        'is_used_in_grid' => true,
                        'is_visible_in_grid' => true,
                        'is_filterable_in_grid' => true,
                        'is_searchable_in_grid' => true,
                        'filterable' => true
				]);

        $customerSetup->addAttribute(
            Customer::ENTITY,
            'recovery_email',
            [
                "type"     => "varchar",
                "backend"  => "",
                "label"    => "Recovery Email",
                "input"    => "text",
                "source"   => "",
                "visible"  => true,
                "required" => true,
                "default" => "",
                "frontend" => "",
                "unique"     => "",
                "note"       => "",
                'is_used_in_grid' => true,
                'is_visible_in_grid' => true,
                'is_filterable_in_grid' => true,
                'is_searchable_in_grid' => true,
                'filterable' => true
            ]);

		// add attribute to form
		/** @var  $attribute */
		$attribute = $customerSetup->getEavConfig()->getAttribute('customer', 'user_name');
		
		$used_in_forms[]="adminhtml_customer";
		$used_in_forms[]="checkout_register";
		$used_in_forms[]="customer_account_create";
		$used_in_forms[]="customer_account_edit";
		$used_in_forms[]="adminhtml_checkout";
		$attribute->setData("used_in_forms", $used_in_forms)
		->setData("is_used_for_customer_segment", true)
		->setData("is_system", 0)
		->setData("is_user_defined", 1)
		->setData("is_visible", 1)
		->setData("sort_order", 100);
		
		$attribute->save();


        /** @var  $attribute */
        $attributeRE = $customerSetup->getEavConfig()->getAttribute('customer', 'recovery_email');

//        $used_in_forms[]="adminhtml_customer";
//        $used_in_forms[]="checkout_register";
//        $used_in_forms[]="customer_account_create";
//        $used_in_forms[]="customer_account_edit";
//        $used_in_forms[]="adminhtml_checkout";
        $attributeRE->setData("used_in_forms", $used_in_forms)
            ->setData("is_used_for_customer_segment", true)
            ->setData("is_system", 0)
            ->setData("is_user_defined", 1)
            ->setData("is_visible", 1)
            ->setData("sort_order", 101);

        $attributeRE->save();


		$setup->endSetup();
	}
}