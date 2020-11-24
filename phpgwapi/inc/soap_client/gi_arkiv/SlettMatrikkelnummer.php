<?php

class SlettMatrikkelnummer
{

    /**
     * @var MatrikkelnummerListe $matrikkelnr
     */
    protected $matrikkelnr = null;

    /**
     * @var Saksnoekkel $saksnokkel
     */
    protected $saksnokkel = null;

    /**
     * @var ArkivKontekst $kontekst
     */
    protected $kontekst = null;

    /**
     * @param MatrikkelnummerListe $matrikkelnr
     * @param Saksnoekkel $saksnokkel
     * @param ArkivKontekst $kontekst
     */
    public function __construct($matrikkelnr, $saksnokkel, $kontekst)
    {
      $this->matrikkelnr = $matrikkelnr;
      $this->saksnokkel = $saksnokkel;
      $this->kontekst = $kontekst;
    }

    /**
     * @return MatrikkelnummerListe
     */
    public function getMatrikkelnr()
    {
      return $this->matrikkelnr;
    }

    /**
     * @param MatrikkelnummerListe $matrikkelnr
     * @return SlettMatrikkelnummer
     */
    public function setMatrikkelnr($matrikkelnr)
    {
      $this->matrikkelnr = $matrikkelnr;
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
     * @return SlettMatrikkelnummer
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
     * @return SlettMatrikkelnummer
     */
    public function setKontekst($kontekst)
    {
      $this->kontekst = $kontekst;
      return $this;
    }

}
