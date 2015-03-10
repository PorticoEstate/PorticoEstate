<?php
/**
 * File for class Bra5StructDocument
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5StructDocument originally named Document
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
class Bra5StructDocument extends Bra5WsdlClass
{
    /**
     * The BFDoubleSided
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 1
     * @var boolean
     */
    public $BFDoubleSided;
    /**
     * The BFSeparateKeySheet
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 1
     * @var boolean
     */
    public $BFSeparateKeySheet;
    /**
     * The Classified
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 1
     * @var boolean
     */
    public $Classified;
    /**
     * The Priority
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 1
     * @var int
     */
    public $Priority;
    /**
     * The ProductionLineID
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 1
     * - nillable : true
     * @var int
     */
    public $ProductionLineID;
    /**
     * The DocSplitTypeID
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 1
     * - nillable : true
     * @var int
     */
    public $DocSplitTypeID;
    /**
     * The Attributes
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var Bra5StructArrayOfAttribute
     */
    public $Attributes;
    /**
     * The ID
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $ID;
    /**
     * The BFDocKey
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $BFDocKey;
    /**
     * The BFNoSheets
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $BFNoSheets;
    /**
     * The BBRegTime
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $BBRegTime;
    /**
     * The Name
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $Name;
    /**
     * The ClassName
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $ClassName;
    /**
     * The BaseClassName
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $BaseClassName;
    /**
     * Constructor method for Document
     * @see parent::__construct()
     * @param boolean $_bFDoubleSided
     * @param boolean $_bFSeparateKeySheet
     * @param boolean $_classified
     * @param int $_priority
     * @param int $_productionLineID
     * @param int $_docSplitTypeID
     * @param Bra5StructArrayOfAttribute $_attributes
     * @param string $_iD
     * @param string $_bFDocKey
     * @param string $_bFNoSheets
     * @param string $_bBRegTime
     * @param string $_name
     * @param string $_className
     * @param string $_baseClassName
     * @return Bra5StructDocument
     */
    public function __construct($_bFDoubleSided,$_bFSeparateKeySheet,$_classified,$_priority,$_productionLineID,$_docSplitTypeID,$_attributes = NULL,$_iD = NULL,$_bFDocKey = NULL,$_bFNoSheets = NULL,$_bBRegTime = NULL,$_name = NULL,$_className = NULL,$_baseClassName = NULL)
    {
        parent::__construct(array('BFDoubleSided'=>$_bFDoubleSided,'BFSeparateKeySheet'=>$_bFSeparateKeySheet,'Classified'=>$_classified,'Priority'=>$_priority,'ProductionLineID'=>$_productionLineID,'DocSplitTypeID'=>$_docSplitTypeID,'Attributes'=>($_attributes instanceof Bra5StructArrayOfAttribute)?$_attributes:new Bra5StructArrayOfAttribute($_attributes),'ID'=>$_iD,'BFDocKey'=>$_bFDocKey,'BFNoSheets'=>$_bFNoSheets,'BBRegTime'=>$_bBRegTime,'Name'=>$_name,'ClassName'=>$_className,'BaseClassName'=>$_baseClassName),false);
    }
    /**
     * Get BFDoubleSided value
     * @return boolean
     */
    public function getBFDoubleSided()
    {
        return $this->BFDoubleSided;
    }
    /**
     * Set BFDoubleSided value
     * @param boolean $_bFDoubleSided the BFDoubleSided
     * @return boolean
     */
    public function setBFDoubleSided($_bFDoubleSided)
    {
        return ($this->BFDoubleSided = $_bFDoubleSided);
    }
    /**
     * Get BFSeparateKeySheet value
     * @return boolean
     */
    public function getBFSeparateKeySheet()
    {
        return $this->BFSeparateKeySheet;
    }
    /**
     * Set BFSeparateKeySheet value
     * @param boolean $_bFSeparateKeySheet the BFSeparateKeySheet
     * @return boolean
     */
    public function setBFSeparateKeySheet($_bFSeparateKeySheet)
    {
        return ($this->BFSeparateKeySheet = $_bFSeparateKeySheet);
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
     * Get Priority value
     * @return int
     */
    public function getPriority()
    {
        return $this->Priority;
    }
    /**
     * Set Priority value
     * @param int $_priority the Priority
     * @return int
     */
    public function setPriority($_priority)
    {
        return ($this->Priority = $_priority);
    }
    /**
     * Get ProductionLineID value
     * @return int
     */
    public function getProductionLineID()
    {
        return $this->ProductionLineID;
    }
    /**
     * Set ProductionLineID value
     * @param int $_productionLineID the ProductionLineID
     * @return int
     */
    public function setProductionLineID($_productionLineID)
    {
        return ($this->ProductionLineID = $_productionLineID);
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
     * Get Attributes value
     * @return Bra5StructArrayOfAttribute|null
     */
    public function getAttributes()
    {
        return $this->Attributes;
    }
    /**
     * Set Attributes value
     * @param Bra5StructArrayOfAttribute $_attributes the Attributes
     * @return Bra5StructArrayOfAttribute
     */
    public function setAttributes($_attributes)
    {
        return ($this->Attributes = $_attributes);
    }
    /**
     * Get ID value
     * @return string|null
     */
    public function getID()
    {
        return $this->ID;
    }
    /**
     * Set ID value
     * @param string $_iD the ID
     * @return string
     */
    public function setID($_iD)
    {
        return ($this->ID = $_iD);
    }
    /**
     * Get BFDocKey value
     * @return string|null
     */
    public function getBFDocKey()
    {
        return $this->BFDocKey;
    }
    /**
     * Set BFDocKey value
     * @param string $_bFDocKey the BFDocKey
     * @return string
     */
    public function setBFDocKey($_bFDocKey)
    {
        return ($this->BFDocKey = $_bFDocKey);
    }
    /**
     * Get BFNoSheets value
     * @return string|null
     */
    public function getBFNoSheets()
    {
        return $this->BFNoSheets;
    }
    /**
     * Set BFNoSheets value
     * @param string $_bFNoSheets the BFNoSheets
     * @return string
     */
    public function setBFNoSheets($_bFNoSheets)
    {
        return ($this->BFNoSheets = $_bFNoSheets);
    }
    /**
     * Get BBRegTime value
     * @return string|null
     */
    public function getBBRegTime()
    {
        return $this->BBRegTime;
    }
    /**
     * Set BBRegTime value
     * @param string $_bBRegTime the BBRegTime
     * @return string
     */
    public function setBBRegTime($_bBRegTime)
    {
        return ($this->BBRegTime = $_bBRegTime);
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
     * Get ClassName value
     * @return string|null
     */
    public function getClassName()
    {
        return $this->ClassName;
    }
    /**
     * Set ClassName value
     * @param string $_className the ClassName
     * @return string
     */
    public function setClassName($_className)
    {
        return ($this->ClassName = $_className);
    }
    /**
     * Get BaseClassName value
     * @return string|null
     */
    public function getBaseClassName()
    {
        return $this->BaseClassName;
    }
    /**
     * Set BaseClassName value
     * @param string $_baseClassName the BaseClassName
     * @return string
     */
    public function setBaseClassName($_baseClassName)
    {
        return ($this->BaseClassName = $_baseClassName);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see Bra5WsdlClass::__set_state()
     * @uses Bra5WsdlClass::__set_state()
     * @param array $_array the exported values
     * @return Bra5StructDocument
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
