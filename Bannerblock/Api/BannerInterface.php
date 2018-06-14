<?php
/**
 * Copyright 2016 iPragmatech. All rights reserved.
 */

namespace Ipragmatech\Bannerblock\Api;


interface BannerInterface
{
	/**
	 * Return polpular menu list.
	 *
	 * @param int $customerId
	 * @return array
	 */
	public function popularMenu();
	
	/**
	 * Return polpular category list.
	 *
	 * @param int $customerId
	 * @return array
	 */
	//public function categoryList($customerId);
}
