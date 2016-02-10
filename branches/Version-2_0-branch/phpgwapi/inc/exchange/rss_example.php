<?php
/*
* Filename.......: rss_example.php
* Author.........: Troy Wolf [troy@troywolf.com]
* Last Modified..: Date: 2005/06/27 16:25:00
* Description....: Example of how to combine the power of class_http and
                   class_xml to consume RSS feeds.
*/

/*
Modify the paths according to your system.
*/
require_once('../class_http/class_http.php');
require_once('../class_xml/class_xml.php'); 

/*
rss20() is a function to consume RSS feeds. It incorporates two PHP classes
from Troy Wolf. class_http.php is used to retrieve the raw XML from a URL.
class_http has methods to provide caching so you don't have to hit the source
URL for every hit to your script.  The second class used by rss20 is
class_xml.php. This class will parse any standard XML document into a nested
object that is easy to use in PHP.

Parameters:
      url: URL of RSS XML
      ttl: Cached data Time-to-Live in seconds. 0 = no cache. (Review class_http)
    count: Number of items to display. 0 = all.
*/
function rss20($url="", $ttl=0, $count=0) {
	if (!$url) {
    echo "<h2>rss20: Oops! You need to pass a URL!</h2>";
    return false;
  }

  /*
  Use http object to retrieve raw RSS XML and to cache the data.
  Review class_http at http://www.troywolf.com/articles/class_http/
  */
  $h = new http();
  $h->dir = "../../../../../cache/";
  if (!$h->fetch($url, $ttl)) {
    /*
    The class has a 'log' property that contains a log of events. This log is
    useful for testing and debugging.
    */
    echo "<h2>There is a problem with the http request!</h2>";
    echo $h->log;
    exit();
  }
  
  
  /*
  Use xml object to parse the raw RSS XML.
  Review class_xml at http://www.troywolf.com/articles/class_xml/
  */
  $x = new xml();
  if (!$x->fetch($h->body)) {
    /*
    The class has a 'log' property that contains a log of events. This log is
    useful for testing and debugging.
    */
    echo "<h2>There was a problem parsing your XML!</h2>";
    echo $x->log;
    exit();
  }


  /*
  Some debugging help.
  */
  #echo "<hr />";
  #echo $h->log;
  #echo "<hr />";
  #echo $x->log;
  
  #echo "<pre>\n";
  #print_r($x->data);
  #echo "</pre>\n";

  /*
  Now that we have the RSS data parsed into an object, here is how to work with
  it.  
  */

  #$version = $x->data->RSS[0]->_attr->VERSION;
  #$channel_link = $x->data->RSS[0]->CHANNEL[0]->LINK[0]->_text;
  #$channel_title = $x->data->RSS[0]->CHANNEL[0]->TITLE[0]->_text;

  echo "<div class=\"rss\">\n";

  echo "<div class=\"head\">\n";
  echo "<a href=\"".$x->data->RSS[0]->CHANNEL[0]->LINK[0]->_text
    ."\"><img border=\"0\" src=\"".$x->data->RSS[0]->CHANNEL[0]->IMAGE[0]->URL[0]->_text."\""
    ." alt=\"".$x->data->RSS[0]->CHANNEL[0]->TITLE[0]->_text."\" /></a>";
  
  echo "</div>\n";
  
  $total_items = count($x->data->RSS[0]->CHANNEL[0]->ITEM);
  if ($count == 0 || $count > $total_items) { $count = $total_items; }
  for ($idx=0; $idx<$count; $idx++) {
    $item = $x->data->RSS[0]->CHANNEL[0]->ITEM[$idx];
    echo "<div class=\"item\">\n";
    echo "<a class=\"title\" href=\"".$item->LINK[0]->_text
      ."\">".$item->TITLE[0]->_text."</a>\n";
    echo "<div class=\"description\">".$item->DESCRIPTION[0]->_text."</div>\n";
    echo "<div class=\"pubdate\">".$item->PUBDATE[0]->_text."</div>\n";
    echo "</div>\n";
  }

  echo "</div>\n";
  
}

?>

<html>
<head>
<title>RSS Example using class_http and class_xml</title>
<style>
body {
    background-color:#ffffff;
  font-family:"Trebuchet MS","Arial";
  font-size:11pt;
}

.rss {
  font-family:"Trebuchet MS","Arial";
  font-size:8pt;
  background-color:#ffffff;
  padding:4px;
  width:200px;
}

.rss .head {
  text-align:center;
  padding-bottom:4px;
}

.rss .item {
  padding:4px;
  margin-bottom:4px;
  background-color:#ffffff;
}

.rss a.title {
  font-size:9pt;
  font-weight:bold;
}

.rss a.title:hover {
  text-decoration:none;
}

.rss .description {
}

.rss .pubdate {
  font-size:xx-small;
  color:#696969;
}

</style>
</head>
<body>

<table border="0" width="100%" height="100%">
  <tr>
    <td valign="top"> 

      <h1>RSS Example using class_http and class_xml</h1>
      Author: Troy Wolf (<a href="mailto:troy@troywolf.com">troy@troywolf.com</a>)
      <br />
      Modified Date: 2005-06-27 16:25
      <br /><br />
      The column shown on the right contains the top 5 news items from
      Google News. The news is syndicated using RSS. Thousands of websites from
      major news sites such as <a href="http://news.yahoo.com/rss">Yahoo</a>
      and <a href="http://www.npr.org/rss/index.html">NPR</a> to expert
      sites such as <a href="http://www.techreport.com">techreport.com</a>
      to personal blogs provide their content in the RSS format making it easy
      for you to incorporate their content on your own website.
      <br /><br />
      This page is a demonstration of the <b>rss20()</b> function that
      combines the power of two PHP classes from Troy Wolf:
      <ol>
        <li><a href="http://www.troywolf.com/articles/php/class_http">class_http.php</a></li>
        <li><a href="http://www.troywolf.com/articles/php/class_xml">class_xml.php</a></li>
      </ol>
      
      <a href="http://www.troywolf.com/articles/php/class_xml/rss_example.phps">
      View the source of this page which includes the rss20() function</a>.
      <br />
    <hr />
    <p>
        <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
        <input type="hidden" name="cmd" value="_s-xclick">
        <input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but21.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
        <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
        <input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
        ">
        </form> 
        <h3>About the author</h3>
        <a href="mailto:troy@troywolf.com">Troy Wolf</a> is the author of
        <a href="http://www.snippetedit.com">SnippetEdit</a>--a PHP application
        providing browser-based website editing that even non-technical people can
        use. Website editing as easy as it gets. Troy has been a professional
        Internet and database application developer for over 12 years. He has many
        years' experience with ASP, VBScript, PHP, Javascript, DHTML, CSS, SQL, and
        XML on Windows and Linux platforms. Check out
        <a href="http://www.troywolf.com/articles">Troy's Code Library</a>.
    </p>
    <a href="http://www.ixwebhosting.com/templates/ix/v2/affiliate/clickthru.cgi?id=troywolf">
<img src="http://www.ixwebhosting.com/templates/ix/v2/images/banners/468x60-unlimitedpro.gif" border=0></a>


      
    </td>
    
    <td width="25">&nbsp;</td>
    
    <td valign="top" style="border-left:dotted 1px #000000;"> 

      <?
      /*
      Display the first 5 news items from Wired.com. Cache the news results for 5
      minutes.
      */
      #$url = "http://www.wired.com/news/feeds/rss2/0,2610,,00.xml";
      $url = "http://news.google.com/?output=rss";
      rss20($url, 300, 5);
      ?>
    
    </td>
  </tr>    
</table>

</body>
</html>
