<?php
/**
 * pbWebMAUI
 * @author Andreas Schiller <aschiller@probusiness.de>
 * @copyright Copyright (C) 2002-2003 probusiness AG
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package pbWebMAUI
 */
    /**
     * Used for sorting
     * 
     * @var string $_cmpRowsSort
     * @access private
     */
    $_cmpRowsSort="";
    
    /**
     * Little helper to sort result from cleaned ldap_get_entries (have a look at data::_getRows)
     *
     * @param array $entry_a
     * @param array $entry_b
     * @return integer
     * @access private
     */
    function _cmpRows($entry_a, $entry_b){
        //global $_cmpRowsSort;

        if (!empty($_cmpRowsSort) && !empty($entry_a[$_cmpRowsSort]) && !empty($entry_b[$_cmpRowsSort]))
            return strcasecmp($entry_a[$_cmpRowsSort][0], $entry_b[$_cmpRowsSort][0]);
        else return 0;
    }


    // Constants used to define buffer states
    /**
     * stateUnknown
     */
    define("stateUnknown", 0);
    /**
     * stateRead
     */
    define("stateRead", 1);
    /**
     * stateDirty
     */
    define("stateDirty", 2);


    /**
     * Data class - for the time being only connection to ldap
     *
     * @package pbWebMAUI
     */
    class Data {
        /**
         * Buffer
         * @access protected
         * @var $_Buffer
         */
        var $_Buffer;

        /**
         * Port
         * @access protected
         * @var string $_port
         */
        var $_port;

        /**
         * Mail domains
         * @access protected
         * @var array $_maildomains
         */
        var $_maildomains;

        /**
         * Mail drops
         * @access protected
         * @var array $_maildrops
         */
        var $_maildrops;

        /**
         * LDAP connection
         * @access protected
         * @var array $_conn
         */
        var $_conn;               
        
        /**
         * Application
         * @access protected
         * @var array $_application
         */
        var $_application;             


        /**
         * Constructor
         *
         * @param $application
         */
        function Data(&$application) {
            $this->_application =& Application::getInstance(null);
						$this->maildomainLocation = $this->_application->_config->get_storageLocation('maildomains');
						$this->maildropLocation   = $this->_application->_config->get_storageLocation('maildrops');
        }

        /**
         * Get application
         *
         * @return object
         */
        function &getApplication() {
            return $this->_application;
        }

        /**
         * Connect to server
         * @param boolean $bind Bind to server, if true
         * @return object
         */
        function _connect($bind=false) {
            if (!isset($this->_conn)) {
                $this->_conn = ldap_connect($this->maildomainLocation['hostspec'], $this->maildomainLocation['port']);
            }

            if ($bind && !$this->_bind && isset($this->_conn)) {
                $this->_bind = ldap_bind($this->_conn, $this->maildomainLocation['username'], $this->maildomainLocation['password']);
            }

            return ($this->_conn);
        }

        /**
         * Create DN
         *
         * @param string $type may be one of
         *               "account" --> full dn for account is returned, needs additional paramameters $domain and $uid
         *               "domain" --> dn for a maildomain is returned, needs additional paramameter $domain
         *               "maildomains" --> dn for ou is returned which contains all of the maildomains
         *               "maildrops" --> dn for ou with aliases is returned
         * @param string $domain
         * @param string $uid
         * @return string DN
         */
        function _create_dn($type, $domain="", $uid="") {
            switch ($type) {
            case "account":
                return 'uid='.$uid.','.'ou='.$domain.','.$this->maildomainLocation['database'];
                //return "uid=".$uid.","."ou=".$domain.",ou=".$this->_maildomains.",".$this->_basedn;
                break;

            case "domain":
                return 'ou='.$domain.','.$this->maildomainLocation['database'];
                break;

            case "maildomains":
                //return "ou=".$this->_maildomains.",".$this->maildomainLocation['database'];
                return $this->maildomainLocation['database'];
                break;

            case "maildrops":
                return $this->maildropLocation['database'];
                break;

            case "maildrop":
                return 'cn='.$uid.','.$this->maildropLocation['database'];
                break;
            }
        }

        /**
         * Get new DN
         *
         * @param string $typ
         * @param array $params
         * @return string
         */
        function _getNewDn($typ, $params) {
            return $this->_create_dn($typ, $params["domain"], $params["id"]);
        }

        /**
         * High level lookup of attribute. The classes methods will take care of efficient reading of data
         *
         * @param string $attr
         * @return string
         */
        function getAttribute($attr) {
            debug(dbgData, 4, "getAttribute($attr)", $this->_Buffer);
            if ($this->_Buffer[$attr]["state"] == stateUnknown)
            {
                $this->_readAttribute($attr);
            }

            return $this->_Buffer[$attr]["value"];
        }

        /**
         * Clears value and state of attribute
         *
         * @param string $attr
         */
        function clearAttribute($attr) {
            $this->_Buffer[$attr]["state"] = stateUnknown;
            unset($this->_Buffer[$attr]["value"]);
        }

        /**
         * Set attribute
         *
         * @param string $attrname
         * @param $value
         * @param boolean $forceset
         */
        function setAttribute($attrname, $value, $forceset=false) {
            debug(dbgData, 2, "setAttribute", array("attrname"=>$attrname,
                                                    "value"=>$value,
                                                    "current value"=>$this->_Buffer[$attrname]["value"],
                                                    "current state"=>$this->_Buffer[$attrname]["state"],
                                                    "forceset"=>$forceset,
                                                    "new state"=>($this->_Buffer[$attrname]["value"] != $value || empty($this->_Buffer[$attrname]["state"]) || $forceset)?stateDirty:$this->_Buffer[$attrname]["state"]));
            if ($this->_Buffer[$attrname]["value"] != $value || empty($this->_Buffer[$attrname]["state"]) || $forceset) {
                $this->_Buffer[$attrname]["value"] = $value;
                $this->_Buffer[$attrname]["state"] = stateDirty;
            }
        }

        /**
         * isDirty
         *
         * @return mixed
         */
        function isDirty() {
            reset($this->_Buffer);
            while (list($key, $value) = each($this->_Buffer)) {
                debug(dbgData,2,"checking dirty flag on $key:", $value);
                if ($value["state"] == stateDirty) {
                    $dirtyAttrs[] = $key;
                }
            }

            debug(dbgData,2,"checked dirty flag; dirtyAttrs=",$dirtyAttrs);
            return (empty($dirtyAttrs)?false:$dirtyAttrs);
        }

        /**
         * Will fill field $attr in buffer with given $value and tries to calculate state of buffer
         *
         * @param $attr
         * @param $value
         * @param $state
         */
        function _prepareBuffer($attr, $value, $state="") {
            //do not overwrite dirty data, because this might overwrite data while saving
            //and asking for a not read attribute
            if ($this->_Buffer[$attr]["state"] != stateDirty) {
                debug(dbgData,3,sprintf("_prepareBuffer(%s,%s,%s)", $attr, $value, $state));
                if (empty($state)) {
                    $this->_Buffer[$attr] = array("state"=>empty($value)?stateUnknown:stateRead,
                                                "value"=>$value);
                }
                else {
                    $this->_Buffer[$attr] = array("state"=>$state, "value"=>$value);
                }
            }
            else {
                debug(dbgData,3,sprintf("skip _prepareBuffer(%s,%s,%s) because of dirty state", $attr, $value, $state));
            }
        }

        /**
         * This must be overridden from descendant to assign attributes to their data source 
         *
         * @param $attr
         * @see class.maildomain.php
         */
        function _readAttribute($attr) {
        }

        /**
         * Attr to ldif
         *
         * @param string $attr
         * @param string $value
         * @return string
         * @access private
         */
        function _attr2ldif($attr, $value) {  
            $regex = "^[[:alnum:]!-/\{\}][[:alnum:]!-/:-\?\{\}]*$";

            if (!ereg($regex, $value)) {
                //ldif allows more than that, but we'd like to keep the regex simple
                $commentlines = explode ("\n", $value);
                foreach ($commentlines as $line) {
                    if ($i++) {
                        $comment .= "# ".$line."\n";
                    }
                    else {
                        $comment = "#".$attr.": ".$line."\n";
                    }
                }

                $value = base64_encode($value);
                $attr = $attr.":";
            }

            $lines = explode ("\r\n", chunk_split($value));
            $ret = $attr.":";
            foreach ($lines as $line) {
                if (strlen($line)) {
                    $ret .= " ".$line."\n";
                }
            }

            return $comment.$ret;
        }

        /**
         * Write
         *
         * @param string $dn
         * @param array $entries
         * @param boolean $new
         * @return boolean
         * @access private
         */
        function _write($dn, $entries, $new=false) {
            //global $cfg_log;

            //deal with empty entries
            while (list($attr, $entry) = each($entries)) {
                if (!strlen($entries[$attr])) {
                    if ($new) {
                        debug (dbgData, 2, "_write new; remove empty entry $attr");
                        unset($entries[$attr]);
                    }
                    else {
                        debug (dbgData, 2, "_write; replace empty entry $attr with empty array");
                        $entries[$attr] = array();
                    }
                }
            }

            debug (dbgData, 2, "_write", array("dn"=>$dn,"entries"=>$entries,"new"=>$new));

            //write data
            if ($ldap = $this->_connect(true))
            {
                /* debug output
                echo '<div align="left"><b>'.$dn.' - new: '.$new.'</b>';
                echo "<pre>";
                print_r($entries);
                echo "</pre>";
                echo "</div>";
								*/
								
                if ($new)
                {
                    $result = ldap_add($ldap, $dn, $entries);
                }
                else {
                    $result = ldap_mod_replace($ldap, $dn, $entries);
                }

                if ($result)
                {
                    //clear dirtyState in Buffer
                    //we should move this to another place to split ldap from internal buffer
                    reset($this->_Buffer);
                    foreach(array_keys($this->_Buffer) as $attr)
                    {
                        if ($this->_Buffer[$attr]["state"] == stateDirty)
                        {
                            debug(dbgData,2,sprintf("reset dirty state on attr %s", $attr));
                            $this->_Buffer[$attr]["state"] = stateRead;
                        }
                    }
                    debug(dbgData,2,"_Buffer after resetting dirty state",$this->_Buffer);
                }
                else
                {
                  echo '<div align="left"><b>'.$dn.' - new: '.$new.'</b>';
                  echo "<pre>";
                  print_r($entries);
                  echo '</pre><br><br>';
                  echo 'LDAP-Error:<br>';
                  echo ldap_error($ldap);
                  echo '</div>';
                }
            }
            else $result = false;

            if (!$result) {
                debug (dbgData, 1, sprintf("error on _write: [%s] %s", ldap_errno($ldap), ldap_error($ldap)));
            }

            return $result;
        }

        /**
         * Delete
         *
         * @return boolean Success
         */
        function delete() {
            $dn = $this->getAttribute("dn");
            debug (dbgData,2,"delete $dn");

            if (!empty($dn)) {
                if ($ldap = $this->_connect(true)) {
                    $result = ldap_delete($ldap, $dn);
                }
                else $result = false;

                if (!$result) {
                    debug (dbgData, 1, sprintf("error on _write: [%s] %s", ldap_errno($ldap), ldap_error($ldap)));
                }

                return $result;
            }
        }

        /**
         * Get ldif
         *
         * @return string
         * @access private
         */
        function _getldif() {
            $dn = $this->getAttribute("dn");

            if (!empty($dn)) {
                $entries = $this->_getObject($dn);
                debug(dbgData, 2, "getldif", array("dn"=>$dn,"entries"=>$entries));

                $ldif = "#This is not yet a real LDIF formatted file, because there is no base64 encoding where neccessary\n";
                $ldif .= sprintf("dn: %s\n", $entries["dn"]);
                for($i=0; $i<$entries["count"]; $i++) {
                    $attrname=$entries[$i];
                    $attrs=$entries[$attrname];
                    if (is_array($attrs)) {
                        for ($j=0; $j<$attrs["count"]; $j++) {
                            $ldif .= sprintf("%s: %s\n", $attrname, $attrs[$j]);
                        }
                    }
                }
            }

            return $ldif;
        }

        /**
         * Recheck DN
         *
         * @param $typ
         * @param array $params
         * @access private
         */
        function _recheck_dn($typ, $params) {

            $newdn = $this->_create_dn($typ,$params["domain"],$params["id"]);
            $olddn = $this->getAttribute("dn");

            if (!empty($olddn) && !empty($newdn) && "$newdn"!="$olddn") {
                debug(dbgData,1,"_recheck_dn",array("newdn"=>$newdn,"olddn"=>$olddn));

                if ($ldap = $this->_connect(true)) {
                    $rdn = split(",", $newdn);
                    for($i=1; $i<count($rdn); $i++) {
                        $parentdn .= ($i>1)? ",".$rdn[$i]:$rdn[$i];
                    }

                    $res1= ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
                    $res = ldap_rename ($ldap, $olddn, $rdn[0], "", true)?"ok":"not ok";
                    debug(dbgData,1,sprintf("ldap_rename(%s,%s,%s,%s,true) returned %s; ldap_option returned %s", $ldap, $olddn, $rdn[0], "NULL", $res, $res1));
                }
            }
        }

        /**
         * Load buffer
         *
         * @access private
         */
        function _loadBuffer() {
            //we could fill the buffer here... not needed yet
        }

        /**
         * Get object
         *
         * @param string $dn
         * @return string
         * @access private
         */
        function _getObject($dn) {
            if ($ldap = $this->_connect(true)) {
                $sr = ldap_search($ldap, $dn, "(objectclass=*)");

                debug (dbgData, 2, sprintf("searched ldap; dn %s, count %d", $dn, ldap_count_entries($ldap, $sr)), $sr);

                if ($sr && ldap_count_entries($ldap, $sr)) {
                    $entries = ldap_get_entries($ldap, $sr);
                    $result = $entries[0];
                }
            }

            return($result);
        }

        /**
         * Lowlevel method to retrive the data
         *
         * @param string $typ
         * @param array $params
         * @param mixed $attrs
         * @param $sort
         * @return mixed
         */
        function _getRows($typ, $params, $attrs="", $sort="") {
            debug (dbgData, 2, "_getRows", array("typ"=>$typ,"params"=>$params,"attrs"=>$attrs));

            if ($ldap = $this->_connect(true)) {
                $searchSubtree = true;

                //create basedn in filter for requested typ and params
                switch ($typ) {
                    case "domain":
                        $basedn = $this->_create_dn("domain", $params["domain"]);
                        $filter = "(objectclass=CourierMailAccount)";
                        break;

                    case "account":
                        if (empty($params["domain"]) || empty($params["uid"]))
                            return false;

                        $basedn = $this->_create_dn("account", $params["domain"], $params["uid"]);
                        $filter = "(objectclass=CourierMailAccount)";
                        break;

                    case "maildrops":
                        $basedn = $this->_create_dn("maildrops");
                        if (!empty($params["mail"]))
                            $filter = "(&(objectclass=CourierMailAlias)(mail=".$params["mail"]."))";
                        else
                            $filter = "(&(objectclass=CourierMailAlias)(mail=*".$params["domain"]."))";
                        break;

                    case "server":
                        $basedn = $this->_create_dn("maildomains");
                        if (!empty($params["mail"])) {
                            if (is_array($params["mail"])) {
                                $filter = "(&(objectclass=CourierMailAccount)(|";

                                foreach ($params["mail"] as $mail) {
                                    $filter .= "(mail=".$mail.")";
                                }

                                $filter .= "))";
                            }
                            else {
                                $filter = "(&(objectclass=CourierMailAccount)(mail=".$params["mail"]."))";
                            }
                        }
                        else {
                            if (!empty($params["domain"])) {
                                $filter = "(&(objectclass=organizationalUnit)(ou=".$params["domain"]."))";
                            }
                            else {
                                $filter = "(objectclass=organizationalUnit)";
                            }

                            $searchSubtree = false;
                        }
                        break;

                    default:
                        die ("unknown datatype $typ");
                }

                if (!empty($params["filter"])) {
                    $filter = "(&".$filter."(|";
                    foreach($attrs as $attr) {
                        $filter .= "(".$attr."=".$params["filter"].")";
                    }
                    $filter .= "))";
                }

                //subtree searches to search for mail adresses and dn
                //list to list maildomains within ou=maildomain
                if (is_array($attrs)) {
                    if ($searchSubtree)
                    {
                    	$sr = @ldap_search($ldap, $basedn, $filter, $attrs);
                    }
                    else
                        $sr = ldap_list($ldap, $basedn, $filter, $attrs);
                }
                else {
                    if ($searchSubtree)
                    {
                        $sr = ldap_search($ldap, $basedn, $filter);
                    }
                    else
                        $sr = ldap_list($ldap, $basedn, $filter);
                }

                debug (dbgData, 2, sprintf("searched ldap; basedn %s, filter %s, count %d", $basedn, $filter, @ldap_count_entries($ldap, $sr)), $sr);

                if ($sr && @ldap_count_entries($ldap, $sr)) {
                    $result = ldap_get_entries($ldap, $sr);

                    //debug (dbgData, 2, "searched ldap; entries=", $result);

                    //remove count items
                    for ($i=0; $i<$result["count"]; $i++) {
                        $entry = &$result[$i];
                        for ($j=0; $j<$entry["count"]; $j++) {
                            unset($entry[$entry[$j]]["count"]);
                            unset($entry[$j]);
                        }
                        unset($entry["count"]);
                    }
                    unset($result["count"]);

                    //sort result on given sortfield
                    if (!empty($sort)) {
                        //global $_cmpRowsSort;
                        $_cmpRowsSort = $sort;
                        debug (dbgData, 2, "sort entries on $sort");

                        uasort($result, "_cmpRows");
                    }

                    debug (dbgData, 3, "searched ldap; return cleaned and sorted entries=", $result);

                }
                else { //no search result resource or result empty
                    $result = false;
                }
            }
            else { //no ldap connection
                $result = false;
            }

            return $result;
        }

        //==========================================================================
        // this is the rest of our old approach, we'd like to exchange this with a
        // class Auth extends Data
        // but for now... it works for authentication...

        /**
         * Get mailaccount data
         *
         * Searches for mailaccount and returns array of data
         *
         * @param array $search array("mail"=>mailadress)
         * @return array Array of data for account or false if not found or error
         */
        function getAccount($search) {
            debug (dbgData, 2, "getAccount", $search);
            if ($ldap = $this->_connect(false)) {
                //create basedn for ldapsearch as ou=<domain>,<basedn>
                $basedn = $this->_create_dn("maildomains");

                //create filter for ldapsearch; filter for objectclass
                $filter = "(&(objectclass=CourierMailAccount)(mail=".$search["mail"]."))";

                //search for account
                $sr = ldap_search($ldap, $basedn, $filter);

                debug (dbgData, 2, sprintf("searching LDAP for $filter from $basedn -> [%s] %s", ldap_errno($ldap), ldap_error($ldap)));
                if ($sr && ldap_count_entries($ldap, $sr)) {
                    $entries = ldap_get_entries($ldap, $sr);
                    debug (dbgData, 2, sprintf("get entry from LDAP -> [%s] %s", ldap_errno($ldap), ldap_error($ldap), $entries));
                    $account = $this->_ldap_entry2account($entries[0]);
                    $account["account"] = $search["mail"];
                    return $account;
                }
            }
            else {
                debug (dbgData, 2, "could not connect to LDAP");
            }

            return false;
        }

        /**
         * LDAP entry to account
         *
         * @param $entry
         * @return array
         * @access private
         */
        function _ldap_entry2account($entry) {
            //extract uid and domain from dn
            if (ereg("uid=([^,]+),ou=([^,]+)", $entry["dn"], $reg)) {
                $uid=$reg[1];
                $domain=$reg[2];
            }

            //create result array
            $result = array (
                "domain" => $domain,
                "level" => 0,
                "uid" => $uid,
                "cn" => utf8_decode($entry["cn"][0]),
                "userPassword" => $entry["userpassword"][0],
                "homeDirectory" => $entry["homedirectory"][0],
                "level" => $entry["pbaccesslevel"][0]
            );

            debug(dbgData, 2, "read attributes, result", $result);

            //extract add attributes from pbWebMAUI
/*
            debug(dbgData, 2, "read add attributes, pbWebMAUI", array("pbWebMAUI"=>$entry["pbwebmaui"]));
            if (is_array($entry["pbwebmaui"])) {
                $addattrs = $entry["pbwebmaui"];
                for ($i=0; $i<$addattrs["count"]; $i++) {
                    if (list($key,$value) = split(":", $addattrs[$i], 2)) {
                        if (strtolower($key) == "accesslevel")
                            $result["level"] = $value;
                    };
                }
            }
*/

            //split mailaccount and maildomain for each mailaddress
            for ($i=0; $i<$entry["mail"]["count"]; $i++) {
                if (ereg("([^,]+)@([^,]+)", $entry["mail"][$i], $reg)) {
                    $mail=$reg[1];
                    $maildomain=$reg[2];
                }
                else {
                    $mail=$entry["mail"][$i];
                    $maildomain="";
                }

                $result["mail"][$i] = array("address"=>$mail,"domain"=>$maildomain);

                while (is_array($maildrops) && (list($key,$value) = each($maildrops))) {
                }
            }

            return ($result);
        }
    }
?>
