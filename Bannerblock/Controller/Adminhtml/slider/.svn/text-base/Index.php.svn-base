<?php
namespace Ipragmatech\Bannerblock\Controller\Adminhtml\slider;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPagee;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }
    
    protected function _isAllowed()
    {
    	//return $this->_authorization->isAllowed('Magento_Cms::page');
    	return true;
    }
    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Ipragmatech_Bannerblock::slider');
        $resultPage->addBreadcrumb(__('Ipragmatech'), __('Ipragmatech'));
        $resultPage->addBreadcrumb(__('Manage item'), __('Manage Slider'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Slider'));

        return $resultPage;
    }
}
?>