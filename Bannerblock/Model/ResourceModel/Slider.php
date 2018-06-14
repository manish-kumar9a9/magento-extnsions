<?php
namespace Ipragmatech\Bannerblock\Model\ResourceModel;

class Slider extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('slider', 'slider_id');
    }
}
?>