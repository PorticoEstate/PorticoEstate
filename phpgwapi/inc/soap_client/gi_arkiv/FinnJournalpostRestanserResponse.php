<?php

class FinnJournalpostRestanserResponse
{

    /**
     * @var JournalpostListe $return
     */
    protected $return = null;

    /**
     * @param JournalpostListe $return
     */
    public function __construct($return)
    {
      $this->return = $return;
    }

    /**
     * @return JournalpostListe
     */
    public function getReturn()
    {
      return $this->return;
    }

    /**
     * @param JournalpostListe $return
     * @return FinnJournalpostRestanserResponse
     */
    public function setReturn($return)
    {
      $this->return = $return;
      return $this;
    }

}
