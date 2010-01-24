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
     * Maildrop encapsules maildop data
     * @package pbWebMAUI
     */
    class Maildrop extends Data {
        /**
         * Filter
         *
         * @var string $_filter
         */
        var $_filter;

        /**
         * Constructor
         *
         * @param $application
         * @param $domain
         * @param $maildrop
         * @param $accounts
         * @param boolean $new
         */
        function Maildrop(&$application, $domain="", $maildrop="", $accounts="", $new=false) {
            $this->Data($application);

            //prepare buffer
            $this->_prepareBuffer("maildrop", $maildrop, $new?stateDirty:"");
            $this->_prepareBuffer("accounts", $accounts, $new?stateDirty:"");
            $this->_prepareBuffer("domain", $domain, $new?stateDirty:"");
            $this->_prepareBuffer("dn", "", $new?stateDirty:"");
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
         * Override method from class data
         *
         * @param string $attr
         */
        function _readAttribute($attr) {
            debug(dbgData, 3, "MailDrop->_readAttribute $attr");
            switch ($attr) {
                case "dn":
                case "accounts":
                case "changes":
                    if($accounts = $this->_getRows("maildrops",
                                                    array("mail"=>$this->_Buffer["maildrop"]["value"],
                                                          "filter"=>empty($this->_filter)?"":$this->_filter),
                                                    array("mail","maildrop","pbchanges","objectclass"),
                                                    "")) {

                        //there should be only one row
                        if (!empty($accounts[0]["maildrop"]))
                            sort($accounts[0]["maildrop"]); //sort values in attribute maildrop

                        //fill buffer
                        $this->_Buffer["accounts"]["value"] = $accounts[0]["maildrop"];
                        $this->_Buffer["dn"]["value"] = $accounts[0]["dn"];
                        debug(dbgData, 3, "_readAttribute $attr;", array("dn"=>$this->_Buffer["dn"]["value"], "accounts"=>$accounts));

                        //extract recent changes from pbChanges attributes
                        if (is_array($changes = $accounts[0]["pbchanges"])) {
                            foreach ($changes as $change) {
                                list($time, $user) = split (":", $change, 2);
                                $ar_changes[$time] = $user;
                            }

                            $this->_prepareBuffer("changes", $ar_changes, stateRead);
                        }
                    }

                    $this->_Buffer["accounts"]["state"] = stateRead;
                    $this->_Buffer["dn"]["state"] = stateRead;

                    break;

                case "domain":
                    //calculate domain from maildrop
                    $maildrop = $this->getAttribute("maildrop");
                    if (list($mail,$domain) = split("@", $maildrop, 2)) {
                        $this->_prepareBuffer("domain", $domain);
                    }
                    break;
            }
        }

        /**
         * save
         *
         * @param attrs
         */
        function save($attrs="") {
            if (empty($attrs)) {
                $attrs = $this->isDirty();
            }

            if ($attrs) {
                foreach($attrs as $attr) {
                    $value = $this->getAttribute($attr);
                    switch($attr) {
                        case "maildrop":
                            $entries["mail"] = $value;
                            $entries['sn']   = 'Maildrop';
                            $entries['cn']   = $value;
                            break;
                        case "accounts":
                            $entries["maildrop"] = $value;
                            break;
                    }
                }

                if (!empty($entries)) {
                    //create changes entry
                    $changes = $this->getAttribute("changes");
                    //$entries["pbChanges"][] = time() . ":" . $this->_application->getUsername();

                    for ($i=0; $i<2 && is_array($changes) && (list($time, $user) = each($changes)); $i++) {
                        //$entries["pbChanges"][] = $time . ":" . $user;
                    }
                }

                $dn = $this->getAttribute("dn");
                if (empty($dn)) {
                    $newdn = $this->_getNewDn("maildrop", array("id"=>$this->getAttribute("maildrop")));
                    $entries["objectClass"][]="CourierMailAlias";
                    $entries["objectClass"][]="person";
                    //$entries["objectClass"][]="pbWebMAUIPerson";
                    $this->_write($newdn, $entries, true);
                }
                else {
                    $entries["objectClass"][]="CourierMailAlias";
                    $entries["objectClass"][]="person";
                    //$entries["objectClass"][]="pbWebMAUIPerson";
                    $this->_write($dn, $entries);
                }

                $this->_recheck_dn("maildrop", array("id"=>$this->getAttribute("maildrop")));
            }
        }

        /**
         * remove any of the given mailadresses
         *
         * remove any of the given mailadresses from this maildrop, if one of them is
         * a target adress of this maildrop
         *
         * @param array $mails array of mailadresses to remove from this maildrop
         * @param boolean $save
         */
        function removetargets($mails, $save=true) {
            $mdtargets = $this->getAttribute("accounts");
            debug(dbgData,3,"remove targets from drop", array("maildrop"=>$this->getAttribute("maildrop"), "mdtargets"=>$mdtargets, "mails"=>$mails));

            $changed = false;
            for ($i=0; $i<count($mdtargets); $i++) {
                if (in_array($mdtargets[$i], $mails)) {
                    debug(dbgData,3,"unset target", array("mdtargets"=>$mdtargets, "i"=>$i, "mails"=>$mails));
                    unset($mdtargets[$i]);
                    $changed = true;
                }
            }

            if ($changed) {
                $this->setAttribute("accounts", array_values($mdtargets)); //use array_values to renumber
                debug(dbgData,3,"done set targets attribute", array("mdtargets"=>$mdtargets = $this->getAttribute("accounts")));
                if ($save)
                    $this->save();
            }
        }

        /**
         * exists
         *
         * @return boolean
         */
        function exists() {
            $dn = $this->getAttribute("dn");
            return (!empty($dn));
        }
    }
?>
