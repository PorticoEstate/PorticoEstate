<?php
/**
 * File for class Bra5StructArrayOfDocumentClassPrivilege
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5StructArrayOfDocumentClassPrivilege originally named ArrayOfDocumentClassPrivilege
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
class Bra5StructArrayOfDocumentClassPrivilege extends Bra5WsdlClass
{
    /**
     * The DocumentClassPrivilege
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * - minOccurs : 0
     * - nillable : true
     * @var Bra5StructDocumentClassPrivilege
     */
    public $DocumentClassPrivilege;
    /**
     * Constructor method for ArrayOfDocumentClassPrivilege
     * @see parent::__construct()
     * @param Bra5StructDocumentClassPrivilege $_documentClassPrivilege
     * @return Bra5StructArrayOfDocumentClassPrivilege
     */
    public function __construct($_documentClassPrivilege = NULL)
    {
        parent::__construct(array('DocumentClassPrivilege'=>$_documentClassPrivilege),false);
    }
    /**
     * Get DocumentClassPrivilege value
     * @return Bra5StructDocumentClassPrivilege|null
     */
    public function getDocumentClassPrivilege()
    {
        return $this->DocumentClassPrivilege;
    }
    /**
     * Set DocumentClassPrivilege value
     * @param Bra5StructDocumentClassPrivilege $_documentClassPrivilege the DocumentClassPrivilege
     * @return Bra5StructDocumentClassPrivilege
     */
    public function setDocumentClassPrivilege($_documentClassPrivilege)
    {
        return ($this->DocumentClassPrivilege = $_documentClassPrivilege);
    }
    /**
     * Returns the current element
     * @see Bra5WsdlClass::current()
     * @return Bra5StructDocumentClassPrivilege
     */
    public function current()
    {
        return parent::current();
    }
    /**
     * Returns the indexed element
     * @see Bra5WsdlClass::item()
     * @param int $_index
     * @return Bra5StructDocumentClassPrivilege
     */
    public function item($_index)
    {
        return parent::item($_index);
    }
    /**
     * Returns the first element
     * @see Bra5WsdlClass::first()
     * @return Bra5StructDocumentClassPrivilege
     */
    public function first()
    {
        return parent::first();
    }
    /**
     * Returns the last element
     * @see Bra5WsdlClass::last()
     * @return Bra5StructDocumentClassPrivilege
     */
    public function last()
    {
        return parent::last();
    }
    /**
     * Returns the element at the offset
     * @see Bra5WsdlClass::last()
     * @param int $_offset
     * @return Bra5StructDocumentClassPrivilege
     */
    public function offsetGet($_offset)
    {
        return parent::offsetGet($_offset);
    }
    /**
     * Returns the attribute name
     * @see Bra5WsdlClass::getAttributeName()
     * @return string DocumentClassPrivilege
     */
    public function getAttributeName()
    {
        return 'DocumentClassPrivilege';
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see Bra5WsdlClass::__set_state()
     * @uses Bra5WsdlClass::__set_state()
     * @param array $_array the exported values
     * @return Bra5StructArrayOfDocumentClassPrivilege
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
