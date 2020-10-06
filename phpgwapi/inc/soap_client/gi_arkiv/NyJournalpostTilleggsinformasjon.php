<?php

class NyJournalpostTilleggsinformasjon
{

    /**
     * @var TilleggsinformasjonListe $tilleggsinfo
     */
    protected $tilleggsinfo = null;

    /**
     * @var Journpostnoekkel $journalnokkel
     */
    protected $journalnokkel = null;

    /**
     * @var ArkivKontekst $kontekst
     */
    protected $kontekst = null;

    /**
     * @param TilleggsinformasjonListe $tilleggsinfo
     * @param Journpostnoekkel $journalnokkel
     * @param ArkivKontekst $kontekst
     */
    public function __construct($tilleggsinfo, $journalnokkel, $kontekst)
    {
      $this->tilleggsinfo = $tilleggsinfo;
      $this->journalnokkel = $journalnokkel;
      $this->kontekst = $kontekst;
    }

    /**
     * @return TilleggsinformasjonListe
     */
    public function getTilleggsinfo()
    {
      return $this->tilleggsinfo;
    }

    /**
     * @param TilleggsinformasjonListe $tilleggsinfo
     * @return NyJournalpostTilleggsinformasjon
     */
    public function setTilleggsinfo($tilleggsinfo)
    {
      $this->tilleggsinfo = $tilleggsinfo;
      return $this;
    }

    /**
     * @return Journpostnoekkel
     */
    public function getJournalnokkel()
    {
      return $this->journalnokkel;
    }

    /**
     * @param Journpostnoekkel $journalnokkel
     * @return NyJournalpostTilleggsinformasjon
     */
    public function setJournalnokkel($journalnokkel)
    {
      $this->journalnokkel = $journalnokkel;
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
     * @return NyJournalpostTilleggsinformasjon
     */
    public function setKontekst($kontekst)
    {
      $this->kontekst = $kontekst;
      return $this;
    }

}
