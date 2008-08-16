<?php
	/**
	* Preferences - user manual
	*
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package preferences
	* @subpackage manual
	* @version $Id$
	*/
	
	$phpgw_flags = Array(
		'currentapp'	=> 'manual',
		'enable_utilities_class'	=> True
	);
	$phpgw_info['flags'] = $phpgw_flags;
	
	/**
	 * Include phpgroupware header
	 */
	include('../../../header.inc.php');
?>
<img src="<?php echo $phpgw->common->image('preferences','navbar.gif'); ?>" border="0" /> 
<font face="<?php echo $phpgw_info['theme']['font']; ?>" size="2">
<p>The place to make changes to your personal piece of groupware.</p>
<ul>
<li><b>Change your Password:</b><br />
Used for changing your login password to the system.
You will be presented with two text boxes, enter your new password, 
then click on the change button.</li>
<li><b>Select different Theme:</b><br />
To change the look of the pages you see, within the system. Displayed is
your current theme (for new users set to default). Simply click on the
theme of your choice. Your pages will change immediately.<br />
<li><b>Change your profile:</b><br />
Here you can set a few details about yourself, for public viewing by the
rest of the users of the system. Title,Phone number, Comments, Picture upload.</li>
<li><b>Monitor Newsgroups:</b><br />
Easily choose which news groups you want to set for reading.</li>
<?php
	$phpgw->common->phpgw_footer();
?>
