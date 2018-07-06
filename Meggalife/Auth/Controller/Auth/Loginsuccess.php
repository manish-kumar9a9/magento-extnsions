<?php
/**
 *
 * Copyright © 2015 Ipragmatechcommerce. All rights reserved.
 */
namespace Meggalife\Auth\Controller\Auth;

class Loginsuccess extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory

    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Flush cache storage
     *
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();

        //$this->resultPage = $this->resultPageFactory->create();
        //return $this->resultPage;

    }

}