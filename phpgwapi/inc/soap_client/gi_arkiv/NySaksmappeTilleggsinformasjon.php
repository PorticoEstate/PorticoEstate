<?php

class NySaksmappeTilleggsinformasjon
{

    /**
     * @var TilleggsinformasjonListe $tilleggsinfo
     */
    protected $tilleggsinfo = null;

    /**
     * @var Saksnoekkel $saksnokkel
     */
    protected $saksnokkel = null;

    /**
     * @var ArkivKontekst $kontekst
     */
    protected $kontekst = null;

    /**
     * @param TilleggsinformasjonListe $tilleggsinfo
     * @param Saksnoekkel $saksnokkel
     * @param ArkivKontekst $kontekst
     */
    public function __construct($tilleggsinfo, $saksnokkel, $kontekst)
    {
      $this->tilleggsinfo = $tilleggsinfo;
      $this->saksnokkel = $saksnokkel;
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
     * @return NySaksmappeTilleggsinformasjon
     */
    public function setTilleggsinfo($tilleggsinfo)
    {
      $this->tilleggsinfo = $tilleggsinfo;
      return $this;
    }

    /**
     * @return Saksnoekkel
     */
    public function getSaksnokkel()
    {
      return $this->saksnokkel;
    }

    /**
     * @param Saksnoekkel $saksnokkel
     * @return NySaksmappeTilleggsinformasjon
     */
    public function setSaksnokkel($saksnokkel)
    {
      $this->saksnokkel = $saksnokkel;
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
     * @return NySaksmappeTilleggsinformasjon
     */
    public function setKontekst($kontekst)
    {
      $this->kontekst = $kontekst;
      return $this;
    }

}
