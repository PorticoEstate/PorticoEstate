<?php

class Journalpost
{

    /**
     * @var string $systemID
     */
    protected $systemID = null;

    /**
     * @var Journalnummer $journalnummer
     */
    protected $journalnummer = null;

    /**
     * @var string $journalpostnummer
     */
    protected $journalpostnummer = null;

    /**
     * @var \DateTime $journaldato
     */
    protected $journaldato = null;

    /**
     * @var Journalposttype $journalposttype
     */
    protected $journalposttype = null;

    /**
     * @var \DateTime $dokumentetsDato
     */
    protected $dokumentetsDato = null;

    /**
     * @var Journalstatus $journalstatus
     */
    protected $journalstatus = null;

    /**
     * @var string $tittel
     */
    protected $tittel = null;

    /**
     * @var boolean $skjermetTittel
     */
    protected $skjermetTittel = null;

    /**
     * @var \DateTime $forfallsdato
     */
    protected $forfallsdato = null;

    /**
     * @var Skjerming $skjerming
     */
    protected $skjerming = null;

    /**
     * @var Arkivdel $referanseArkivdel
     */
    protected $referanseArkivdel = null;

    /**
     * @var string $tilleggskode
     */
    protected $tilleggskode = null;

    /**
     * @var string $antallVedlegg
     */
    protected $antallVedlegg = null;

    /**
     * @var string $offentligTittel
     */
    protected $offentligTittel = null;

    /**
     * @var Saksnummer $saksnr
     */
    protected $saksnr = null;

    /**
     * @var string $tilgangsgruppeNavn
     */
    protected $tilgangsgruppeNavn = null;

    /**
     * @var SakSystemId $referanseSakSystemID
     */
    protected $referanseSakSystemID = null;

    /**
     * @var KorrespondansepartListe $korrespondansepart
     */
    protected $korrespondansepart = null;

    /**
     * @var EksternNoekkel $referanseEksternNoekkel
     */
    protected $referanseEksternNoekkel = null;

    /**
     * @var EksternNoekkel $referanseMappeEksternNoekkel
     */
    protected $referanseMappeEksternNoekkel = null;

    /**
     * @var AvskrivningListe $referanseAvskrivninger
     */
    protected $referanseAvskrivninger = null;

    /**
     * @var MerknadListe $merknader
     */
    protected $merknader = null;

    /**
     * @var TilleggsinformasjonListe $tilleggsinformasjon
     */
    protected $tilleggsinformasjon = null;

    /**
     * @param string $tittel
     */
    public function __construct($tittel)
    {
      $this->tittel = $tittel;
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
     * @return Journalpost
     */
    public function setSystemID($systemID)
    {
      $this->systemID = $systemID;
      return $this;
    }

    /**
     * @return Journalnummer
     */
    public function getJournalnummer()
    {
      return $this->journalnummer;
    }

    /**
     * @param Journalnummer $journalnummer
     * @return Journalpost
     */
    public function setJournalnummer($journalnummer)
    {
      $this->journalnummer = $journalnummer;
      return $this;
    }

    /**
     * @return string
     */
    public function getJournalpostnummer()
    {
      return $this->journalpostnummer;
    }

    /**
     * @param string $journalpostnummer
     * @return Journalpost
     */
    public function setJournalpostnummer($journalpostnummer)
    {
      $this->journalpostnummer = $journalpostnummer;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getJournaldato()
    {
      if ($this->journaldato == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->journaldato);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $journaldato
     * @return Journalpost
     */
    public function setJournaldato(\DateTime $journaldato = null)
    {
      if ($journaldato == null) {
       $this->journaldato = null;
      } else {
        $this->journaldato = $journaldato->format(\DateTime::ATOM);
      }
      return $this;
    }

    /**
     * @return Journalposttype
     */
    public function getJournalposttype()
    {
      return $this->journalposttype;
    }

    /**
     * @param Journalposttype $journalposttype
     * @return Journalpost
     */
    public function setJournalposttype($journalposttype)
    {
      $this->journalposttype = $journalposttype;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDokumentetsDato()
    {
      if ($this->dokumentetsDato == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->dokumentetsDato);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $dokumentetsDato
     * @return Journalpost
     */
    public function setDokumentetsDato(\DateTime $dokumentetsDato = null)
    {
      if ($dokumentetsDato == null) {
       $this->dokumentetsDato = null;
      } else {
        $this->dokumentetsDato = $dokumentetsDato->format(\DateTime::ATOM);
      }
      return $this;
    }

    /**
     * @return Journalstatus
     */
    public function getJournalstatus()
    {
      return $this->journalstatus;
    }

    /**
     * @param Journalstatus $journalstatus
     * @return Journalpost
     */
    public function setJournalstatus($journalstatus)
    {
      $this->journalstatus = $journalstatus;
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
     * @return Journalpost
     */
    public function setTittel($tittel)
    {
      $this->tittel = $tittel;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getSkjermetTittel()
    {
      return $this->skjermetTittel;
    }

    /**
     * @param boolean $skjermetTittel
     * @return Journalpost
     */
    public function setSkjermetTittel($skjermetTittel)
    {
      $this->skjermetTittel = $skjermetTittel;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getForfallsdato()
    {
      if ($this->forfallsdato == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->forfallsdato);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $forfallsdato
     * @return Journalpost
     */
    public function setForfallsdato(\DateTime $forfallsdato = null)
    {
      if ($forfallsdato == null) {
       $this->forfallsdato = null;
      } else {
        $this->forfallsdato = $forfallsdato->format(\DateTime::ATOM);
      }
      return $this;
    }

    /**
     * @return Skjerming
     */
    public function getSkjerming()
    {
      return $this->skjerming;
    }

    /**
     * @param Skjerming $skjerming
     * @return Journalpost
     */
    public function setSkjerming($skjerming)
    {
      $this->skjerming = $skjerming;
      return $this;
    }

    /**
     * @return Arkivdel
     */
    public function getReferanseArkivdel()
    {
      return $this->referanseArkivdel;
    }

    /**
     * @param Arkivdel $referanseArkivdel
     * @return Journalpost
     */
    public function setReferanseArkivdel($referanseArkivdel)
    {
      $this->referanseArkivdel = $referanseArkivdel;
      return $this;
    }

    /**
     * @return string
     */
    public function getTilleggskode()
    {
      return $this->tilleggskode;
    }

    /**
     * @param string $tilleggskode
     * @return Journalpost
     */
    public function setTilleggskode($tilleggskode)
    {
      $this->tilleggskode = $tilleggskode;
      return $this;
    }

    /**
     * @return string
     */
    public function getAntallVedlegg()
    {
      return $this->antallVedlegg;
    }

    /**
     * @param string $antallVedlegg
     * @return Journalpost
     */
    public function setAntallVedlegg($antallVedlegg)
    {
      $this->antallVedlegg = $antallVedlegg;
      return $this;
    }

    /**
     * @return string
     */
    public function getOffentligTittel()
    {
      return $this->offentligTittel;
    }

    /**
     * @param string $offentligTittel
     * @return Journalpost
     */
    public function setOffentligTittel($offentligTittel)
    {
      $this->offentligTittel = $offentligTittel;
      return $this;
    }

    /**
     * @return Saksnummer
     */
    public function getSaksnr()
    {
      return $this->saksnr;
    }

    /**
     * @param Saksnummer $saksnr
     * @return Journalpost
     */
    public function setSaksnr($saksnr)
    {
      $this->saksnr = $saksnr;
      return $this;
    }

    /**
     * @return string
     */
    public function getTilgangsgruppeNavn()
    {
      return $this->tilgangsgruppeNavn;
    }

    /**
     * @param string $tilgangsgruppeNavn
     * @return Journalpost
     */
    public function setTilgangsgruppeNavn($tilgangsgruppeNavn)
    {
      $this->tilgangsgruppeNavn = $tilgangsgruppeNavn;
      return $this;
    }

    /**
     * @return SakSystemId
     */
    public function getReferanseSakSystemID()
    {
      return $this->referanseSakSystemID;
    }

    /**
     * @param SakSystemId $referanseSakSystemID
     * @return Journalpost
     */
    public function setReferanseSakSystemID($referanseSakSystemID)
    {
      $this->referanseSakSystemID = $referanseSakSystemID;
      return $this;
    }

    /**
     * @return KorrespondansepartListe
     */
    public function getKorrespondansepart()
    {
      return $this->korrespondansepart;
    }

    /**
     * @param KorrespondansepartListe $korrespondansepart
     * @return Journalpost
     */
    public function setKorrespondansepart($korrespondansepart)
    {
      $this->korrespondansepart = $korrespondansepart;
      return $this;
    }

    /**
     * @return EksternNoekkel
     */
    public function getReferanseEksternNoekkel()
    {
      return $this->referanseEksternNoekkel;
    }

    /**
     * @param EksternNoekkel $referanseEksternNoekkel
     * @return Journalpost
     */
    public function setReferanseEksternNoekkel($referanseEksternNoekkel)
    {
      $this->referanseEksternNoekkel = $referanseEksternNoekkel;
      return $this;
    }

    /**
     * @return EksternNoekkel
     */
    public function getReferanseMappeEksternNoekkel()
    {
      return $this->referanseMappeEksternNoekkel;
    }

    /**
     * @param EksternNoekkel $referanseMappeEksternNoekkel
     * @return Journalpost
     */
    public function setReferanseMappeEksternNoekkel($referanseMappeEksternNoekkel)
    {
      $this->referanseMappeEksternNoekkel = $referanseMappeEksternNoekkel;
      return $this;
    }

    /**
     * @return AvskrivningListe
     */
    public function getReferanseAvskrivninger()
    {
      return $this->referanseAvskrivninger;
    }

    /**
     * @param AvskrivningListe $referanseAvskrivninger
     * @return Journalpost
     */
    public function setReferanseAvskrivninger($referanseAvskrivninger)
    {
      $this->referanseAvskrivninger = $referanseAvskrivninger;
      return $this;
    }

    /**
     * @return MerknadListe
     */
    public function getMerknader()
    {
      return $this->merknader;
    }

    /**
     * @param MerknadListe $merknader
     * @return Journalpost
     */
    public function setMerknader($merknader)
    {
      $this->merknader = $merknader;
      return $this;
    }

    /**
     * @return TilleggsinformasjonListe
     */
    public function getTilleggsinformasjon()
    {
      return $this->tilleggsinformasjon;
    }

    /**
     * @param TilleggsinformasjonListe $tilleggsinformasjon
     * @return Journalpost
     */
    public function setTilleggsinformasjon($tilleggsinformasjon)
    {
      $this->tilleggsinformasjon = $tilleggsinformasjon;
      return $this;
    }

}
