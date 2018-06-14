<?php
namespace Ipragmatech\Bannerblock\Model\ResourceModel\Slider;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ipragmatech\Bannerblock\Model\Slider', 'Ipragmatech\Bannerblock\Model\ResourceModel\Slider');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }

}
?>