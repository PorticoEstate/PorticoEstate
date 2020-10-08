<?php

class Dokument
{

    /**
     * @var string $systemID
     */
    protected $systemID = null;

    /**
     * @var string $dokumentnummer
     */
    protected $dokumentnummer = null;

    /**
     * @var TilknyttetRegistreringSom $tilknyttetRegistreringSom
     */
    protected $tilknyttetRegistreringSom = null;

    /**
     * @var Dokumenttype $dokumenttype
     */
    protected $dokumenttype = null;

    /**
     * @var string $tittel
     */
    protected $tittel = null;

    /**
     * @var Dokumentstatus $dokumentstatus
     */
    protected $dokumentstatus = null;

    /**
     * @var Variantformat $variantformat
     */
    protected $variantformat = null;

    /**
     * @var Format $format
     */
    protected $format = null;

    /**
     * @var string $referanseJournalpostSystemID
     */
    protected $referanseJournalpostSystemID = null;

    /**
     * @var Fil $Fil
     */
    protected $Fil = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return string
     */
    public function getSystemID()
    {
      return $this->systemID;
    }

    /**
     * @param string $systemID
     * @return Dokument
     */
    public function setSystemID($systemID)
    {
      $this->systemID = $systemID;
      return $this;
    }

    /**
     * @return string
     */
    public function getDokumentnummer()
    {
      return $this->dokumentnummer;
    }

    /**
     * @param string $dokumentnummer
     * @return Dokument
     */
    public function setDokumentnummer($dokumentnummer)
    {
      $this->dokumentnummer = $dokumentnummer;
      return $this;
    }

    /**
     * @return TilknyttetRegistreringSom
     */
    public function getTilknyttetRegistreringSom()
    {
      return $this->tilknyttetRegistreringSom;
    }

    /**
     * @param TilknyttetRegistreringSom $tilknyttetRegistreringSom
     * @return Dokument
     */
    public function setTilknyttetRegistreringSom($tilknyttetRegistreringSom)
    {
      $this->tilknyttetRegistreringSom = $tilknyttetRegistreringSom;
      return $this;
    }

    /**
     * @return Dokumenttype
     */
    public function getDokumenttype()
    {
      return $this->dokumenttype;
    }

    /**
     * @param Dokumenttype $dokumenttype
     * @return Dokument
     */
    public function setDokumenttype($dokumenttype)
    {
      $this->dokumenttype = $dokumenttype;
      return $this;
    }

    /**
     * @return string
     */
    public function getTittel()
    {
      return $this->tittel;
    }

    /**
     * @param string $tittel
     * @return Dokument
     */
    public function setTittel($tittel)
    {
      $this->tittel = $tittel;
      return $this;
    }

    /**
     * @return Dokumentstatus
     */
    public function getDokumentstatus()
    {
      return $this->dokumentstatus;
    }

    /**
     * @param Dokumentstatus $dokumentstatus
     * @return Dokument
     */
    public function setDokumentstatus($dokumentstatus)
    {
      $this->dokumentstatus = $dokumentstatus;
      return $this;
    }

    /**
     * @return Variantformat
     */
    public function getVariantformat()
    {
      return $this->variantformat;
    }

    /**
     * @param Variantformat $variantformat
     * @return Dokument
     */
    public function setVariantformat($variantformat)
    {
      $this->variantformat = $variantformat;
      return $this;
    }

    /**
     * @return Format
     */
    public function getFormat()
    {
      return $this->format;
    }

    /**
     * @param Format $format
     * @return Dokument
     */
    public function setFormat($format)
    {
      $this->format = $format;
      return $this;
    }

    /**
     * @return string
     */
    public function getReferanseJournalpostSystemID()
    {
      return $this->referanseJournalpostSystemID;
    }

    /**
     * @param string $referanseJournalpostSystemID
     * @return Dokument
     */
    public function setReferanseJournalpostSystemID($referanseJournalpostSystemID)
    {
      $this->referanseJournalpostSystemID = $referanseJournalpostSystemID;
      return $this;
    }

    /**
     * @return Fil
     */
    public function getFil()
    {
      return $this->Fil;
    }

    /**
     * @param Fil $Fil
     * @return Dokument
     */
    public function setFil($Fil)
    {
      $this->Fil = $Fil;
      return $this;
    }

}
