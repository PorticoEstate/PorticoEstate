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

$scale_factor = 2;

$_text = '<table width="100%" align="center">'
       . '<tr><td align="left">' . $f_body_open . '<b>' . $text['mount'] . '</b>' . $f_body_close . '</td>'
       . '<td align="left">' . $f_body_open . '<b>' . $text['type'] . '</b>' . $f_body_close . '</td>'
       . '<td align="left">' . $f_body_open . '<b>' . $text['partition'] . '</b>' . $f_body_close . '</td>'
       . '<td align="left">' . $f_body_open . '<b>' . $text['percent'] . '</b>' . $f_body_close . '</td>'
       . '<td align="right">' . $f_body_open . '<b>' . $text['free'] . '</b>' . $f_body_close . '</td>'
       . '<td align="right">' . $f_body_open . '<b>' . $text['used'] . '</b>' . $f_body_close . '</td>'
       . '<td align="right">' . $f_body_open . '<b>' . $text['size'] . '</b>' . $f_body_close . '</td></tr>';

$fs = sys_fsinfo();

for ($i=0; $i<sizeof($fs); $i++) {
    $sum['size'] += $fs[$i]['size'];
    $sum['used'] += $fs[$i]['used'];
    $sum['free'] += $fs[$i]['free']; 

    $_text .= "\t<tr>\n";
    $_text .= "\t\t<td align=\"left\">$f_body_open" . $fs[$i]['mount'] . "$f_body_close</td>\n";
    $_text .= "\t\t<td align=\"left\">$f_body_open" . $fs[$i]['fstype'] . "$f_body_close</td>\n";
    $_text .= "\t\t<td align=\"left\">$f_body_open" . $fs[$i]['disk'] . "$f_body_close</td>\n";
    $_text .= "\t\t<td align=\"left\">$f_body_open";

    $_text .= create_bargraph($fs[$i]['percent'], $fs[$i]['percent'], $scale_factor);

    $_text .= "&nbsp;" . $fs[$i]['percent'] . "$f_body_close</td>\n";
    $_text .= "\t\t<td align=\"right\">$f_body_open" . format_bytesize($fs[$i]['free']) . "$f_body_close</td>\n";
    $_text .= "\t\t<td align=\"right\">$f_body_open" . format_bytesize($fs[$i]['used']) . "$f_body_close</td>\n";
    $_text .= "\t\t<td align=\"right\">$f_body_open" . format_bytesize($fs[$i]['size']) . "$f_body_close</td>\n";
    $_text .= "\t</tr>\n";
}

$_text .= '<tr><td colspan="3" align="right">' . $f_body_open . '<i>' . $text['totals'] . ' :&nbsp;&nbsp;</i>' . $f_body_close . '</td>';
$_text .= "\t\t<td align=\"left\">$f_body_open";

$sum_percent = round(($sum['used'] * 100) / $sum['size']);
$_text .= create_bargraph($sum_percent, $sum_percent, $scale_factor);

$_text .= "&nbsp;" . $sum_percent . "%" .  $f_body_close . "</td>\n";

$_text .= '<td align="right">' . $f_body_open . format_bytesize($sum['free']) . $f_body_close . '</td>'
        . '<td align="right">' . $f_body_open . format_bytesize($sum['used']) . $f_body_close . '</td>'
        . '<td align="right">' .  $f_body_open . format_bytesize($sum['size']) . $f_body_close . '</td></tr>'
        . '</table>';

$tpl->set_var('filesystems', makebox($text['fs'], $_text, '100%'));

?>
