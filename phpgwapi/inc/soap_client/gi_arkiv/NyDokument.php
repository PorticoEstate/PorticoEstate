<?php

class NyDokument
{

    /**
     * @var Dokument $dokument
     */
    protected $dokument = null;

    /**
     * @var boolean $returnerFil
     */
    protected $returnerFil = null;

    /**
     * @var ArkivKontekst $kontekst
     */
    protected $kontekst = null;

    /**
     * @param Dokument $dokument
     * @param boolean $returnerFil
     * @param ArkivKontekst $kontekst
     */
    public function __construct($dokument, $returnerFil, $kontekst)
    {
      $this->dokument = $dokument;
      $this->returnerFil = $returnerFil;
      $this->kontekst = $kontekst;
    }

    /**
     * @return Dokument
     */
    public function getDokument()
    {
      return $this->dokument;
    }

    /**
     * @param Dokument $dokument
     * @return NyDokument
     */
    public function setDokument($dokument)
    {
      $this->dokument = $dokument;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getReturnerFil()
    {
      return $this->returnerFil;
    }

    /**
     * @param boolean $returnerFil
     * @return NyDokument
     */
    public function setReturnerFil($returnerFil)
    {
      $this->returnerFil = $returnerFil;
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
     * @return NyDokument
     */
    public function setKontekst($kontekst)
    {
      $this->kontekst = $kontekst;
      return $this;
    }

}
