<?php

class OppdateringService extends \SoapClient
{

    /**
     * @var array $classmap The defined classes
     */
    private static $classmap = array (
      'KodeListe' => '\\KodeListe',
      'Kode' => '\\Kode',
      'DokumentListe' => '\\DokumentListe',
      'Dokument' => '\\Dokument',
      'DokumenttypeListe' => '\\DokumenttypeListe',
      'Dokumenttype' => '\\Dokumenttype',
      'Filinnhold' => '\\Filinnhold',
      'Filreferanse' => '\\Filreferanse',
      'DokumentstatusListe' => '\\DokumentstatusListe',
      'Dokumentstatus' => '\\Dokumentstatus',
      'FilListe' => '\\FilListe',
      'Fil' => '\\Fil',
      'FormatListe' => '\\FormatListe',
      'Format' => '\\Format',
      'TilknyttetRegistreringSomListe' => '\\TilknyttetRegistreringSomListe',
      'TilknyttetRegistreringSom' => '\\TilknyttetRegistreringSom',
      'VariantformatListe' => '\\VariantformatListe',
      'Variantformat' => '\\Variantformat',
      'KoordinatsystemKodeListe' => '\\KoordinatsystemKodeListe',
      'KoordinatsystemKode' => '\\KoordinatsystemKode',
      'FlateListe' => '\\FlateListe',
      'Flate' => '\\Flate',
      'Geometri' => '\\Geometri',
      'KoordinatListe' => '\\KoordinatListe',
      'Koordinat' => '\\Koordinat',
      'Kurve' => '\\Kurve',
      'PunktListe' => '\\PunktListe',
      'Punkt' => '\\Punkt',
      'RingListe' => '\\RingListe',
      'Ring' => '\\Ring',
      'BboxListe' => '\\BboxListe',
      'Bbox' => '\\Bbox',
      'OmraadeListe' => '\\OmraadeListe',
      'Omraade' => '\\Omraade',
      'AnsvarligEnumListe' => '\\AnsvarligEnumListe',
      'SoekeOperatorEnumListe' => '\\SoekeOperatorEnumListe',
      'BboxKriterie' => '\\BboxKriterie',
      'Ansvarlig' => '\\Ansvarlig',
      'KriterieListe' => '\\KriterieListe',
      'Kriterie' => '\\Kriterie',
      'Soekefelt' => '\\Soekefelt',
      'SoekskriterieListe' => '\\SoekskriterieListe',
      'Soekskriterie' => '\\Soekskriterie',
      'AdministrativenhetsnummerListe' => '\\AdministrativenhetsnummerListe',
      'Administrativenhetsnummer' => '\\Administrativenhetsnummer',
      'Fylke' => '\\Fylke',
      'Kommune' => '\\Kommune',
      'NasjonalArealplanIdListe' => '\\NasjonalArealplanIdListe',
      'NasjonalArealplanId' => '\\NasjonalArealplanId',
      'Stat' => '\\Stat',
      'ByggIdentListe' => '\\ByggIdentListe',
      'ByggIdent' => '\\ByggIdent',
      'MatrikkelnummerListe' => '\\MatrikkelnummerListe',
      'Matrikkelnummer' => '\\Matrikkelnummer',
      'Saksnoekkel' => '\\Saksnoekkel',
      'SaksnummerListe' => '\\SaksnummerListe',
      'Saksnummer' => '\\Saksnummer',
      'ApplicationFault' => '\\ApplicationFault',
      'ArkivKontekst' => '\\ArkivKontekst',
      'FinderFault' => '\\FinderFault',
      'GeointegrasjonFault' => '\\GeointegrasjonFault',
      'ImplementationFault' => '\\ImplementationFault',
      'Kontekst' => '\\Kontekst',
      'MatrikkelKontekst' => '\\MatrikkelKontekst',
      'OperationalFault' => '\\OperationalFault',
      'PlanKontekst' => '\\PlanKontekst',
      'StringListe' => '\\StringListe',
      'SystemFault' => '\\SystemFault',
      'ValidationFault' => '\\ValidationFault',
      'EnkelAdressetypeListe' => '\\EnkelAdressetypeListe',
      'EnkelAdressetype' => '\\EnkelAdressetype',
      'LandkodeListe' => '\\LandkodeListe',
      'Landkode' => '\\Landkode',
      'ElektroniskAdresseListe' => '\\ElektroniskAdresseListe',
      'ElektroniskAdresse' => '\\ElektroniskAdresse',
      'EnkelAdresseListe' => '\\EnkelAdresseListe',
      'EnkelAdresse' => '\\EnkelAdresse',
      'Epost' => '\\Epost',
      'Faks' => '\\Faks',
      'PostadministrativeOmraaderListe' => '\\PostadministrativeOmraaderListe',
      'PostadministrativeOmraader' => '\\PostadministrativeOmraader',
      'Telefon' => '\\Telefon',
      'Meldingsboks' => '\\Meldingsboks',
      'PersonidentifikatorTypeListe' => '\\PersonidentifikatorTypeListe',
      'PersonidentifikatorType' => '\\PersonidentifikatorType',
      'KontaktListe' => '\\KontaktListe',
      'Kontakt' => '\\Kontakt',
      'Organisasjon' => '\\Organisasjon',
      'Person' => '\\Person',
      'PersonidentifikatorListe' => '\\PersonidentifikatorListe',
      'Personidentifikator' => '\\Personidentifikator',
      'KorrespondansepartListe' => '\\KorrespondansepartListe',
      'Korrespondansepart' => '\\Korrespondansepart',
      'SaksmappeListe' => '\\SaksmappeListe',
      'Saksmappe' => '\\Saksmappe',
      'Dokumentnummer' => '\\Dokumentnummer',
      'EksternNoekkelListe' => '\\EksternNoekkelListe',
      'EksternNoekkel' => '\\EksternNoekkel',
      'JournalpostListe' => '\\JournalpostListe',
      'Journalpost' => '\\Journalpost',
      'Journpostnoekkel' => '\\Journpostnoekkel',
      'KlasseListe' => '\\KlasseListe',
      'Klasse' => '\\Klasse',
      'JournalnummerListe' => '\\JournalnummerListe',
      'Journalnummer' => '\\Journalnummer',
      'SakspartListe' => '\\SakspartListe',
      'Sakspart' => '\\Sakspart',
      'SakspartRolleListe' => '\\SakspartRolleListe',
      'SakspartRolle' => '\\SakspartRolle',
      'TilleggsinformasjonListe' => '\\TilleggsinformasjonListe',
      'Tilleggsinformasjon' => '\\Tilleggsinformasjon',
      'ArkivdelListe' => '\\ArkivdelListe',
      'Arkivdel' => '\\Arkivdel',
      'AvskrivningListe' => '\\AvskrivningListe',
      'Avskrivning' => '\\Avskrivning',
      'AvskrivningsmaateListe' => '\\AvskrivningsmaateListe',
      'Avskrivningsmaate' => '\\Avskrivningsmaate',
      'DokumentmediumListe' => '\\DokumentmediumListe',
      'Dokumentmedium' => '\\Dokumentmedium',
      'ForsendelsesmaateListe' => '\\ForsendelsesmaateListe',
      'Forsendelsesmaate' => '\\Forsendelsesmaate',
      'InformasjonstypeListe' => '\\InformasjonstypeListe',
      'Informasjonstype' => '\\Informasjonstype',
      'JournalenhetListe' => '\\JournalenhetListe',
      'Journalenhet' => '\\Journalenhet',
      'JournalposttypeListe' => '\\JournalposttypeListe',
      'Journalposttype' => '\\Journalposttype',
      'JournalstatusListe' => '\\JournalstatusListe',
      'Journalstatus' => '\\Journalstatus',
      'JournpostEksternNoekkel' => '\\JournpostEksternNoekkel',
      'JournpostSystemID' => '\\JournpostSystemID',
      'KassasjonsvedtakListe' => '\\KassasjonsvedtakListe',
      'Kassasjonsvedtak' => '\\Kassasjonsvedtak',
      'KlassifikasjonssystemListe' => '\\KlassifikasjonssystemListe',
      'Klassifikasjonssystem' => '\\Klassifikasjonssystem',
      'KorrespondanseparttypeListe' => '\\KorrespondanseparttypeListe',
      'Korrespondanseparttype' => '\\Korrespondanseparttype',
      'MappetypeListe' => '\\MappetypeListe',
      'Mappetype' => '\\Mappetype',
      'MerknadListe' => '\\MerknadListe',
      'Merknad' => '\\Merknad',
      'SakEksternNoekkel' => '\\SakEksternNoekkel',
      'SaksstatusListe' => '\\SaksstatusListe',
      'Saksstatus' => '\\Saksstatus',
      'SakSystemIdListe' => '\\SakSystemIdListe',
      'SakSystemId' => '\\SakSystemId',
      'SkjermingListe' => '\\SkjermingListe',
      'Skjerming' => '\\Skjerming',
      'SkjermingOpphorerAksjonListe' => '\\SkjermingOpphorerAksjonListe',
      'SkjermingOpphorerAksjon' => '\\SkjermingOpphorerAksjon',
      'SkjermingsHjemmel' => '\\SkjermingsHjemmel',
      'SystemIDListe' => '\\SystemIDListe',
      'SystemID' => '\\SystemID',
      'TilgangsrestriksjonListe' => '\\TilgangsrestriksjonListe',
      'Tilgangsrestriksjon' => '\\Tilgangsrestriksjon',
      'NySaksmappe' => '\\NySaksmappe',
      'NySaksmappeResponse' => '\\NySaksmappeResponse',
      'OppdaterMappeStatus' => '\\OppdaterMappeStatus',
      'OppdaterMappeStatusResponse' => '\\OppdaterMappeStatusResponse',
      'OppdaterMappeEksternNoekkel' => '\\OppdaterMappeEksternNoekkel',
      'OppdaterMappeEksternNoekkelResponse' => '\\OppdaterMappeEksternNoekkelResponse',
      'OppdaterMappeAnsvarlig' => '\\OppdaterMappeAnsvarlig',
      'OppdaterMappeAnsvarligResponse' => '\\OppdaterMappeAnsvarligResponse',
      'NyMatrikkelnummer' => '\\NyMatrikkelnummer',
      'NyMatrikkelnummerResponse' => '\\NyMatrikkelnummerResponse',
      'SlettMatrikkelnummer' => '\\SlettMatrikkelnummer',
      'SlettMatrikkelnummerResponse' => '\\SlettMatrikkelnummerResponse',
      'NyBygning' => '\\NyBygning',
      'NyBygningResponse' => '\\NyBygningResponse',
      'SlettBygning' => '\\SlettBygning',
      'SlettBygningResponse' => '\\SlettBygningResponse',
      'NyPunkt' => '\\NyPunkt',
      'NyPunktResponse' => '\\NyPunktResponse',
      'SlettPunkt' => '\\SlettPunkt',
      'SlettPunktResponse' => '\\SlettPunktResponse',
      'NySakspart' => '\\NySakspart',
      'NySakspartResponse' => '\\NySakspartResponse',
      'SlettSakspart' => '\\SlettSakspart',
      'SlettSakspartResponse' => '\\SlettSakspartResponse',
      'OppdaterPlan' => '\\OppdaterPlan',
      'OppdaterPlanResponse' => '\\OppdaterPlanResponse',
      'FinnJournalpostRestanser' => '\\FinnJournalpostRestanser',
      'FinnJournalpostRestanserResponse' => '\\FinnJournalpostRestanserResponse',
      'FinnJournalposterUnderArbeid' => '\\FinnJournalposterUnderArbeid',
      'FinnJournalposterUnderArbeidResponse' => '\\FinnJournalposterUnderArbeidResponse',
      'NyJournalpost' => '\\NyJournalpost',
      'NyJournalpostResponse' => '\\NyJournalpostResponse',
      'OppdaterJournalpostAnsvarlig' => '\\OppdaterJournalpostAnsvarlig',
      'OppdaterJournalpostAnsvarligResponse' => '\\OppdaterJournalpostAnsvarligResponse',
      'NyKorrespondansepart' => '\\NyKorrespondansepart',
      'NyKorrespondansepartResponse' => '\\NyKorrespondansepartResponse',
      'SlettKorrespondansepart' => '\\SlettKorrespondansepart',
      'SlettKorrespondansepartResponse' => '\\SlettKorrespondansepartResponse',
      'NyDokument' => '\\NyDokument',
      'NyDokumentResponse' => '\\NyDokumentResponse',
      'OppdaterJournalpostEksternNoekkel' => '\\OppdaterJournalpostEksternNoekkel',
      'OppdaterJournalpostEksternNoekkelResponse' => '\\OppdaterJournalpostEksternNoekkelResponse',
      'OppdaterJournalpostStatus' => '\\OppdaterJournalpostStatus',
      'OppdaterJournalpostStatusResponse' => '\\OppdaterJournalpostStatusResponse',
      'NyAvskrivning' => '\\NyAvskrivning',
      'NyAvskrivningResponse' => '\\NyAvskrivningResponse',
      'SlettAvskrivning' => '\\SlettAvskrivning',
      'SlettAvskrivningResponse' => '\\SlettAvskrivningResponse',
      'NyJournalpostMerknad' => '\\NyJournalpostMerknad',
      'NyJournalpostMerknadResponse' => '\\NyJournalpostMerknadResponse',
      'NyJournalpostTilleggsinformasjon' => '\\NyJournalpostTilleggsinformasjon',
      'NyJournalpostTilleggsinformasjonResponse' => '\\NyJournalpostTilleggsinformasjonResponse',
      'NySaksmappeMerknad' => '\\NySaksmappeMerknad',
      'NySaksmappeMerknadResponse' => '\\NySaksmappeMerknadResponse',
      'NySaksmappeTilleggsinformasjon' => '\\NySaksmappeTilleggsinformasjon',
      'NySaksmappeTilleggsinformasjonResponse' => '\\NySaksmappeTilleggsinformasjonResponse',
      'SlettJournalpostMerknad' => '\\SlettJournalpostMerknad',
      'SlettJournalpostMerknadResponse' => '\\SlettJournalpostMerknadResponse',
      'SlettJournalpostTilleggsinformasjon' => '\\SlettJournalpostTilleggsinformasjon',
      'SlettJournalpostTilleggsinformasjonResponse' => '\\SlettJournalpostTilleggsinformasjonResponse',
      'SlettSaksmappeMerknad' => '\\SlettSaksmappeMerknad',
      'SlettSaksmappeMerknadResponse' => '\\SlettSaksmappeMerknadResponse',
      'SlettSaksmappeTilleggsinformasjon' => '\\SlettSaksmappeTilleggsinformasjon',
      'SlettSaksmappeTilleggsinformasjonResponse' => '\\SlettSaksmappeTilleggsinformasjonResponse',
      'SystemFaultInfo' => '\\SystemFaultInfo',
      'ImplementationFaultInfo' => '\\ImplementationFaultInfo',
      'OperationalFaultInfo' => '\\OperationalFaultInfo',
      'ApplicationFaultInfo' => '\\ApplicationFaultInfo',
      'FinderFaultInfo' => '\\FinderFaultInfo',
      'ValidationFaultInfo' => '\\ValidationFaultInfo',
    );

    /**
     * @param array $options A array of config values
     * @param string $wsdl The wsdl file to use
     */
    public function __construct(array $options = array(), $wsdl = null)
    {
      foreach (self::$classmap as $key => $value) {
        if (!isset($options['classmap'][$key])) {
          $options['classmap'][$key] = $value;
        }
      }
      $options = array_merge(array (
      'features' => 1,
    ), $options);
      if (!$wsdl) {
 //       $wsdl = 'http://rep.geointegrasjon.no/Arkiv/Oppdatering/xml.wsdl/2012.01.31/giArkivOppdatering20120131.wsdl';
      }
      parent::__construct($wsdl, $options);
    }

    /**
     * @param NySaksmappeTilleggsinformasjon $parameters
     * @return NySaksmappeTilleggsinformasjonResponse
     */
    public function NySaksmappeTilleggsinformasjon(NySaksmappeTilleggsinformasjon $parameters)
    {
      return $this->__soapCall('NySaksmappeTilleggsinformasjon', array($parameters));
    }

    /**
     * @param NySaksmappeMerknad $parameters
     * @return NySaksmappeMerknadResponse
     */
    public function NySaksmappeMerknad(NySaksmappeMerknad $parameters)
    {
      return $this->__soapCall('NySaksmappeMerknad', array($parameters));
    }

    /**
     * @param NyJournalpostTilleggsinformasjon $parameters
     * @return NyJournalpostTilleggsinformasjonResponse
     */
    public function NyJournalpostTilleggsinformasjon(NyJournalpostTilleggsinformasjon $parameters)
    {
      return $this->__soapCall('NyJournalpostTilleggsinformasjon', array($parameters));
    }

    /**
     * @param NyJournalpostMerknad $parameters
     * @return NyJournalpostMerknadResponse
     */
    public function NyJournalpostMerknad(NyJournalpostMerknad $parameters)
    {
      return $this->__soapCall('NyJournalpostMerknad', array($parameters));
    }

    /**
     * @param SlettAvskrivning $parameters
     * @return SlettAvskrivningResponse
     */
    public function SlettAvskrivning(SlettAvskrivning $parameters)
    {
      return $this->__soapCall('SlettAvskrivning', array($parameters));
    }

    /**
     * @param NyAvskrivning $parameters
     * @return NyAvskrivningResponse
     */
    public function NyAvskrivning(NyAvskrivning $parameters)
    {
      return $this->__soapCall('NyAvskrivning', array($parameters));
    }

    /**
     * @param OppdaterJournalpostStatus $parameters
     * @return OppdaterJournalpostStatusResponse
     */
    public function OppdaterJournalpostStatus(OppdaterJournalpostStatus $parameters)
    {
      return $this->__soapCall('OppdaterJournalpostStatus', array($parameters));
    }

    /**
     * @param OppdaterJournalpostEksternNoekkel $parameters
     * @return OppdaterJournalpostEksternNoekkelResponse
     */
    public function OppdaterJournalpostEksternNoekkel(OppdaterJournalpostEksternNoekkel $parameters)
    {
      return $this->__soapCall('OppdaterJournalpostEksternNoekkel', array($parameters));
    }

    /**
     * @param NyDokument $parameters
     * @return NyDokumentResponse
     */
    public function NyDokument(NyDokument $parameters)
    {
      return $this->__soapCall('NyDokument', array($parameters));
    }

    /**
     * @param SlettKorrespondansepart $parameters
     * @return SlettKorrespondansepartResponse
     */
    public function SlettKorrespondansepart(SlettKorrespondansepart $parameters)
    {
      return $this->__soapCall('SlettKorrespondansepart', array($parameters));
    }

    /**
     * @param NyKorrespondansepart $parameters
     * @return NyKorrespondansepartResponse
     */
    public function NyKorrespondansepart(NyKorrespondansepart $parameters)
    {
      return $this->__soapCall('NyKorrespondansepart', array($parameters));
    }

    /**
     * @param OppdaterJournalpostAnsvarlig $parameters
     * @return OppdaterJournalpostAnsvarligResponse
     */
    public function OppdaterJournalpostAnsvarlig(OppdaterJournalpostAnsvarlig $parameters)
    {
      return $this->__soapCall('OppdaterJournalpostAnsvarlig', array($parameters));
    }

    /**
     * @param NyJournalpost $parameters
     * @return NyJournalpostResponse
     */
    public function NyJournalpost(NyJournalpost $parameters)
    {
      return $this->__soapCall('NyJournalpost', array($parameters));
    }

    /**
     * @param FinnJournalposterUnderArbeid $parameters
     * @return FinnJournalposterUnderArbeidResponse
     */
    public function FinnJournalposterUnderArbeid(FinnJournalposterUnderArbeid $parameters)
    {
      return $this->__soapCall('FinnJournalposterUnderArbeid', array($parameters));
    }

    /**
     * @param FinnJournalpostRestanser $parameters
     * @return FinnJournalpostRestanserResponse
     */
    public function FinnJournalpostRestanser(FinnJournalpostRestanser $parameters)
    {
      return $this->__soapCall('FinnJournalpostRestanser', array($parameters));
    }

    /**
     * @param OppdaterPlan $parameters
     * @return OppdaterPlanResponse
     */
    public function OppdaterPlan(OppdaterPlan $parameters)
    {
      return $this->__soapCall('OppdaterPlan', array($parameters));
    }

    /**
     * @param SlettSakspart $parameters
     * @return SlettSakspartResponse
     */
    public function SlettSakspart(SlettSakspart $parameters)
    {
      return $this->__soapCall('SlettSakspart', array($parameters));
    }

    /**
     * @param NySakspart $parameters
     * @return NySakspartResponse
     */
    public function NySakspart(NySakspart $parameters)
    {
      return $this->__soapCall('NySakspart', array($parameters));
    }

    /**
     * @param SlettPunkt $parameters
     * @return SlettPunktResponse
     */
    public function SlettPunkt(SlettPunkt $parameters)
    {
      return $this->__soapCall('SlettPunkt', array($parameters));
    }

    /**
     * @param NyPunkt $parameters
     * @return NyPunktResponse
     */
    public function NyPunkt(NyPunkt $parameters)
    {
      return $this->__soapCall('NyPunkt', array($parameters));
    }

    /**
     * @param SlettBygning $parameters
     * @return SlettBygningResponse
     */
    public function SlettBygning(SlettBygning $parameters)
    {
      return $this->__soapCall('SlettBygning', array($parameters));
    }

    /**
     * @param NyBygning $parameters
     * @return NyBygningResponse
     */
    public function NyBygning(NyBygning $parameters)
    {
      return $this->__soapCall('NyBygning', array($parameters));
    }

    /**
     * @param SlettMatrikkelnummer $parameters
     * @return SlettMatrikkelnummerResponse
     */
    public function SlettMatrikkelnummer(SlettMatrikkelnummer $parameters)
    {
      return $this->__soapCall('SlettMatrikkelnummer', array($parameters));
    }

    /**
     * @param NyMatrikkelnummer $parameters
     * @return NyMatrikkelnummerResponse
     */
    public function NyMatrikkelnummer(NyMatrikkelnummer $parameters)
    {
      return $this->__soapCall('NyMatrikkelnummer', array($parameters));
    }

    /**
     * @param OppdaterMappeAnsvarlig $parameters
     * @return OppdaterMappeAnsvarligResponse
     */
    public function OppdaterMappeAnsvarlig(OppdaterMappeAnsvarlig $parameters)
    {
      return $this->__soapCall('OppdaterMappeAnsvarlig', array($parameters));
    }

    /**
     * @param OppdaterMappeEksternNoekkel $parameters
     * @return OppdaterMappeEksternNoekkelResponse
     */
    public function OppdaterMappeEksternNoekkel(OppdaterMappeEksternNoekkel $parameters)
    {
      return $this->__soapCall('OppdaterMappeEksternNoekkel', array($parameters));
    }

    /**
     * @param OppdaterMappeStatus $parameters
     * @return OppdaterMappeStatusResponse
     */
    public function OppdaterMappeStatus(OppdaterMappeStatus $parameters)
    {
      return $this->__soapCall('OppdaterMappeStatus', array($parameters));
    }

    /**
     * @param NySaksmappe $parameters
     * @return NySaksmappeResponse
     */
    public function NySaksmappe(NySaksmappe $parameters)
    {
      return $this->__soapCall('NySaksmappe', array($parameters));
    }

    /**
     * @param SlettSaksmappeTilleggsinformasjon $parameters
     * @return SlettSaksmappeTilleggsinformasjonResponse
     */
    public function SlettSaksmappeTilleggsinformasjon(SlettSaksmappeTilleggsinformasjon $parameters)
    {
      return $this->__soapCall('SlettSaksmappeTilleggsinformasjon', array($parameters));
    }

    /**
     * @param SlettSaksmappeMerknad $parameters
     * @return SlettSaksmappeMerknadResponse
     */
    public function SlettSaksmappeMerknad(SlettSaksmappeMerknad $parameters)
    {
      return $this->__soapCall('SlettSaksmappeMerknad', array($parameters));
    }

    /**
     * @param SlettJournalpostTilleggsinformasjon $parameters
     * @return SlettJournalpostTilleggsinformasjonResponse
     */
    public function SlettJournalpostTilleggsinformasjon(SlettJournalpostTilleggsinformasjon $parameters)
    {
      return $this->__soapCall('SlettJournalpostTilleggsinformasjon', array($parameters));
    }

    /**
     * @param SlettJournalpostMerknad $parameters
     * @return SlettJournalpostMerknadResponse
     */
    public function SlettJournalpostMerknad(SlettJournalpostMerknad $parameters)
    {
      return $this->__soapCall('SlettJournalpostMerknad', array($parameters));
    }

}
