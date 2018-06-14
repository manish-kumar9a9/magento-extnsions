<?php 
/**
 * Copyright © 2015 Ipragmatech. All rights reserved.
 */
namespace Ipragmatech\Pricenegotiator\Cron;
use Magento\Framework\Event\ObserverInterface;

class Query {
	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $_storeManager;
	protected $_objectManager;
	
	public function __construct(
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\ObjectManagerInterface $objectManager
		) {
			$this->_storeManager = $storeManager;
			$this->_objectManager = $objectManager;
		}
		
	public function execute(){
		try{
			$this->_resources = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\ResourceConnection');
			$connection= $this->_resources->getConnection();
			$negotiateTable = $this->_resources->getTableName('pricenegotiator_negotiator');
			
			$adminExpTime = $this->_objectManager->create('Ipragmatech\Pricenegotiator\Helper\Data')->getConfig('pricenegotiatorsection/query_expire_time/admin_no_reply');
			$customerExpTime = $this->_objectManager->create('Ipragmatech\Pricenegotiator\Helper\Data')->getConfig('pricenegotiatorsection/query_expire_time/customer_no_apply');
			
			$currentTime = $idt = date("Y-m-d H:i:s");
			
			//admin no reply
			$adminNoRplySql = "select id,created_at, TIMESTAMPDIFF(HOUR , created_at,  '".$currentTime."' ) from ".$negotiateTable." where replied_status IN(0,2) AND query_status = 1 AND TIMESTAMPDIFF(HOUR , created_at , '".$currentTime."' ) > ".$adminExpTime;
			$result = $connection->fetchAll($adminNoRplySql);
			foreach ($result as $item){
				$id = $item['id'];
				if ($id) {
					$model = $this->_objectManager->create('Ipragmatech\Pricenegotiator\Model\Negotiator');
					$model->load($id);
					try{
						$model->setQueryStatus(2);
						$model->save();
					}catch (\Exception  $e){
						$this->messageManager->addException($e, __('Something went wrong while saving the coupan status.'));
					}
				}
			}
			//customer not uses
			$customerNotUsesSql = "select id, created_at, TIMESTAMPDIFF(HOUR , created_at,  '".$currentTime."' ) from ".$negotiateTable." where replied_status IN(1) AND query_status = 1 AND TIMESTAMPDIFF(HOUR , created_at , '".$currentTime."' ) > ".$customerExpTime;
			$result = $connection->fetchAll($customerNotUsesSql);
			foreach ($result as $item){
				$id = $item['id'];
				if ($id) {
					$model = $this->_objectManager->create('Ipragmatech\Pricenegotiator\Model\Negotiator');
					$model->load($id);
					try{
						$model->setQueryStatus(2);
						$model->save();
					}catch (\Exception  $e){
						$this->messageManager->addException($e, __('Something went wrong while saving the coupan status.'));
					}
				}
			}
			
		}catch (\Exception $e){
			$this->messageManager->addException($e, __('Something went wrong while saving the coupan status.'));
		}
	}
}
