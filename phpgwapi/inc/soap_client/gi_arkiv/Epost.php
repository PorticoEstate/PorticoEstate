<?php

class Epost extends ElektroniskAdresse
{

    /**
     * @var string $epostadresse
     */
    protected $epostadresse = null;

    /**
     * @param string $epostadresse
     */
    public function __construct($epostadresse)
    {
      $this->epostadresse = $epostadresse;
    }

    /**
     * @return string
     */
    public function getEpostadresse()
    {
      return $this->epostadresse;
    }

    /**
     * @param string $epostadresse
     * @return Epost
     */
    public function setEpostadresse($epostadresse)
    {
      $this->epostadresse = $epostadresse;
      return $this;
    }

}
