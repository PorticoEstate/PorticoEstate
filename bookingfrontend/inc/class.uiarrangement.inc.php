<?php

phpgw::import_class("booking.uicommon");

class bookingfrontend_uiarrangement extends booking_uicommon
{


    public $public_functions = array
    (
        'show' => true
    );

    public function show()
    {
        $config = CreateObject('phpgwapi.config', 'booking');
        $config->read();
        self::render_template_xsl('arrangement', array('arrangement' => '', 'config_data' => $config));

    }

    public function query()
    {
        // TODO: Implement query() method.
    }
}