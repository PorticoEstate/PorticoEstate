<?php

class Telefon extends ElektroniskAdresse
{

    /**
     * @var string $telefonnummer
     */
    protected $telefonnummer = null;

    /**
     * @param string $telefonnummer
     */
    public function __construct($telefonnummer)
    {
      $this->telefonnummer = $telefonnummer;
    }

    /**
     * @return string
     */
    public function getTelefonnummer()
    {
      return $this->telefonnummer;
    }

    /**
     * @param string $telefonnummer
     * @return Telefon
     */
    public function setTelefonnummer($telefonnummer)
    {
      $this->telefonnummer = $telefonnummer;
      return $this;
    }

}
