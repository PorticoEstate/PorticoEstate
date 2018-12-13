<?php
	/**
	* Helps manage the portal boxes for phpGroupWares main page
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @copyright Copyright (C) 2000-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage gui
	* @version $Id$
	*/

	/**
	* Helps manage the portal boxes for phpGroupWares main page
	* 
	* @package phpgwapi
	* @subpackage gui
	*/
	class portalbox
	{
		//Set up the Object, reserving memory space for variables

		var $width;
		var $innerwidth;
		var $controls;
		var $classname;
		var $up;
		var $down;
		var $close;
		var $question;
		var $edit;
		
		var $data = Array();

		// Textual variables
		var $title;

		// Template
		var $p;

		/*
		Use these functions to get and set the values of this
		object's variables. This is good OO practice, as it means
		that datatype checking can be completed and errors raised accordingly.
		*/
		function setvar($var,$value='')
		{
			if ($value=='')
			{
				global $$var;
				$value = $$var;
			}
			$this->$var = $value;
			// echo $var." = ".$this->$var."<br>\n";
		}

		function getvar($var='')
		{
			if ($var=='' || !isset($this->$var))
			{
				echo 'Programming Error: '.$this->getvar('classname').'->getvar('.$var.')!<br>'."\n";
				$GLOBALS['phpgw']->common->phpgw_exit();
			}
			//echo "Var = ".$var."<br>\n";
			//echo $var." = ".$this->$var."<br>\n";
			return $this->$var;
		}

		/*
		This is the constructor for the object.
		*/
		function __construct($title='', $primary='', $secondary='', $tertiary='')
		{
			$this->setvar('title',$title);
			// echo 'After SetVar Title = '.$this->getvar('title')."<br>\n";
			$this->setvar('titlebgcolor',$primary);
			$this->setvar('innerbgcolor',$secondary);
		}

		function start_template()
		{
			$this->p = createObject('phpgwapi.template',$GLOBALS['phpgw']->common->get_tpl_dir('home'));
			$this->p->set_file('portal', 'portal.tpl');

			$this->p->set_block('portal','portal_box','portal_box');
			$this->p->set_block('portal','portal_row','portal_row');
			$this->p->set_block('portal','portal_listbox_header','portal_listbox_header');
			$this->p->set_block('portal','portal_listbox_link','portal_listbox_link');
			$this->p->set_block('portal','portal_listbox_footer','portal_listbox_footer');
			$this->p->set_block('portal','portal_control','portal_control');
			$this->p->set_block('portal','link_field','link_field');

			$var = Array(
				'outer_width'	=> $this->getvar('width'),
				'title'	=> $this->getvar('title'),
				'inner_width'	=> $this->getvar('width'),
				'control_link'	=> ''
			);
			$this->p->set_var($var);
			$this->p->set_var('row','',False);
		}

		function set_controls($control='',$control_param='')
		{
			if($control != '' && $control_param != '')
			{
				$this->setvar($control,$GLOBALS['phpgw']->link($control_param['url'],array('app'=>$control_param['app'],'control'=>$control)));
			}
		}

		function set_internal($data='')
		{
			if(empty($data) && !count($this->data))
			{
				$data = '&nbsp;';
			}
			if(!empty($data))
			{
				$this->p->set_var('output',$data);
				$this->p->parse('row','portal_row',true);
			}
		}

		function draw_box()
		{
			$control = '';
			if($this->up || $this->down || $this->close || $this->question || $this->edit)
			{
				$control_array = Array(
					'up',
					'down',
					'question',
					'close',
					'edit'
				);
				//@reset($control_array);
				//while(list($key,$param) = each($control_array))
				foreach($control_array as $key => $param)
				{
					if(isset($this->$param) && $this->$param)
					{
						$image_width = 15;
						if($param == 'edit')
						{
							$image_width = 30;
						}
						$this->p->set_var('link_field_data','<a href="'.$this->$param.'"><img src="'.$GLOBALS['phpgw']->common->image('phpgwapi',$param.'.button').'" border="0" width="'.$image_width.'" height="15" alt="'.lang($param).'"></a>');
						$this->p->parse('control_link','link_field',True);
					}
				}
				$this->p->parse('portal_controls','portal_control',True);
			}
			return $this->p->fp('out','portal_box');
		}
	}
