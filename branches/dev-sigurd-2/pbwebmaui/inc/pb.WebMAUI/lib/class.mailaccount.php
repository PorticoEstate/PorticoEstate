<?php
/**
 * pbWebMAUI
 *
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
     * maildrop include file
     * @see maildrop
     */
    //require_once "class.maildrop.php";

    /**
     * Mailaccount capsules mailaccount data
     * @package pbWebMAUI
     */
    class Mailaccount extends Data {
        /**
         * Constructor
         *
         * @param $application
         * @param $dn
         * @param $cn
         * @param $mail
         * @param $domain
         */
        function Mailaccount(&$application, $dn="", $cn="", $mail="", $domain="") {
            $this->Data($application);

            //prepare buffer
            $this->_prepareBuffer("dn", $dn);
            $this->_prepareBuffer("cn", $cn);
            if (empty($dn)){ //prepare mail and domain only if dn is not given to avoid inconsistent data
                $this->_prepareBuffer("domain", $domain);

                if (is_array($mail)) {
                    $this->_prepareBuffer("mail", $mail);
                }
                else {
                    $this->_prepareBuffer("mail", array($mail));
                }
            }
        }


        /**
         * Homedir
         *
         * @param $domain
         * @param $uid
         * @return string Path to homedir
         */
        function _homedir($domain, $uid="") {
            $cfg_courierctl = $this->_application->_config->get_courierscript();
            if (strlen($uid)) {
                $path = $cfg_courierctl['mailaccountdir']."/".$domain."/".$uid[0]."/".$uid;
            }
            else {
                $path = $cfg_courierctl['mailaccountdir']."/".$domain;
            }

            return $path;
        }

        /**
         * Override method from class data
         *
         * @param $attr
         */
        function _readAttribute($attr) {
            //global $quota; //quota array from config.php
            //global $cfg_inetaccess;

            debug(dbgData,2,sprintf("_readAttribute(%s)",$attr));
            switch ($attr) {
                case "dn":
                    //if we get here, there was no dn given and we must get it from mailadress
                    if($accounts = $this->_getRows("server",
                                                    array("mail"=>$this->getAttribute("mail")),
                                                    "",
                                                    "")) {

                        //use first row - there should only be one
                        $this->_prepareBuffer("dn", $accounts[0]["dn"], stateRead);

                        //I'd like to fill in the other attributes here, but then I'd have to
                        //do it twice (here and in following block)
                    }

                    break;

                case "cn":
                case "mail":
                case "cryptpassword":
                case "accesslevel":
                case "quota":
                case "quotabytes":
                case "inetaccess":
                case "md":
                case "bukr":
                case "kstl":
                case "comment":
                case "changes":
                case "quotawarnings":
                case "uidnumber":
                case "gidnumber":
                    if($accounts = $this->_getRows("account",
                                                    array("domain"=>$this->getAttribute("domain"),
                                                          "uid"=>$this->getAttribute("uid")),
                                                    "",
                                                    "")) {

                        //there can only be one row... I think
                        $this->_prepareBuffer("cn", utf8_decode($accounts[0]["cn"][0]), stateRead);
                        $this->_prepareBuffer("mail", $accounts[0]["mail"], stateRead);
                        $this->_prepareBuffer("cryptpassword", $accounts[0]["userpassword"][0], stateRead);

                        //set defaults for additional attributes
                        $this->_prepareBuffer("accesslevel", $accounts[0]["pbaccesslevel"][0], stateRead);
                        $this->_prepareBuffer("inetaccess", $accounts[0]["pbinetaccess"][0], stateRead);
                        $this->_prepareBuffer("quota", $accounts[0]["pbquota"][0], stateRead);

                        $this->_prepareBuffer("quotawarnings", strlen($accounts[0]["pbquotawarnings"][0])?$accounts[0]["pbquotawarnings"][0]:"0", stateRead);

                        $quotakey = $this->getAttribute("quota");
                        $this->_prepareBuffer("quotabytes", $quota[$quotakey]["size"] * 1024 * 1024, stateRead);

                        $this->_prepareBuffer("comment", utf8_decode($accounts[0]['description'][0]), stateRead);
                        $this->_prepareBuffer("uidnumber", utf8_decode($accounts[0]['uidnumber'][0]), stateRead);
                        $this->_prepareBuffer("gidnumber", utf8_decode($accounts[0]['gidnumber'][0]), stateRead);

                        //extract additional pbWebMAUI attributes from array (md,bukr,kstl)
                        debug(dbgData, 3, "additional attributes, pbwebmaui", $accounts[0]["pbwebmaui"]);
                        if (is_array($addattrs = $accounts[0]["pbwebmaui"])) {
                            foreach ($addattrs as $addattr) {
                                list($key, $value) = split (":", $addattr, 2);
                                $this->_prepareBuffer(strtolower($key), $value, stateRead);
                            }
                        }

                        //extract recent changes from pbChanges attributes
                        if (is_array($changes = $accounts[0]["pbchanges"])) {
                            foreach ($changes as $change) {
                                list($time, $user) = split (":", $change, 2);
                                $ar_changes[$time] = $user;
                            }

                            $this->_prepareBuffer("changes", $ar_changes, stateRead);
                        }
                    }

                    $this->_Buffer[$attr]["state"] = stateRead;
                    break;

                case "maildrops":
                    //get maildrops for each mail address
                    if ($mails = $this->getAttribute("mail")) {
                        foreach ($mails as $mail) {
                            if(!empty($mail) && $drops = $this->_getRows("maildrops",
                                                                        array("filter"=>$mail),
                                                                        array("mail","maildrop"),
                                                                        "maildrop")) {

                                //copy rows to list of maildrops
                                foreach ($drops as $drop) {
                                    $this->_Buffer[$attr]["value"][] = &new MailDrop($this->getApplication(), "", $drop["mail"][0]);
                                }
                            }
                        }
                    }

                    $this->_Buffer[$attr]["state"] = stateRead;
                    break;

                case "uid":
                case "domain":
                    //these two attributes are calculated from dn
                    $dn = $this->getAttribute("dn");
                    if (ereg("uid=([^,]+),ou=([^,]+)", $dn, $reg)) {
                        $this->_prepareBuffer("uid", $reg[1]);
                        $this->_prepareBuffer("domain", $reg[2]);
                    }
                    break;

                case "rawfilters":
                    $this->_prepareBuffer("rawfilters", $this->_loadmailfilter());
                    break;

                case "filters":
                    $filter = $this->_extractfilter($this->getAttribute("rawfilters"));
                    $this->_prepareBuffer("filters", $filter, stateRead);
                    break;

                case "diskusage":
                    $this->_prepareBuffer("diskusage", $this->_readDiskusage(), stateRead);
                    break;

                case "folders":
                    $this->_prepareBuffer("folders", $this->_listmaildir(), stateRead);
                    debug(dbgData,1,"read folders",$this->_listmaildir());
                    break;

                case "inetaccesstext":
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
                    debug(dbgData,1,"inetaccess of account",array("inetaccess"=>"(".$inetaccess.")","text"=>$inetaccesstext));
                    break;
            }
        }

        /**
         * Save
         *
         * @param array $attrs Array of attribute names to save
         */
        function save($attrs="") {
            $cfg_courierctl = $this->_application->_config->get_courierscript();

            //check for dirty attributes, if no attrs are given
            if (empty($attrs)) {
                $attrs = $this->isDirty();
            }

            if ($attrs) {
                //-prepare attributes to save--------------------------------------
                foreach($attrs as $attr) {
                    $value = $this->getAttribute($attr);
                    if (is_string($value))
                        $value = trim($value);

                    debug (dbgData,2,"in save",array("attr"=>$attr, "value"=>$value, "is_array(value)"=>is_array($value)?"true":"false"));
                    if (strlen($value) || $attr=="quota" || $attr=="inetaccess" || $attr=="comment") {
                        switch($attr) {
                            case "cn":
                                $entries["cn"] = utf8_encode($value);
                                break;

                            case "mail":
                                //remove empty mailadresses
                                if (!empty($value)) {
                                    while (list($n,$v)=each($value)) {
                                        if (empty($v)) unset($value[$n]);
                                    }
                                    $entries["mail"] = array_values($value);
                                }
                                break;

                            case "cryptpassword":
                                $entries["userPassword"] = $value;
                                break;

                            case "password":
                                $entries["userPassword"] = "{crypt}".crypt($value);
                                break;

                            case "accesslevel":
                                //$entries["pbAccesslevel"] = $value;
                                break;

                            case "quota":
                                //$entries["pbQuota"] = $value;
                                break;

                            case "quotawarnings":
                                //$entries["pbQuotaWarnings"] = $value;
                                break;

                            case "comment":
                                $entries["description"] = utf8_encode($value);
                                break;

                            case "inetaccess":
                                //$entries["pbInetAccess"] = $value;
                                break;

                            case "md":
                            case "bukr":
                            case "kstl":
                                //$entries["pbWebMAUI"][] = $attr.":".$value;
                                break;

                            case "filters":
                                $this->_savemailfilter();
                                break;

                            case 'uidnumber':
                                $entries['uidnumber'] = $value;
                                break;

                            case 'gidnumber':
                                $entries['gidnumber'] = $value;
                                break;

                            default:
                                die("don't know, how to save attribute $attr");
                        }
                    }
                }

                //-anything to save?-----------------------------------------------
                if (!empty($entries)) {
                    //create changes entry
                    $changes = $this->getAttribute("changes");
                    //$entries["pbChanges"][] = time() . ":" . $this->_application->getUsername();

                    //for ($i=0; $i<2 && is_array($changes) && (list($time, $user) = each($changes)); $i++) {
                        //$entries["pbChanges"][] = $time . ":" . $user;
                    //}

                    debug(dbgData,2,"in save",array("entries"=>$entries));

                    //save object
                    $dn = $this->getAttribute("dn");
                    if (empty($dn)) {
                        //create new object
                        $mails = $this->getAttribute("mail");
                        $uid="";

                        //calculate uid from first mailadress given
                        debug(dbgData,2,sprintf("calculating uid"),$mails);
                        if (is_array($mails)) {
                            foreach($mails as $mail) {
                                $mail_components = split("@", $mail);
                                $uid = $mail_components[0];
                                debug(dbgData,2,sprintf("calculating uid, split mail_components"),$mail_components);
                                if (!empty($uid)) break;
                            }
                        }

                        debug(dbgData,2,sprintf("calculating uid, result"),$uid);

                        if (empty($uid))
                            return ("Keine gültige Mailadresse angegeben");

                        $newdn = $this->_getNewDn("account", array("domain"=>$this->getAttribute("domain"), "id"=>$uid));

                        $homeDir = $this->_homedir($this->getAttribute("domain"), $uid);
                        $entries["homeDirectory"] = $homeDir;
                        $entries["objectClass"][]="courierMailAccount";
                        $entries["objectClass"][]="account";
                        //$entries["objectClass"][]="pbWebMAUIPerson";

                        $cn = $this->getAttribute("cn");
                        if (empty($cn))
                            $entries["cn"] = $uid;
												$entries['uid'] = $uid;

                        if ($this->_write($newdn, $entries, true)) {
                        	  exec ($cfg_courierctl['location'].' createmaildir '.$homeDir, $execoutput);
                            debug(dbgData, 2, "created homedir; output", $execoutput);
                        }
                    } //empty dn (new object)
                    else {
                        $entries["objectClass"][]="CourierMailAccount";
                        $entries["objectClass"][]="account";
                        $this->_write($dn, $entries);
                    } //!empty dn
                } //!empty entries
            }
        }

        /**
         * Escapes strings that might be interpreted as regex
         *
         * @param string $s
         * @return string
         * @access private
         */
        function _escape($s) {
            $special = '|!\$()[]\+*?.&;`-~<>^{}"'."'";
            for ($i=0; $i<strlen($s); $i++) {
                $c = substr($s,$i,1);
                if (strpos($special, $c) === false) {
                    $return .= $c;
                }
                else {
                    $return .= '\\'.$c;
                }
            }

            return ($return);
        }

        /**
         * Loads filter from .mailfilter file
         *
         * @return array Output from exec
         * @access private
         */
        function _loadmailfilter() {
            $cfg_courierctl = $this->_application->_config->get_courierscript();

            $homeDir = $this->_homedir($this->getAttribute("domain"), $this->getAttribute("uid"));
            $cmd = $cfg_courierctl['location']." loadmailfilter ".$homeDir;
            exec ($cmd, $execoutput, $ret);
            return ($execoutput);
        }

        /**
         * Comparision between to quotalist items, used with usort
         *
         * @param array $a
         * @param array $b
         * @return integer
         * @access private
         */
        function _comparefilter($a, $b) {

            return $b["type"]-$a["type"];
        }

        /**
         * Save filter to .mailfilter file
         *
         * @access private
         */
        function _savemailfilter() {
            $cfg_courierctl = $this->_application->_config->get_courierscript();

            $rawfilters = $this->getAttribute("rawfilters");
            $filters = $this->getAttribute("filters");
            //sort autoreply first; keep ids
            if (is_array($filters))
                uasort($filters, array($this, "_comparefilter"));

            $homeDir = $this->_homedir($this->getAttribute("domain"), $this->getAttribute("uid"));

            //extract autoreply text from array and save it to additional file
            $this->_extractautoreply($filters);

            //create pipe to courierctl and send filter as stdin
            $cmd = $cfg_courierctl['location'].' savemailfilter '.$homeDir;
            $fp = popen($cmd, "w");
            debug (dbgData, 3, "popen($cmd) for writing returned $fp");

            //write serialized filter array
            fputs($fp, "#pb.WebMAUI-begin\n");
            fputs($fp, "#".serialize($filters)."\n\n");

            //create filter rules
            fputs($fp, "exception {\n"); //start exception block
            fputs($fp, " MAILBOT=/usr/lib/courier/bin/mailbot\n");
            fputs($fp, " import RECIPIENT\n");

            foreach ($filters as $filter) {
                if ($filter["active"]
                  && (!empty($filter["cc"]) || !empty($filter["to"]) || !empty($filter["autoreplyfile"]))){

                    $rules = $filter["rules"];
                    $op = $rules["op"]; //may be one of "&&" or "||"
                    unset ($rules["op"]);

                    //write patterns to mailfilter
                    $first = true;
                    fputs($fp, " #-- ".$filter["name"]." --\n");
                    fputs($fp, " if (");
                    if (empty($rules)) {
                        fputs($fp, "1");
                    }
                    else {
                        foreach ($rules as $rule) {
                            if (!$first) fputs($fp, $op);
                            if (!empty($rule["not"])) fputs($fp, "!");
                            $pattern = $this->_escape($rule["pattern"]);
                            switch ($rule["where"]) {
                                case 0:
                                    fputs($fp, "/^[(to)(cc)]+:.*".$pattern."/");
                                    break;
                                case 1:
                                    fputs($fp, "/^from:.*".$pattern."/");
                                    break;
                                case 2:
                                    fputs($fp, "/^subject:.*".$pattern."/");
                                    break;
                                case 3:
                                    fputs($fp, "/".$pattern."/:b");
                                    break;
                                case 4:
                                    fputs($fp, "(/^subject:.*".$pattern."/ || /".$pattern."/:b)");
                                    break;
                            }
                            $first = false;
                        }
                    }
                    fputs($fp, ")\n {\n");

                    //eventually use active date
                    if (!empty($filter["time"])) {
                        fputs($fp, "  if (");

                        $fromtime = $filter["time"]["from"];
                        list($fd,$fm,$fy,$fh,$fn) = split("[/.-]",$fromtime.'-00.00');
                        $totime = $filter["time"]["to"];
                        list($td,$tm,$ty,$th,$tn) = split("[/.-]",$totime.'-24.00');
                        fputs($fp, "time >= ".mktime($fh,$fn,0,$fm,$fd,$fy)." && time < ".mktime($th,$tn,0,$tm,$td,$ty));
                        fputs($fp, ") #$fromtime - $totime\n");
//                        fputs($fp, ") #".date('r', mktime($fh,$fn,0,$fm,$fd,$fy)). " - " . date('r', mktime($th,$tn,0,$tm,$td,$ty)) ."\n");
                        fputs($fp, "  {\n");
                    }

                    //write autoreply to filter
                    if (!empty($filter["autoreplyfile"])) {
                        $fn = $filter["autoreplyfile"];
                        fputs($fp, "   cc \"| \$MAILBOT -t ".$fn." -d ".$fn.".db -D 9999 -A \\\"From: \$RECIPIENT \\\" \$SENDMAIL \"\n");
                    }

                    //write ccs to mailfilter
                    $ccs = $filter["cc"];
                    if (!empty($ccs)) {
                        foreach ($ccs as $cc) {
                            if (substr($cc,0,1) == "!") fputs($fp, "   cc \"".$cc."\"\n");
                            else {
                                $d = "./Maildir/.".$cc;
                                fputs($fp, "   if ( `test -d \"".$d."\" && echo 1` )\n");
                                fputs($fp, "   {\n");
                                fputs($fp, "    cc \"".$d."\"\n");
                                fputs($fp, "   }\n");
                            }
                        }
                    }

                    //write "to" to mailfilter
                    $to = $filter["to"];
                    if (!empty($to)) {
                        if (substr($to,0,1) == "!") fputs($fp, "   to \"".$to."\"\n");
                        else {
                            $d = "./Maildir/.".$to;
                            fputs($fp, "   if ( `test -d \"".$d."\" && echo 1` )\n");
                            fputs($fp, "   {\n");
                            fputs($fp, "    to \"".$d."\"\n");
                            fputs($fp, "   }\n");
                        }
                    }

                    //eventually close using of active date
                    if (!empty($filter["time"])) {
                        fputs($fp, "  }\n");
                    }

                    fputs($fp, " }\n");
                }

                if (!$filter["active"] && !empty($filter["autoreplyfile"])){
                    //deactivate reply counter
                    $cmdRmCounter = $cfg_courierctl." rmautoreplydb ".$homeDir." ".$filter["autoreplyfile"];
                    exec($cmdRmCounter);
                }
            }
            fputs($fp,"}\n"); //end of exception block

            fputs($fp, "#pb.WebMAUI-end\n");

            $pbwm = false;

            //write anything else than old pbWebMAUI to .mailfilter
            foreach($rawfilters as $line) {
                if ("$line" == "#pb.WebMAUI-begin")
                    $pbwm = true;
                else if ("$line" == "#pb.WebMAUI-end")
                    $pbwm = false;
                else if (!$pbwm)
                    fwrite($fp, $line."\n");
            }

            pclose($fp);
        }

        /**
         * Extracts autoreplytext from filter array and writes this to a file
         *
         * @param array $filters
         */
        function _extractautoreply(&$filters) {
            $cfg_courierctl = $this->_application->_config->get_courierscript();

            foreach(array_keys($filters) as $key) {
                if (!empty($filters[$key]["autoreply"])) {
                    //create filename for autoreply file
                    $filters[$key]["autoreplyfile"] = ".autoreply_".$key;

                    //create pipe to courierctl and send text as stdin
                    $homeDir = $this->_homedir($this->getAttribute("domain"), $this->getAttribute("uid"));
                    $cmd = $cfg_courierctl['location']." saveautoreply ".$homeDir." ".$filters[$key]["autoreplyfile"];
                    $fp = popen($cmd, "w");
                    debug (dbgData, 3, "popen($cmd) for writing returned $fp");

                    fputs($fp, $filters[$key]["autoreply"]);
                    pclose($fp);

                    unset($filters[$key]["autoreply"]);
                }
            }
        }

        /**
         * Includes autoreplytext from file into filter array
         *
         * @param array $filters
         */
        function _includeautoreply(&$filters) {
            $cfg_courierctl = $this->_application->_config->get_courierscript();

            if (is_array($filters)) {
                foreach(array_keys($filters) as $key) {
                    if (!empty($filters[$key]["autoreplyfile"])) {
                        //create pipe to courierctl and receive text from stdout
                        $homeDir = $this->_homedir($this->getAttribute("domain"), $this->getAttribute("uid"));
                        $cmd = $cfg_courierctl['location']." loadautoreply ".$homeDir." ".$filters[$key]["autoreplyfile"];
                        $fp = popen($cmd, "r");
                        debug (dbgData, 3, "popen($cmd) for reading returned $fp");

                        $filters[$key]["autoreply"] = "";
                        while (!feof($fp)) {
                            $filters[$key]["autoreply"] .= fgets($fp, 1024);
                        }
                        pclose($fp);
                    }
                }
            }
        }

        /**
         * Extracts filter from raw filter data
         *
         * @param array $rawfilter Array of string containing the raw filter data from .mailfilter
         * @return array
         * @access private
         */
        function _extractfilter($rawfilters) {
            $pbwm = false;

            foreach($rawfilters as $line) {
                debug (dbgData, 3, "_extractfilter reading line $line");

                if ("$line" == "#pb.WebMAUI-end")
                    break;

                if ($pbwm && substr($line,0,1) == "#") $filter .= substr($line, 1);

                if ("$line" == "#pb.WebMAUI-begin")
                    $pbwm = true;
            }

            $filterarray = unserialize($filter);
            $this->_includeautoreply($filterarray);
            debug (dbgData, 3, "_extractfilter found", array("filter"=>$filter,"array"=>$filterarray));
            return $filterarray;
        }


        /**
         * Calculates diskusage for account
         *
         * @return array Folders and their diskusage
         * @access private
         */
        function _readDiskusage() {
            $cfg_courierctl = $this->_application->_config->get_courierscript();
            //global $cfg_quota;

            $homeDir = $this->_homedir($this->getAttribute("domain"), $this->getAttribute("uid"));
            $cmd = $cfg_courierctl['location']." duaccount ".$homeDir;
            exec ($cmd, $execoutput, $ret);

            //execoutput is an array like this
            /*
                0=4096 /var/spool/courier/koenigsmann.org/T/Test/Maildir/cur
                1=4096 /var/spool/courier/koenigsmann.org/T/Test/Maildir/new
                2=4096 /var/spool/courier/koenigsmann.org/T/Test/Maildir/tmp
                3=4096 /var/spool/courier/koenigsmann.org/T/Test/Maildir
            */
            foreach($execoutput as $line) {
                if (list($size, $path) = split("\t", $line, 2)) {
                    $subdirs = split("/", $path);
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

                        $du[$folder] += $size;
                    }
                }
            }

            debug(dbgData,1,"readDiskusage",array("du"=>$du,"adminsize"=>$cfg_quota["adminsize"]));
            return ($du);
        }

        /**
         * delete
         *
         * @return array
         */
        function delete() {
            $cfg_courierctl = $this->_application->_config->get_courierscript();
            $uid = $this->getAttribute("uid");
            $domain = $this->getAttribute("domain");

            $homeDirDomain = $this->_homedir($domain);
            $homeDirAccount = $this->_homedir($domain, $uid);

            //create pipe to courierctl and send filter as stdin
            /* not yet working
            $cmd = $cfg_courierctl['location']." archiveaccount ".$homeDirDomain." ".$homeDirAccount." ".$uid;
            $fp = popen($cmd, "w");
            debug (dbgData, 3, "popen($cmd) for writing returned $fp");

            //write serialized filter array
            fputs($fp, $this->_getldif());
            $ret = pclose($fp);

            debug (dbgData, 3, "pclose returned $ret");
            */
            $ret = 0;
            if ($ret == 0) {
                $cmd = $cfg_courierctl['location']." removeaccount ".$homeDirAccount;
                exec ($cmd, $execoutput, $ret);

                debug (dbgData, 3, "exec $cmd returned $ret");
                if ($ret == 0) {
                    //remove any mail adress from any maildrop it's in
                    $aliases = $this->getAttribute("mail");
                    $maildrops = $this->getAttribute("maildrops");
                    if (is_array($maildrops)) {
                        foreach(array_keys($maildrops) as $key) {
                            $maildrop = & $maildrops[$key];
                            $maildrop->removetargets($aliases);
                        }
                    }

                    //delete object from datasource
                    if (!parent::delete()) {
                        $ret = -1;
                        $state = array("state"=>-3,"message"=>"LDAP-Eintrag konnte nicht gelöscht werden");
                    };
                }
                else {
                    $state = array("state"=>-2,"message"=>"Maildir konnte nicht gelöscht werden; courierctl lieferte ".$ret);
                }
            }
            else {
                $state = array("state"=>-1,"message"=>"Archiv konnte nicht erstellt werden; courierctl lieferte ".$ret);
            }

            if ($ret == 0) {
                $state = array("state"=>1, "archive"=>$homeDirDomain."/".$uid.".tgz");
            }

            return $state;
        }

        /**
         * Remove alias
         *
         * @param $deleteMail
         * @param $save
         */
        function removealias($deleteMail, $save) {
            if ($this->getAttribute("uid")."@".$this->getAttribute("domain") == "$deleteMail") {
                return ("Die Hauptmailadresse darf nicht gelöscht werden");
            }

            $mails=$this->getAttribute("mail");

            $changed = true;
            while (list($key,$value)=each($mails)) {
                if ($value=="$deleteMail") {
                    debug(dbgData,1,"unset mails[$key]");
                    unset($mails[$key]);
                    $changed = true;
                }
            }

            if ($changed) {
                //remove alias from drops
                $maildrops = $this->getAttribute("maildrops");
                if (is_array($maildrops)) {
                    debug(dbgData,3,"remove alias from drops", $maildrops);
                    foreach(array_keys($maildrops) as $key) {
                        $maildrop = & $maildrops[$key];
                        $maildrop->removetargets(array($deleteMail), true);
                    }
                }

                $this->setAttribute("mail", array_values($mails));//use array_values to renumber

                if ($save)
                    $this->save();
            }
        }


        /**
         * Exists
         *
         * @return boolean
         */
        function exists() {
            $dn = $this->getAttribute("dn");
            return (!empty($dn));
        }

        /**
         * Import mail dir
         *
         * @param $archive
         */
        function importmaildir($archive) {
            $cfg_courierctl = $this->_application->_config->get_courierscript();

            $uid = $this->getAttribute("uid");
            $domain = $this->getAttribute("domain");
            $homeDirAccount = $this->_homedir($domain, $uid);

            $cmd = $cfg_courierctl['location']." importmaildir ".$archive." ".$homeDirAccount;
            exec ($cmd, $execoutput, $ret);
        }

        /**
         * List mail dir
         *
         * @return array Output from exec
         * @access private
         */
        function _listmaildir() {
            $cfg_courierctl = $this->_application->_config->get_courierscript();

            $uid = $this->getAttribute("uid");
            $domain = $this->getAttribute("domain");
            $homeDirAccount = $this->_homedir($domain, $uid);

            $cmd = $cfg_courierctl['location']." listmaildir ".$homeDirAccount;
            exec ($cmd, $execoutput, $ret);

            return ($execoutput);
        }

    }
?>
