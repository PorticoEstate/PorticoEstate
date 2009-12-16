<?php
    phpgw::import_class('phpgwapi.yui');
    phpgw::import_class('property.soitem');

    /**
     * FIXME: Description
     * @package property
     */

    class property_uiitem
    {
        private $so;
        public $public_functions = array('index' => true);

        function __construct()
        {
            $this->so = property_soitem::singleton();
        }



        function index()
        {
            $datatable = $this->so->get();
            $datatable = $this->so->populate($datatable);
/*
            phpgwapi_yui::load_widget('datatable');
            // Prepare template variables and process XSLT
			$template_vars = array();
			$template_vars['datatable'] = $datatable;
			$GLOBALS['phpgw']->xslttpl->add_file(array('datatable'));
	      	$GLOBALS['phpgw']->xslttpl->set_var('phpgw', $template_vars);

	      	if ( !isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css) )
	      	{
	        	$GLOBALS['phpgw']->css = createObject('phpgwapi.css');
	      	}
			// Prepare CSS Style
		  	$GLOBALS['phpgw']->css->validate_file('datatable');
		  	$GLOBALS['phpgw']->css->validate_file('property');
		  	$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');

			//Title of Page
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('actor') . ': ' . lang('list ' . $this->role);

	  		// Prepare YUI Library
  			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'actor.index', 'property' );

*/
            echo '<pre></code>';
            print_r($datatable);
            echo '</code></pre>';
        }

    }
