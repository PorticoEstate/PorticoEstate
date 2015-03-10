<?php
/**
 * File for class Bra5StructGetAvailableClassesResponse
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5StructGetAvailableClassesResponse originally named GetAvailableClassesResponse
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
class Bra5StructGetAvailableClassesResponse extends Bra5WsdlClass
{
    /**
     * The GetAvailableClassesResult
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var Bra5StructArrayOfDocumentClassPrivilege
     */
    public $GetAvailableClassesResult;
    /**
     * Constructor method for GetAvailableClassesResponse
     * @see parent::__construct()
     * @param Bra5StructArrayOfDocumentClassPrivilege $_getAvailableClassesResult
     * @return Bra5StructGetAvailableClassesResponse
     */
    public function __construct($_getAvailableClassesResult = NULL)
    {
        parent::__construct(array('GetAvailableClassesResult'=>($_getAvailableClassesResult instanceof Bra5StructArrayOfDocumentClassPrivilege)?$_getAvailableClassesResult:new Bra5StructArrayOfDocumentClassPrivilege($_getAvailableClassesResult)),false);
    }
    /**
     * Get GetAvailableClassesResult value
     * @return Bra5StructArrayOfDocumentClassPrivilege|null
     */
    public function getGetAvailableClassesResult()
    {
        return $this->GetAvailableClassesResult;
    }
    /**
     * Set GetAvailableClassesResult value
     * @param Bra5StructArrayOfDocumentClassPrivilege $_getAvailableClassesResult the GetAvailableClassesResult
     * @return Bra5StructArrayOfDocumentClassPrivilege
     */
    public function setGetAvailableClassesResult($_getAvailableClassesResult)
    {
        return ($this->GetAvailableClassesResult = $_getAvailableClassesResult);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see Bra5WsdlClass::__set_state()
     * @uses Bra5WsdlClass::__set_state()
     * @param array $_array the exported values
     * @return Bra5StructGetAvailableClassesResponse
     */
    public static function __set_state(array $_array,$_className = __CLASS__)
    {
        return parent::__set_state($_array,$_className);
    }
    /**
     * Method returning the class name
     * @return string __CLASS__
     */
    public function __toString()
    {
        return __CLASS__;
    }
}
