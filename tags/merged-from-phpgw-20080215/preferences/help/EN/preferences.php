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
		'currentapp'	=> 'manual'
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
<li><b>Change your Password:</b>
<br>Used for changing your login password to the system.
You will be presented with two text boxes, enter your new password, 
then click on the change button.</li>
<li><b>Select different Theme:</b><br />
To change the look of the pages you see, within the system. Displayed is
your current theme (for new users set to default). Simply click on the
theme of your choice. Your pages will change immediately.</li>
<li><b>Change your Settings:</b><br />
<i>Max matches per page:</i><br />
Choose how many instances of items will be displayed on your screen at one time, default is 15.<br />
<i>Show text on navigation icons:</i><br />
Add text under the navigation icons at the top of the screen.<br />
<i>Time zone offset:</i><br />
Set your time zone, default setting is Central Europian Standard CEST.<br />
<i>Date format:</i><br />
Choose the order of day/month/year, default is m/d/y.<br />
<i>Time format:</i><br />
Choose from am/pm=12hours or 24hours settings.<br />
<i>Language:</i><br />
Set your language, options German,Spanish,Norwegen,Italian,French, default is English.<br />
<i>Show birthday reminders on main screen:</i><br />
Selecting this option, enables birthday reminders to be shown on the day as
an alert. When you log on to the system on the day a birthday is entered, an alert
will be displayed on the home screen. Birthday settings are made in the address book.<br />
<i>Show high priority events on main screen:</i><br />
Selecting this option, enables reminders for priority tasks assigned to you in either
todo list or ticketing system.<br />
<i>Weekday starts on:</i><br />
<i>Choose the day your week starts.</i><br />
<i>Workday starts on:</i><br />
Choose the start time of your work day. This will effect the beginning time slot
in the calendar when displaying the days schedule.<br />
<i>Workday ends on:</i><br />
Choose the end time of your work day. This will effect the ending time slot 
in the calendar when displaying the days schedule.<br />
<i>Select Headline News sites:</i><br />
Click on as many news headline news sites as you wish, these will be displayed for you
when using the headlines function. The systems admin set these as default, so be sure to
let them know if you want some that are not there ;)</li><br />
<li><b>Change your profile:</b><br />
Here you can set a few details about yourself, for public viewing by the rest of the users
of the system. Title,Phone number, Comments, Picture upload.</li><br />
<li><b>Monitor Newsgroups:</b><br />
Easily choose which news groups you want to set for reading.</li>
</ul>
</font>
<?php
	$phpgw->common->phpgw_footer();
?>
