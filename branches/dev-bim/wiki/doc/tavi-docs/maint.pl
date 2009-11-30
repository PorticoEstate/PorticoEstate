#!/usr/bin/perl

# $Id: maint.pl 11896 2003-03-04 21:21:05Z ralfbecker $
#
# This script is used by WikkiTikkiTavi versions 0.1 and greater to maintain
# the list of known remote pages.  This is used by the TwinPages feature.
# Typically, it will be set up as a cron job to run periodically (e.g.,
# once per week).
# 
# See http://tavi.sourceforge.net/SisterWiki for more information.

$database = "";                         # Database name.
$user     = "";                         # Database use name.
$pass     = "";                         # Database password.
$prefix   = "";                         # Table name prefix (e.g. "wiki_").
$linkptn  = "([A-Z][a-z]+[A-Z][A-Za-z]*(/[A-Z][A-Za-z]+)?)";

use DBI;

$dbh = DBI->connect("DBI:mysql:$database:127.0.0.1", $user, $pass)
       or die "Connecting: $DBI::errstr\n";

sub insert_page
{
  my ($page) = @_;
  my ($qid);

  $qid = $dbh->prepare("SELECT page FROM " . $prefix . "remote_pages " .
                       "WHERE site='$site' and page='$page'");
  $qid->execute;
  if(!$qid->fetchrow_hashref)
  {
    $qid = $dbh->prepare("INSERT INTO " . $prefix . "remote_pages " .
                         "VALUES('$page', '$site')");
    $qid->execute;
  }

  return "";
}

$qid = $dbh->prepare("SELECT prefix, url FROM " . $prefix . "sisterwiki)");
$qid->execute;

while($row = $qid->fetchrow_hashref)
{
  $site = $row->{'prefix'};
  print "Scanning $site\n";

  $_ = "lynx -source " . $row->{'url'};
  s/&/\\&/g;
  s/;/\\;/g;
  $html = `$_`;

  $q2 = $dbh->prepare("DELETE FROM " . $prefix . "remote_pages " .
                       "WHERE site='$site'");
  $q2->execute;

  foreach(split(/\n/, $html))
  {
    s/<[Aa].*>($linkptn)<\/[Aa]>/&insert_page($1)/geo;
  }
}

