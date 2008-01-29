#!/bin/sh
#
# cronfile to start the phpgroupware data backup
#

#
# paranoia settings
#
umask 022

PATH=/sbin:/bin:/usr/sbin:/usr/bin
export PATH

{php_path} -q {script_path}/phpgw_data_backup.php

echo -e -n "\nbackup of the phpgroupware data done\n" ;

exit 0
