<?php
	/**
	* EMail - Settings hook
	*
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package email
	* @subpackage hooks
	* @version $Id$
	*/
	
	list(,$acctnum) = explode('/',$_GET['prefix']);
	
	if ($acctnum)
	{
		create_check_box('enable this email account','ex_account_enabled', 'Have this account available');
	}
	create_input_box('Account Name','account_name',
		'This is the name that appears in the account combobox. If for leave this blank, your accounts will be given a standard name like Account[1]: Jane Doe, where Jane Doe is the name you give below as "Your full name". If you want to give an account a special name you can fill this in. No matter what, this is for your use, your emails will still use "Your full name" as your FROM name for email messages. Note that "Your full name" for your email account 0 is the name you gave in the phpgroupware setup.');
		
	if ($acctnum)
	{
		create_input_box('Your full name','fullname',
			'This is the name that appears in the users FROM address. The default mail account gets this value automatically from the phpgwapi. Additional accounts are created with the phpgwapi supplied fullname you can specify a different fullname for each extra email account.');
	}	
	create_text_area('email signature','email_sig',5,64,
		'This text will be appended to the bottom of the users emails for this email account. Currently, html tags are not supported. Also, forwarded email will NOT get this email sig appended to it for reasons such as it clutters the email, the forwarded part probably has its own email sig from the original author, which is really the information that matters the most.');
		
	$lang_oldest = lang('oldest');
	$lang_newest = lang('newest');
	$options = array(
		'old_new' => $lang_oldest.' -> '.$lang_newest,
		'new_old' => $lang_newest.' -> '.$lang_oldest
	);
	create_select_box('Default sorting order','default_sorting',$options,
		'In the email index page, the page which lists all the mails in a folder, mail may be sorted by date, author, size, or subject, HOWEVER all of these need to be ordered from first to last, this options controlls what is first and last. For example, if sorting by date, the newest to oldest displays the most recent emails first, in decending order down to the oldest emails last.');
	
	$options = array(
		'1' => lang('Layout 1'),
		'2' => lang('Layout 2')
	);
	create_select_box('Message List Layout','layout',$options,
		'The email application offers 2 different layouts for the index page, that is the page that lists the emails in a folder. This page may be the page the user looks at the most and so different layouts, or looks, are offered.');
		
	$options = array(
		'evo' => lang('Evolution Style'),
		'moz' => lang('Mozilla Modern Style'),
		'noia' => lang('Noia &#64; Carlitus Style')
	);
	create_select_box('Icon Theme','icon_theme',$options,
		'The email application offers different icon image themes, groups of images of a similar style which are used in this email application. Currently the available themes are images based on Evolution by Ximian and the Netscape6 / Mozilla browser buttons. Additional themes are anticipated and welcome.');
		
	$options = array(
		'16' => lang('Small'),
		'24' => lang('Big')
	);
	create_select_box('Icon Size','icon_size',$options,
		'The email application offers different icon image themes, these icons can be big or small.');
		
	$options = array(
		'text' => lang('Text'),
		'image' => lang('Image'),
		'both' => lang('Both')
	);
	create_select_box('Button Type','button_type',$options,
		'The email application offers different button displays, these buttons can be text, images, or both.');
		
	if (!$acctnum)
	{
		$options = array(
			'none' => lang('none'),
			'From' => lang('From'),
			'ReplyTo' => lang('ReplyTo')
		);
		create_select_box('Show sender\'s email address with name','show_addresses',$options,
			'This confusing and often misunderstood option is left over from this email apps origins as Aeromail by Mark Cushman. When viewing a list of emails in a folder, the FROM column may show you<br />a) the senders name only, if a name was provided,<br />b) the senders From email address, in addition to the senders name, or<br />c) the senders reply to address if it is different from the senders<br />from address, in addition to the senders name if it was provided. Typically users set this to none, which will show only the senders name. If no name was supplied by the sender, then the senders FROM email address will be shown, whether a seperate reply to address is provided has no effect on this, the FROM address is always used if the senders name is not provided.');
		
		create_check_box('show new messages on main screen','mainscreen_showmail',
			'Each user has a summary page which can display a variety of information. This option will show a small list of email messages in the INBOX of the users default email account onthe users summary home page.');
	}	
	create_check_box('Deleted messages go to Trash','use_trash_folder',
		'If checked, Deleted message will be sent to the &quot;Trash&quot; folder name which you specify in the box for &quot;Deleted messages (Trash) folder&quot; Only works with IMAP servers, POP servers do not have folders.');
			
	create_input_box('Deleted messages (Trash) folder','trash_folder_name',
		'If &quot;Deleted messages go to Trash&quot; is checked, Deleted message will be sent to the folder name you type in this box. If this folder does not exist, it will be created for you automatically. Default name is &quot;Trash&quot; This will be your &quot;Trash&quot; folder, but it does not have to actually be called &quot;Trash&quot;, you can name it anything. Only works with IMAP servers, POP servers do not have folders.');
			
	create_check_box('Sent messages saved in &quot;Sent&quot; folder','use_sent_folder',
		'If checked, a copy of your sent mail will be stored in the &quot;Sent&quot; folder name which you specify in the box for &quot;Sent messages folder&quot; Only works with IMAP servers, POP servers do not have folders.');
		
	create_input_box('Sent messages folder','sent_folder_name',
		'If &quot;Sent messages folder&quot; is checked, a copy of your sent mail will be stored in the folder name you type in this box. If this folder does not exist, it will be created for you automatically. Default name is &quot;Sent&quot; This will be your &quot;Sent&quot; folder, but it does not have to actually be called &quot;Sent&quot;, you can name it anything. Only works with IMAP servers, POP servers do not have folders.');
			
	create_check_box('enable UTF-7 encoded folder names','enable_utf7',
		'Most US and European users do not need to enable this. If this option is checked then your email server can handle folder names with non US-ASCII charactors in them / default is disabled, not checked. Only use if you are really sure you need it. Only works with IMAP servers, POP servers do not have folders.');
		
	create_check_box('Send forwarded mail as quoted attachment','fwd_inline_text',
		'Select this box if you want the text body of the message you are forwarding to appear inline in the body of your sent message.');
		
	$options = array(
		'orig'=> lang('Simple'),
		'lex' => lang('Javascript')
	);
	create_select_box('Select your style for the addressbook. The traditional, simple style. Or the new javascript enabled complex addressbook',
		'addressbook_choice',$options,'We have recently added this new addressbook so that users can choose to have a more complex addressbook that features<br />a) Easy, point and click searching,<br />b) Best suited for organizations with large central addressbooks with many categories.<br />You can choose here which addressbook do you prefer.');

	$options = array(
		'900' =>'1200x1600',
		'800' => '1024x768',
		'700' => '800x600'
	);
	create_select_box('Select your screensize for propper showing of the Javascript addressbook',
		'js_addressbook_screensize',$options,'We have three sizes that tell us how to better render the addressbook for you: 800x600 (addressbook will popout in a 700 pixel wide box), 1024x768 (it will be a 800 box), 1200x1600 (will be a 900 box). The fonts for all html stuff will be, respectively set to xx-small, x-small and normal (no font setting).');

	create_check_box('Show New Messages in ComboBox','newmsg_combobox',
		'This specifies whether or not to show the number of new message in the folders combo box on the index screen.');
	
	create_check_box('Show total folder size by default','show_foldersize',
		'This specifies whether or not to show the total size of folders by default. If this is not checked, you will be presented with a button allowing you to display folder size.');
    
	if ($acctnum)
	{
		show_list(lang('Extra E-Mail preferences Number %1',$acctnum));
	}
	else
	{
		show_list(lang('Standard E-Mail preferences'));
		
		create_check_box('Use custom settings','use_custom_settings',
			'Your server administrator will set the default values for the following options. You may never need to change any of them. If you do need to use settings that are different from the defaults for the options below here, then check this box. Default is disabled, not checked. If you fill in some of the options, but later decide to go back to the default values, unchecking this box will erase your custom values and put back the default values. All of the following options start out with the default value, so you may see some settings below even if you have never filled them in. This checkbox only shows up for the default email account. If you are setting up additional email accounts, you will be required to fill in the following options and this checkbox will not be displayed, it will be checked for all extra email accounts.');
	}	
	create_input_box('Email Account Name','userid',
		'The login name to use when checking mail for this email account. This may be the same as your phpGroupWare login name, or the server administrator may have set it for you. If your have multiple email accounts set up, you will need to fill this in. If you have only one email account set up, then you can probably leave this alone. If you clear this box, then it goes back to the default value. If you only need some custom settings but want this one to be the default value, then leave this box blank, the default value will be used, and you will see that default value in this box the next time you come to this preferences page.');

	create_password_box('Email Password','passwd',
		'The login name to use when checking mail for this email account. This may be the same as your phpGroupWare login name, or the server administrator may have set it for you. If your have multiple email accounts set up, you will need to fill this in. If you have only one email account set up, then you can probably leave this alone. If you do set a custom password, this box will be blank the next time you come to this settings page. This is a security feature because your custom email password is not sent to your browser after you set it. To change your custom password, simply enter a new password in the box. Exra email accounts require you to set this. For your default email account, you can clear your custom password by unchecking the &quot;Use Custom Settings&quot; option.');

	create_input_box('Email address','address',
		'Mail you send will use this address as the &quot;From&quot; address. This may be the same as your phpGroupWare login name or the server administrator may have set it for you. When the recipient clicks reply, this address will be used. You can leave this box blank and the default value will be used.');

	create_input_box('Mail Server','mail_server',
		'Name of the mail server you want to access. Should be a name like &quot;mail.example.com&quot;. If you leave this box blank then the default value will be used.',
		$GLOBALS["phpgw_info"]["server"]["mail_server"]);

	$options = array(
		'imap'	=> 'IMAP',
		'pop3'	=> 'POP-3',
		'imaps'	=> 'IMAPS',
		'pop3s'	=> 'POP-3S'
	);
	create_select_box('Mail Server type','mail_server_type',$options,'The type of mail server you want to access. IMAP mail servers have folders, such as the Sent and Trash folders. POP servers do not have folders. POP, POP-3, and POP3 are the same thing. You can have the server connection encrypted by using IMAPS or POPS, only if the mailserver supports it and if your phpGroupWare installation has a &quot;SSL&quot; capabable version of PHP.',
		$GLOBALS["phpgw_info"]["server"]["mail_server_type"]);

	$options = array(
		'Cyrus'		=> 'Cyrus '.lang('or').' Courier',
		'UWash'		=> 'UWash',
		'UW-Maildir'=> 'UW-Maildir'
	);
	create_select_box('IMAP Server Type - If Applicable','imap_server_type',$options,'If using an IMAP server, what kind is it, most often this option can safely be set to &quot;Cyrus or Courier&quot;. Technically, this means the server uses a dot between the different parts of the folder names, such as &quot;INBOX.Sent&quot;. The other major kind of IMAP server is the University of Washington &quot;UWash&quot; IMAP server. It uses slashes instead of the dots the other servers use, and although it has a folder called &quot;INBOX&quot;, it is not considered the &quot;Namespace&quot; for the other folder names. The &quot;UW-Maildir&quot; is a rare combination of the two above types. This is the least used kind of IMAP server. If you are unsure, ask your IT administrator. Only applies to IMAP servers.',
		$GLOBALS["phpgw_info"]["server"]["imap_server_type"]);

	create_input_box('U-Wash Mail Folder - If Applicable','mail_folder',
		'Only needed with the University of Washington &quot;UWash&quot; IMAP server. The default value is &quot;mail&quot; which means your mail folders, other then INBOX, are located in a directory called &quot;mail&quot; directly under your &quot;HOME&quot; directory. This box may be left empty, which means your mail folders are located in your &quot;HOME&quot; directory, not a subdirectory. If your mail folders are located in a subdirectory of &quot;HOME&quot; then put the name of that subdirectory here. Generally, it is not necessary to use any special slashes or tildes, &quot;HOME&quot; is always considered the base directory, and the slash bewteen &quot;HOME&quot; and the subdirectory will be added for you automatically, do not put the slash in this box.',
		$GLOBALS["phpgw_info"]["server"]["mail_folder"]);

	show_list(lang('Custom E-Mail preferences').($acctnum?' -- '.lang('(required)'):''));
