#!/bin/sh
#
# checks the dir {server_root}/backup for cronfile to copy it into the
# cron_dir 
# copy this file into your /etc/cron.hourly dir, chown to root and chmod u+x
#

#
# paranoia settings
#
umask 022

PATH=/sbin:/bin:/usr/sbin:/usr/bin
export PATH

# check for daily backup-file
if	test -e {server_root}/backup/phpgw_start_backup.daily ; then
    mv {server_root}/backup/phpgw_start_backup.daily /etc/cron.daily/phpgw_start_backup.daily ;
	chown root.root /etc/cron.daily/phpgw_start_backup.daily ;
	chmod go-r /etc/cron.daily/phpgw_start_backup.daily ;
	chmod u+x /etc/cron.daily/phpgw_start_backup.daily ;
	chmod u+x {script_path}/phpgw_data_backup.php ;
	echo -e -n "\nmoved script for daily backup of the phpgroupware data to the cron.daily dir\n" ;
	if test -e /etc/cron.weekly/phpgw_start_backup.weekly ; then
		rm /etc/cron.weekly/phpgw_start_backup.weekly ;
		echo -e -n "\nremoved script for weekly backup of the phpgroupware data from the cron.weekly dir\n" ;
	elif test -e /etc/cron.monthly/phpgw_start_backup.monthly ; then
		rm /etc/cron.monthly/phpgw_start_backup.monthly ;
		echo -e -n "\nremoved script for monthly backup of the phpgroupware data from the cron.monthly dir\n" ;
	fi
# else echo -e -n "\nno script for daily backup of the phpgroupware data\n" ;
fi

# check for weekly backup-file
if test -e {server_root}/backup/phpgw_start_backup.weekly ; then
    mv {server_root}/backup/phpgw_start_backup.weekly /etc/cron.weekly/phpgw_start_backup.weekly ;
	chown root.root /etc/cron.weekly/phpgw_start_backup.weekly ;
	chmod go-r /etc/cron.weekly/phpgw_start_backup.weekly ;
	chmod u+x /etc/cron.weekly/phpgw_start_backup.weekly ;
	chmod u+x {script_path}/phpgw_data_backup.php ;
	echo -e -n "\nmoved script for weekly backup of the phpgroupware data to the cron.weekly dir\n" ;
	if test -e /etc/cron.daily/phpgw_start_backup.daily ; then
		rm /etc/cron.daily/phpgw_start_backup.daily ;
		echo -e -n "\nremoved script for daily backup of the phpgroupware data from the cron.daily dir\n" ;
	elif test -e /etc/cron.monthly/phpgw_start_backup.monthly ; then
		rm /etc/cron.monthly/phpgw_start_backup.monthly ;
		echo -e -n "\nremoved script for monthly backup of the phpgroupware data from the cron.monthly dir\n" ;
	fi
# else echo -e -n "no script for weekly backup of the phpgroupware data\n";
fi

# check for monthly backup-file
if test -e {server_root}/backup/phpgw_start_backup.monthly ; then
    mv {server_root}/backup/phpgw_start_backup.monthly /etc/cron.monthly/phpgw_start_backup.monthly ;
	chown root.root /etc/cron.monthly/phpgw_start_backup.monthly ;
	chmod go-r /etc/cron.monthly/phpgw_start_backup.monthly ;
	chmod u+x /etc/cron.monthly/phpgw_start_backup.monthly ;
	chmod u+x {script_path}/phpgw_data_backup.php ;
	echo -e -n "\nmoved script for monthly backup of the phpgroupware data to the cron.monthly dir\n" ;
	if test -e /etc/cron.daily/phpgw_start_backup.daily ; then
		rm /etc/cron.daily/phpgw_start_backup.daily ;
		echo -e -n "\nremoved script for daily backup of the phpgroupware data from the cron.daily dir\n" ;
	elif test -e /etc/cron.weekly/phpgw_start_backup.weekly ; then
		rm /etc/cron.weekly/phpgw_start_backup.weekly ;
		echo -e -n "\nremoved script for weekly backup of the phpgroupware data from the cron.weekly dir\n" ;
	fi
# else echo -e -n "no script for monthly backup of the phpgroupware data\n";
fi

# check for delete backup-files
if test -e {server_root}/backup/phpgw_delete_backup.all ; then
	if test -e /etc/cron.daily/phpgw_start_backup.daily ; then
		rm /etc/cron.monthly/phpgw_start_backup.daily ;
	elif test -e /etc/cron.monthly/phpgw_start_backup.monthly ; then
		rm /etc/cron.monthly/phpgw_start_backup.monthly ;
	elif test -e /etc/cron.weekly/phpgw_start_backup.weekly ; then
		rm /etc/cron.weekly/phpgw_start_backup.weekly ;
	fi
	rm {server_root}/backup/phpgw_delete_backup.all ;
	echo -e -n "\nremoved all scripts for backup of the phpgroupware data from the cron dirs\n" ;
fi

exit 0
