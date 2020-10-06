<?php

class NySaksmappeMerknad
{

    /**
     * @var MerknadListe $merknad
     */
    protected $merknad = null;

    /**
     * @var Saksnoekkel $saksnokkel
     */
    protected $saksnokkel = null;

    /**
     * @var ArkivKontekst $kontekst
     */
    protected $kontekst = null;

    /**
     * @param MerknadListe $merknad
     * @param Saksnoekkel $saksnokkel
     * @param ArkivKontekst $kontekst
     */
    public function __construct($merknad, $saksnokkel, $kontekst)
    {
      $this->merknad = $merknad;
      $this->saksnokkel = $saksnokkel;
      $this->kontekst = $kontekst;
    }

    /**
     * @return MerknadListe
     */
    public function getMerknad()
    {
      return $this->merknad;
    }

    /**
     * @param MerknadListe $merknad
     * @return NySaksmappeMerknad
     */
    public function setMerknad($merknad)
    {
      $this->merknad = $merknad;
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
     * @return NySaksmappeMerknad
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
     * @return NySaksmappeMerknad
     */
    public function setKontekst($kontekst)
    {
      $this->kontekst = $kontekst;
      return $this;
    }

}
