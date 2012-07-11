<?php
	/**
	* phpGroupWare - Rich Text Editor Handler class
	*
	* Wrap a rich text editor widget for phpGroupWare
	* Allows widgets to be swapped without code refactoring
	*
	* @author Dave Hall <skwashd@phpgroupware.org>
	* @copyright Copyright (C) 2006 - 2008 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	* @package phpgroupware
	* @subpackage phpgwapi
	* @version $Id$
	*/

	/**
	* Rich Text Editor Handler class
	*
	* @package phpgroupware
	* @subpackage phpgwapi
	*/
	class phpgwapi_richtext
	{
		/**
		 * @var the base URL for all links, defaults to phpgw url
		 */
		 protected $_base_url;
		 
		/**
		* @var string $rte which rich text editor will be used
		*/
		protected $_rte;

		/**
		* @var array $targets the targets which will be replaced by the rich text editor
		*/
		protected $_targets = array();
		
		/**
		* Constructor
		*
		* @return void
		*/
		public function __construct()
		{
			$this->_base_url = $GLOBALS['phpgw_info']['server']['webserver_url'];
			$this->_rte = '';
			$this->_init_head();
		}

		/**
		* Generate the dynamic script content for the header
		*
		* @return void
		*/
		public function generate_script()
		{
			$js = '';
			
			foreach ( array_keys($this->_targets) as $target )
			{
				$js .= <<<SCRIPT
			(function() {
				var Dom = YAHOO.util.Dom,
				Event = YAHOO.util.Event;
				
				var editorConfig = {
					height: '300px',
					width: '600px',
					animate: true,
					dompath: true,
 					handleSubmit: true
				};
				
				var editorWidget = new YAHOO.widget.Editor('{$target}', editorConfig);
				editorWidget.render();
			})();
				
SCRIPT;
			}

			$GLOBALS['phpgw']->js->add_event('load', $js);
		}

		/**
		* Replaces an element with a rich text editor instance
		*
		* @return void
		*/
		public function replace_element($html_id)
		{
			$this->_targets[$html_id] = true; // stops duplicates
		}

		/**
		 * Set the base URL for all links
		 *
		 * @return void
		 */
		 public function set_base_url($url)
		 {
			$this->_base_url = $url;
		 }

		/**
		* Add the appropriate <script>s to the <head> section
		*
		* @access private
		*/
		protected function _init_head()
		{
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/editor/assets/skins/sam/editor.css');
			phpgw::import_class('phpgwapi.yui');
			phpgwapi_yui::load_widget('editor');
		}
	}
