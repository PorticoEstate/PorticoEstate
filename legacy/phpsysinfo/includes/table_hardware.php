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

$sys = sys_cpu();

$ar_buf = sys_pcibus(); 

if (count($ar_buf)) {
    for ($i=0;$i<sizeof($ar_buf);$i++) {
        $pci_devices .= $ar_buf[$i] . '<br>';
    }
} else {
    $pci_devices .= '<i>'. $text['none'] . '</i>';
}

$ar_buf = sys_idebus(); 

ksort($ar_buf);

if (count($ar_buf)) {
    while (list($key, $value) = each($ar_buf)) {
        $ide_devices .= $key . ': ' . $ar_buf[$key]['model'];
        if (isset($ar_buf[$key]['capacity'])) {
            $ide_devices .= ' (Capacity: ' . sprintf('%.2f', $ar_buf[$key]['capacity'] / (1024 * 1024 * 2)) . ' GB )';
        }
        $ide_devices .= '<br>';
    }
} else {
    $ide_devices .= '<i>' . $text['none']. '</i>';
}

$ar_buf = sys_scsibus(); 

if (count($ar_buf)) {
    for ($i=0;$i<sizeof($ar_buf);$i++) {
        $scsi_devices .= $ar_buf[$i] . '<br>';
    }
} else {
    $scsi_devices .= '<i>' . $text['none'] . '</i>';
}


$_text = '<table border="0" width="90%" align="center">'
       . '<tr><td><font size="-1">'. $text['numcpu'] .'</font></td><td><font size="-1">' . $sys['cpus'] . '</font></td></tr>'
       . '<tr><td><font size="-1">'. $text['cpumodel'] .'</font></td><td><font size="-1">' . $sys['model'] . '</font></td></tr>'
       . '<tr><td><font size="-1">'. $text['mhz'] .'</font></td><td><font size="-1">' . $sys['mhz'] . ' MHz</font></td></tr>'
       . '<tr><td><font size="-1">'. $text['cache'] .'</font></td><td><font size="-1">' . $sys['cache'] . '</font></td></tr>'
       . '<tr><td><font size="-1">'. $text['bogomips'] .'</font></td><td><font size="-1">' . $sys['bogomips'] . '</font></td></tr>'
       . '<tr><td><font size="-1">'. $text['pci'] .'</font></td><td><font size="-1">' . $pci_devices . '</font></td></tr>'
       . '<tr><td><font size="-1">'. $text['ide'] .'</font></td><td><font size="-1">' . $ide_devices . '</font></td></tr>'
       . '<tr><td><font size="-1">'. $text['scsi'] .'</font></td><td><font size="-1">' . $scsi_devices . '</font></td></tr>'
       . '</table>';

$tpl->set_var('hardware', makebox($text['hardware'], $_text, '100%'));

?>
