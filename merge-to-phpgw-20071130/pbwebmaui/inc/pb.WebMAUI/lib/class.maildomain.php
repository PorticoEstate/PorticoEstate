<?php
/**
 * pbWebMAUI
 * @author Andreas Schiller <aschiller@probusiness.de>
 * @copyright Copyright (C) 2002-2003 probusiness AG
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package pbWebMAUI
 */
    /**
     * data include file
     * @see data
     */
    require_once "class.data.php";
    /**
     * mailaccount include file
     * @see mailaccount
     */
    require_once "class.mailaccount.php";

    /**
     * Maildomain
     * @package pbWebMAUI
     */
    class Maildomain extends Data {
        /**
         * 
         *
         * @var $_filter
         */
        var $_filter;

        /**
         * 
         *
         * @var $_dropsfilter
         */
        var $_dropsfilter;

        /**
         * Constructor
         *
         * @param $application
         * @param string $domain
         * @param boolean $new
         */
        function Maildomain(&$application, $domain="", $new=false) {
            $this->Data($application);

            //prepare buffer
            $this->_prepareBuffer("domain", $domain, $new?stateDirty:stateRead);
            $this->_prepareBuffer("accounts", "");
        }

        /**
         * Set filter
         *
         * @param $value
         */
        function setFilter($value) {
            $this->_filter = $value;
            $this->clearAttribute("accounts");
        }

        /**
         * Set drop filter
         *
         * @param $value
         */
        function setDropsFilter($value) {
            $this->_dropsfilter = $value;
            $this->clearAttribute("maildrops");
        }

        /**
         * Override method from class data
         *
         * @param string $attr
         * @access private
         */
        function _readAttribute($attr) {
            //global $quota; //from config.php

            switch ($attr) {
                case "accounts":
                    if($accounts = $this->_getRows("domain",
                                                    array("domain"=>$this->getAttribute("domain"),
                                                          "filter"=>empty($this->_filter)?"":utf8_encode($this->_filter)),
                                                    array("cn","mail"),
                                                    "cn")) {

                        //copy rows to domain
                        foreach ($accounts as $account) {
                            $this->_Buffer[$attr]["value"][] = &new MailAccount($this->getApplication(), $account["dn"], utf8_decode($account["cn"][0]), $account["mail"]);
                        }
                    }
                    $this->_Buffer[$attr]["state"] = stateRead;
                    break;

                case "maildrops":
                    if($drops = $this->_getRows("maildrops",
                                                    array("domain"=>$this->getAttribute("domain"),
                                                          "filter"=>empty($this->_dropsfilter)?"":$this->_dropsfilter),
                                                    array("mail", "maildrop"),
                                                    "mail")) {

                        //copy rows to domain
                        foreach ($drops as $drop) {
                            $this->_Buffer[$attr]["value"][] = &new MailDrop($this->getApplication(), $this->getAttribute("domain"), $drop["mail"][0], $drop["maildrop"]);
                        }
                    }
                    $this->_Buffer[$attr]["state"] = stateRead;
                    break;

                case "dn":
                case "quota":
                case "quotabytes":
                case "inetaccess":
                case "md":
                case "bukr":
                case "kstl":
                    if($domain = $this->_getRows("server",
                                                    array("domain"=>$this->getAttribute("domain")),
                                                    "",//array("ou"),
                                                    ""//array("dn","pbWebMAUI","pbAccesslevel","pbQuota","pbInetAccess"))
                                                    )) {

                        //there can only be one row... I think
                        //set defaults for additional attributes
                        $this->_prepareBuffer("accesslevel", $domain[0]["pbaccesslevel"][0], stateRead);
                        $this->_prepareBuffer("inetaccess", $domain[0]["pbinetaccess"][0], stateRead);
                        $this->_prepareBuffer("quota", $domain[0]["pbquota"][0], stateRead);

                        $quotakey = $this->getAttribute("quota");
                        $this->_prepareBuffer("quotabytes", $quota[$quotakey]["size"] * 1024 * 1024, stateRead);

                        //extract additional pbWebMAUI attributes from array
                        debug(dbgData, 3, "additional attributes, pbwebmaui", $domain[0]["pbwebmaui"]);
                        if (is_array($addattrs = $domain[0]["pbwebmaui"])) {
                            foreach ($addattrs as $addattr) {
                                list($key, $value) = split (":", $addattr, 2);
                                $this->_prepareBuffer(strtolower($key), $value, stateRead);
                            }
                        }

                        $this->_prepareBuffer("dn", $domain[0]["dn"], stateRead);
                    }

                    break;

                case "diskusage":
                    $this->_prepareBuffer("diskusage", $this->_readDiskusage(), stateRead);
                    break;

                case "quotas":
                    $accounts = $this->getAttribute("accounts");
                    foreach ($accounts as $account) {
                        $uid = $account->getAttribute("uid");

                        $quotakey = $account->getAttribute("quota");
                        if ($quotakey == 0)
                            $quotakey = $this->getAttribute("quota");

                        $quotas[$uid] = $quota[$quotakey]["size"] * 1024 * 1024;
                    }
                    $this->_prepareBuffer("quotas", $quotas, stateRead);
                    break;

                case "inetaccesstext":
                    //global $cfg_inetaccess;
                    $inetaccess = $this->getAttribute("inetaccess");

                    if (empty($inetaccess)) {
                        $inetaccesstext = "";
                    }
                    else if (!empty($cfg_inetaccess[$inetaccess])) {
                        $inetaccesstext = $cfg_inetaccess[$inetaccess];
                    }
                    else {
                        $y = substr($inetaccess, 0, 4);
                        $m = substr($inetaccess, 4, 2);
                        $d = substr($inetaccess, 6, 2);
                        $dt = mktime(0,0,0,$m,$d,$y);

                        $inetaccesstext = date("d.m.Y", $dt);
                    }

                    $this->_prepareBuffer("inetaccesstext", $inetaccesstext, stateRead);
                    debug(dbgData,1,"inetaccess of domain",array("inetaccess"=>$inetaccess,"text"=>$inetaccesstext));
                    break;

                case "billings":
                    $accounts = $this->getAttribute("accounts");
                    foreach ($accounts as $account) {
                        $uid = $account->getAttribute("uid");
                        ($md = $account->getAttribute("md")) || ($md = $this->getAttribute("md"));
                        ($kstl = $account->getAttribute("kstl")) || ($kstl = $this->getAttribute("kstl"));
                        ($bukr = $account->getAttribute("bukr")) || ($bukr = $this->getAttribute("bukr"));
                        ($quotakey = $account->getAttribute("quota")) || ($quotakey = $this->getAttribute("quota"));
                        ($inetaccesstext = $account->getAttribute("inetaccesstext")) || ($inetaccesstext = $this->getAttribute("inetaccesstext"));

                        $billings[$uid] = array("md"=>trim($md),"kstl"=>trim($kstl),"bukr"=>trim($bukr),"quota"=>$quotakey,"inetaccesstext"=>$inetaccesstext);
                    }
                    $this->_prepareBuffer("billings", $billings, stateRead);
                    break;
            }
        }

        /**
         * Save
         *
         * @param array $attrs Array of attribute names to save
         */
        function save($attrs="") {
            //global $cfg_chk4maildir;

            //check for dirty attributes, if no attrs are given
            if (empty($attrs)) {
                $attrs = $this->isDirty();
            }

            if ($attrs) {
                foreach($attrs as $attr) {
                    $value = trim($this->getAttribute($attr));
                    debug (dbgData,2,sprintf("in save, attr=%s, value=%s",$attr,$value));
                    if (/**/true || /**/strlen($value)) {
                        switch($attr) {
                            case "domain":
                                $entries["ou"] = $value;
                                break;

                            case "quota":
                                $entries["pbQuota"] = $value;
                                break;

                            case "inetaccess":
                                $entries["pbInetAccess"] = $value;
                                break;

                            case "md":
                            case "bukr":
                            case "kstl":
                                $entries["pbWebMAUI"][] = $attr.":".$value;
                                break;

                            default:
                                debug(dbgData, 2, "don't know, how to save", array("attribute"=>$attr, "value"=>$value) );
//                                die("don't know, how to save attribute $attr");

                        }
                    }
                }

                $dn = $this->getAttribute("dn");

                if (empty($dn)) {
                    debug(dbgData,2,"empty dn on Maildomain object", $this);
                    //create new domain
                    $dn = $this->_getNewDn("domain", array("domain"=>$this->getAttribute("domain")));
                    $entries["objectClass"][]="organizationalUnit";
                    $this->_write($dn, $entries, true);
                }
                else {
                    $this->_write($dn, $entries);
                }
            }
        }

        /**
         * Home dir
         *
         * @param string $domain
         * @return string
         * @access private
         */
        function _homedir($domain) {
            //global $cfg_maildirs;
            $path = $cfg_maildirs."/".$domain;

            return $path;
        }


        /**
         * read disk usage
         *
         * @return array
         * @access private
         */
        function _readDiskusage() {
            //global $cfg_courierctl;

            $homeDir = $this->_homedir($this->getAttribute("domain"));
            $cmd = $cfg_courierctl." dudomain ".$homeDir;
            exec ($cmd, $execoutput, $ret);

            //execoutput is an array like this
            /*
                0=4096 /var/spool/courier/koenigsmann.org/T/Test/Maildir/cur
                1=4096 /var/spool/courier/koenigsmann.org/T/Test/Maildir/new
                2=4096 /var/spool/courier/koenigsmann.org/T/Test/Maildir/tmp
                3=4096 /var/spool/courier/koenigsmann.org/T/Test/Maildir
            */
            foreach($execoutput as $line) {
                //global $cfg_quota;

                if (list($size, $path) = split("\t", $line, 2)) {
                    $subdirs = split("/", $path);
                    debug(dbgData,2,"readDiskusage",array("subdirs"=>$subdirs, "size"=>$size));

                    if (count($subdirs) >= 8) {
                        $folder = $subdirs[count($subdirs)-1];
                        if ($folder=="cur" || $folder=="new" || $folder=="tmp") {
                            $folder = $subdirs[count($subdirs)-2];
                        }
                        else {
                            $size -= $cfg_quota["adminsize"];
                            if ($size < 0) $size = 0;
                        }

                        if ($folder=="Maildir") $folder="INBOX";
                        else $folder="INBOX".$folder;

                        $account = $subdirs[6];

                        if (!strlen($du[$account]["INBOX"]))
                            $du[$account]["INBOX"] = 0;

                        $du[$account][$folder] += $size;
                    }
                }
            }

            debug(dbgData,2,"readDiskusage",array("du"=>$du));
            return ($du);
        }

        /**
         * delete
         *
         * @return string
         */
        function delete() {
            //global $cfg_courierctl;
            //global $cfg_domainarchive;

            //remove maildrops
            $maildrops = $this->getAttribute("maildrops");
            if (!empty($maildrops)) {
                foreach($maildrops as $maildrop) {
                    $maildrop->delete();
                }
            }

            //remove accounts
            $accounts = $this->getAttribute("accounts");
            if (!empty($accounts)) {
                foreach($accounts as $account) {
                    $account->delete();
                }
            }

            //archive domain
            $cmd = $cfg_courierctl." archivedomain ".$cfg_domainarchive." ".$this->_homeDir($this->getAttribute("domain"))." ".$this->getAttribute("domain");
            exec ($cmd, $execoutput, $ret);

            //remove domain
            $cmd = $cfg_courierctl." removedomain ".$cfg_maildirs." ".$this->getAttribute("domain");
            exec ($cmd, $execoutput, $ret);

            parent::delete();
            return $cfg_domainarchive."/".$this->getAttribute("domain").".tgz";
        }
    }
?>
