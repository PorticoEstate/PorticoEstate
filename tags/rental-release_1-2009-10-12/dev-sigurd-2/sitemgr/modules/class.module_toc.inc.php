<?php

	class module_toc extends Module
	{
		function module_toc()
		{
			$this->arguments = array('category_id' => array('type' => 'textfield', 'label' => lang('The category to display, 0 for complete table of contents')));
			$this->title = lang('Table of contents');
			$this->description = lang('This module provides a complete table of contents, it is automatically used by the toc and category_id GET parameters');
		}

		function get_content(&$arguments,$properties)
		{
			global $objbo;
			global $page;
			$category_id = $arguments['category_id'];
			if ($category_id)
			{
				$cat = $objbo->getcatwrapper($category_id);
				if ($cat)
				{
					$page->title = lang('Category').' '.$cat->name;
					$page->subtitle = '<i>'.$cat->description.'</i>';
					$content = '<b><a href="'.sitemgr_link2('/index.php','toc=1').'">' . lang('Up to table of contents') . '</a></b>';
					if ($cat->depth > 1)
					{
						$content .= ' | <b><a href="'.sitemgr_link2('/index.php','category_id='.$cat->parent).'">' . lang('Up to parent') . '</a></b>';
					}
					$children = $objbo->getCatLinks((int) $category_id,false);
					if (count($children))
					{
						$content .= '<br><br><b>' . lang('Subcategories') . ':</b><br>';
						foreach ($children as $child)
						{
							$content .= '<br>&nbsp;&nbsp;&nbsp;&middot;&nbsp;<b>'.
								$child['link'].'</b> &ndash; '.$child['description'];
						}
					}
					$content .= '<br><br><b>' . lang('Pages') . ':</b><br>';
					$links = $objbo->getPageLinks($category_id,true);
					if (count($links)>0)
					{
						foreach($links as $pg)
						{
							$content .= "\n<br>".
								'&nbsp;&nbsp;&nbsp;&middot;&nbsp;'.$pg['link'];
							if (!empty($pg['subtitle']))
							{
								$content .= ' &ndash; <i>'.$pg['subtitle'].'</i>';
							}
							$content .= '';
						}
					}
					else
					{
						$content .= '<li>' . lang('There are no pages in this section') . '</li>';
					}
				}
				else
				{
					$content = lang('There was an error accessing the requested page. Either you do not have permission to view this page, or the page does not exist.');
				}
			}
			else
			{
				$content = '<b>' . lang('Choose a category') . ':</b><br>';
				$links = $objbo->getCatLinks();
				if (count($links)>0)
				{
					foreach($links as $cat)
					{
						$buffer = str_pad('', $cat['depth']*24,'&nbsp;').'&middot;&nbsp;';
						if (!$cat['depth'])
						{
							$buffer = '<br>'.$buffer;
						}
						$content .= "\n".$buffer.$cat['link'].' &mdash; <i>'.$cat['description'].
							'</i><br>';
					}
				}
				else
				{
					$content .= lang('There are no sections available to you.');
				}
			}
			return $content;
	}
}
?>
