<?php

class module_bookmarks extends Module 
{
	var $template;
	var $startlevel;

	function module_bookmarks()
	{
		$this->title = lang('Bookmarks');
		$this->description = lang('This module displays bookmarks in a javascript based tree');
		$this->cookie = array('expanded');
		$this->arguments = array(
			'category' => array(
				'type' => 'select', 
				'label' => lang('Choose the categories to display'), 
				'options' => array(),
				'multiple' => True
			)
		);
	}

	function get_user_interface()
	{
		$cat = createobject('phpgwapi.categories','','bookmarks');
		$cats = $cat->return_array('all',0,False,'','cat_name','',True);
		$cat_ids = array();
		while (list(,$category) = @each($cats))
		{
			$cat_ids[$category['id']] = $GLOBALS['phpgw']->strip_html($category['name']);
		}
		$this->arguments['category']['options'] = $cat_ids;
		return parent::get_user_interface();
	}

	function set_block(&$block,$produce=False)
	{
		parent::set_block($block,$produce);

		if ($produce)
		{
			require_once(PHPGW_INCLUDE_ROOT . SEP . 'sitemgr' . SEP . 'inc' . SEP . 'class.xslt_transform.inc.php');
			$this->add_transformer(new xslt_transform(
				$this->find_template_dir() . SEP . 'xbel.xsl',
				array('blockid' => $this->block->id)
			));
		}
	}

	function get_content(&$arguments,$properties)
	{
		if ($arguments['expanded'])
		{
			$expandedcats = array_keys($arguments['expanded']);
		}
		else
		{
			$expandedcats = Array();
		}
		$bo = createobject('bookmarks.bo');
		return $bo->export($arguments['category'],'xbel',$expandedcats);
	}
}
