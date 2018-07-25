<?php
/**
 * Login with Meggalife
 * Copyright (C) 2017  meggaife
 *
 * This file is part of Meggalife/Auth.
 *
 * Meggalife/Auth is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Meggalife\Auth\Model;

use Meggalife\Auth\Api\PointManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\InputException;



class PointManagement implements PointManagementInterface
{

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $_customerRepositoryInterface;

    /**
     * PointManagement constructor.
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     */
    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
    ) {
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function getPoint($customerId){
//        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/auth.log');
//        $logger = new \Zend\Log\Logger();
//        $logger->addWriter($writer);
//        $logger->info("getPoint::" . $customerId);

        if (empty($customerId) || ! isset($customerId) || $customerId == "") {
            throw new InputException(__('Customer token required'));
        }
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $baseurl = $objectManager->create('Meggalife\Auth\Helper\Data')->getConfig('meggalife_auth/general/base_url');
        $apiKey = $objectManager->create('Meggalife\Auth\Helper\Data')->getConfig('meggalife_auth/general/meggalife_key');
        $secretKey = $objectManager->create('Meggalife\Auth\Helper\Data')->getConfig('meggalife_auth/general/meggalife_secrete');
        $version = $objectManager->create('Meggalife\Auth\Helper\Data')->getConfig('meggalife_auth/general/meggalife_version');

        $customer = $this->_customerRepositoryInterface->getById($customerId);
        $user = $customer->getCustomAttribute('user_name')->getValue();

        try{
            $url =  $baseurl . "meggasup/index/getuserpoints?username=". $user ."&apiKey=".$apiKey."&secretKey=".$secretKey."&version=". $version;

            $ch = curl_init();
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
            //curl_setopt($ch,CURLOPT_POST,true);
            //curl_setopt($ch,CURLOPT_POSTFIELDS,$params);

            $output=curl_exec($ch);
            if($output === false)
            {
                throw new LocalizedException(__('ERROR: '.curl_error($ch)));
            }

            curl_close($ch);
            $resultData = json_decode($output, true);
            $status = isset($resultData['status']) ? $resultData['status'] : '';
            if( !$status ){
                throw new LocalizedException(__($resultData['message']));
            }

            return $resultData['points'];

        }catch (\Exception $e) {
            throw new LocalizedException(__($e->getMessage()));
        }
    }
}
