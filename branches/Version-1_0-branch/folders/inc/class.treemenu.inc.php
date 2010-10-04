<?php
/**
 * folders module
 *
 * @author Marco Pratesi <marco@telug.it>
 * @author Philipp Kamps <pkamps@probusiness.de>
 * @copyright Copyright (C) 2001-2003 Marco Pratesi
 * @copyright Copyright (C) 2003,2005 Free Software Foundation http://www.fsf.org/
 * @license http://www.fsf.org/licenses/gpl.html GNU General Public License
 * @package folders
 * @version $Id$
 */


// require_once('folders/inc/class.treemenu.inc.php');


/**
* phpgroupware tree menu
*
* @package folders
*/
class phpGWTreeMenu extends TreeMenu
{

	function phpGWTreeMenu()
	{
		$this->TreeMenu();
	}

/**
* The method to parse the current menu table and correspondingly update related variables
* @access public
* @param string $menu_name the name to be attributed to the menu
*   whose structure has to be parsed
* @param string $language i18n language; either omit it or pass
*   an empty string ("") if you do not want to use any i18n table
* @return void
*/
	function scanTableForMenu(
		$menu_name = '', // non consistent default...
		$language = '',
		$content
		) {
		$this->_maxLevel[$menu_name] = 0;
		$this->_firstLevelCnt[$menu_name] = 0;
		unset($this->tree[$this->_nodesCount+1]);
		$this->_firstItem[$menu_name] = $this->_nodesCount + 1;

/*
		for($i=0; $i < count($mailFolders); $i++)
		{
			$this->_tmpArray[$mailFolders[$i]['id']] = array ('parent_id' => $mailFolders[$i]['parent_id'],
																												'title'     => $mailFolders[$i]['title'],
																												'text'      => $mailFolders[$i]['name'],
																												'icon'      => $mailFolders[$i]['icon'],
																												'href'      => $mailFolders[$i]['href'],
																												'target'    => $mailFolders[$i]['target']
																											 );
		}

*/
		$this->_tmpArray = $content;

		$this->_depthFirstSearch($this->_tmpArray, $menu_name, '0', 1);

		$this->_lastItem[$menu_name] = count($this->tree);
		$this->_nodesCount = $this->_lastItem[$menu_name];
		$this->tree[$this->_lastItem[$menu_name]+1]["level"] = 0;
		$this->_postParse($menu_name);
	}
	
/**
* Method to prepare a new Tree Menu.
*
* This method processes items of a menu to prepare and return
* the corresponding Tree Menu code.
*
* @access public
* @param string $menu_name the name of the menu whose items have to be processed
* @return string
*/
	function newTreeMenu(
	                     $menu_name = ""	// non consistent default...
	                    ) {
	if (!isset($this->_firstItem[$menu_name]) || !isset($this->_lastItem[$menu_name])) {
		$this->error("newTreeMenu: the first/last item of the menu '$menu_name' is not defined; please check if you have parsed its menu data.");
		return 0;
	}

	$this->_treeMenu[$menu_name] = "";

	$img_space		= 'folders/phplayersmenu/images/tree_space.'.$this->treeMenuImagesType;
	$alt_space		= '  ';
	$img_vertline		= 'folders/phplayersmenu/images/tree_vertline.'.$this->treeMenuImagesType;
	$alt_vertline		= '| ';
	$img_expand		= 'folders/phplayersmenu/images/tree_expand.'.$this->treeMenuImagesType;
	$alt_expand		= '+-';
	$img_expand_first	= 'folders/phplayersmenu/images/tree_expand_first.'.$this->treeMenuImagesType;
	$alt_expand_first	= '+-';
	$img_expand_corner	= 'folders/phplayersmenu/images/tree_expand_corner.'.$this->treeMenuImagesType;
	$alt_expand_corner	= '+-';
	$img_collapse		= 'folders/phplayersmenu/images/tree_collapse.'.$this->treeMenuImagesType;
	$alt_collapse		= '--';
	$img_collapse_first	= 'folders/phplayersmenu/images/tree_collapse_first.'.$this->treeMenuImagesType;
	$alt_collapse_first	= '--';
	$img_collapse_corner	= 'folders/phplayersmenu/images/tree_collapse_corner.'.$this->treeMenuImagesType;
	$alt_collapse_corner	= '--';
	$img_split		= 'folders/phplayersmenu/images/tree_split.' . $this->treeMenuImagesType;
	$alt_split		= '|-';
	$img_split_first	= 'folders/phplayersmenu/images/tree_split_first.' . $this->treeMenuImagesType;
	$alt_split_first	= '|-';
	$img_corner		= 'folders/phplayersmenu/images/tree_corner.'.$this->treeMenuImagesType;
	$alt_corner		= '`-';
	$img_folder_closed	= 'folders/phplayersmenu/images/tree_folder_closed.' . $this->treeMenuImagesType;
	$alt_folder_closed	= '->';
	$img_folder_open	= 'folders/phplayersmenu/images/tree_folder_open.' . $this->treeMenuImagesType;
	$alt_folder_open	= '->';
	$img_leaf		= 'folders/phplayersmenu/images/tree_leaf.' . $this->treeMenuImagesType;
	$alt_leaf		= '->';

	for ($i=0; $i<=$this->_maxLevel[$menu_name]; $i++) {
		$levels[$i] = 0;
	}

	// Find last nodes of subtrees
	$last_level = $this->_maxLevel[$menu_name];
	for ($i=$this->_lastItem[$menu_name]; $i>=$this->_firstItem[$menu_name]; $i--) {
		if ($this->tree[$i]["level"] < $last_level) {
			for ($j=$this->tree[$i]["level"]+1; $j<=$this->_maxLevel[$menu_name]; $j++) {
				$levels[$j] = 0;
			}
		}
		if ($levels[$this->tree[$i]["level"]] == 0) {
			$levels[$this->tree[$i]["level"]] = 1;
			$this->tree[$i]["last_item"] = 1;
		} else {
			$this->tree[$i]["last_item"] = 0;
		}
		$last_level = $this->tree[$i]["level"];
	}

	$toggle = "";
	$toggle_function_name = "toggle" . $menu_name;

	for ($cnt=$this->_firstItem[$menu_name]; $cnt<=$this->_lastItem[$menu_name]; $cnt++) {
		$this->_treeMenu[$menu_name] .= "<div id=\"jt" . $cnt . "\" class=\"treemenudiv\">\n";

		// vertical lines from higher levels
		for ($i=0; $i<$this->tree[$cnt]["level"]-1; $i++) {
			if ($levels[$i] == 1) {
				$img = $img_vertline;
				$alt = $alt_vertline;
			} else {
				$img = $img_space;
				$alt = $alt_space;
			}
			$this->_treeMenu[$menu_name] .= "<img align=\"top\" border=\"0\" class=\"imgs\" src=\"" . $img . "\" alt=\"" . $alt . "\" />";
		}

		$not_a_leaf = $cnt<$this->_lastItem[$menu_name] && $this->tree[$cnt+1]["level"]>$this->tree[$cnt]["level"];

		if ($this->tree[$cnt]["last_item"] == 1) {
		// corner at end of subtree or t-split
			if ($not_a_leaf) {
				$this->_treeMenu[$menu_name] .= "<a onmousedown=\"". $toggle_function_name . "('" . $cnt . "')\"><img align=\"top\" border=\"0\" class=\"imgs\" id=\"jt" . $cnt . "node\" src=\"" . $img_collapse_corner . "\" alt=\"" . $alt_collapse_corner . "\" /></a>";
			} else {
				$this->_treeMenu[$menu_name] .= "<img align=\"top\" border=\"0\" class=\"imgs\" src=\"" . $img_corner . "\" alt=\"" . $alt_corner . "\" />";
			}
			$levels[$this->tree[$cnt]["level"]-1] = 0;
		} else {
			if ($not_a_leaf) {
				if ($cnt == $this->_firstItem[$menu_name]) {
					$img = $img_collapse_first;
					$alt = $alt_collapse_first;
				} else {
					$img = $img_collapse;
					$alt = $alt_collapse;
				}
				$this->_treeMenu[$menu_name] .= "<a onmousedown=\"". $toggle_function_name . "('" . $cnt . "');\"><img align=\"top\" border=\"0\" class=\"imgs\" id=\"jt" . $cnt . "node\" src=\"" . $img . "\" alt=\"" . $alt . "\" /></a>";
			} else {
				if ($cnt == $this->_firstItem[$menu_name]) {
					$img = $img_split_first;
					$alt = $alt_split_first;
				} else {
					$img = $img_split;
					$alt = $alt_split;
				}
				$this->_treeMenu[$menu_name] .= "<a onmousedown=\"". $toggle_function_name . "('" . $cnt . "');\"><img align=\"top\" border=\"0\" class=\"imgs\" id=\"jt" . $cnt . "node\" src=\"" . $img . "\" alt=\"" . $alt . "\" /></a>";
			}
			$levels[$this->tree[$cnt]["level"]-1] = 1;
		}

		if ($this->tree[$cnt]["parsed_href"] == "" || $this->tree[$cnt]["parsed_href"] == "#") {
			$a_href_open_img = "";
			$a_href_close_img = "";
			$a_href_open = "<a class=\"phplmnormal\">";
			$a_href_close = "</a>";
		} else {
			$a_href_open_img = "<a href=\"" . $this->tree[$cnt]["parsed_href"] . "\"" . $this->tree[$cnt]["parsed_title"] . $this->tree[$cnt]["parsed_target"] . ">";
			$a_href_close_img = "</a>";
			$a_href_open = "<a href=\"" . $this->tree[$cnt]["parsed_href"] . "\"" . $this->tree[$cnt]["parsed_title"] . $this->tree[$cnt]["parsed_target"] . " class=\"phplm\">";
			$a_href_close = "</a>";
		}

		if ($not_a_leaf) {
			$this->_treeMenu[$menu_name] .= $a_href_open_img . "<img align=\"top\" border=\"0\" class=\"imgs\" id=\"jt" . $cnt . "folder\" src=\"" . $img_folder_open . "\" alt=\"" . $alt_folder_open . "\" />" . $a_href_close_img;
		} else {
			if ($this->tree[$cnt]["parsed_icon"] != "") {
				$this->_treeMenu[$menu_name] .= $a_href_open_img . "<img align=\"top\" border=\"0\" src=\"" . $this->imgwww . $this->tree[$cnt]["parsed_icon"] . "\" width=\"" . $this->tree[$cnt]["iconwidth"] . "\" height=\"" . $this->tree[$cnt]["iconheight"] . "\" alt=\"" . $alt_leaf . "\" />" . $a_href_close_img;
			} else {
				$this->_treeMenu[$menu_name] .= $a_href_open_img . "<img align=\"top\" border=\"0\" class=\"imgs\" src=\"" . $img_leaf . "\" alt=\"" . $alt_leaf . "\" />" . $a_href_close_img;
			}
		}
		$this->_treeMenu[$menu_name] .= "&nbsp;" . $a_href_open . $this->tree[$cnt]["text"] . $a_href_close . "\n";
		$this->_treeMenu[$menu_name] .= "</div>\n";

		if ($cnt<$this->_lastItem[$menu_name] && $this->tree[$cnt]["level"]<$this->tree[$cnt+1]["level"]) {
			$this->_treeMenu[$menu_name] .= "<div id=\"jt" . $cnt . "son\" class=\"treemenudiv\">\n";
			if ($this->tree[$cnt]["expanded"] != 1) {
				$toggle .= "if (expand[" . $cnt . "] != 1) " . $toggle_function_name . "('" . $cnt . "');\n";
			} else {
				$toggle .= "if (collapse[" . $cnt . "] == 1) " . $toggle_function_name . "('" . $cnt . "');\n";
			}
		}

		if ($cnt>$this->_firstItem[$menu_name] && $this->tree[$cnt]["level"]>$this->tree[$cnt+1]["level"]) {
			for ($i=max(1, $this->tree[$cnt+1]["level"]); $i<$this->tree[$cnt]["level"]; $i++) {
				$this->_treeMenu[$menu_name] .= "</div>\n";
			}
		}
	}

/*
	$this->_treeMenu[$menu_name] =
	"<div class=\"phplmnormal\">\n" .
	$this->_treeMenu[$menu_name] .
	"</div>\n";
*/
	// Some (old) browsers do not support the "white-space: nowrap;" CSS property...
	$this->_treeMenu[$menu_name] =
	"<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n" .
	"<tr>\n" .
	"<td class=\"phplmnormal\" nowrap=\"nowrap\">\n" .
	$this->_treeMenu[$menu_name] .
	"</td>\n" .
	"</tr>\n" .
	"</table>\n";

	$t = new Template_PHPLIB();
	$t->setFile("tplfile", $this->libjsdir . "layerstreemenu.ijs");
	$t->setVar(array(
		"toggle_function_name"	=> $toggle_function_name,
		"img_expand"		=> $img_expand,
		"img_expand_first"	=> $img_expand_first,
		"img_collapse"		=> $img_collapse,
		"img_collapse_first"	=> $img_collapse_first,
		"img_collapse_corner"	=> $img_collapse_corner,
		"img_folder_open"	=> $img_folder_open,
		"img_expand_corner"	=> $img_expand_corner,
		"img_folder_closed"	=> $img_folder_closed
	));
	$toggle_function = $t->parse("out", "tplfile");
	$toggle_function =
	"<script language=\"JavaScript\" type=\"text/javascript\">\n" .
	"<!--\n" .
	$toggle_function .
	"// -->\n" .
	"</script>\n";

	$toggle =
	"<script language=\"JavaScript\" type=\"text/javascript\">\n" .
	"<!--\n" .
	"if ((DOM && !Opera56 && !Konqueror22) || IE4) {\n" .
	$toggle .
	"}\n" .
	"if (NS4) alert('Only the accessibility is provided to Netscape 4 on the JavaScript Tree Menu.\\nWe *strongly* suggest you to upgrade your browser.');\n" .
	"// -->\n" .
	"</script>\n";

	$this->_treeMenu[$menu_name] = $toggle_function . "\n" . $this->_treeMenu[$menu_name] . "\n" . $toggle;

	return $this->_treeMenu[$menu_name];
  }	
	
}
?>
