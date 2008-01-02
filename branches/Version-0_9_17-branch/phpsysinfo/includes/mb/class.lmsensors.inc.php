<?php 

// phpSysInfo - A PHP System Information Script
// http://phpsysinfo.sourceforge.net/

// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.

// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.

// $Id: class.lmsensors.inc.php 16207 2005-11-19 05:29:54Z skwashd $

class mbinfo {
    var $lines;

  function temperature() {
    $ar_buf = array();
    $results = array();

    if (!isset($this->lines)) {
        $this->lines = execute_program("sensors", "");
    }

    // Martijn Stolk: Dirty fix for misinterpreted output of sensors, 
    // where info could come on next line when the label is too long.
    $sensors_value = $this->lines;
    $sensors_value = preg_replace("/:\n/", ":", $sensors_value);
    $sensors_value = explode("\n", $sensors_value);

    foreach($sensors_value as $line) {
      $data = array();
      if (ereg("(.*):(.*)\((.*)=(.*),(.*)=(.*)\)(.*)", $line, $data))
        ;
      else
        ereg("(.*):(.*)\((.*)=(.*)\)(.*)", $line, $data);

      $temp = substr(rtrim($data[2]), -1);
      switch ($temp) {
        case "C";
        case "F":
          array_push($ar_buf, $line);
          break;
      }
    }

    $i = 0;
    foreach($ar_buf as $line) {
      if (ereg("(.*):(.*).C[ ]*\((.*)=(.*).C,(.*)=(.*).C\)(.*)\)", $line, $data)) ;
      elseif (ereg("(.*):(.*).C[ ]*\((.*)=(.*).C,(.*)=(.*).C\)(.*)", $line, $data)) ;
      else
        ereg("(.*):(.*).C[ ]*\((.*)=(.*).C\)(.*)", $line, $data);

      $alarm = substr(trim($data[7]), 0, 5);

      $results[$i]['label'] = trim($data[1]);
      $results[$i]['value'] = trim($data[2]);
      $results[$i]['limit'] = trim($data[4]);
      $results[$i]['percent'] = trim($data[6]);
      if ($results[$i]['limit'] < $results[$i]['percent']) {
        $results[$i]['limit'] = $results[$i]['percent'];
      }
      $i++;
    }

    asort($results);
    return array_values($results);
  }

  function fans() {
    $ar_buf = array();
    $results = array();

    if (!isset($this->lines)) {
        $this->lines = execute_program("sensors", "");
    }

    $sensors_value = $this->lines;

    foreach($sensors_value as $line) {
      $data = array();
      ereg("(.*):(.*)\((.*)=(.*),(.*)=(.*)\)(.*)", $line, $data);
      $temp = explode(" ", trim($data[2]));
      if (count($temp) == 1)
        $temp = explode("\xb0", trim($data[2]));
      switch ($temp[1]) {
        case "RPM":
          array_push($ar_buf, $line);
          break;
      }
    }

    $i = 0;
    foreach($ar_buf as $line) {
      if (ereg("(.*):(.*) RPM  \((.*)=(.*) RPM,(.*)=(.*)\)(.*)\)", $line, $data));
      else
        ereg("(.*):(.*) RPM  \((.*)=(.*) RPM,(.*)=(.*)\)(.*)", $line, $data);
      $alarm = substr(trim($data[7]), 0, 5);

      $results[$i]['label'] = trim($data[1]);
      $results[$i]['value'] = trim($data[2]);
      $results[$i]['min'] = trim($data[4]);
      $results[$i]['div'] = trim($data[6]);
      $i++;
    }

    asort($results);
    return array_values($results);
  }

  function voltage() {
    $ar_buf = array();
    $results = array();

    if (!isset($this->lines)) {
        $this->lines = execute_program("sensors", "");
    }

    $sensors_value = $this->lines;

    foreach($sensors_value as $line) {
      $data = array();
      ereg("(.*):(.*)\((.*)=(.*),(.*)=(.*)\)(.*)", $line, $data);
      $temp = explode(" ", trim($data[2]));
      if (count($temp) == 1)
        $temp = explode("\xb0", trim($data[2]));
      switch ($temp[1]) {
        case "V":
          array_push($ar_buf, $line);
          break;
      }
    }

    $i = 0;
    foreach($ar_buf as $line) {
      if (ereg("(.*):(.*) V  \((.*)=(.*) V,(.*)=(.*) V\)(.*)\)", $line, $data));
      else
        ereg("(.*):(.*) V  \((.*)=(.*) V,(.*)=(.*) V\)(.*)", $line, $data);
      $alarm = substr(trim($data[7]), 0, 5);

      $results[$i]['label'] = trim($data[1]);
      $results[$i]['value'] = trim($data[2]);
      $results[$i]['min'] = trim($data[4]);
      $results[$i]['max'] = trim($data[6]);
      $i++;
    }

    return $results;
  }
}

?>
