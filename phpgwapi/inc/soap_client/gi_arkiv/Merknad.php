<?php

class Merknad
{

    /**
     * @var string $systemID
     */
    protected $systemID = null;

    /**
     * @var string $merknadstekst
     */
    protected $merknadstekst = null;

    /**
     * @var string $merknadstype
     */
    protected $merknadstype = null;

    /**
     * @var \DateTime $merknadsdato
     */
    protected $merknadsdato = null;

    /**
     * @var string $merknadRegistrertAv
     */
    protected $merknadRegistrertAv = null;

    /**
     * @var string $merknadRegistrertAvInit
     */
    protected $merknadRegistrertAvInit = null;

    /**
     * @param string $merknadstekst
     */
    public function __construct($merknadstekst)
    {
      $this->merknadstekst = $merknadstekst;
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
     * @return Merknad
     */
    public function setSystemID($systemID)
    {
      $this->systemID = $systemID;
      return $this;
    }

    /**
     * @return string
     */
    public function getMerknadstekst()
    {
      return $this->merknadstekst;
    }

    /**
     * @param string $merknadstekst
     * @return Merknad
     */
    public function setMerknadstekst($merknadstekst)
    {
      $this->merknadstekst = $merknadstekst;
      return $this;
    }

    /**
     * @return string
     */
    public function getMerknadstype()
    {
      return $this->merknadstype;
    }

    /**
     * @param string $merknadstype
     * @return Merknad
     */
    public function setMerknadstype($merknadstype)
    {
      $this->merknadstype = $merknadstype;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getMerknadsdato()
    {
      if ($this->merknadsdato == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->merknadsdato);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $merknadsdato
     * @return Merknad
     */
    public function setMerknadsdato(\DateTime $merknadsdato = null)
    {
      if ($merknadsdato == null) {
       $this->merknadsdato = null;
      } else {
        $this->merknadsdato = $merknadsdato->format(\DateTime::ATOM);
      }
      return $this;
    }

    /**
     * @return string
     */
    public function getMerknadRegistrertAv()
    {
      return $this->merknadRegistrertAv;
    }

    /**
     * @param string $merknadRegistrertAv
     * @return Merknad
     */
    public function setMerknadRegistrertAv($merknadRegistrertAv)
    {
      $this->merknadRegistrertAv = $merknadRegistrertAv;
      return $this;
    }

    /**
     * @return string
     */
    public function getMerknadRegistrertAvInit()
    {
      return $this->merknadRegistrertAvInit;
    }

    /**
     * @param string $merknadRegistrertAvInit
     * @return Merknad
     */
    public function setMerknadRegistrertAvInit($merknadRegistrertAvInit)
    {
      $this->merknadRegistrertAvInit = $merknadRegistrertAvInit;
      return $this;
    }

}
