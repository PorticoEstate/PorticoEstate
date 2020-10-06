<?php

class Meldingsboks extends ElektroniskAdresse
{

    /**
     * @var string $tilbyder
     */
    protected $tilbyder = null;

    /**
     * @var string $meldingsboksadresse
     */
    protected $meldingsboksadresse = null;

    /**
     * @param string $tilbyder
     * @param string $meldingsboksadresse
     */
    public function __construct($tilbyder, $meldingsboksadresse)
    {
      $this->tilbyder = $tilbyder;
      $this->meldingsboksadresse = $meldingsboksadresse;
    }

    /**
     * @return string
     */
    public function getTilbyder()
    {
      return $this->tilbyder;
    }

    /**
     * @param string $tilbyder
     * @return Meldingsboks
     */
    public function setTilbyder($tilbyder)
    {
      $this->tilbyder = $tilbyder;
      return $this;
    }

    /**
     * @return string
     */
    public function getMeldingsboksadresse()
    {
      return $this->meldingsboksadresse;
    }

    /**
     * @param string $meldingsboksadresse
     * @return Meldingsboks
     */
    public function setMeldingsboksadresse($meldingsboksadresse)
    {
      $this->meldingsboksadresse = $meldingsboksadresse;
      return $this;
    }

}
