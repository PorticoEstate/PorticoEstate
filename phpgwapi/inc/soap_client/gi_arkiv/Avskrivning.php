<?php

class Avskrivning
{

    /**
     * @var string $systemID
     */
    protected $systemID = null;

    /**
     * @var \DateTime $avskrivningsdato
     */
    protected $avskrivningsdato = null;

    /**
     * @var string $avskrevetAv
     */
    protected $avskrevetAv = null;

    /**
     * @var Avskrivningsmaate $avskrivningsmaate
     */
    protected $avskrivningsmaate = null;

    /**
     * @var Journalnummer $referanseAvskriverJournalnummer
     */
    protected $referanseAvskriverJournalnummer = null;

    /**
     * @var Journalnummer $referanseAvskrivesAvJournalnummer
     */
    protected $referanseAvskrivesAvJournalnummer = null;

    /**
     * @var EksternNoekkel $referanseAvskriverEksternNoekkel
     */
    protected $referanseAvskriverEksternNoekkel = null;

    /**
     * @var EksternNoekkel $referanseAvskrivesAvEksternNoekkel
     */
    protected $referanseAvskrivesAvEksternNoekkel = null;

    
    public function __construct()
    {
    
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
     * @return Avskrivning
     */
    public function setSystemID($systemID)
    {
      $this->systemID = $systemID;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getAvskrivningsdato()
    {
      if ($this->avskrivningsdato == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->avskrivningsdato);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $avskrivningsdato
     * @return Avskrivning
     */
    public function setAvskrivningsdato(\DateTime $avskrivningsdato = null)
    {
      if ($avskrivningsdato == null) {
       $this->avskrivningsdato = null;
      } else {
        $this->avskrivningsdato = $avskrivningsdato->format(\DateTime::ATOM);
      }
      return $this;
    }

    /**
     * @return string
     */
    public function getAvskrevetAv()
    {
      return $this->avskrevetAv;
    }

    /**
     * @param string $avskrevetAv
     * @return Avskrivning
     */
    public function setAvskrevetAv($avskrevetAv)
    {
      $this->avskrevetAv = $avskrevetAv;
      return $this;
    }

    /**
     * @return Avskrivningsmaate
     */
    public function getAvskrivningsmaate()
    {
      return $this->avskrivningsmaate;
    }

    /**
     * @param Avskrivningsmaate $avskrivningsmaate
     * @return Avskrivning
     */
    public function setAvskrivningsmaate($avskrivningsmaate)
    {
      $this->avskrivningsmaate = $avskrivningsmaate;
      return $this;
    }

    /**
     * @return Journalnummer
     */
    public function getReferanseAvskriverJournalnummer()
    {
      return $this->referanseAvskriverJournalnummer;
    }

    /**
     * @param Journalnummer $referanseAvskriverJournalnummer
     * @return Avskrivning
     */
    public function setReferanseAvskriverJournalnummer($referanseAvskriverJournalnummer)
    {
      $this->referanseAvskriverJournalnummer = $referanseAvskriverJournalnummer;
      return $this;
    }

    /**
     * @return Journalnummer
     */
    public function getReferanseAvskrivesAvJournalnummer()
    {
      return $this->referanseAvskrivesAvJournalnummer;
    }

    /**
     * @param Journalnummer $referanseAvskrivesAvJournalnummer
     * @return Avskrivning
     */
    public function setReferanseAvskrivesAvJournalnummer($referanseAvskrivesAvJournalnummer)
    {
      $this->referanseAvskrivesAvJournalnummer = $referanseAvskrivesAvJournalnummer;
      return $this;
    }

    /**
     * @return EksternNoekkel
     */
    public function getReferanseAvskriverEksternNoekkel()
    {
      return $this->referanseAvskriverEksternNoekkel;
    }

    /**
     * @param EksternNoekkel $referanseAvskriverEksternNoekkel
     * @return Avskrivning
     */
    public function setReferanseAvskriverEksternNoekkel($referanseAvskriverEksternNoekkel)
    {
      $this->referanseAvskriverEksternNoekkel = $referanseAvskriverEksternNoekkel;
      return $this;
    }

    /**
     * @return EksternNoekkel
     */
    public function getReferanseAvskrivesAvEksternNoekkel()
    {
      return $this->referanseAvskrivesAvEksternNoekkel;
    }

    /**
     * @param EksternNoekkel $referanseAvskrivesAvEksternNoekkel
     * @return Avskrivning
     */
    public function setReferanseAvskrivesAvEksternNoekkel($referanseAvskrivesAvEksternNoekkel)
    {
      $this->referanseAvskrivesAvEksternNoekkel = $referanseAvskrivesAvEksternNoekkel;
      return $this;
    }

}
