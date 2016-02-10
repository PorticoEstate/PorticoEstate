<?php
/**
 * File for class Bra5StructDocumentSplitType
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5StructDocumentSplitType originally named DocumentSplitType
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
class Bra5StructDocumentSplitType extends Bra5WsdlClass
{
    /**
     * The DocSplitTypeID
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 1
     * @var int
     */
    public $DocSplitTypeID;
    /**
     * The DocClassID
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 1
     * @var int
     */
    public $DocClassID;
    /**
     * The IsConcatDocument
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 1
     * @var boolean
     */
    public $IsConcatDocument;
    /**
     * The SplitAttributeID
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 1
     * @var int
     */
    public $SplitAttributeID;
    /**
     * The Active
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 1
     * @var boolean
     */
    public $Active;
    /**
     * The NewDocSplitTypeID
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 1
     * - nillable : true
     * @var int
     */
    public $NewDocSplitTypeID;
    /**
     * The Default
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 1
     * @var boolean
     */
    public $Default;
    /**
     * The Name
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $Name;
    /**
     * Constructor method for DocumentSplitType
     * @see parent::__construct()
     * @param int $_docSplitTypeID
     * @param int $_docClassID
     * @param boolean $_isConcatDocument
     * @param int $_splitAttributeID
     * @param boolean $_active
     * @param int $_newDocSplitTypeID
     * @param boolean $_default
     * @param string $_name
     * @return Bra5StructDocumentSplitType
     */
    public function __construct($_docSplitTypeID,$_docClassID,$_isConcatDocument,$_splitAttributeID,$_active,$_newDocSplitTypeID,$_default,$_name = NULL)
    {
        parent::__construct(array('DocSplitTypeID'=>$_docSplitTypeID,'DocClassID'=>$_docClassID,'IsConcatDocument'=>$_isConcatDocument,'SplitAttributeID'=>$_splitAttributeID,'Active'=>$_active,'NewDocSplitTypeID'=>$_newDocSplitTypeID,'Default'=>$_default,'Name'=>$_name),false);
    }
    /**
     * Get DocSplitTypeID value
     * @return int
     */
    public function getDocSplitTypeID()
    {
        return $this->DocSplitTypeID;
    }
    /**
     * Set DocSplitTypeID value
     * @param int $_docSplitTypeID the DocSplitTypeID
     * @return int
     */
    public function setDocSplitTypeID($_docSplitTypeID)
    {
        return ($this->DocSplitTypeID = $_docSplitTypeID);
    }
    /**
     * Get DocClassID value
     * @return int
     */
    public function getDocClassID()
    {
        return $this->DocClassID;
    }
    /**
     * Set DocClassID value
     * @param int $_docClassID the DocClassID
     * @return int
     */
    public function setDocClassID($_docClassID)
    {
        return ($this->DocClassID = $_docClassID);
    }
    /**
     * Get IsConcatDocument value
     * @return boolean
     */
    public function getIsConcatDocument()
    {
        return $this->IsConcatDocument;
    }
    /**
     * Set IsConcatDocument value
     * @param boolean $_isConcatDocument the IsConcatDocument
     * @return boolean
     */
    public function setIsConcatDocument($_isConcatDocument)
    {
        return ($this->IsConcatDocument = $_isConcatDocument);
    }
    /**
     * Get SplitAttributeID value
     * @return int
     */
    public function getSplitAttributeID()
    {
        return $this->SplitAttributeID;
    }
    /**
     * Set SplitAttributeID value
     * @param int $_splitAttributeID the SplitAttributeID
     * @return int
     */
    public function setSplitAttributeID($_splitAttributeID)
    {
        return ($this->SplitAttributeID = $_splitAttributeID);
    }
    /**
     * Get Active value
     * @return boolean
     */
    public function getActive()
    {
        return $this->Active;
    }
    /**
     * Set Active value
     * @param boolean $_active the Active
     * @return boolean
     */
    public function setActive($_active)
    {
        return ($this->Active = $_active);
    }
    /**
     * Get NewDocSplitTypeID value
     * @return int
     */
    public function getNewDocSplitTypeID()
    {
        return $this->NewDocSplitTypeID;
    }
    /**
     * Set NewDocSplitTypeID value
     * @param int $_newDocSplitTypeID the NewDocSplitTypeID
     * @return int
     */
    public function setNewDocSplitTypeID($_newDocSplitTypeID)
    {
        return ($this->NewDocSplitTypeID = $_newDocSplitTypeID);
    }
    /**
     * Get Default value
     * @return boolean
     */
    public function getDefault()
    {
        return $this->Default;
    }
    /**
     * Set Default value
     * @param boolean $_default the Default
     * @return boolean
     */
    public function setDefault($_default)
    {
        return ($this->Default = $_default);
    }
    /**
     * Get Name value
     * @return string|null
     */
    public function getName()
    {
        return $this->Name;
    }
    /**
     * Set Name value
     * @param string $_name the Name
     * @return string
     */
    public function setName($_name)
    {
        return ($this->Name = $_name);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see Bra5WsdlClass::__set_state()
     * @uses Bra5WsdlClass::__set_state()
     * @param array $_array the exported values
     * @return Bra5StructDocumentSplitType
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
