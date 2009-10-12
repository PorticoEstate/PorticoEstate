<?php
/**
 * pbWebMAUI configuration
 * @author Andreas Schiller <aschiller@probusiness.de>
 * @copyright Copyright (C) 2002-2003 probusiness AG
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package pbWebMAUI
 */

    /**
     * Data container - right now type ldap is the only one
     * @global string $cfg_data["type"]
     */
    $cfg_data["type"] = "ldap";

    /**
     * Data container - LDAP config
     * @global string $cfg_data["ldap"]
     */
    $cfg_data["ldap"]["host"] = "127.0.0.1";
    $cfg_data["ldap"]["port"] = "389";
    $cfg_data["ldap"]["basedn"] = "dc=example,dc=com";
    $cfg_data["ldap"]["binddn"] = "cn=Manager,dc=example,dc=com";
    $cfg_data["ldap"]["bindpw"] = "secret";
    $cfg_data["ldap"]["ou_maildomains"] = "maildomains";  //dn for maildomains subtree is built as "ou=".$cfg_data["ldap"]["ou_maildomains"].",".$cfg_data["ldap"]["basedn"]
    $cfg_data["ldap"]["ou_maildrops"] = "maildrops";      //dn for maildrop subtree is built as "ou=".$cfg_data["ldap"]["ou_maildrops"].",".$cfg_data["ldap"]["basedn"]

    /**
     * Superuser(s)
     * @global mixed $superusers["root"]
     */
    $superusers["root"]["password"] = "secret";
    $superusers["root"]["options"]["level"] = 3; // Sysadmin

    /**
     * Quotalevels, size in MByte=1024kB; -1 for unlimited, don't use 0 as key
     *
     * @global array $quota
     */
    $quota[1]["size"] = 0;
    $quota[2]["size"] = 10;
    $quota[3]["size"] = 50;
    $quota[4]["size"] = 100;
    $quota[5]["size"] = 300;
    $quota[99]["size"] = -1;

    /**
     * Quota
     *
     * @global array $cfg_quota
     */
    $cfg_quota["adminsize"] = 20480;
    $cfg_quota["high"] = 999.99; //high percentage value for "using space with quota=0"

    /**
     * Special Internetaccess Values (other values are dates yyyymmdd)
     *
     * @global array $cfg_inetaccess
     */
    $cfg_inetaccess["00000000"]="kein Zugriff";
    $cfg_inetaccess["99999999"]="unbegrenzt";

    /**
     * Maildir path, maildir is created as $cfg_maildirs."/".$domain."/".$uid[0]."/".$uid
     *
     * @global string $cfg_maildirs
     */
    $cfg_maildirs = "/var/spool/courier";

    /**
     * Domainarchive
     *
     * @global string $cfg_domainarchive
     */
    $cfg_domainarchive = "/var/spool/courier/archiv";

    /**
     * Command to control some courier needs
     *
     * @global string $cfg_courierctl
     */
    $cfg_courierctl = "/usr/bin/sudo -u courier /opt/probusiness/courierctl.sh";

    /**
     * Send mail
     *
     * @global array $cfg_mail
     */
    $cfg_mail["backend"]="smtp"; //sendmail or smtp
    $cfg_mail["params"]["host"] = "192.168.0.1";
    $cfg_mail["params"]["port"] = "25";
    //$cfg_mail["params"]["sendmail_path"] = '/usr/bin/sendmail';
    //$cfg_mail["params"]["sendmail_args"] = '';

    $cfg_mail["sender"]="postmaster@example.com";
    $cfg_mail["quota"]["subject"] = "Quota Warnung";
    $cfg_mail["quota"]["template"] = "BillingMail.tpl"; //template in ./templates
    $cfg_mail["billing"]["recipient"] = "billing@example.com";
    $cfg_mail["billing"]["subject"] = "Billing List";
    $cfg_mail["billing"]["template"] = "BillingList.tpl"; //template in ./templates

    /**
     * List size
     *
     * @global array $cfg_lists
     */
    $cfg_lists["size"] = 15; //lines to show on lists

    /**
     * Logging of ldap write activities
     *
     * @global string $cfg_log["ldap"]
     */
    $cfg_log["ldap"]="/tmp/pb.WebMAUI.ldif";
?>
