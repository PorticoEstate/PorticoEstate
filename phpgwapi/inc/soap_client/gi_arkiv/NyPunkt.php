<?php

class NyPunkt
{

    /**
     * @var PunktListe $posisjon
     */
    protected $posisjon = null;

    /**
     * @var Saksnoekkel $saksnokkel
     */
    protected $saksnokkel = null;

    /**
     * @var ArkivKontekst $kontekst
     */
    protected $kontekst = null;

    /**
     * @param PunktListe $posisjon
     * @param Saksnoekkel $saksnokkel
     * @param ArkivKontekst $kontekst
     */
    public function __construct($posisjon, $saksnokkel, $kontekst)
    {
      $this->posisjon = $posisjon;
      $this->saksnokkel = $saksnokkel;
      $this->kontekst = $kontekst;
    }

    /**
     * @return PunktListe
     */
    public function getPosisjon()
    {
      return $this->posisjon;
    }

    /**
     * @param PunktListe $posisjon
     * @return NyPunkt
     */
    public function setPosisjon($posisjon)
    {
      $this->posisjon = $posisjon;
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
     * @return NyPunkt
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
     * @return NyPunkt
     */
    public function setKontekst($kontekst)
    {
      $this->kontekst = $kontekst;
      return $this;
    }

}
