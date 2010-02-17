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
            $tmpl_search_path = array();
            $tmpl_search_path[] = PHPGW_SERVER_ROOT . '/phpgwapi/templates/base';
            $tmpl_search_path[] = PHPGW_SERVER_ROOT . '/phpgwapi/templates/' . $GLOBALS['phpgw_info']['server']['template_set'];
            $tmpl_search_path[] = PHPGW_SERVER_ROOT . '/' . $GLOBALS['phpgw_info']['flags']['currentapp'] . '/templates/base';

			$GLOBALS['phpgw']->xslttpl->set_output('html');

            // Loop through search paths
            foreach($tmpl_search_path as $path) {
                $filename = $path . '/' . $tpl . '.xsl';

                // If found, apply to xslfiles register and break loop
                if (file_exists($filename))
                {
                    $GLOBALS['phpgw']->xslttpl->xslfiles[$tpl] = $filename;
                    break;
                }
            }
            
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('data' => $data));
		}
    }

?>
