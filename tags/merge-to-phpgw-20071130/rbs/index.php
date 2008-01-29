<?php
#Index is just a stub to redirect to the appropriate day view (Sam)

$day   = date("d");
$month = date("m");
$year  = date("Y");

# Had to take the redir out so I wouldn't have to append the other passed stuff manually.
# I hope this doesn't break anything (Stephan)
#header("Location: day.php?day=$day&month=$month&year=$year");

require("day.php");
?>
