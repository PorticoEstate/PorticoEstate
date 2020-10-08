<?php

class NySaksmappe
{

    /**
     * @var Saksmappe $mappe
     */
    protected $mappe = null;

    /**
     * @var ArkivKontekst $kontekst
     */
    protected $kontekst = null;

    /**
     * @param Saksmappe $mappe
     * @param ArkivKontekst $kontekst
     */
    public function __construct($mappe, $kontekst)
    {
      $this->mappe = $mappe;
      $this->kontekst = $kontekst;
    }

    /**
     * @return Saksmappe
     */
    public function getMappe()
    {
      return $this->mappe;
    }

    /**
     * @param Saksmappe $mappe
     * @return NySaksmappe
     */
    public function setMappe($mappe)
    {
      $this->mappe = $mappe;
      return $this;
    }

    /**
     * @return ArkivKontekst
     */
    public function getKontekst()
    {
      return $this->kontekst;
    }

    /**
     * @param ArkivKontekst $kontekst
     * @return NySaksmappe
     */
    public function setKontekst($kontekst)
    {
      $this->kontekst = $kontekst;
      return $this;
    }

}
