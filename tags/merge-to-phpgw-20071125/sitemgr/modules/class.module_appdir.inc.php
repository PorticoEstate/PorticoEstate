<?php 

// this module is only for demonstrative purposes,
// a real appdir would be better defined as a real phpgw application 
// and a sitemgr module would not have to handle the data, but only to fetch it
class module_appdir extends Module
{

	function module_appdir()
	{
		$this->arguments = array();
		$this->title = lang('An application directory');
		$this->description = lang('This module demonstrates how handling data stored in XML and building an interacvite interface from it');
	}

	function get_user_interface()
	{
		$interface = array();

		$allapps = $this->block->arguments['directory'];
		//xmltool2 is a slightly modified version of phpgwapi.xmltool of HEAD
		$xmltool = CreateObject('sitemgr.xmltool2');
		$xmltool->import_xml($allapps);
		$apparray = $xmltool->export_var();		
		$i = 0;
		while (list(,$app) = @each($apparray['app']))
		{
			$element['label'] = '<hr>';
			$element['form'] = '<hr>';
			$interface[] = $element;
			$element['label'] = '<b>'.$app['name'][0].'</b>';
			$element['form'] = '';
			$interface[] = $element;
			foreach(array('name','maintainer','url','description') as $key)
			{
				$elementname = 'element[' . $this->block->version . '][' .$key . '][' . $i .']';
				$element['label'] = ucfirst($key);
				$element['form'] = $this->build_input_element(
					array(
						'type' => ($key == 'description') ? 'textarea' : 'textfield',
						'params' => ($key == 'description') ? array('cols' => 50,'rows' => 15) : array('size' => 50)),
					$app[$key][0],
					$elementname
				);
				$interface[] = $element;
			}
			$element['label'] = lang('Delete this application');
			$element['form'] = $this->build_input_element(
				array('type' => 'checkbox'),
				False,
				'element[' . $this->block->version . '][delete][' . $i . ']'
			);
			$interface[] = $element;
			$i++;
		}
		$element['label'] = '<hr>';
		$element['form'] = '<hr>';
		$interface[] = $element;
		$element['label'] = lang('Add a new application');
		$element['form'] = $this->build_input_element(
			array('type' => 'checkbox'),
			False,
			'element[' . $this->block->version . '][addnew]'
		);
		$interface[] = $element;
		return $interface;
	}

	function validate(&$data)
	{
		$xmltool = CreateObject('sitemgr.xmltool2','node','directory','');
		$i = 0;
		while (isset($data['name'][$i]))
		{
			if (!$data['delete'][$i])
			{
				$xmltool->import_var(
					'app',
					array(
						'name' => $data['name'][$i],
						'maintainer' => $data['maintainer'][$i],
						'url'  => $data['url'][$i],
						'description' => $data['description'][$i],
					)
				);
			}
			$i++;
		}
		if ($data['addnew'])
		{
			$xmltool->import_var(
				'app',
				array(
					'name' => lang('New application'),
					'maintainer' => lang('Maintainer'),
					'url' => 'http://',
					'description' => lang('Description')
				)
			);
		}
			
		$newdata['directory'] = $xmltool->export_xml();
		$data = $newdata;
		return true;
	}

	function set_block(&$block,$produce=False)
	{
		parent::set_block($block,$produce);

		if ($produce)
		{
			require_once(PHPGW_INCLUDE_ROOT . SEP . 'sitemgr' . SEP . 'inc' . SEP . 'class.xslt_transform.inc.php');
			$this->add_transformer(new xslt_transform($this->find_template_dir() . SEP . 'list.xsl'));
		}
	}

	function get_content(&$arguments,$properties)
	{
		return $arguments['directory'];
	}
}
