<?php

namespace Ipragmatech\Registration\Observer;
require_once  "Services/Twilio.php";
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Controller\ResultFactory;

class SaveByRequestObserver implements ObserverInterface
{
	/**
	 * @var ObjectManagerInterface
	 */
	protected $_objectManager;

	/**
	 * @param \Magento\Framework\ObjectManagerInterface $objectManager
	 */
	public function __construct(
			\Magento\Framework\ObjectManagerInterface $objectManager
			) {
				$this->_objectManager = $objectManager;
	}

	
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/ip.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);

		$event = $observer->getEvent();
		$customer = $event->getCustomer();
// 		$logger->info('Informational message 1'.json_encode($observer->getData()));
// 		$logger->info('Informational message 1'.json_encode($customer));
// 		$logger->info('Informational message 1'.$customer->getId());
		
// 		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
// 		$customer = $objectManager->create('Magento\Customer\Model\Customer')->load($customer->getId());
// 		$customer->setWebsiteId(0);
// 		$customer->save();
		
// 		$model = $this->_objectManager->create('Ipragmatech\Registration\Model\Otp');
// 		$model->setOtp(12323);
// 		$model->setCustomerId(1);
// 		$model->save();
		
// 		$model = $this->_objectManager->create('Ipragmatech\Registration\Model\Twilio');
// 		$model->setAccountSid("dfdfdfeef");
// 		$model->setAuthToken("434fd434");
// 		$model->save();
		// Step 2: set our AccountSid and AuthToken from www.twilio.com/user/account
// 		$AccountSid = $this->helper('Ipragmatech\Registration\Helper\Data')->getConfig('registration/twiliosetting/account_sid');;
// 		$AuthToken = "5ee66a494f2055be3f69a576695f1c20";
		
// 		// Step 3: instantiate a new Twilio Rest Client
// 		$client = new \Services_Twilio($AccountSid,$AuthToken);
	
// 		$sms = $client->account->messages->sendMessage(

// 		// Step 6: Change the 'From' number below to be a valid Twilio number
// 		// that you've purchased, or the (deprecated) Sandbox number
// 		"+12405737837",
// 		// the number we are sending to - Any phone number
// 		'+91 88020 45390',

// 		// the sms body
// 		"Hey, Monkey Party at 6PM. Bring Bananas!"
// 		);
		
	}
}
