<?php

class NyAvskrivning
{

    /**
     * @var AvskrivningListe $avskrivning
     */
    protected $avskrivning = null;

    /**
     * @var Journpostnoekkel $journalnokkel
     */
    protected $journalnokkel = null;

    /**
     * @var ArkivKontekst $kontekst
     */
    protected $kontekst = null;

    /**
     * @param AvskrivningListe $avskrivning
     * @param Journpostnoekkel $journalnokkel
     * @param ArkivKontekst $kontekst
     */
    public function __construct($avskrivning, $journalnokkel, $kontekst)
    {
      $this->avskrivning = $avskrivning;
      $this->journalnokkel = $journalnokkel;
      $this->kontekst = $kontekst;
    }

    /**
     * @return AvskrivningListe
     */
    public function getAvskrivning()
    {
      return $this->avskrivning;
    }

    /**
     * @param AvskrivningListe $avskrivning
     * @return NyAvskrivning
     */
    public function setAvskrivning($avskrivning)
    {
      $this->avskrivning = $avskrivning;
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
     * @return NyAvskrivning
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
     * @return NyAvskrivning
     */
    public function setKontekst($kontekst)
    {
      $this->kontekst = $kontekst;
      return $this;
    }

}
