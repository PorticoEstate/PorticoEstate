<?php 

class module_currentsection extends Module
{
	function module_currentsection()
	{
		$this->arguments = array();
		$this->properties = array();
		$this->title = lang('Current Section');
		$this->description = lang('This block displays the current section\'s table of contents');
	}

	function get_content(&$arguments,$properties)
	{
		global $page;
		if ($page->cat_id == $GLOBALS['Common_BO']->current_site['site_id'])
		{
			return '';
		}

		$catlinks = $GLOBALS['objbo']->getCatLinks((int)$page->cat_id,false);
		$pagelinks = $GLOBALS['objbo']->getPageLinks($page->cat_id,false);
		$category = $GLOBALS['objbo']->getcatwrapper($page->cat_id);
		$this->block->title = $category->name;
		$parent = $category->parent;
		unset($category);

		$content = '';
		if ($parent)
		{
			$parentcat = $GLOBALS['objbo']->getcatwrapper($parent);
			$content .= "\n".'<b>Parent Section:</b><br>&nbsp;&middot;&nbsp;<a href="'.
				sitemgr_link2('/index.php','category_id='.$parent).'">'.$parentcat->name.
				'</a><br><br>';
			unset($parentcat);
		}
		if (count($catlinks))
		{
			$content .= "\n".'<b>Subsections:</b><br>';
			foreach ($catlinks as $catlink)
			{
				$content .= "\n".'&nbsp;&middot;&nbsp;'.$catlink['link'].'<br>';
			}
			$content .= '<br>';
		}
		if (count($pagelinks)>1 || (count($pagelinks)>0 && $content))
		{
			$content .= "\n".'<b>Pages:</b>';
			$content .= ' (<a href="'.sitemgr_link2('/index.php','category_id='.$page->cat_id).
				'"><i>show all</i></a>)<br>';
			reset($pagelinks);
			while(list($pagelink_id,$pagelink) = each($pagelinks))
			{
				if ($page->page_id && $page->page_id == $pagelink_id)
				{
					$content .= '&nbsp;&gt;'.$pagelink['link'].'&lt;<br>';
				}
				else
				{
					$content .= '&nbsp;&middot;&nbsp;'.$pagelink['link'].'<br>';
				}
			}
		}
		return $content;
	}
}
