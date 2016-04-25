<?php
/**
 * File for class Bra5StructVariant
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5StructVariant originally named Variant
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
class Bra5StructVariant extends Bra5WsdlClass
{
    /**
     * The FileName
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $FileName;
    /**
     * The FileExtension
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $FileExtension;
    /**
     * The VaultName
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $VaultName;
    /**
     * Constructor method for Variant
     * @see parent::__construct()
     * @param string $_fileName
     * @param string $_fileExtension
     * @param string $_vaultName
     * @return Bra5StructVariant
     */
    public function __construct($_fileName = NULL,$_fileExtension = NULL,$_vaultName = NULL)
    {
        parent::__construct(array('FileName'=>$_fileName,'FileExtension'=>$_fileExtension,'VaultName'=>$_vaultName),false);
    }
    /**
     * Get FileName value
     * @return string|null
     */
    public function getFileName()
    {
        return $this->FileName;
    }
    /**
     * Set FileName value
     * @param string $_fileName the FileName
     * @return string
     */
    public function setFileName($_fileName)
    {
        return ($this->FileName = $_fileName);
    }
    /**
     * Get FileExtension value
     * @return string|null
     */
    public function getFileExtension()
    {
        return $this->FileExtension;
    }
    /**
     * Set FileExtension value
     * @param string $_fileExtension the FileExtension
     * @return string
     */
    public function setFileExtension($_fileExtension)
    {
        return ($this->FileExtension = $_fileExtension);
    }
    /**
     * Get VaultName value
     * @return string|null
     */
    public function getVaultName()
    {
        return $this->VaultName;
    }
    /**
     * Set VaultName value
     * @param string $_vaultName the VaultName
     * @return string
     */
    public function setVaultName($_vaultName)
    {
        return ($this->VaultName = $_vaultName);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see Bra5WsdlClass::__set_state()
     * @uses Bra5WsdlClass::__set_state()
     * @param array $_array the exported values
     * @return Bra5StructVariant
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
