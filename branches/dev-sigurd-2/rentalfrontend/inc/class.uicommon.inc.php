<?php

    class rentalfrontend_uicommon
    {
        /**
         * Render XSLT
         *
         * @param string $tpl
         * @param mixed $data
         */
        public static function render_template($tpl, $data)
		{
            $GLOBALS['phpgw']->xslttpl->add_file($tpl); // $tpl bør komme inn som array ( i tilfelle flere..)
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('data' => $data));
		}
    }
