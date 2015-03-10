<?php
/**
 * File for class Bra5StructDocumentClassPrivilege
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5StructDocumentClassPrivilege originally named DocumentClassPrivilege
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
class Bra5StructDocumentClassPrivilege extends Bra5WsdlClass
{
    /**
     * The DocClassId
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 1
     * @var int
     */
    public $DocClassId;
    /**
     * The ParentId
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 1
     * @var int
     */
    public $ParentId;
    /**
     * The Classified
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 1
     * @var boolean
     */
    public $Classified;
    /**
     * The DocClassName
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $DocClassName;
    /**
     * Constructor method for DocumentClassPrivilege
     * @see parent::__construct()
     * @param int $_docClassId
     * @param int $_parentId
     * @param boolean $_classified
     * @param string $_docClassName
     * @return Bra5StructDocumentClassPrivilege
     */
    public function __construct($_docClassId,$_parentId,$_classified,$_docClassName = NULL)
    {
        parent::__construct(array('DocClassId'=>$_docClassId,'ParentId'=>$_parentId,'Classified'=>$_classified,'DocClassName'=>$_docClassName),false);
    }
    /**
     * Get DocClassId value
     * @return int
     */
    public function getDocClassId()
    {
        return $this->DocClassId;
    }
    /**
     * Set DocClassId value
     * @param int $_docClassId the DocClassId
     * @return int
     */
    public function setDocClassId($_docClassId)
    {
        return ($this->DocClassId = $_docClassId);
    }
    /**
     * Get ParentId value
     * @return int
     */
    public function getParentId()
    {
        return $this->ParentId;
    }
    /**
     * Set ParentId value
     * @param int $_parentId the ParentId
     * @return int
     */
    public function setParentId($_parentId)
    {
        return ($this->ParentId = $_parentId);
    }
    /**
     * Get Classified value
     * @return boolean
     */
    public function getClassified()
    {
        return $this->Classified;
    }
    /**
     * Set Classified value
     * @param boolean $_classified the Classified
     * @return boolean
     */
    public function setClassified($_classified)
    {
        return ($this->Classified = $_classified);
    }
    /**
     * Get DocClassName value
     * @return string|null
     */
    public function getDocClassName()
    {
        return $this->DocClassName;
    }
    /**
     * Set DocClassName value
     * @param string $_docClassName the DocClassName
     * @return string
     */
    public function setDocClassName($_docClassName)
    {
        return ($this->DocClassName = $_docClassName);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see Bra5WsdlClass::__set_state()
     * @uses Bra5WsdlClass::__set_state()
     * @param array $_array the exported values
     * @return Bra5StructDocumentClassPrivilege
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
