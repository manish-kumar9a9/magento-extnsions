<?php
namespace Ipragmatech\Pricenegotiator\Controller\Adminhtml\Negotiator;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;


class Index extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Backend\Model\View\Result\Page
     */
    protected $resultPage;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }
     protected function _isAllowed()
    {
     	return $this->_authorization->isAllowed('Ipragmatech_Pricenegotiator::ipragmatech_negotiator_index');
    }

    public function execute()
    {
		
		$this->resultPage = $this->resultPageFactory->create();  
		$this->resultPage->setActiveMenu('Ipragmatech_Negotiator::negotiator');
		$this->resultPage ->getConfig()->getTitle()->set((__('Negotiator')));
		return $this->resultPage;
    }
}
