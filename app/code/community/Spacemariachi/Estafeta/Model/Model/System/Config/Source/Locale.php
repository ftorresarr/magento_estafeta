<?php
/**
 * Created by PhpStorm.
 * User: fer
 * Date: 9/09/15
 * Time: 02:56 PM
 */

class spacemariachi_estafeta_Model_System_Config_Source_Locale
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'SPA', 'label'=>Mage::helper('spacemariachi_estafeta')->__('Español')),
            array('value' => 'ENG', 'label'=>Mage::helper('spacemariachi_estafeta')->__('Ingles')),
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
            'SPA' => Mage::helper('spacemariachi_estafeta')->__('Español'),
            'ENG' => Mage::helper('spacemariachi_estafeta')->__('Ingles'),
        );
    }

}