<?php
namespace Ipragmatech\Pricenegotiator\Block\Adminhtml;
class Negotiator extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
		
        $this->_controller = 'adminhtml_negotiator';/*block grid.php directory*/
        $this->_blockGroup = 'Ipragmatech_Pricenegotiator';
        $this->_headerText = __('Negotiator');
        $this->_addButtonLabel = __('Add New Entry'); 
        parent::_construct();
		
    }
}