<?php

class Soekefelt extends Kriterie
{

    /**
     * @var string $feltnavn
     */
    protected $feltnavn = null;

    /**
     * @var string $feltverdi
     */
    protected $feltverdi = null;

    /**
     * @param string $feltnavn
     * @param string $feltverdi
     */
    public function __construct($feltnavn, $feltverdi)
    {
      $this->feltnavn = $feltnavn;
      $this->feltverdi = $feltverdi;
    }

    /**
     * @return string
     */
    public function getFeltnavn()
    {
      return $this->feltnavn;
    }

    /**
     * @param string $feltnavn
     * @return Soekefelt
     */
    public function setFeltnavn($feltnavn)
    {
      $this->feltnavn = $feltnavn;
      return $this;
    }

    /**
     * @return string
     */
    public function getFeltverdi()
    {
      return $this->feltverdi;
    }

    /**
     * @param string $feltverdi
     * @return Soekefelt
     */
    public function setFeltverdi($feltverdi)
    {
      $this->feltverdi = $feltverdi;
      return $this;
    }

}
