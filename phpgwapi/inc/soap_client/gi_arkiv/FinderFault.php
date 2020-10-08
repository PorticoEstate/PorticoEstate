<?php

class FinderFault extends ApplicationFault
{

    /**
     * @param string $feilKode
     */
    public function __construct($feilKode)
    {
      parent::__construct($feilKode);
    }

}
