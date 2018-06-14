<?php
namespace Ipragmatech\Bannerblock\Model;

class Slider extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ipragmatech\Bannerblock\Model\ResourceModel\Slider');
    }
}
?>