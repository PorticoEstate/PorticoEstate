<?php

class Saksmappe
{

    /**
     * @var string $systemID
     */
    protected $systemID = null;

    /**
     * @var Saksnummer $saksnr
     */
    protected $saksnr = null;

    /**
     * @var Mappetype $mappetype
     */
    protected $mappetype = null;

    /**
     * @var \DateTime $saksdato
     */
    protected $saksdato = null;

    /**
     * @var string $tittel
     */
    protected $tittel = null;

    /**
     * @var string $offentligTittel
     */
    protected $offentligTittel = null;

    /**
     * @var boolean $skjermetTittel
     */
    protected $skjermetTittel = null;

    /**
     * @var Skjerming $skjerming
     */
    protected $skjerming = null;

    /**
     * @var Saksstatus $saksstatus
     */
    protected $saksstatus = null;

    /**
     * @var Dokumentmedium $dokumentmedium
     */
    protected $dokumentmedium = null;

    /**
     * @var Arkivdel $referanseArkivdel
     */
    protected $referanseArkivdel = null;

    /**
     * @var Journalenhet $journalenhet
     */
    protected $journalenhet = null;

    /**
     * @var string $bevaringstid
     */
    protected $bevaringstid = null;

    /**
     * @var Kassasjonsvedtak $kassasjonsvedtak
     */
    protected $kassasjonsvedtak = null;

    /**
     * @var \DateTime $kassasjonsdato
     */
    protected $kassasjonsdato = null;

    /**
     * @var string $prosjekt
     */
    protected $prosjekt = null;

    /**
     * @var string $administrativEnhetInit
     */
    protected $administrativEnhetInit = null;

    /**
     * @var string $administrativEnhet
     */
    protected $administrativEnhet = null;

    /**
     * @var string $saksansvarligInit
     */
    protected $saksansvarligInit = null;

    /**
     * @var string $saksansvarlig
     */
    protected $saksansvarlig = null;

    /**
     * @var string $tilgangsgruppeNavn
     */
    protected $tilgangsgruppeNavn = null;

    /**
     * @var MatrikkelnummerListe $Matrikkelnummer
     */
    protected $Matrikkelnummer = null;

    /**
     * @var KlasseListe $klasse
     */
    protected $klasse = null;

    /**
     * @var SakspartListe $sakspart
     */
    protected $sakspart = null;

    /**
     * @var PunktListe $Punkt
     */
    protected $Punkt = null;

    /**
     * @var TilleggsinformasjonListe $tilleggsinformasjon
     */
    protected $tilleggsinformasjon = null;

    /**
     * @var ByggIdentListe $ByggIdent
     */
    protected $ByggIdent = null;

    /**
     * @var EksternNoekkel $referanseEksternNoekkel
     */
    protected $referanseEksternNoekkel = null;

    /**
     * @var MerknadListe $merknader
     */
    protected $merknader = null;

    /**
     * @var NasjonalArealplanId $planIdent
     */
    protected $planIdent = null;

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
     * @return Saksmappe
     */
    public function setSystemID($systemID)
    {
      $this->systemID = $systemID;
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
     * @return Saksmappe
     */
    public function setSaksnr($saksnr)
    {
      $this->saksnr = $saksnr;
      return $this;
    }

    /**
     * @return Mappetype
     */
    public function getMappetype()
    {
      return $this->mappetype;
    }

    /**
     * @param Mappetype $mappetype
     * @return Saksmappe
     */
    public function setMappetype($mappetype)
    {
      $this->mappetype = $mappetype;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getSaksdato()
    {
      if ($this->saksdato == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->saksdato);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $saksdato
     * @return Saksmappe
     */
    public function setSaksdato(\DateTime $saksdato = null)
    {
      if ($saksdato == null) {
       $this->saksdato = null;
      } else {
        $this->saksdato = $saksdato->format(\DateTime::ATOM);
      }
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
     * @return Saksmappe
     */
    public function setTittel($tittel)
    {
      $this->tittel = $tittel;
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
     * @return Saksmappe
     */
    public function setOffentligTittel($offentligTittel)
    {
      $this->offentligTittel = $offentligTittel;
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
     * @return Saksmappe
     */
    public function setSkjermetTittel($skjermetTittel)
    {
      $this->skjermetTittel = $skjermetTittel;
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
     * @return Saksmappe
     */
    public function setSkjerming($skjerming)
    {
      $this->skjerming = $skjerming;
      return $this;
    }

    /**
     * @return Saksstatus
     */
    public function getSaksstatus()
    {
      return $this->saksstatus;
    }

    /**
     * @param Saksstatus $saksstatus
     * @return Saksmappe
     */
    public function setSaksstatus($saksstatus)
    {
      $this->saksstatus = $saksstatus;
      return $this;
    }

    /**
     * @return Dokumentmedium
     */
    public function getDokumentmedium()
    {
      return $this->dokumentmedium;
    }

    /**
     * @param Dokumentmedium $dokumentmedium
     * @return Saksmappe
     */
    public function setDokumentmedium($dokumentmedium)
    {
      $this->dokumentmedium = $dokumentmedium;
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
     * @return Saksmappe
     */
    public function setReferanseArkivdel($referanseArkivdel)
    {
      $this->referanseArkivdel = $referanseArkivdel;
      return $this;
    }

    /**
     * @return Journalenhet
     */
    public function getJournalenhet()
    {
      return $this->journalenhet;
    }

    /**
     * @param Journalenhet $journalenhet
     * @return Saksmappe
     */
    public function setJournalenhet($journalenhet)
    {
      $this->journalenhet = $journalenhet;
      return $this;
    }

    /**
     * @return string
     */
    public function getBevaringstid()
    {
      return $this->bevaringstid;
    }

    /**
     * @param string $bevaringstid
     * @return Saksmappe
     */
    public function setBevaringstid($bevaringstid)
    {
      $this->bevaringstid = $bevaringstid;
      return $this;
    }

    /**
     * @return Kassasjonsvedtak
     */
    public function getKassasjonsvedtak()
    {
      return $this->kassasjonsvedtak;
    }

    /**
     * @param Kassasjonsvedtak $kassasjonsvedtak
     * @return Saksmappe
     */
    public function setKassasjonsvedtak($kassasjonsvedtak)
    {
      $this->kassasjonsvedtak = $kassasjonsvedtak;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getKassasjonsdato()
    {
      if ($this->kassasjonsdato == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->kassasjonsdato);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $kassasjonsdato
     * @return Saksmappe
     */
    public function setKassasjonsdato(\DateTime $kassasjonsdato = null)
    {
      if ($kassasjonsdato == null) {
       $this->kassasjonsdato = null;
      } else {
        $this->kassasjonsdato = $kassasjonsdato->format(\DateTime::ATOM);
      }
      return $this;
    }

    /**
     * @return string
     */
    public function getProsjekt()
    {
      return $this->prosjekt;
    }

    /**
     * @param string $prosjekt
     * @return Saksmappe
     */
    public function setProsjekt($prosjekt)
    {
      $this->prosjekt = $prosjekt;
      return $this;
    }

    /**
     * @return string
     */
    public function getAdministrativEnhetInit()
    {
      return $this->administrativEnhetInit;
    }

    /**
     * @param string $administrativEnhetInit
     * @return Saksmappe
     */
    public function setAdministrativEnhetInit($administrativEnhetInit)
    {
      $this->administrativEnhetInit = $administrativEnhetInit;
      return $this;
    }

    /**
     * @return string
     */
    public function getAdministrativEnhet()
    {
      return $this->administrativEnhet;
    }

    /**
     * @param string $administrativEnhet
     * @return Saksmappe
     */
    public function setAdministrativEnhet($administrativEnhet)
    {
      $this->administrativEnhet = $administrativEnhet;
      return $this;
    }

    /**
     * @return string
     */
    public function getSaksansvarligInit()
    {
      return $this->saksansvarligInit;
    }

    /**
     * @param string $saksansvarligInit
     * @return Saksmappe
     */
    public function setSaksansvarligInit($saksansvarligInit)
    {
      $this->saksansvarligInit = $saksansvarligInit;
      return $this;
    }

    /**
     * @return string
     */
    public function getSaksansvarlig()
    {
      return $this->saksansvarlig;
    }

    /**
     * @param string $saksansvarlig
     * @return Saksmappe
     */
    public function setSaksansvarlig($saksansvarlig)
    {
      $this->saksansvarlig = $saksansvarlig;
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
     * @return Saksmappe
     */
    public function setTilgangsgruppeNavn($tilgangsgruppeNavn)
    {
      $this->tilgangsgruppeNavn = $tilgangsgruppeNavn;
      return $this;
    }

    /**
     * @return MatrikkelnummerListe
     */
    public function getMatrikkelnummer()
    {
      return $this->Matrikkelnummer;
    }

    /**
     * @param MatrikkelnummerListe $Matrikkelnummer
     * @return Saksmappe
     */
    public function setMatrikkelnummer($Matrikkelnummer)
    {
      $this->Matrikkelnummer = $Matrikkelnummer;
      return $this;
    }

    /**
     * @return KlasseListe
     */
    public function getKlasse()
    {
      return $this->klasse;
    }

    /**
     * @param KlasseListe $klasse
     * @return Saksmappe
     */
    public function setKlasse($klasse)
    {
      $this->klasse = $klasse;
      return $this;
    }

    /**
     * @return SakspartListe
     */
    public function getSakspart()
    {
      return $this->sakspart;
    }

    /**
     * @param SakspartListe $sakspart
     * @return Saksmappe
     */
    public function setSakspart($sakspart)
    {
      $this->sakspart = $sakspart;
      return $this;
    }

    /**
     * @return PunktListe
     */
    public function getPunkt()
    {
      return $this->Punkt;
    }

    /**
     * @param PunktListe $Punkt
     * @return Saksmappe
     */
    public function setPunkt($Punkt)
    {
      $this->Punkt = $Punkt;
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
     * @return Saksmappe
     */
    public function setTilleggsinformasjon($tilleggsinformasjon)
    {
      $this->tilleggsinformasjon = $tilleggsinformasjon;
      return $this;
    }

    /**
     * @return ByggIdentListe
     */
    public function getByggIdent()
    {
      return $this->ByggIdent;
    }

    /**
     * @param ByggIdentListe $ByggIdent
     * @return Saksmappe
     */
    public function setByggIdent($ByggIdent)
    {
      $this->ByggIdent = $ByggIdent;
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
     * @return Saksmappe
     */
    public function setReferanseEksternNoekkel($referanseEksternNoekkel)
    {
      $this->referanseEksternNoekkel = $referanseEksternNoekkel;
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
     * @return Saksmappe
     */
    public function setMerknader($merknader)
    {
      $this->merknader = $merknader;
      return $this;
    }

    /**
     * @return NasjonalArealplanId
     */
    public function getPlanIdent()
    {
      return $this->planIdent;
    }

    /**
     * @param NasjonalArealplanId $planIdent
     * @return Saksmappe
     */
    public function setPlanIdent($planIdent)
    {
      $this->planIdent = $planIdent;
      return $this;
    }

}
