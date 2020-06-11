

# PorticoEstate

## PorticoEstate Application Development

This document explains PorticoEstate's infrastructure and API, along
with what is required to integrate applications into it.

## Introduction
--------------------------------------------------------------------

PorticoEstate is a web based groupware application framework (API), for
writing applications. Integrated applications such as email, calendar,
helpdesk, address book, rental management and facilities management is included.

### Overview of application writing

We have attempted to make writing application for PorticoEstate as
painless as possible. We hope any pain and suffering is cause by making
your application work, but not dealing with PorticoEstate itself.

### What does the PorticoEstate API provide?

The PorticoEstate API handles session management, user/group management,
has support for multiple databases, using the PHPLIB database
abstraction method, we support templates using the PHPLIB Templates
class, a file system interface, and even a network i/o
interface.

On top of these standard functions, PorticoEstate provides several
functions to give you the information you need about the users
environment, and to properly plug into
PorticoEstate.


### Requirements

These guidelines must be followed for any application that wants
considered for inclusion into the PorticoEstate distribution:

-   It must run on PHP 7.2 or later
-   SQL statements must be compatible with both MySQL, PostgreSQL, MSSQL Server and SAP-DB
-   It must use our default header.inc.php include.
-   It must use our $GLOBALS['phpgw']->link($url) for all links (this is for session support).
-   It must use "POST" for form submit methods.
-   It must respect phpGW group rights and phpGW user permissions.
-   It must use our directory structure, template support and lang (multi-language) support.
-   Where possible it should run on both Unix and NT platforms.


### Writing/porting your application

#### Include files

Each PHP page you write will need to include the header.inc.php along
with a few variables.
This is done by putting this at the top of each PHP
page.

```
<?php
	$phpgw_info["flags"]["currentapp"] = "appname";
	include("../header.inc.php");

```

Of course change application name to fit.
This include will provide the following things:

-   The phpgwAPI - The PorticoEstate API will be   loaded.
-   The phpGW navbar will be loaded (by default, but can be disabled
    until a later point.
-   appname/inc/functions.inc.php - This file is loaded just after the
    phpgwAPI and before any HTML code is generated. This file should
    include all your application specific functions.. You are welcome to
    include any additional files you need from within this file.
    Note:
    Depricated and not used for OOP
    (/index.php?menuaction=app.obj.method)
    calls.
-   appname/inc/header.inc.php - This file is loaded just after the
    system header/navbar, and allows developers to use it for whatever
    they need to load.
    Note:
    Depricated and not used for OOP
    (/index.php?menuaction=app.obj.method)
    calls.
-   appname/inc/footer.inc.php - This file is loaded just before the
    system footer, allowing developers to close connections and whatever
    else they need.
    Note:
    Depricated and not used for OOP
    (/index.php?menuaction=app.obj.method)
    calls.]{style="font-family:'Times New Roman'"}
-   The phpGW footer will be loaded, which closes several
    connections.

## Installing your application
-----------------------------------------------------------------------------------

### Overview

It is fairly simple to add and delete applications to/from
PorticoEstate.

### Automatic features

To make things easy for developers we go ahead and load the following
files.

-   appname/inc/functions.inc.php - This file should include all your
    application specific
    functions.
    Note:
    Depricated and not used for OOP
    (/index.php?menuaction=app.obj.method)
    calls.
-   appname/inc/header.inc.php - This file is loaded by
    \$phpgw-&gt;common-&gt;header just after the system header/navbar,
    and allows developers to use it for whatever they need to
    load.
    Note:
    Depricated and not used for OOP
    (/index.php?menuaction=app.obj.method)
    calls.
-   appname/inc/footer.inc.php - This file is loaded by
    \$phpgw-&gt;common-&gt;footer just before the system footer,
    allowing developers to close connections and whatever else they
    need.
    Note:
    Depricated and not used for OOP
    (/index.php?menuaction=app.obj.method)
    calls.

### Adding files, directories and icons.

You will need to create the following directories for your code
(replace ’appname’ with your application name)

```

--appname
  |
  +--inc
  |  |
  |  +--functions.inc.php
  |  |
  |  +--header.inc.php
  |  |
  |  +--hook_preferences.inc.php
  |  |
  |  +--hook_admin.inc.php
  |  |
  |  +--footer.inc.php
  |
  +--templates
     |
     +--base
```

### Making PorticoEstate aware of your application

To make the application aware of your application, add your application
details to the applications table. This can be done via the GUI
administration screen, or via a SQL script. The script below should only
be used during initial development. You should use the PorticoEstate
setup system for install and updating the final version of your
application.

```
	insert into applications (app_name, app_title, app_enabled) values ('appname', 'The App name', 1);
```

### Hooking into Administration page

When a user goes to the Administration page, it starts
appname/inc/hook\_admin.inc.php for each application that is enabled, in
alphabetical order of application title. If the file exists, it is
include()d in the hopes it will display a selection of links to
configure that application.

Simple Example:

```
<?php
	// Old linear script style
	$file['Site Configuration'] = $GLOBALS['phpgw']->link('myapp/myAdminPage.php');
	// OR - OOP Style
	$file['Site Configuration'] = $GLOBALS['phpgw']->link('/index.php',
	                                    array(menuaction => 'myapp.uiobj.admin_method');
	display_section('myapp',$file);
?>
```

Look at headlines/inc/hook\_admin.inc.php and
admin/inc/hook\_admin.inc.php for more
examples.

Things to note:

-   Links are relative to the admin/index.php file, not your
    application's base directory. (so use "appname" in your link()
    calls)
-   The file is brought in with include() so be careful to not pollute
    the name-space too much

The standard $GLOBALS\['phpgw'\] and $GLOBALS\['phpgw\_info'\]
variables are in-scope, as is \$appname which corresponds to the
application name in the path.

### Hooking into Preferences page

The mechanism to hook into the preferences page is identical to the one
used to hook into the administration page, however it looks for
appname/inc/hook\_preferences.inc.php instead of
appname/inc/hook\_admin.inc.php. The same functions and variables are
defined.

## infrastructure
--------------------------------------------------------------------------

### overview 

PorticoEstate attempts to provide developers with a sound directory
structure to work from.
The directory layout may seem complex at first, but after some use, you
will see that it is designed to accommodate a large number of
applications and functions.

### directory-tree

```
--portico
  |
  +--admin
  |
  +--docs (installation docs)
  |
  +--files (Note: must be out of webserver document root!)
  |   |
  |   +--groups
  |   |
  |   +--homes
  |   |
  |   +--users
  |
  +--phpgwapi
  |   |
  |   +--cron (phpgroupware's optional daemons)
  |   |
  |   +--doc (developers docs)
  |   |
  |   +--inc
  |   |   |
  |   |   +--class.phpgw.inc.php
  |   |   |
  |   |   +--phpgw_info.inc.php
  |   |   |
  |   |   +--class.common.inc.php
  |   |   |
  |   |   +--etc..
  |   |
  |   +--js (javascript)
  |   |   |
  |   |   +--base
  |   |   |
  |   |   +--js_package_name
  |   |
  |   +--manual
  |   |
  |   +--setup
  |   |   |
  |   |   +--baseline.inc.php
  |   |   |
  |   |   +--default_records.inc.php
  |   |   |
  |   |   +--tables_current.inc.php
  |   |   |
  |   |   +--tables_update.inc.php
  |   |
  |   +--templates
  |   |   |
  |   |   +--default
  |   |   |   |
  |   |   |   +--images
  |   |   |
  |   |   +--verilak
  |   |       |
  |   |       +--images
  |   |
  |   +--themes
  |       |
  |       +--default.theme
  |
  +--preferences
  |
  +--setup

```

### Translations

See [section 7]where this is explained in
detail.

## The API
---------------------------------------------------------------

### Introduction

PorticoEstate attempts to provide developers with a useful API to handle
common tasks.

To do this we have created a multi-dimensional class
$GLOBALS\['phpgw'\]-&gt;.

This allows for terrific code organization, and help developers easily
identify the file that the function is in. All the files that are part
of this class are in the inc/core directory and are named to match the
sub-class.

Example: 
$phpgw-&gt;send-&gt;msg() is in the
inc/phpgwapi/class.send.inc.php
file.

### Basic functions

#### $GLOBALS\['phpgw'\]->link

```
	$GLOBALS['phpgw']->link($url,$args);
```
	
Add support for session management. ALL links must use this, that
includes href's form actions and header
location's.

If you are just doing a form action back to the same page, you can use
it without any parameters.

This function is right at the core of the class because it is used so
often, we wanted to save developers a few keystrokes.
Example:
```

	<form name=copy method=post action="<?php echo $GLOBALS['phpgw']->link();?>">
	/* If session management is done via passing url parameters */
	/* The the result would be */
	/* <form name=copy method=post action="somepage.php?sessionid=87687693276?kp3=kjh98u80"> */
```

### Application Functions

#### $GLOBALS\['phpgw'\]-&gt;common-&gt;phpgw\_header
```
	$GLOBALS['phpgw']->phpgw_header();
```
Print out the start of the HTML page, including the navigation bar and
includes appname/inc/header.php, if using deprecated linear scripts
style.

#### $GLOBALS\['phpgw'\]-&gt;common-&gt;phpgw\_footer
```
	$GLOBALS['phpgw']->phpgw_footer();
```
Prints the system footer, and includes
appname/inc/footer.php

#### $GLOBALS\['phpgw'\]-&gt;common-&gt;appsession

$GLOBALS\['phpgw'\]-&gt;common-&gt;appsession(\$data)
Store important information session information that your application
needs.
$GLOBALS\['phpgw'\]-&gt;appsession will return the value of your
session data is you leave the parameter empty \[i.e.
$GLOBALS\['phpgw'\]-&gt;appsession()\], otherwise it will store
whatever data you send to it.
You can also store a comma delimited string and use explode() to turn
it back into an array when you receive the value
back.

Example:
```
	$GLOBALS['phpgw']->common->appsession("/path/to/something");
	echo "Dir: " . $GLOBALS'phpgw'->common->appsession();
```

### File functions

[See ][[Virtual File System (VFS)Developers Guide

for more info.

### Email/NNTP Functions

#### $phpgw->send->msg 

	$phpgw->msg->send($service, $to, $subject, $body, $msgtype,$cc, $bcc)
Send a message via email or NNTP and returns any error
codes.
Example:
```
      $to = 'someuser@domain.com';
      $subject = 'Hello buddy';
      $body = "Give me a call\n Been wondering what your up to.";
      $errors = $GLOBALS['phpgw']->msg->send('email', $to, $subject, $body);
```
      
## configuration-variables
-------------------------------------------------------------------------------

### Introduction

PorticoEstate attempts to provide developers with as much information
about the user, group, server, and application configuration as
possible.

To do this we provide a multi-dimensional array called
$GLOBALS\['phpgw\_info'\]", which includes all the information about
your environment.

Due to the multi-dimensional array approach. getting these values is
easy.

Here are some examples:
```
	<?php
		// To do a hello username
		echo "Hello " . $GLOBALS['phpgw_info']['user']['fullname'];
		//If username first name is John and last name is Doe, prints: 'Hello John Doe'
	?>

	<?php
		// To find out the location of the imap server
		echo 'IMAP Server is named: ' . $GLOBALS['phpgw_info']['server']['imap_server']; 
		//If imap is running on localhost, prints: 'IMAP Server is named: localhost'
	?>
```

### User information

$GLOBALS['phpgw_info']['user']['userid'] = The user ID.

$GLOBALS['phpgw_info']['user']['sessionid'] = The session ID

$GLOBALS['phpgw_info']['user']['theme'] = Selected theme

$GLOBALS['phpgw_info']['user']['private_dir'] = Users private dir. 
	Use phpGroupWare core functions for access to the files.

$GLOBALS['phpgw_info']['user']['firstname'] = Users first name

$GLOBALS['phpgw_info']['user']['lastname'] = Users last name

$GLOBALS['phpgw_info']['user']['fullname'] = Users Full Name

$GLOBALS['phpgw_info']['user']['groups'] = Groups the user is a member of

$GLOBALS['phpgw_info']['user']['app_perms'] = If the user has access to the current application

$GLOBALS['phpgw_info']['user']['lastlogin'] = Last time the user logged in.

$GLOBALS['phpgw_info']['user']['lastloginfrom'] = Where they logged in from the last time.

$GLOBALS['phpgw_info']['user']['lastpasswd_change'] = Last time they changed their password.

$GLOBALS['phpgw_info']['user']['passwd'] = Hashed password.

$GLOBALS['phpgw_info']['user']['status'] = If the user is enabled.

$GLOBALS['phpgw_info']['user']['logintime'] = Time they logged into their current session.

$GLOBALS['phpgw_info']['user']['session_dla'] = Last time they did anything in their current session

$GLOBALS['phpgw_info']['user']['session_ip'] = Current IP address

### Group information

$GLOBALS['phpgw_info']['group']['group_names'] = List of groups.

### Server information

$GLOBALS['phpgw_info']['server']['server_root'] = Main installation directory

$GLOBALS['phpgw_info']['server']['include_root'] = Location of the 'inc' directory.

$GLOBALS['phpgw_info']['server']['temp_dir'] = Directory that can be used for temporarily storing files

$GLOBALS['phpgw_info']['server']['files_dir'] = Directory user and group files are stored

$GLOBALS['phpgw_info']['server'']['common_include_dir'] = Location of the core/shared include files.

$GLOBALS['phpgw_info']['server']['template_dir'] = Active template files directory. 
	This is defaulted by the server, and changeable by the user. 

$GLOBALS['phpgw_info']['server']['encrpytkey'] = Key used for encryption functions

$GLOBALS['phpgw_info']['server']['site_title'] = Site Title will show in the title bar of each webpage.

$GLOBALS['phpgw_info']['server']['webserver_url'] = URL to phpGroupWare installation.

$GLOBALS['phpgw_info']['server']['hostname'] = Name of the server phpGroupWare is installed upon.

$GLOBALS['phpgw_info']['server']['charset'] = user's charset, default:iso-8859-1

$GLOBALS['phpgw_info']['server']['version'] = phpGroupWare version.


### Database information

It is unlikely you will need these, because $GLOBALS\['phpgw'\]-&gt;db
will already be loaded as a database for you to use.

$GLOBALS['phpgw_info']['server']['db_host'] = Address of the database server. 
	Usually this is set to localhost - but don't assume.

$GLOBALS['phpgw_info']['server']['db_port'] = Database port.

$GLOBALS['phpgw_info']['server']['db_name'] = Database name.

$GLOBALS['phpgw_info']['server']['db_user'] = User name.

$GLOBALS['phpgw_info']['server']['db_pass'] = Password

$GLOBALS['phpgw_info']['server']['db_type'] = Type of database. 
	Currently M$ SQL Server, MySQL and PostgreSQL are supported.


### Mail information

It is unlikely you will need these, because most email needs are
services thru core functions.

$GLOBALS['phpgw_info']['server']['mail_server'] = Address of the IMAP server. 
	Usually this is set to localhost.

$GLOBALS['phpgw_info']['server']['mail_server_type'] = IMAP or POP3

$GLOBALS['phpgw_info']['server']['imap_server_type'] = Courier/Cyrus, Uwash or UW-Maildir

$GLOBALS['phpgw_info']['server']['imap_port'] = This is usually 143.  
	Should only be changed if there is a good reason.

$GLOBALS['phpgw_info']['server']['mail_suffix'] = This is the domain name, used to add to email address

$GLOBALS['phpgw_info']['server']['mail_login_type'] = This adds support for VMailMgr. 
	Generally this should be set to 'standard'.

$GLOBALS['phpgw_info']['server']['smtp_server'] = Address of the SMTP server. 
	Usually this is set to localhost.

$GLOBALS['phpgw_info']['server']['smtp_port'] = This is usually 25.
	Should only be changed if there is a good reason


### NNTP information

$GLOBALS['phpgw_info']['server']['nntp_server'] = Address of the NNTP server.

$GLOBALS['phpgw_info']['server']['nntp_port'] = This is usually 119.
	Should only be changed if there is a good reason.

$GLOBALS['phpgw_info']['server']['nntp_sender'] = Unknown

$GLOBALS['phpgw_info']['server']['nntp_organization'] = Unknown

$GLOBALS['phpgw_info']['server']['nntp_admin'] = Unknown

### Application information

Each application has the following information
available.
$GLOBALS['phpgw_info']['apps'][$appname]['title'] = The title of the application.

$GLOBALS['phpgw_info']['apps'][$appname]['enabled'] = If the application is enabled. True or False.

$GLOBALS['phpgw_info']['server']['app_include_dir'] = Location of the current application include files.

$GLOBALS['phpgw_info']['server']['app_template_dir'] = Location of the current application tpl files.

$GLOBALS['phpgw_info']['server']['app_lang_dir'] = Location of the current lang directory.

$GLOBALS['phpgw_info']['server']['app_auth'] = DEPRECATED?
	If the server and current user have access to current application

$GLOBALS['phpgw_info']['server']['app_current'] = name of the current application.


## Using Language Support
------------------------------------------------------------------------------

### Overview

PorticoEstate is built using a multi-language support scheme. This means
the pages can be translated to other languages very easily. Translations
of text strings are stored in the PorticoEstate database, and can be
modified by the PorticoEstate
administrator.

### How to use lang support

[he lang() function is your application's interface to PorticoEstate's
internationalization support.

While developing your application, just wrap all your text output with
calls to lang(), as in the following
code:
```
	$x = 42;
	echo lang('The counter is %1', $x).'<br />';
```

This will attempt to translate \`\`The counter is %1'', and return a
translated version based on the current application and language in use.
Note how the position that $x will end up is controlled by the format
string,by building up the string in your code. This allows your application to
be translated to languages where the actual number is **not** placed at the
end of the string.

When a translation is not found, the original text will be returned
with a \* after the string. This makes it easy to develop your
application, then go back and add missing translations (identified by
the \*) later.

Without a specific translation in the lang table, the above code will
print:

	The counter is 42<br />

If the current user speaks Italian, the string returned will
be:

	il contatore è 42<br />

#### The lang function
```
	lang($key, $m1="", $m2="", $m3="", $m4="", $m5="", $m6="", $m7="", $m8="", $m9="", $m10="")
```

\[$key\]

is the string to translate and may contain replacement directives of
the form %n. This string should be lower
case.

\[$m1\]

is the first replacement value or may be an array of replacement values
(in which case \$m2 and above are
ignored).

\[$m2 - $m10\] 

the 2nd through 10th replacement values if \$m1 is not an
array.

The database is searched for rows with a lang.message\_id that matches
$key. If a translation is not found, the original \$key is used. The
translation engine then replaces all tokens of the form %N with the Nth
parameter (either $m1 or $mN).


#### Adding translation data

An application called **Translation Tools** has
been developed to make this easier. Please use this application or edit
the lang files manually. The table information is here as a reference,
but direct database insertions should not be
used.

#### The lang table

The translation class uses the lang table for all translations. We are
concerned with 4 of the columns to create a translation:

\[message\_id\]

The key to identify the message (the \$key passed to the lang()
function). This is written in
English.

\[app\_name\] 

The application the translation applies to, or common if it is common
across multiple applications.

\[lang\]

The code for the language the translation is
in.

\[content\] 

The translated string.

#### phpgw\_??.lang

The translations are now being done thru the database, and may be
configurable to use other mechanisms in future
releases.

You can use the developer\_tools translations application for creating
the "lang files", which will be installed through the setup application.
Alternatively you can edit the files manually. The file naming
convention for the lang files is phpgw\_&lt;langcode&gt;.lang
The files are stored in the **app/setup** directory. The format of the files is as
follows:

```
	english phrase in lower case    appname 	**      Translated phrase in desired case.
```

**Notes:**


-   replace \*\* with the desired language code, as used in the
    filename
-   tabs are used to deliniate "columns"

Translating the content to reflect the message\_id string in the lang
language. If the string is specific to your application, put your
application name in for app\_name otherwise use the name common. The
message\_id should be in lower case for a small increase in
speed.

### Common return codes

If you browse through the PorticoEstate sources, you may notice a
pattern to the return codes used in the higher-level functions. The
codes used are partially documented in the doc/developers/CODES
file.

Codes are used as a simple way to communicate common error and progress
conditions back to the user. They are mapped to a text string through
the check\_code() function, which passes the strings through lang()
before returning.

For example, calling
```
	echo check_code(13);
```
Would print
```
	Your message has been sent
```
translated into the current language.

## Using Templates
-----------------------------------------------------------------------

### Overview

PorticoEstate har inheritet the templatesystem from phpGroupware.

PorticoEstate is built using a templates based design. This means the
display pages, stored in tpl files, can be translated to other
languages, made to look completely
different.

PorticoEstate is changing template engines for the 0.9.18 release. All
versions of PorticoEstate upto 0.9.16 use the PHPLIB template engine. As
of the 0.9.18 release PorticoEstate will use a "home grown" XSLT based
template engine.

### How to use PHPLIB templates

For Further info read the PHPLIBs documentation for their template
class.[http://phplib.sanisoft.com](http://phplib.sanisoft.com)

### How to use XSLT templates

Whoops, there is no documentation available on this - hassle the
docteam to produce something.

## About this document
---------------------------------------------------------------------------

### History

-   This document was written by Dan Kuykendall.
-   2000-09-25 documentation on lang(), codes, administration and preferences
    extension added by Steve Brown.
-   2001-01-08 fixed directory structure, minor layout changes, imported to lyx  source - Darryl VanDorp
-   2003-12-01 Started clean up - skwashd
-   2004-08-04 More cleaning up - skwashd
-   2020-06-10 Converted to MD - Sigurd Nes

###  Copyrights and Trademarks]

Copyright © Free Software Foundarion. Permission is granted to copy,
distribute and/or modify this document under the terms of the GNU Free
Documentation License, Version 1.1 or any later version published by the
Free Software Foundation.

A copy of the license is available at
[http://www.gnu.org/copyleft/fdl.html](http://www.gnu.org/copyleft/fdl.html)

### Acknowledgments and Thanks

Thanks to Joesph Engo for starting phpGroupware - (at the time called
webdistro) which has evolved into PorticoEstate. Thanks to all the developers and users who has contributed.


