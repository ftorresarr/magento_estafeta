<?php
/**
 * Created by PhpStorm.
 * User: fer
 * Date: 9/09/15
 * Time: 02:56 PM
 */

class spacemariachi_estafeta_Model_System_Config_Source_Historytype
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'ALL', 'label'=>Mage::helper('spacemariachi_estafeta')->__('ALL')),
            array('value' => 'ONLY_EXCEPTIONS', 'label'=>Mage::helper('spacemariachi_estafeta')->__('ONLY_EXCEPTIONS')),
            array('value' => 'LAST_EVENT', 'label'=>Mage::helper('spacemariachi_estafeta')->__('LAST_EVENT')),
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'LAST_EVENT' => Mage::helper('spacemariachi_estafeta')->__('LAST_EVENT'),
            'ONLY_EXCEPTIONS' => Mage::helper('spacemariachi_estafeta')->__('ONLY_EXCEPTIONS'),
            'ALL' => Mage::helper('spacemariachi_estafeta')->__('ALL'),
        );
    }

}