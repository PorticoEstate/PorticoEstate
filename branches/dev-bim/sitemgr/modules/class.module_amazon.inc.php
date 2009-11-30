<?php 
/**************************************************************\
* this was originally a phpNuke block.  the amazon_id has been *
* changed.  feel free to change it yourself, of course.  the   *
* below directions still apply.                                *
\**************************************************************/

/***************************************************************/
/* To use this block you only need to download .jpg or .gif    */
/* images from amazon.com and copy them to the /images/amazon  */
/* directory, then edit the $amazon_id variable to fit your ID */
/* of the Associates program. If you don't change the ID, all  */
/* the comissions ($) will go to my account! You're advised.   */
/*                                                             */
/* You need to know that any image in the amazon's directory   */
/* has the same ASIN name as its filename given by Amazon. If  */
/* you don't know what this is, leave it as is or disable it.  */
/***************************************************************/
class module_amazon extends Module
{
	function module_amazon()
	{
		$this->arguments = array();
		$this->properties = array();
		$this->title = lang('Amazon');
		$this->description = lang('Use this module for displaying book ads for the amazon web site');
	}

	function get_content(&$arguments,$properties)
	{
		$amazon_id = "phpgwsitemgr-20";

		mt_srand((double)microtime()*1000000);
		$imgs = dir('images/amazon');
		while ($file = $imgs->read()) {
			if (eregi("gif", $file) || eregi("jpg", $file)) {
			$imglist .= "$file ";
			}
		}
		closedir($imgs->handle);
		$imglist = explode(" ", $imglist);
		$a = sizeof($imglist)-2;
		$random = mt_rand(0, $a);
		$image = $imglist[$random];
		$asin = explode(".", $image);
		return "<br><center><a href=\"http://www.amazon.com/exec/obidos/ASIN/$asin[0]/$amazon_id\" target=\"_blank\">" .
			"<img src=\"images/amazon/$image\" border=\"0\" alt=\"\"><br><br></center>";
	}
}
