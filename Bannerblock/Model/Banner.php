<?php

/**
 * Copyright 2015 iPragmatech. All rights reserved.
 */
namespace Ipragmatech\Bannerblock\Model;

use Ipragmatech\Bannerblock\Api\BannerInterface;
use Ipragmatech\Bannerblock\Block\Widget\Popularmenu;

/**
 * Defines the implementaiton class of the Banner service contract.
 */
class Banner implements BannerInterface
{

    public function popularMenu()
    {

        $objectManager = \Magento\Framework\App\ObjectManager
            ::getInstance();
        $popularMenu = $objectManager->create(
            'Ipragmatech\Bannerblock\Block\Widget\Popularmenu');
        $listWidget = $objectManager->create(
            'Ipragmatech\Bannerblock\Block\Widget\Listwidget');
        $title = $popularMenu->getTitle();
        $categoryIds = $popularMenu->getCategoryList();
        if (count($categoryIds)) {
            foreach ($categoryIds as $categoryId) {
                $category = $popularMenu->getCategorymodel($categoryId);
                $childCategories = $listWidget->getLeafChild($category);
                $data = [
                    'category_id'   => $categoryId,
                    'category_name' => $category->getName(),
                    'category_children' => $childCategories
                ];
                $popularMenuArray[] = $data;
            }
        }
        /**
         * Array containing popular menu
         */
        $popularMenus[] = [
            'title' => $title,
            'menus' => $popularMenuArray
        ];

        /* Getting banner categories */
        $objectManager = \Magento\Framework\App\ObjectManager
            ::getInstance();
        $helper = $objectManager->create(
            'Ipragmatech\Bannerblock\Helper\Data');

        $selectedCategoriesId = $helper->getConfig(
            'bannerblocksection/bannerblock_setting/bannerlist');
        $selectedCategoriesId = (explode(',', $selectedCategoriesId));
        foreach ($selectedCategoriesId as $categoryId) {
            $category = $listWidget->getCategorymodel($categoryId);
            $categoryName = $category->getName();
            $categoryImageUrl = $category->getImageUrl();

            $childrenCategory = $listWidget->getLeafChild($category);

            $subCategoyIds = $listWidget->getChildCategories($category);
            $subCategoryArray = '';

            foreach ($subCategoyIds as $subCategoyId) {
                $subCategoy = $listWidget->getCategorymodel($subCategoyId);
                $subChildCategory = $listWidget->getLeafChild($subCategoy);
                $data = [
                    'id'    => $subCategoyId,
                    'name'  => $subCategoy->getName(),
                    'image' => $subCategoy->getImageUrl(),
                    'subcategory_children' => $subChildCategory
                ];
                $subCategoryArray[] = $data;
            }
            $categoryArray = [
                'category_id'    => $categoryId,
                'category_name'  => $categoryName,
                'category_image' => $categoryImageUrl,
                'category_children' => $childrenCategory,
                'subcategories'  => $subCategoryArray
            ];
            /**
             * Array containing Category Banner
             */
            $categoryBanner[] = $categoryArray;
        }
        /**
         * Array containing topchoice banner
         */
        $objectManager = \Magento\Framework\App\ObjectManager
            ::getInstance();
        $featureClass = $objectManager->create(
            'Ipragmatech\Bannerblock\Block\Widget\Feature');
        $imagehelper = $objectManager->create(
            'Magento\Catalog\Helper\Image');
        $featureProducts = $featureClass->getFeatureProducts();
        $bestSellerProducts = $featureClass->getBestProduct();
        // Feature product
        $featureProductArray = [];
        foreach ($featureProducts as $featureProduct) {
            $data = [
                'sku'   => $featureProduct->getSku(),
                'id'    => $featureProduct->getEntityId(),
                'name'  => $featureProduct->getName(),
                'image' => $featureProduct->getImage()
            ];
            $featureProductArray[] = $data;
        }
        // Bestproduct
        $bestProductArray = [];
        if($bestSellerProducts) {
            foreach ($bestSellerProducts as $item) {
                $pId = $item["product_id"];
                $bestProduct = $objectManager->create('Magento\Catalog\Model\Product')->load($pId);
                $data = [
                    'sku'   => $bestProduct->getSku(),
                    'id'    => $bestProduct->getEntityId(),
                    'name'  => $bestProduct->getName(),
                    'image' => $bestProduct->getImage()
                ];
                $bestProductArray[] = $data;
            }
        }

        $topChoiceBanner[] = [
            'topchoice' => $bestProductArray,
            'feature'   => $featureProductArray
        ];

        /**
         * Array containing topchoice banner
         */

        $sliderClass = $objectManager->create(
            'Ipragmatech\Bannerblock\Block\Widget\SliderSlider');
        $imagehelper = $objectManager->create(
            'Magento\Catalog\Helper\Image');
        $sliderImage = $sliderClass->getMobileSlider();
        $media = $sliderClass->getImageMediaPath();
        $bannerSlider = [];
        foreach ($sliderImage as $imageData) {
            $data = [
                'title' => $imageData->getTitle(),
                'image' => $media . $imageData->getImage(),
                'link_product_sku' => $imageData->getLink()
            ];

            $bannerSlider[] = $data;
        }

        /**
         * Array containing new product banner
         */
        $objectManager = \Magento\Framework\App\ObjectManager
            ::getInstance();
        $helper = $objectManager->create(
            'Ipragmatech\Bannerblock\Helper\Data');
        $newProductLimit = $helper->getConfig(
            'bannerblocksection/newproduct/new_product_qty');
        $productFactory = $objectManager->create(
            '\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
        $collection = $productFactory->create();
        $todayDate = date('Y-m-d', time());
        $date = $objectManager
            ->create('\Magento\Framework\Stdlib\DateTime');
        $todayStartOfDayDate = date('Y-m-d H:i:s', time(0, 0,
            0)); // $date->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        $todayEndOfDayDate = date('Y-m-d H:i:s', time(23, 59,
            59)); // $date->setTime(23, 59, 59)->format('Y-m-d H:i:s');
        $collection->addAttributeToSelect('*');
        $collection->addAttributeToFilter('news_from_date',
            [
                'or' => [
                    0 => [
                        'date' => true,
                        'to'   => $todayEndOfDayDate
                    ],
                    1 => [
                        'is' => new \Zend_Db_Expr('null')
                    ]
                ]
            ], 'left')
            ->addAttributeToFilter('news_to_date',
                [
                    'or' => [
                        0 => [
                            'date' => true,
                            'from' => $todayStartOfDayDate
                        ],
                        1 => [
                            'is' => new \Zend_Db_Expr('null')
                        ]
                    ]
                ], 'left')
            ->addAttributeToFilter(
                [
                    [
                        'attribute' => 'news_from_date',
                        'is'        => new \Zend_Db_Expr('not null')
                    ],
                    [
                        'attribute' => 'news_to_date',
                        'is'        => new \Zend_Db_Expr('not null')
                    ]
                ])
            ->addAttributeToSort('news_from_date', 'desc')
            ->getSelect()
            ->limit($newProductLimit);
        $imagehelper = $objectManager->create(
            'Magento\Catalog\Helper\Image');
        foreach ($collection as $product) {
            $data = [
                'sku'   => $product->getSku(),
                'id'    => $product->getEntityId(),
                'name'  => $product->getName(),
                'image' => $product->getImage()
            ];

            $newProductArray[] = $data;
        }
        /**
         * returning
         */
        $returnData[] = [
            'banner'            => $bannerSlider,
            'categoryBanner'    => $categoryBanner,
            'topchoicebanner'   => $topChoiceBanner,
            'newproduct'        => $newProductArray,
            'popularMenuBanner' => $popularMenus
        ];

        return $returnData;
    }

}

