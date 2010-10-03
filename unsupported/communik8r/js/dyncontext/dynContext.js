/**
* This file is part of the dynContext package (http://www.phpguru.org/)
*
* dynContext is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* dynContext is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with phpSQLiteAdmin; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
* 
* © Copyright 2005 Richard Heyes
*/

if (!dynContext_path) {
    dynContext_path = '';
}

if (document.all && document.getElementById) {
    document.write('<scr' + 'ipt src="' + dynContext_path + 'dynContext.msie.js" type="text/javascript"></scri' + 'pt>');

} else if (document.getElementById && navigator.userAgent.indexOf('') != -1) {
    document.write('<scr' + 'ipt src="' + dynContext_path + 'dynContext.dom.js" type="text/javascript"></scri' + 'pt>');
}