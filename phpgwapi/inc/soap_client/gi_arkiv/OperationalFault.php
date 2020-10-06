<?php

class OperationalFault extends SystemFault
{

    /**
     * @param string $feilKode
     */
    public function __construct($feilKode)
    {
      parent::__construct($feilKode);
    }

}
