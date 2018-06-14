<?php
namespace Ipragmatech\Pricenegotiator\Block\Adminhtml\Negotiator;


class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory]
     */
    protected $_setsFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Type
     */
    protected $_type;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    protected $_status;
	protected $_collectionFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_visibility;

    /**
     * @var \Magento\Store\Model\WebsiteFactory
     */
    protected $_websiteFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Store\Model\WebsiteFactory $websiteFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setsFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Product\Type $type
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status $status
     * @param \Magento\Catalog\Model\Product\Visibility $visibility
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
		\Ipragmatech\Pricenegotiator\Model\ResourceModel\Negotiator\Collection $collectionFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
		
		$this->_collectionFactory = $collectionFactory;
        $this->_websiteFactory = $websiteFactory;
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
		
        $this->setId('productGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setVarNameFilter('my_filter');
        $this->setUseAjax(false);
       
    }

    /**
     * @return Store
     */
    protected function _getStore()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        return $this->_storeManager->getStore($storeId);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
		try{
			
			
			$collection =$this->_collectionFactory->load();

		  

			$this->setCollection($collection);

			parent::_prepareCollection();
		  
			return $this;
		}
		catch(Exception $e)
		{
			echo $e->getMessage();die;
		}
    }

    /**
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            if ($column->getId() == 'websites') {
                $this->getCollection()->joinField(
                    'websites',
                    'catalog_product_website',
                    'website_id',
                    'product_id=entity_id',
                    null,
                    'left'
                );
            }
        }
        return parent::_addColumnFilterToCollection($column);
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
		$this->addColumn(
            'product_id',
            [
                'header' => __('Product ID'),
                'index' => 'product_id',
                'class' => 'product_id'
            ]
        );
		$this->addColumn(
				'product_name',
				[
						'header' => __('Product Name'),
						'index' => 'product_name',
						'class' => 'product_name'
				]
		);
		/* $this->addColumn(
            'customer_id',
            [
                'header' => __('customer_id'),
                'index' => 'customer_id',
                'class' => 'customer_id'
            ]
        ); */
		$this->addColumn(
            'customer_name',
            [
                'header' => __('Customer Name'),
                'index' => 'customer_name',
                'class' => 'customer_name'
            ]
        );
		$this->addColumn(
            'customer_price',
            [
                'header' => __('Customer Price'),
                'index' => 'customer_price',
                'class' => 'customer_price'
            ]
        );
		/* $this->addColumn(
            'customer_message',
            [
                'header' => __('customer_message'),
                'index' => 'customer_message',
                'class' => 'customer_message'
            ]
        ); */
		$this->addColumn(
            'original_price',
            [
                'header' => __('Original Price'),
                'index' => 'original_price',
                'class' => 'original_price'
            ]
        );
		$this->addColumn(
            'owner_price',
            [
                'header' => __('Owner Price'),
                'index' => 'owner_price',
                'class' => 'owner_price'
            ]
        );
		/* $this->addColumn(
            'owner_msg',
            [
                'header' => __('owner_msg'),
                'index' => 'owner_msg',
                'class' => 'owner_msg'
            ]
        );
		 */
		
		$this->addColumn(
            'coupan_code',
            [
                'header' => __('Coupon Code'),
                'index' => 'coupan_code',
                'class' => 'coupan_code'
            ]
        );
		$this->addColumn(
				'replied_status',
				[
						'header' => __('Status'),
						'index' => 'replied_status',
						'class' => 'replied_status',
						'type'	=> 'options',
						'options' => array(
								'0' => 'New',
								'1' => 'Accepted',
								'2' => 'Rejected'
						),
				]
		);
		/* $this->addColumn(
            'created_at',
            [
                'header' => __('created_at'),
                'index' => 'created_at',
                'type' => 'date',
            ]
        );
		$this->addColumn(
            'replied_at',
            [
                'header' => __('replied_at'),
                'index' => 'replied_at',
                'type' => 'date',
            ]
        ); */
		/*{{CedAddGridColumn}}*/
		$this->addColumn(
				'edit',
				[
						'header' => __('Action'),
						'type' => 'action',
						'getter' => 'getId',
						'actions' => [
								[
										'caption' => __('Edit'),
										'url' => [
												'base' => 'pricenegotiator/*/edit'
										],
										'field' => 'id'
								]
						],
						'filter' => false,
						'sortable' => false,
						'index' => 'stores',
						'header_css_class' => 'col-action',
						'column_css_class' => 'col-action'
				]
		);
		
        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }

     /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('id');

        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label' => __('Delete'),
                'url' => $this->getUrl('pricenegotiator/*/massDelete'),
                'confirm' => __('Are you sure?')
            )
        );
        return $this;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('pricenegotiator/*/index', ['_current' => true]);
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl(
            'pricenegotiator/*/edit',
            ['store' => $this->getRequest()->getParam('store'), 'id' => $row->getId()]
        );
    }
}
