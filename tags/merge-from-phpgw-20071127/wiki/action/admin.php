<?php
// $Id: admin.php 15975 2005-05-15 12:55:31Z skwashd $

// Don't freak out lib/init.php.
$document = $categories = $comment = $page = '';

//require('lib/init.php');
require('parse/html.php');
//require('parse/transforms.php');
require('template/admin.php');

if(!$GLOBALS['phpgw_info']['user']['apps']['admin'])
  { die($ErrorAdminDisabled); }

// Harvest script parameters.

$REMOTE_ADDR = isset($_SERVER['REMOTE_ADDR'])
               ? $_SERVER['REMOTE_ADDR'] : '';

if(isset($_GET['locking']))
  { $locking = $_GET['locking']; }
if(isset($_GET['blocking']))
  { $blocking = $_GET['blocking']; }
if(!isset($locking))
{
  $locking  = isset($_POST['locking'])
              ? $_POST['locking'] : '';
}
if(!isset($blocking))
{
  $blocking = isset($_POST['blocking'])
              ? $_POST['blocking'] : '';
}

$Block   = isset($_POST['Block'])
           ? $_POST['Block'] : '';
$Unblock = isset($_POST['Unblock'])
           ? $_POST['Unblock'] : '';
$Save    = isset($_POST['Save'])
           ? $_POST['Save'] : '';
$address = isset($_POST['address'])
           ? $_POST['address'] : '';

$count = isset($_POST['count']) ? $_POST['count'] : 0;

if($locking && $count > 0)
{
  for($i = 1; $i <= $count; $i++)
  {
    $var = 'name' + $i;
    $$var = isset($_POST[$var]) ? $_POST[$var] : '';
    $var = 'lock' + $i;
    $$var = isset($_POST[$var]) ? $_POST[$var] : '';
  }
}

if($locking)                            // Locking/unlocking pages.
{
  if(empty($Save))                      // Not saving results; display form.
  {
    $GLOBALS['phpgw']->common->phpgw_header();
	
    $html = html_lock_start();
    $pagelist = $pagestore->allpages();
    foreach($pagelist as $page)
      { $html = $html . html_lock_page($page[1], $page[6]); }
    template_admin(array('html' => $html . html_lock_end(count($pagelist))));
  }
  else                                  // Lock/unlock pages at admin's request.
  {
    $pagestore->lock();                 // Exclusive access to database.
    for($i = 1; $i <= $count; $i++)
    {
      $page = urldecode($_POST['name' . $i]);
      if(isset($_POST['lock' . $i]))
        { $lock = $_POST['lock' . $i]; }
      else
        { $lock = 0; }
      $pg = $pagestore->page($page);
      $pg->read();
      $pg->version++;
      $pg->hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
      $pg->username = $UserName;
      $pg->comment = '';
      $pg->text = str_replace('\\', '\\\\', $pg->text);
      $pg->text = str_replace('\'', '\\\'', $pg->text);
      if($pg->exists && $pg->mutable && $lock)
      {
        $pg->mutable = 0;
        $pg->write();
      }
      else if($pg->exists && !$pg->mutable && !$lock)
      {
        $pg->mutable = 1;
        $pg->write();
      }
    }

    $pagestore->unlock();
    header('Location: ' . $AdminScript);
  }
}
else if($blocking)                      // Blocking/unblocking IP addrs.
{
  if(empty($Block) && empty($Unblock))  // Not saving results; display form.
  {
    $GLOBALS['phpgw']->common->phpgw_header();
	
	$html = '';
    if($RatePeriod == 0)
    {
      $html = $html . html_bold_start() .
              'Rate control / IP blocking disabled' .
              html_bold_end() . html_newline();
    }

    $html = $html . html_rate_start();
    $blocklist = $pagestore->rateBlockList();
    foreach($blocklist as $block)
      { $html = $html . html_rate_entry($block); }
    $html = $html . html_rate_end();

    template_admin(array('html' => $html));
  }
  else                                  // Block/unblock an address group.
  {
    if(!empty($Block))
      { $pagestore->rateBlockAdd($address); }
    else if(!empty($Unblock))
      { $pagestore->rateBlockRemove($address); }

    header('Location: ' . $AdminScript);
  }
}
else                                    // Display main menu for admin.
{
  $GLOBALS['phpgw']->common->phpgw_header();

  template_admin(array('html' => html_url($AdminScript . '&locking=1',
                                          'Lock / unlock pages') .
                                 html_newline() .
                                 html_url($AdminScript . '&blocking=1',
                                          'Block / unblock hosts') .
                                 html_newline()));
}

?>
