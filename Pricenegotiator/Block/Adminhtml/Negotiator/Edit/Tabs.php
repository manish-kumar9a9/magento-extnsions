<?php
namespace Ipragmatech\Pricenegotiator\Block\Adminhtml\Negotiator\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
		
        parent::_construct();
        $this->setId('checkmodule_negotiator_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Negotiator Information'));
    }
}