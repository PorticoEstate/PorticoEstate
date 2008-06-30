<?php 

class Transformer
{
	var $arguments;

	function Transformer($arguments=array())
	{
		$this->arguments = $arguments;
	}

	function apply_transform($title,$content)
	{
		return $content;
	}
}


class Module 
{
	var $i18n; //flag a module must use if it wants its content to be translatable
	var $validation_error;
	var $transformer_chain;
	var $arguments;
	var $properties;
	var $block;
	var $session;
	var $post;
	var $get;
	var $cookie;


	function Module()
	{
		
		$this->arguments = array();
		$this->properties = array();
		$this->transformer_chain = array();
		$this->title = "Standard module";
		$this->description = "Parent class that all modules should extend";
	}

	function add_transformer(&$transformer)
	{
		$this->transformer_chain[] =& $transformer;
	}

	//before calling the functions get_user_interface, get_output,
	//the function set_block is used, so that we know in what scope we are, know the arguments, 
	//and can retrieve the properties
	//this function can be overriden (but do not forget to call parent::set_block) in order to do some configuration
	//that depends on the blocks arguments
	//the produce argument is set when content is generated, so we can do some stuff we do not need when editing the block
	function set_block(&$block,$produce=False)
	{
		if ($produce)
		{
			if ($this->session)
			{
				$sessionarguments = $GLOBALS['phpgw']->session->appsession('block[' . $block->id . ']', 'sitemgr-site');
				while (list(,$argument) = @each($this->session))
				{
					if (isset($sessionarguments[$argument]))
					{
						$block->arguments[$argument] = $sessionarguments[$argument];
					}
				}
			}
			while (list(,$argument) = @each($this->get))
			{
				if (isset($_GET['block'][$block->id][$argument]))
				{
					$block->arguments[$argument] = $_GET['block'][$block->id][$argument];
				}
			}
			//contrary to $this->get, cookie and session, the argument name is the key in $this->post because this array also
			//defines the form element

			while (list($argument,) = @each($this->post))
			{
				if (isset($_POST['block'][$block->id][$argument]))
				{
					$block->arguments[$argument] = $_POST['block'][$block->id][$argument];
				}
			}
			while (list(,$argument) = @each($this->cookie))
			{
				if (isset($_COOKIE['block'][$block->id][$argument]))
				{
					$block->arguments[$argument] = $_COOKIE['block'][$block->id][$argument];
				}
			}
		}
		$this->block =& $block;
	}

	function link($modulevars=array())
	{
		while (list($key,$value) = @each($modulevars))
		{
			//%5B and %5D are urlencoded [ and ]
			$extravars['block' . '%5B'. $this->block->id  .'%5D%5B' . $key . '%5D'] = $value;
		}
		if ($GLOBALS['page']->name)
		{
			$extravars['page_name'] = $GLOBALS['page']->name;
		}
		elseif ($GLOBALS['page']->cat_id)
		{
			$extravars['category_id'] = $GLOBALS['page']->cat_id;
		}
		elseif ($GLOBALS['page']->toc)
		{
			$extravars['toc'] = 1;
		}
		elseif ($GLOBALS['page']->index)
		{
			$extravars['index'] = 1;
		}
		return sitemgr_link($extravars);
	}

	function find_template_dir()
	{
		$templaterootformat = "{$GLOBALS['sitemgr_info']['site_dir']}/templates/%s/modules/{$this->block->module_name}";
		$themetemplatedir = sprintf($templaterootformat,$GLOBALS['sitemgr_info']['themesel']);
		if (is_dir($themetemplatedir))
		{
			return $themetemplatedir;
		}
		else
		{
			return sprintf($templaterootformat,'default');
		}
	}

	function get_properties($cascading=True)
	{
		if ($this->properties)
		{
			if ($cascading)
			{
				return $GLOBALS['Common_BO']->modules->getcascadingmoduleproperties(
					$this->block->module_id,
					$this->block->area,
					$this->block->cat_id,
					$this->block->module_name
				);
			}
			else
			{
				return $GLOBALS['Common_BO']->modules->getmoduleproperties(
					$this->block->module_id,
					$this->block->area,
					$this->block->cat_id
				);
			}
		}
		else
		{
			return False;
		}
	}

	function get_user_interface()
	{
		//if you override this function you can fetch properties and adapt the interface accordingly
		//$properties = $this->get_properties();
		$interface = array();
		reset($this->arguments);
		while (list($key,$input) = @each($this->arguments))
		{
			$elementname = 'element[' . $this->block->version . ']';
			$elementname .= ($input['i18n'] ? ('[i18n][' .$key . ']') : ('[' .$key . ']'));
			//arrays of input elements are only implemented for the user interface
			if ($input['type'] == 'array')
			{
				$i = 0;
				while (isset($input[$i]))
				{
					$element['label'] = $input[$i]['label'];
					$element['form'] = $this->build_input_element($input[$i],$this->block->arguments[$key][$i],$elementname.'[]');
					$interface[] = $element;
					$i++;
				}
			}
			else
			{
				$element['label'] = $input['label'];
				$element['form'] = $this->build_input_element($input,$this->block->arguments[$key],$elementname);
				$interface[] = $element;
			}
		}
		return $interface;
	}


	function get_translation_interface($fromblock,$toblock)
	{
		//if you override this function you can fetch properties and adapt the interface accordingly
		//$properties = $this->get_properties();
		$interface = array();
		reset($this->arguments);
		while (list($key,$input) = @each($this->arguments))
		{
			if ($input['i18n'])
			{
				$elementname = 'element[' . $this->block->version . '][i18n][' .$key . ']';
				//arrays of input elements are only implemented for the user interface
				if ($input['type'] == 'array')
				{
					$i = 0;
					while (isset($input[$i]))
					{
						$element['label'] = $input[$i]['label'];
						$element['form'] = $this->build_input_element($input[$i],$toblock->arguments[$key][$i],$elementname.'[]');
						$element['value'] = $fromblock->arguments[$key][$i];
						$interface[] = $element;
						$i++;
					}
				}
				else
				{
					$element['label'] = $input['label'];
					$element['form'] = $this->build_input_element($input,$toblock->arguments[$key],$elementname);
					$element['value'] = $fromblock->arguments[$key];
					$interface[] = $element;
				}
			}
		}
		return $interface;
	}


	function get_admin_interface()
	{
		$properties = $this->get_properties(False);
		$interface = array();
		while (list($key,$input) = @each($this->properties))
		{
			$elementname = 'element[' .$key . ']';
			$element['label'] = $input['label'];
			$element['form'] = $this->build_input_element($input,$properties[$key],$elementname);
			$interface[$key] = $element;
		}
		return $interface;
	}

	function build_post_element($key,$default=False)
	{
		return $this->build_input_element(
			$this->post[$key],
			($default !== False) ? $default : $this->block->arguments[$key],
			('block[' . $this->block->id  . '][' . $key . ']')
		);
	}

	//this function strips html and curly braces from the default values of the input elements
	//the former is necessary for valid input forms, the latter would hurt phpgw's template
	function escape_default(&$default)
	{
		$trans = array('{' => '&#123;', '}' => '&#125;');
		if (is_array($default))
		{
			reset($default);
			while (list($key,$val) = each($default))
			{
				$this->escape_default($data[$key]);
			}
		}
		else
		{
			$default = strtr($GLOBALS['phpgw']->strip_html($default),$trans);
		}
	}

	function build_input_element($input,$default,$elementname)
	{
		if ($default)
		{
			$this->escape_default($default);
		}
		$paramstring = '';
		while (list($param,$value) = @each($input['params']))
		{
			$paramstring .= $param . '="' . $value . '" ';
		}
		$inputdef = $paramstring . ' name="' . $elementname . ($input['multiple'] ? '[]' : '') . '"';
		switch($input['type'])
		{
			case 'textarea':
				return '<textarea ' . $inputdef . '>' . $default . '</textarea>';
			case 'textfield':
				return '<input type="text" ' . $inputdef . ' value ="' . $default . '" />';
			case 'checkbox':
				return '<input type="checkbox" ' . $inputdef . ($default ? 'checked="checked"' :'') . '" />';
			case 'select':
				$select = '<select ' .($input['multiple'] ? 'multiple="multiple"' : '') . $inputdef . '>';
				foreach ($input['options'] as $value => $display)
				{
					$selected='';
					if 
					(
						($input['multiple'] && is_array($default) && in_array($value,$default)) || 
						(!$input['multiple'] && ($default == $value))
					)
					{
						$selected = 'selected="selected"';
					}
					$select .= '<option value="'. $value . '" ' . $selected . '>' . $display . '</option>';
				}
				$select .= '</select>';
				return $select;
			case 'submit':
				return '<input type="submit" ' . $inputdef .' value ="' . $input['value'] . '" />';
			case 'image':
				return '<input type="image" ' . $inputdef .' src ="' . $input['src'] . '" />';
			case 'file':
				return '<input type="file" ' . $inputdef . ' value ="'. $input['value'] . '" />';
			case 'hidden':
				return '<input type="hidden" '.$inputdef . ' value ="'.$input['value'] . '" />';
		}
	}

	function validate(&$data)
	{
		return true;
	}

	function validate_properties(&$data)
	{
		return true;
	}

	//never call get_content directly, get_output takes care of passing it the right arguments
	function get_content(&$arguments,$properties)
	{

	}

	function get_output($type='html')
	{
		$content= $this->get_content($this->block->arguments,$this->get_properties());
		if (!$content)
		{
			return '';
		}
		if ($type == 'raw')
		{
			return $content;
		}
		else
		{
			for ( $i = 0; $i < count( $this->transformer_chain ); ++$i )
			{
				$content = $this->transformer_chain[$i]->apply_transform($this->block->title,$content);
			}
			//store session variables
			if ($this->session)
			{
				reset($this->session);
				while (list(,$argument) = each($this->session))
				{
					if (isset($this->block->arguments[$argument]))
					{
						$sessionarguments[$argument] = $this->block->arguments[$argument];
					}
				}
				$GLOBALS['phpgw']->session->appsession('block[' . $this->block->id . ']','sitemgr-site',$sessionarguments);
			}
			return $content;
		}
	}
}
