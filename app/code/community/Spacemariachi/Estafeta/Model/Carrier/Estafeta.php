<?php
/**
 * Created by PhpStorm.
 * User: Fernando Torres
 * Date: 17/02/15
 * Time: 17:51
 */

class Spacemariachi_Estafeta_Model_Carrier_Estafeta extends Mage_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface {
    public  $_code = 'estafeta';
    public $url;
    private $_frecuenciaTestUrl = 'http://frecuenciacotizadorqa.estafeta.com/Service.asmx?WSDL';
    private $_frequienciaProdUrl = 'http://frecuenciacotizador.estafeta.com/Service.asmx?WSDL';
    private $request;
    protected $_rates;

    /**
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return bool|Mage_Shipping_Model_Rate_Result
     */


    public function collectRates(Mage_Shipping_Model_Rate_Request $request) {
        if (!$this->getConfigData('active')) {
            return false;
        }
        $this->_setRequest($request);

        $result = Mage::getModel('shipping/rate_result');
        foreach($this->_getRates() as $rate){
            $method = Mage::getModel('shipping/rate_result_method');
            $method->setCarrier($this->_code);
            $method->setMethod($this->_code.$rate->DescripcionServicio);
            $method->setCarrierTitle($this->getConfigData('title'));
            $method->setMethodTitle($rate->DescripcionServicio);
            $method->setPrice($this->_getRatePrice($rate));
            $result->append($method);
        }
        return $result;
    }

    /**
     * @param $rate
     * @return float
     * Estafeta returns MXN with no option for other currencies, convert if necessary
     */

    private function _getRatePrice($rate) {
        $amount = $rate->CostoTotal;
        $currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();
        if ('MXN' != $currentCurrencyCode) {
            $allowedCurrencies = Mage::getModel('directory/currency')->getConfigAllowCurrencies();
            $rates = Mage::getModel('directory/currency')->getCurrencyRates($currentCurrencyCode, array_values($allowedCurrencies));
            $amount = $amount/$rates['MXN'];
        }
        return round($amount, 2);
    }


    private function _ejecutarFrecuencia() {
        $options = array(
            'trace' => $this->getConfigData('debug')
        );
        $cliente = new SoapClient($this->_getUrl(), $options);
        $result = $cliente->FrecuenciaCotizador($this->_getRequestArray());
        return $result->FrecuenciaCotizadorResult->Respuesta->TipoServicio->TipoServicio;

    }

    private function _getRequestArray() {
        return array(
            'idusuario'     => $this->getConfigData('id_usuario'),
            'usuario'       => $this->getConfigData('usuario'),
            'contra'        => $this->getConfigData('password'),
            'esFrecuencia'  => $this->getConfigData('es_frecuencia'),
            'esLista'       => true,
            'tipoEnvio'     => $this->_getTipoEnvio(),
            'datosOrigen'   => array($this->getConfigData('origin_zip')),
            'datosDestino'  => array($this->_getRequestZip()),
        );
    }

    public function getAllowedMethods() {
        return array('estafeta'=>$this->getConfigData('name'));
    }

    public function isTrackingAvailable() {
        return true;
    }

    private function _getRequestZip() {
        return $this->_getRequest()->getDestPostcode();
    }
    private function _getTipoEnvio() {
        switch ($this->getConfigData('cotizar')) {
            case 0:
                return false;
                break;
            case 1:
                return array(
                    'EsPaquete' => true,
                    'Largo' => $this->getConfigData('largo'),
                    'Peso'  => $this->getConfigData('peso'),
                    'Alto'  => $this->getConfigData('alto'),
                    'Ancho' => $this->getConfigData('ancho')
                );
                break;
        }
        return false;

    }

    public function getTrackingInfo($trackNumber) {
        return Mage::getModel('spacemariachi_estafeta/carrier_tracking_track')->setStore($this->getStore())->track($trackNumber, $this->getConfigData('debug'));
    }

    public function _getUrl() {
        if(empty($this->url)) {
            $this->_setUrl();
        }
        return $this->url;
    }
    private function _setUrl() {
        if(!$this->getConfigData('debug')){
            $this->url = $this->_frequienciaProdUrl;
        } else {
            $this->url = $this->_frecuenciaTestUrl;
        }
    }

    /**
     * @return Mage_Shipping_Model_Rate_Request
     */
    private function _getRequest() {
        return $this->request;
    }
    private function _setRequest(Mage_Shipping_Model_Rate_Request $request) {
        if(empty($this->request)){
            $this->request = $request;
        }
    }
    private function _getRates() {
        if(empty($this->_rates)) {
            $this->_setRates();
        }
        return $this->_rates;
    }

    private function _setRates() {
        $this->_rates = $this->_ejecutarFrecuencia();
    }

}
