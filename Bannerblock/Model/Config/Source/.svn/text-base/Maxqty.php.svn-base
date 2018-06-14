<?php
namespace Ipragmatech\Bannerblock\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Maxqty implements ArrayInterface
{

	/*
	 * Option getter
	 * @return array
	 */
	public function toOptionArray()
	{
		$arr = $this->toArray();
		$ret = [];
		foreach ($arr as $key => $value)
		{
			$ret[] = [
					'value' => $key,
					'label' => $value
			];
		}
		return $ret;
	}

	/*
	 * Get options in "key-value" format
	 * @return array
	 */
	public function toArray()
	{
		$maxvalList = array(
				'1' => 1,
				'2' => 2,
				'3' => 3,
				'4' => 4,
				'5' => 5,
		);
		return $maxvalList;
	}
}