<?php

class FinnJournalpostRestanser
{

    /**
     * @var Ansvarlig $ansvarlig
     */
    protected $ansvarlig = null;

    /**
     * @var boolean $returnerMerknad
     */
    protected $returnerMerknad = null;

    /**
     * @var boolean $returnerTilleggsinformasjon
     */
    protected $returnerTilleggsinformasjon = null;

    /**
     * @var boolean $returnerKorrespondansepart
     */
    protected $returnerKorrespondansepart = null;

    /**
     * @var boolean $returnerAvskrivning
     */
    protected $returnerAvskrivning = null;

    /**
     * @var ArkivKontekst $kontekst
     */
    protected $kontekst = null;

    /**
     * @param Ansvarlig $ansvarlig
     * @param boolean $returnerMerknad
     * @param boolean $returnerTilleggsinformasjon
     * @param boolean $returnerKorrespondansepart
     * @param boolean $returnerAvskrivning
     * @param ArkivKontekst $kontekst
     */
    public function __construct($ansvarlig, $returnerMerknad, $returnerTilleggsinformasjon, $returnerKorrespondansepart, $returnerAvskrivning, $kontekst)
    {
      $this->ansvarlig = $ansvarlig;
      $this->returnerMerknad = $returnerMerknad;
      $this->returnerTilleggsinformasjon = $returnerTilleggsinformasjon;
      $this->returnerKorrespondansepart = $returnerKorrespondansepart;
      $this->returnerAvskrivning = $returnerAvskrivning;
      $this->kontekst = $kontekst;
    }

    /**
     * @return Ansvarlig
     */
    public function getAnsvarlig()
    {
      return $this->ansvarlig;
    }

    /**
     * @param Ansvarlig $ansvarlig
     * @return FinnJournalpostRestanser
     */
    public function setAnsvarlig($ansvarlig)
    {
      $this->ansvarlig = $ansvarlig;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getReturnerMerknad()
    {
      return $this->returnerMerknad;
    }

    /**
     * @param boolean $returnerMerknad
     * @return FinnJournalpostRestanser
     */
    public function setReturnerMerknad($returnerMerknad)
    {
      $this->returnerMerknad = $returnerMerknad;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getReturnerTilleggsinformasjon()
    {
      return $this->returnerTilleggsinformasjon;
    }

    /**
     * @param boolean $returnerTilleggsinformasjon
     * @return FinnJournalpostRestanser
     */
    public function setReturnerTilleggsinformasjon($returnerTilleggsinformasjon)
    {
      $this->returnerTilleggsinformasjon = $returnerTilleggsinformasjon;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getReturnerKorrespondansepart()
    {
      return $this->returnerKorrespondansepart;
    }

    /**
     * @param boolean $returnerKorrespondansepart
     * @return FinnJournalpostRestanser
     */
    public function setReturnerKorrespondansepart($returnerKorrespondansepart)
    {
      $this->returnerKorrespondansepart = $returnerKorrespondansepart;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getReturnerAvskrivning()
    {
      return $this->returnerAvskrivning;
    }

    /**
     * @param boolean $returnerAvskrivning
     * @return FinnJournalpostRestanser
     */
    public function setReturnerAvskrivning($returnerAvskrivning)
    {
      $this->returnerAvskrivning = $returnerAvskrivning;
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
     * @return FinnJournalpostRestanser
     */
    public function setKontekst($kontekst)
    {
      $this->kontekst = $kontekst;
      return $this;
    }

}
