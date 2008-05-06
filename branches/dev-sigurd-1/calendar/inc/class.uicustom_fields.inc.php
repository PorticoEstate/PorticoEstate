<?php
  /**************************************************************************\
  * phpGroupWare - Calendar - Custom fields and sorting                      *
  * http://www.phpgroupware.org                                              *
  * Written by Ralf Becker <RalfBecker@outdoor-training.de>                  *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

	/* $Id$ */

	phpgw::import_class('calendar.bocustom_fields');

	class calendar_uicustom_fields extends calendar_bocustom_fields
	{
		var $public_functions = array
		(
			'index' => True,
			'submited'  => True
		);
		
		var $classname;
		
		public function __construct()
		{
			parent::__construct();

			$this->tpl = $GLOBALS['phpgw']->template;
			if (!is_object($GLOBALS['phpgw']->nextmatchs))
			{
				$GLOBALS['phpgw']->nextmatchs = CreateObject('phpgwapi.nextmatchs');
			}
			$this->html = CreateObject('calendar.html');
		}

		function index($error='')
		{
			unset($GLOBALS['phpgw_info']['flags']['noheader']);
			unset($GLOBALS['phpgw_info']['flags']['nonavbar']);
			$GLOBALS['phpgw_info']['flags']['app_header'] = $GLOBALS['phpgw_info']['apps']['calendar']['title'].' - '.lang('Custom fields and sorting');
			$GLOBALS['phpgw']->common->phpgw_header(true);

			$this->tpl = $GLOBALS['phpgw']->template;
			$this->tpl->set_root(PHPGW_APP_TPL);
			$this->tpl->set_unknowns('remove');
			$this->tpl->set_file(array(
				'custom_fields_tpl'	=> 'custom_fields.tpl'
			));
			$this->tpl->set_block('custom_fields_tpl','custom_fields','custom_fields');
			$this->tpl->set_block('custom_fields_tpl','row','row');

			$n = 0;
			foreach($this->fields as $field => $data)
			{
				$data['order'] = ($n += 10);
				if (isset($this->stock_fields[$field]))
				{
					$this->set_row($data,$field);
				}
				else
				{
					$this->set_row($data,$field,'delete','Delete');
				}
			}
			$this->tpl->set_var(array(
				'hidden_vars'  => '',
				'lang_error'   => $error,
				'lang_name'    => lang('Name'),
				'lang_length'  => lang('Length<br />(<= 255)'),
				'lang_shown'   => lang('Length shown<br />(emtpy for full length)'),
				'lang_order'   => lang('Order'),
				'lang_title'   => lang('Title-row'),
				'lang_disabled'=> lang('Disabled'),
				'action_url'   => $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'calendar.uicustom_fields.submited')),
				'save_button'  => $this->html->submit_button('save','Save'),
				'cancel_button'=> $this->html->submit_button('cancel','Cancel'),
			));

			$this->set_row(array('order' => $n+10),'***new***','add','Add');

			$this->tpl->pfp('out','custom_fields');
		}

		function set_row($values,$id='',$name='',$label='')
		{
			if ($id !== '')
			{
				$id = '['.htmlspecialchars($id).']';
			}
			$this->tpl->set_var(array(
				'name'    => isset($values['label']) && $values['label'] ? lang($values['label']) : $this->html->input('name'.$id,(isset($values['name'])?$values['name']:''),'','SIZE="40" MAXLENGTH="40"'),
				'length'  => isset($values['label']) && $values['label'] ? '&nbsp' : $this->html->input('length'.$id,(isset($values['length'])?$values['length']:''),'','SIZE="3"'),
				'shown'   => isset($values['label']) && $values['label'] ? '&nbsp' : $this->html->input('shown'.$id,(isset($values['shown'])?$values['shown']:''),'','SIZE="3"'),
				'order'   => $this->html->input('order'.$id,$values['order'],'','SIZE="3"'),
				'title'   => $this->html->checkbox('title'.$id,(isset($values['title'])?$values['title']:'')),
				'disabled'=> $this->html->checkbox('disabled'.$id,(isset($values['disabled'])?$values['disabled']:'')),
				'button'  => $name ? $this->html->submit_button($name.$id,$label) : '&nbsp'
			));
			
			$this->classname = $GLOBALS['phpgw']->nextmatchs->alternate_row_class($this->classname);
			if ($name !== 'add')
			{
				$this->tpl->set_var('tr_color', $this->classname);
				$this->tpl->parse('rows','row',True);
			}
		}

		function submited()
		{
			if ( phpgw::get_var('cancel', 'bool', 'POST') )
			{
				$GLOBALS['phpgw']->redirect_link('/admin/');
			}
			//echo "<pre>"; print_r($_POST); echo "</pre>";

			$delete = phpgw::get_var('delete', 'bool', 'POST');
			$names = phpgw::get_var('name', 'string', 'POST');
			$length = phpgw::get_var('length', 'int', 'POST');
			$shown = phpgw::get_var('shown', 'bool', 'POST');
			$title = phpgw::get_var('title', 'bool', 'POST');
			$disabled = phpgw::get_var('disabled', 'bool', 'POST');
			$orders = phpgw::get_var('order', 'int', 'POST');

			foreach ( $orders as $field => $order)
			{
				if ( isset($delete[$field]) || $field == '***new***')
				{
					continue;
				}
				while(isset($ordered[$order]))
				{
					++$order;
				}
				$ordered[$order] = array
				(
					'field'     => $field,
					'name'      => isset($names[$field]) ? $names[$field] : '',
					'length'    => isset($length[$field]) ?  $length[$field] : 0,
					'shown'     => isset($shown[$field]) ? $shown[$field] : 0,
					'title'     => $title[$field],
					'disabled'  => $disabled[$field]
				);
				if (isset($this->stock_fields[$field]))
				{
					$ordered[$order]['name']  = $this->fields[$field]['name'];
					$ordered[$order]['label'] = $this->fields[$field]['label'];
				}
			}
			if ( phpgw::get_var('add', 'bool', 'POST') || strlen($names['***new***']))
			{
				$name = $names['***new***'];

				if (!strlen($name) || array_search($name, $names) != '***new***')
				{
					$error .= lang('New name must not exist and not be empty!!!');
				}
				else
				{
					$order = $orders['***new***'];
					while ( isset($orders[$order]) )
					{
						++$order;
					}
					$ordered[$order] = array(
						'field'     => '#'.$name,
						'name'      => $name,
						'length'    => $length['***new***'],
						'shown'     => $shown['***new***'],
						'title'     => $title['***new***'],
						'disabled'  => $disabled['***new***']
					);
				}
			}
			//echo "<pre>"; print_r($ordered); echo "</pre>\n";
			ksort($ordered,SORT_NUMERIC);

			$this->fields = array();
			foreach($ordered as $order => $data)
			{
				if ($data['length'] > 255)
				{
					$data['length'] = 255;
				}
				if ($data['length'] <= 0)
				{
					unset($data['length']);
				}
				if ($data['shown'] >= (isset($disabled[$field]) ? $disabled[$field] : false) || $data['shown'] <= 0)
				{
					unset($data['shown']);
				}
				if (!$data['title'])
				{
					unset($data['title']);
				}
				if (!$data['disabled'])
				{
					unset($data['disabled']);
				}
				$field = $data['field'];
				unset($data['field']);
				$this->fields[$field] = $data;
			}
			if ( (!isset($error) || !$error) 
				&& phpgw::get_var('save', 'bool', 'POST') )
			{
				$this->save();
				$GLOBALS['phpgw']->redirect_link('/admin/');
			}
			$this->index($error);
		}
	}
