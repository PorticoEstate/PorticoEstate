<?php
/**
 * File for class Bra5StructArrayOfDocumentSplitType
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5StructArrayOfDocumentSplitType originally named ArrayOfDocumentSplitType
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
class Bra5StructArrayOfDocumentSplitType extends Bra5WsdlClass
{
    /**
     * The DocumentSplitType
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * - minOccurs : 0
     * - nillable : true
     * @var Bra5StructDocumentSplitType
     */
    public $DocumentSplitType;
    /**
     * Constructor method for ArrayOfDocumentSplitType
     * @see parent::__construct()
     * @param Bra5StructDocumentSplitType $_documentSplitType
     * @return Bra5StructArrayOfDocumentSplitType
     */
    public function __construct($_documentSplitType = NULL)
    {
        parent::__construct(array('DocumentSplitType'=>$_documentSplitType),false);
    }
    /**
     * Get DocumentSplitType value
     * @return Bra5StructDocumentSplitType|null
     */
    public function getDocumentSplitType()
    {
        return $this->DocumentSplitType;
    }
    /**
     * Set DocumentSplitType value
     * @param Bra5StructDocumentSplitType $_documentSplitType the DocumentSplitType
     * @return Bra5StructDocumentSplitType
     */
    public function setDocumentSplitType($_documentSplitType)
    {
        return ($this->DocumentSplitType = $_documentSplitType);
    }
    /**
     * Returns the current element
     * @see Bra5WsdlClass::current()
     * @return Bra5StructDocumentSplitType
     */
    public function current()
    {
        return parent::current();
    }
    /**
     * Returns the indexed element
     * @see Bra5WsdlClass::item()
     * @param int $_index
     * @return Bra5StructDocumentSplitType
     */
    public function item($_index)
    {
        return parent::item($_index);
    }
    /**
     * Returns the first element
     * @see Bra5WsdlClass::first()
     * @return Bra5StructDocumentSplitType
     */
    public function first()
    {
        return parent::first();
    }
    /**
     * Returns the last element
     * @see Bra5WsdlClass::last()
     * @return Bra5StructDocumentSplitType
     */
    public function last()
    {
        return parent::last();
    }
    /**
     * Returns the element at the offset
     * @see Bra5WsdlClass::last()
     * @param int $_offset
     * @return Bra5StructDocumentSplitType
     */
    public function offsetGet($_offset)
    {
        return parent::offsetGet($_offset);
    }
    /**
     * Returns the attribute name
     * @see Bra5WsdlClass::getAttributeName()
     * @return string DocumentSplitType
     */
    public function getAttributeName()
    {
        return 'DocumentSplitType';
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see Bra5WsdlClass::__set_state()
     * @uses Bra5WsdlClass::__set_state()
     * @param array $_array the exported values
     * @return Bra5StructArrayOfDocumentSplitType
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
