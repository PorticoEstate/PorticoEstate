<?php
//
// phpSysInfo - A PHP System Information Script
// http://phpsysinfo.sourceforge.net/
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
//
// $Id$

$net = sys_netdevs();

$_text = '<table width="100%" align="center">'
       . '<tr><td align="left"><b><font size="-1">' . $text['device'] . '</font></b></td>'
       . '<td align="left"><b><font size="-1">' . $text['received'] . '</font></b></td>'
       . '<td align="right"><b><font size="-1">' . $text['sent'] . '</font></b></td>'
       . '<td align="right"><b><font size="-1">' . $text['errors'] . '</font></b></td>';

while (list($dev, $stats) = each($net)) {
    $_text .= "\t<tr>\n";
    $_text .= "\t\t<td align=\"left\">$f_body_open" . $dev . "$f_body_close</td>\n";
    $_text .= "\t\t<td align=\"left\">$f_body_open" . format_bytesize($stats['rx_bytes'] / 1024) . "$f_body_close</td>\n";
    $_text .= "\t\t<td align=\"right\">$f_body_open" . format_bytesize($stats['tx_bytes'] / 1024) . "$f_body_close</td>\n";
    $_text .= "\t\t<td align=\"right\">$f_body_open" . $stats['errs'] . '/' . $stats['drop'] . "$f_body_close</td>\n";
    $_text .= "\t</tr>\n";
}             

$_text .= '</table>';

$tpl->set_var('network', makebox($text['netusage'], $_text, '100%'));

?>
