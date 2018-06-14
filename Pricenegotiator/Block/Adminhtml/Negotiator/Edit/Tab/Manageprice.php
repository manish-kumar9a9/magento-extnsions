<?php
namespace Ipragmatech\Pricenegotiator\Block\Adminhtml\Negotiator\Edit\Tab;
class Manageprice extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = array()
    ) {
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
		/* @var $model \Magento\Cms\Model\Page */
        $model = $this->_coreRegistry->registry('pricenegotiator_negotiator');
		$isElementDisabled = false;
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');
        $htmlIdPrefix = $form->getHtmlIdPrefix();
        $fieldset = $form->addFieldset('base_fieldset', array('legend' => __('Manage Price #'.$model->getId())));

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', array('name' => 'id'));
        }

		$fieldset->addField(
            'product_name',
            'text',
            array(
                'name' => 'product_name',
                'label' => __('Product Name'),
                'title' => __('Product Name'),
                /*'required' => true,*/
            )
        );
		$fieldset->addField(
				'product_sku',
				'text',
				array(
						'name' => 'product_sku',
						'label' => __('Product SKU'),
						'title' => __('Product SKU'),
						/*'required' => true,*/
				)
				);
		$fieldset->addField(
            'customer_name',
            'text',
            array(
                'name' => 'customer_name',
                'label' => __('Customer Name'),
                'title' => __('Customer Name'),
                /*'required' => true,*/
            )
        );
		$fieldset->addField(
            'customer_price',
            'text',
            array(
                'name' => 'customer_price',
                'label' => __('Customer Price'),
                'title' => __('Customer Price'),
                /*'required' => true,*/
            )
        );
		$fieldset->addField(
            'original_price',
            'text',
            array(
                'name' => 'original_price',
                'label' => __('Original Price'),
                'title' => __('Original Price'),
                /*'required' => true,*/
            )
        );
		$fieldset->addField(
            'customer_message',
            'textarea',
            array(
                'name' => 'customer_message',
                'label' => __('Customer Message'),
                'title' => __('Customer Message'),
                /*'required' => true,*/
            )
        );
		$fieldset->addField(
            'replied_status',
            'select',
            array(
                'name' => 'replied_status',
                'label' => __('Reply status'),
                'title' => __('Reply status'),
            	'values' => array('-1'=>'Please Select..','1' => 'Accept','2' => 'Reject'),
                'required' => true,
            	'after_element_html' => '<small>Accept then <a target="_blank" href="'.$this->getUrl('sales_rule/promo_quote/index/index').'">create coupon</a></small>',
            )
        );
		$fieldset->addField(
				'coupan_code',
				'text',
				array(
						'name' => 'coupan_code',
						'label' => __('Coupon Code'),
						'title' => __('Coupon Code'),
						'required' => true,
						
				)
		);
		$fieldset->addField(
            'owner_price',
            'text',
            array(
                'name' => 'owner_price',
                'label' => __('Owner Price'),
                'title' => __('Owner Price'),
                'required' => true,
            )
        );
		$fieldset->addField(
            'owner_msg',
            'textarea',
            array(
                'name' => 'owner_msg',
                'label' => __('Owner Message'),
                'title' => __('Owner Message'),
                /*'required' => true,*/
            )
        );
		
		/*{{CedAddFormField}}*/
        
        if (!$model->getId()) {
            $model->setData('status', $isElementDisabled ? '2' : '1');
        }

        $form->setValues($model->getData());
        
        //Dependency
		$this->setChild ( 
				'form_after', $this->getLayout ()->createBlock ( 'Magento\Backend\Block\Widget\Form\Element\Dependence' )
				->addFieldMap ( "{$htmlIdPrefix}replied_status", 'replied_status' )
				->addFieldMap ( "{$htmlIdPrefix}coupan_code", 'coupan_code' )
				->addFieldMap ( "{$htmlIdPrefix}owner_price", 'owner_price' )
				
				->addFieldDependence ( 'coupan_code', 'replied_status', 1 ) 
				->addFieldDependence ( 'owner_price', 'replied_status', 1 ) 
		);
        
        $this->setForm($form);

        return parent::_prepareForm();   
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Manage Price');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Manage Price');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
