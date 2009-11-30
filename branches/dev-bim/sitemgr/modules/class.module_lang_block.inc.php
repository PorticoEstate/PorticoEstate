<?php

	class module_lang_block extends Module
	{
		function module_lang_block()
		{
			$this->arguments = array();
			$this->properties = array();
			$this->title = lang('Choose language');
			$this->description = lang('This module lets users choose language');
		}
	
		function get_content(&$arguments,$properties)
		{
			if ($GLOBALS['sitemgr_info']['sitelanguages'])
			{
				$content = '<form name="langselect" method="post">';
				$content .= '<select onChange="location.href=this.value" name="language">';
				$content .= '<option value="">' . lang('please select') . '</option>';
				foreach ($GLOBALS['sitemgr_info']['sitelanguages'] as $lang)
				{
					$selected='';
					if ($lang == $GLOBALS['sitemgr_info']['userlang'])
					{
						$selected = 'selected="selected" ';
					}
					$content .= '<option ' . $selected . 'value="' . $this->link(array('select'=>$lang)) . '&lang='.$lang.'">'. $GLOBALS['Common_BO']->getlangname($lang) . '</option>';
				}
				$content .= '</select>';
				$content .= '</form>';

				return $content;
			}
			else
			{
				$content = lang('No sitelanguages configured');
			}
		}
	}
