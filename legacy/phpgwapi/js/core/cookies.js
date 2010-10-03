/**
* Cookie Functions -- "Night of the Living Cookie" Version (25-Jul-96)
*
* @author Bill Dortch, hIdaho Design <bdortch@hidaho.com>
* @license Public domain
* @link http:*www.webwoman.biz/articles/Cookies/cookie.txt
*
*
* This version takes a more aggressive approach to deleting
* cookies.  Previous versions set the expiration date to one
* millisecond prior to the current time; however, this method
* did not work in Netscape 2.02 (though it does in earlier and
* later versions), resulting in "zombie" cookies that would not
* die.  DeleteCookie now sets the expiration date to the earliest
* usable date (one second into 1970), and sets the cookie's value
* to null for good measure.
*
* Also, this version adds optional path and domain parameters to
* the DeleteCookie function.  If you specify a path and/or domain
* when creating (setting) a cookie**, you must specify the same
* path/domain when deleting it, or deletion will not occur.
*
* The FixCookieDate is used to correct the IE2.x Mac date bug,
* and has been removed as IE2 Mac is too old to be supported in phpgw
*
* This version also incorporates several minor coding improvements.
*
* Note that it is possible to set multiple cookies with the same
* name but different (nested) paths.  For example:
*
*	SetCookie ("color","red",null,"/outer");
*	SetCookie ("color","blue",null,"/outer/inner");
*
* However, GetCookie cannot distinguish between these and will return
* the first cookie that matches a given name.  It is therefore
* recommended that you *not* use the same name for cookies with
* different paths.  (Bear in mind that there is *always* a path
* associated with a cookie; if you don't explicitly specify one,
* the path of the setting document is used.)
*  
* Revision History:
*
*   Doc'd! Version (31-Dec-2004) - Dave Hall
*     - Changed docs to JSdoc
*
*   "Toss Your Cookies" Version (22-Mar-96)
*     - Added FixCookieDate() function to correct for Mac date bug
*
*   "Second Helping" Version (21-Jan-96)
*     - Added path, domain and secure parameters to SetCookie
*     - Replaced home-rolled encode/decode functions with Netscape's
*       new (then) escape and unescape functions
*
*   "Free Cookies" Version (December 95)
*
*
* For information on the significance of cookie parameters, and
* and on cookies in general, please refer to the official cookie
* spec, at:
*
* @link http://www.netscape.com/newsref/std/cookie_spec.html    
*
*/

/**
* Decode the value of a cookie
*
* Internal method!
* @param int offset the offset of the cookie
* @returns string decoded value
*/
function getCookieVal (offset) {
  var endstr = document.cookie.indexOf (";", offset);
  if (endstr == -1)
    endstr = document.cookie.length;
  return unescape(document.cookie.substring(offset, endstr));
}

/**
* Get the value of a cookie
* @param string name cookie name
* @returns string cookie value, or null if the cookie does not exist.
*/
function GetCookie (name) {
  var arg = name + "=";
  var alen = arg.length;
  var clen = document.cookie.length;
  var i = 0;
  while (i < clen) {
    var j = i + alen;
    if (document.cookie.substring(i, j) == arg)
      return getCookieVal (j);
    i = document.cookie.indexOf(" ", i) + 1;
    if (i == 0) break; 
  }
  return null;
}
/**
* Create or update a cookie.
*
* @param string name Cookie name.
* @param string value Cookie value, may contain any valid string character/s
* @param object [expires] Date object containing the expiration data of the 
*	cookie.  If omitted or null, a session cookie is created.
* @param string [path] Path for which the cookie is valid.  If omitted or
*	null, uses the path of the calling document.
* @param string [domain] Domain for which the cookie is valid.  If omitted or 
*	null, uses the domain of the calling document.
* @param boolean [secure] Cookie transmission requires secure channel (HTTPS)?  
*
* The first two parameters are required.  The others, if supplied, must
* be passed in the order listed above.  To omit an unused optional field,
* use null as a place holder.  For example, to call SetCookie using name,
* value and path, you would code:
*
*	SetCookie ("myCookieName", "myCookieValue", null, "/");
*
* Note that trailing omitted parameters do not require a placeholder.
*
* To set a secure cookie for path "/myPath", that expires after the
* current session, you might code:
*
*	SetCookie (myCookieVar, cookieValueVar, null, "/myPath", null, true);
*/
function SetCookie (name,value,expires,path,domain,secure) {
  document.cookie = name + "=" + escape (value) +
    ((expires) ? "; expires=" + expires.toGMTString() : "") +
    ((path) ? "; path=" + path : "") +
    ((domain) ? "; domain=" + domain : "") +
    ((secure) ? "; secure" : "");
}

/**
* Delete a cookie. (Sets expiration date to start of epoch)
* @param string name cookie name
* @param string path The path of the cookie to delete.  This MUST be the same 
*	as the path used to create the cookie, or null/omitted if no path was 
*	specified when creating the cookie.
* @param string domain The domain of the cookie to delete.  This MUST be the 
*	same as the domain used to create the cookie, or null/omitted if
*	no domain was specified when creating the cookie.
*/
function DeleteCookie (name,path,domain) {
  if (GetCookie(name)) {
    document.cookie = name + "=" +
      ((path) ? "; path=" + path : "") +
      ((domain) ? "; domain=" + domain : "") +
      "; expires=Thu, 01-Jan-70 00:00:01 GMT";
  }
}
