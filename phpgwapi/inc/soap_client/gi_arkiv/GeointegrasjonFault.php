<?php

class GeointegrasjonFault
{

    /**
     * @var string $feilKode
     */
    protected $feilKode = null;

    /**
     * @var string $feilBeskrivelse
     */
    protected $feilBeskrivelse = null;

    /**
     * @var StringListe $feilDetaljer
     */
    protected $feilDetaljer = null;

    /**
     * @param string $feilKode
     */
    public function __construct($feilKode)
    {
      $this->feilKode = $feilKode;
    }

    /**
     * @return string
     */
    public function getFeilKode()
    {
      return $this->feilKode;
    }

    /**
     * @param string $feilKode
     * @return GeointegrasjonFault
     */
    public function setFeilKode($feilKode)
    {
      $this->feilKode = $feilKode;
      return $this;
    }

    /**
     * @return string
     */
    public function getFeilBeskrivelse()
    {
      return $this->feilBeskrivelse;
    }

    /**
     * @param string $feilBeskrivelse
     * @return GeointegrasjonFault
     */
    public function setFeilBeskrivelse($feilBeskrivelse)
    {
      $this->feilBeskrivelse = $feilBeskrivelse;
      return $this;
    }

    /**
     * @return StringListe
     */
    public function getFeilDetaljer()
    {
      return $this->feilDetaljer;
    }

    /**
     * @param StringListe $feilDetaljer
     * @return GeointegrasjonFault
     */
    public function setFeilDetaljer($feilDetaljer)
    {
      $this->feilDetaljer = $feilDetaljer;
      return $this;
    }

}
