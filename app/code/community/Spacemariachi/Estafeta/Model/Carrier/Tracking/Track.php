<?php
/**
 * Created by PhpStorm.
 * User: fer
 * Date: 9/09/15
 * Time: 12:34 PM
 */

class Spacemariachi_Estafeta_Model_Carrier_Tracking_Track extends Varien_Object
{
    public  $_code = 'estafeta';
    public $trackTestUrl = 'https://trackingqa.estafeta.com/Service.asmx?wsdl';
    public $trackProdUrl = 'https://tracking.estafeta.com/Service.asmx?wsdl';
    private $trackingNumber, $_trackingInfo, $_locale, $_trackingData;
    private $_progressDetail = array();

    public function track($trackingNumber) {
        $this->_setTrackingNumber($trackingNumber);
        return $this->_getTrackingInfoObj();
    }

    private function _getTrackingInfoObj() {
        $trackingInfo = $this->_getTrackingInfo();
        $trackingInfoObj = new Varien_Object();
        $trackingInfoObj->setCarrierTitle($this->getConfigData('title'))
            ->setTracking($this->_getTrackingNumber());

        if($trackingInfo->ExecuteQueryResult->errorCode) {
            $trackingInfoObj->setErrorMessage($trackingInfo->ExecuteQueryResult->{'errorCodeDescription'.$this->_getLocale()});
        } else {
            $trackingInfoObj->setStatus($this->_getTrackingData()->{'status'.$this->_getLocale()});
            $datetime = DateTime::createFromFormat('d/m/Y H:i:s a',$this->_getTrackingData()->deliveryData->deliveryDateTime);
            $trackingInfoObj->setDeliverydate($datetime->format('m/d/Y'));
            $trackingInfoObj->setDeliverytime($datetime->format('H:i:s'));
            $trackingInfoObj->setDeliverylocation($this->_getTrackingData()->deliveryData->destinationName);
            $trackingInfoObj->setDeliverylocation($this->_getTrackingData()->deliveryData->receiverName);
            $trackingInfoObj->setNumber($this->_getTrackingNumber());
            $trackingInfoObj->setProgressdetail($this->_getProgressdetail());
        }
        return $trackingInfoObj;
    }

    private function _getTrackingData() {
        if(empty($this->_trackingData)) {
            $this->_setTrackingData();
        }
        return $this->_trackingData;
    }

    private function _setTrackingData() {
        $this->_trackingData = $this->_getTrackingInfo()->ExecuteQueryResult->trackingData->TrackingData;
        return $this;
    }

    private function _getProgressdetail() {
        if(empty($this->_progressDetail)) {
            $this->_setProgressdetail();
        }
        return $this->_progressDetail;

    }

    private function _setProgressdetail() {
        foreach($this->_getTrackingData()->history->History as $history) {
            $datetime = DateTime::createFromFormat('d/m/Y H:i:s a',$history->eventDateTime);
            $activity = !empty($history->{'exceptionCodeDescription'.$this->_getLocale()}) ? $history->{'exceptionCodeDescription'.$this->_getLocale()} : $history->{'eventDescription'.$this->_getLocale()};
            $this->_progressDetail[] = array(
                'deliverydate'      => $datetime->format('M d Y'),
                'deliverytime'      => $datetime->format('H:i:s'),
                'deliverylocation'  => $history->eventPlaceName,
                'activity'          => $activity
            );
        }
        return $this;
    }

    private function _getLocale() {
        if(empty($this->_locale)) {
            $this->_setLocale();
        }
        return $this->_locale;
    }

    private function _setLocale() {
        $this->_locale = $this->getConfigData('locale');
        return $this;
    }

    private function _getTrackingInfo() {
        if(empty($this->_trackingInfo)) {
            $this->_trackingInfo = $this->_setTrackingInfo();
        }
        return $this->_trackingInfo;
    }

    private function _setTrackingInfo(){
        $options = array(
            'trace' => $this->getConfigData('debug')
        );
        $cliente = new SoapClient($this->_getUrl(), $options);
        $result = $cliente->ExecuteQuery($this->_getRequestArray());
        return $result;
    }

    private function _getRequestArray() {
        return array(
            'suscriberId'           => $this->getConfigData('id_usuario'),
            'login'                 => $this->getConfigData('usuario'),
            'password'              => $this->getConfigData('password'),
            'searchType'            => $this->_getSearchType(),
            'searchConfiguration'   => $this->_getSearchConfiguration()
        );
    }

    private function _getSearchConfiguration() {
        return array(
            'includeDimensions'         => false,
            'includeWaybillReplaceData' => false,
            'includeReturnDocumentData' => false,
            'includeMultipleServiceData'=> false,
            'includeInternationalData'  => false,
            'includeSignature'          => true,
            'includeCustomerInfo'       => false,
            'historyConfiguration'      => $this->_getHistoryConfiguration(),
            'filterType'                => $this->_getFilterType()
        );
    }

    private function _getFilterType() {
        return array(
            'filterInformation' => false,
        );
    }

    private function _getHistoryConfiguration() {
        return array(
            'includeHistory'    => true,
            'historyType'       => $this->getConfigData('history_type')
        );
    }

    private function _getSearchType() {
        return array(
            'type'          => 'L', //Request a list of one waybill
            'waybillList'   => $this->_getWaybillList(),
         );
    }

    private function _getWaybillList() {
        return array(
            'waybillType'   => $this->_getWaybillType(),
            'waybills'      => array($this->_getTrackingNumber())
        );
    }

    private function _getWaybillType() {
        if(strlen(trim($this->_getTrackingNumber())) == 22) { //Estafeta has two types of waybills, one HAS to be 22 chars
            return 'G';
        } else {
            return 'R';
        }
    }

    private function _setTrackingNumber($trackingNumber) {
        $this->trackingNumber = $trackingNumber;
        return $this;
    }
    private function _getTrackingNumber() {
        return $this->trackingNumber;

    }


    private function _getUrl() {
        if(empty($this->url)) {
            $this->_setUrl();
        }
        return $this->url;
    }
    private function _setUrl() {
        if(!$this->getConfigData('debug')){
            $this->url = $this->trackProdUrl;
        } else {
            $this->url = $this->trackTestUrl;
        }
    }
    /**
     * Retrieve information from carrier configuration
     *
     * @param   string $field
     * @return  mixed
     */
    public function getConfigData($field)
    {
        if (empty($this->_code)) {
            return false;
        }
        $path = 'carriers/'.$this->_code.'/'.$field;
        return Mage::getStoreConfig($path, $this->getStore());
    }

}
