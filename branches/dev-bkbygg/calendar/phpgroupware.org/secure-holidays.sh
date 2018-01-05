#!/bin/bash
#
# phpGroupWare secure-holidays
# Copyright (c) 2003 Free Software Foundation Inc
# Written by Dave Hall
# This code is GPL
# Use at your own risk
echo 'This script will change the name of all of your holidays to a more secure name'
echo 'You run this script at your own risk - no support is provided for this script'
read -p 'Press any key to continue or [ctrl]-c to cancel' -n 1
for i in $(find . -name 'holiday*');
	do 
		mv $i $i.txt ;
		echo 'Moving '$i' to '$i'.txt';
	done;
echo 'Done'
