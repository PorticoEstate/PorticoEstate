<?php 

class module_guestbook extends Module
{
	function module_guestbook()
	{
		$this->i18n = False;
		$this->post = array(
			'name' => array('type' => 'textfield'),
			'comment' => array('type' => 'textarea'),
			'save' => array('type' => 'submit', 'value' => lang('Save'))
		);
		$this->arguments = array(
			'book' => array(
				'type' => 'select', 
				'label' => lang('Choose a guestbook'), 
				'options' => array(),
			),
		);
		$this->properties = array('allownew' => array('type' => 'checkbox', 'label' => lang('Are contributors allowed to define new guestbooks?')));
		$this->bo = createobject('sitemgr_module_guestbook.guestbook_BO');
	}

	function validate(&$data)
	{
		if ($data['new'])
		{
			$book_id = $this->bo->create_book($data['new']);
			$data['book'] = $book_id;
		}
		unset($data['new']);
		return true;
	}

	function get_user_interface()
	{
		if (!$this->bo)
		{
			return array(array('label' => lang('Application sitemgr_module_guestbook must be installed as a phpgroupware application for this module to run')));
		}
		$properties = $this->get_properties();
		$interface = array();
		$book_ids = array();

		$books = $this->bo->get_books();
		while (list($id,$title) = @each($books))
		{

			$book_ids[$id] = $title;
		}
		$this->arguments['book']['options'] = $book_ids;
		if ($properties['allownew'])
		{
			$this->arguments['new'] = array(
				'type' => 'textfield',
				'label' => lang('or enter the title for a new guestbook'),
			);
		}
		return parent::get_user_interface();
	}

	function get_admin_interface()
	{
		if (!$this->bo)
		{
			return array(array('label' => lang('Application sitemgr_module_guestbook must be installed as a phpgroupware application for this module to run')));
		}

		$books = $this->bo->get_books();
		while (list($id,$title) = @each($books))
		{
			$element['label'] = '<hr>';
			$element['form'] = '<hr>';
			$interface[] = $element;
			$element['label'] = "<b>Guestbook #$id</b>";
			$element['form'] = '';
			$interface[] = $element;
			$elementname = 'element[title][' . $id . ']';
			$element['label'] = lang('Name');
			$element['form'] = $this->build_input_element(array('type' => 'textfield'),$title,$elementname);
			$interface[] = $element;
			$element['label'] = lang('Delete this guestbook');
			$element['form'] = $this->build_input_element(
				array('type' => 'checkbox'),
				False,
				'element[delete][' . $id . ']'
			);
			$interface[] = $element;
		}
		$element['label'] = '<hr>';
		$element['form'] = '<hr>';
		$interface[] = $element;
		$element['label'] = lang('Add a new guestbook');
		$element['form'] = $this->build_input_element(
			array('type' => 'checkbox'),
			False,
			'element[addnew]'
		);
		$interface[] = $element;
		return array_merge($interface,parent::get_admin_interface());
	}

	function validate_properties(&$data)
	{
		while (list($id,$title) = @each($data['title']))
		{
			if ($data['delete'][$id])
			{
				$this->bo->delete_book($id);
			}
			else
			{
				$this->bo->save_book($id,$title);
			}
		}
		if ($data['addnew'])
		{
			$this->bo->create_book(lang('New guestbook'));
		}
		return true;
	}

	function get_content(&$arguments,$properties)
	{
		if (!$this->bo)
		{
			return lang('Application sitemgr_module_guestbook must be installed as a phpgroupware application for this module to run');
		}

		if ($arguments['save'])
		{
			$this->bo->add_entry($arguments['name'],$arguments['comment'],$arguments['book']);
		}

		$this->template = Createobject('phpgwapi.Template');
		$this->template->set_root($this->find_template_dir());
		$this->template->set_file('form','form.tpl');
		$this->template->set_block('form','entry','Eblock');
		$this->template->set_var(array(
			'lang_sign' => lang('Please sign the guestbook'),
			'inputname' => $this->build_post_element('name',lang('Your name')),
			'inputcomment' => $this->build_post_element('comment',lang('Your comment')),
			'btnsave' => $this->build_post_element('save'),
			'lang_lastentries' => lang('Last entries to the guestbook')
		));
		$entries = $this->bo->get_entries($arguments['book']);
 		while (list(,$entry) = @each($entries))
 		{
 			$this->template->set_var(array(
				'name' => $entry['name'],
				'comment' => nl2br($entry['comment']),
				'timestamp' => date('r',$entry['timestamp'])
			));
			$this->template->parse('Eblock','entry',true);
 		}
		return $this->template->parse('out','form');
	}
}