<?php

class ExtendedDocument extends Document
{

    /**
     * @var ArrayOfVariant $Variants
     */
    protected $Variants = null;

    /**
     * @param boolean $BFDoubleSided
     * @param boolean $BFSeparateKeySheet
     * @param boolean $Classified
     * @param int $Priority
     */
    public function __construct($BFDoubleSided, $BFSeparateKeySheet, $Classified, $Priority)
    {
      parent::__construct($BFDoubleSided, $BFSeparateKeySheet, $Classified, $Priority);
    }

    /**
     * @return ArrayOfVariant
     */
    public function getVariants()
    {
      return $this->Variants;
    }

    /**
     * @param ArrayOfVariant $Variants
     * @return ExtendedDocument
     */
    public function setVariants($Variants)
    {
      $this->Variants = $Variants;
      return $this;
    }

}
