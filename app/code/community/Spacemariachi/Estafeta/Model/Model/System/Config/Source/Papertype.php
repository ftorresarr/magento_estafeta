<?php
/**
 * Created by PhpStorm.
 * User: fer
 * Date: 8/09/15
 * Time: 02:35 PM
 */

class spacemariachi_estafeta_Model_System_Config_Source_Papertype
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 3, 'label'=>Mage::helper('spacemariachi_estafeta')->__('Plantilla Tamaño Oficio de 4 Etiquetas')),
            array('value' => 2, 'label'=>Mage::helper('spacemariachi_estafeta')->__('Papel Etiqueta Térmica de 6 x 4 pulgadas')),
            array('value' => 1, 'label'=>Mage::helper('spacemariachi_estafeta')->__('Papel Bond Tamaño Carta')),
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
            0 => Mage::helper('spacemariachi_estafeta')->__('Cotizacion con sobre'),
            1 => Mage::helper('spacemariachi_estafeta')->__('Cotizacion con paquete datos ficticios'),
            2 => Mage::helper('spacemariachi_estafeta')->__('Cotizacion con paquete datos reales'),
            3 => Mage::helper('spacemariachi_estafeta')->__('Solo frecuencia'),
        );
    }

}