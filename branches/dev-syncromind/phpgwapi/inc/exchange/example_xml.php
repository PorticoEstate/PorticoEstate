<?php
/*
* example.php
* class_xml.php example usage
* Author: Troy Wolf (troy@troywolf.com)
*/
/*
Include the class. Modify path according to where you put the class file.
*/
require_once(dirname(__FILE__).'/class_xml.php'); 

/*
Instantiate a new xml object.
*/
$x = new xml();

/*
Pass a string containing your XML to the fetch() method.
*/
$source = file_get_contents("sample.xml");
if (!$x->fetch($source)) {
  /*
  The class has a 'log' property that contains a log of events. This log is
  useful for testing and debugging.
  */
  echo "<h2>There was a problem parsing your XML!</h2>";
  echo $x->log;
  exit();
}


/*
Display the data property's structure and contents.
*/
echo "<pre>\n";
print_r($x->data);
echo "</pre>\n";

/*
You can iterate objects within the data or directly access any item.
*/
foreach ($x->data->CARS[0]->MAKE[1]->MODEL[1]->CAR as $car) {
  echo "<hr />ID: ".$car->_attr->ID;
  echo "<br />YEAR: ".$car->_attr->YEAR;
  echo "<br />MILEAGE: ".number_format($car->_attr->MILEAGE,0);
}

/*
The log property contains a log of the objects events. Very useful for
testing and debugging. If there are problems, the log will tell you what
is wrong.
*/
echo "<h1>Log</h1>";
echo $x->log; 
  
?>
