<?php

class NyJournalpostMerknad
{

    /**
     * @var MerknadListe $merknad
     */
    protected $merknad = null;

    /**
     * @var Journpostnoekkel $journalnokkel
     */
    protected $journalnokkel = null;

    /**
     * @var ArkivKontekst $kontekst
     */
    protected $kontekst = null;

    /**
     * @param MerknadListe $merknad
     * @param Journpostnoekkel $journalnokkel
     * @param ArkivKontekst $kontekst
     */
    public function __construct($merknad, $journalnokkel, $kontekst)
    {
      $this->merknad = $merknad;
      $this->journalnokkel = $journalnokkel;
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
     * @return NyJournalpostMerknad
     */
    public function setMerknad($merknad)
    {
      $this->merknad = $merknad;
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
     * @return NyJournalpostMerknad
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
     * @return NyJournalpostMerknad
     */
    public function setKontekst($kontekst)
    {
      $this->kontekst = $kontekst;
      return $this;
    }

}
