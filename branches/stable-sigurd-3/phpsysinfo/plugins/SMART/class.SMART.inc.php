<?php 
/**
 * SMART Plugin
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_Plugin_SMART
 * @author    Antoine Bertin <diaoulael@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id$
 * @link      http://phpsysinfo.sourceforge.net
 */
 
 /**
 * SMART plugin, which displays all SMART informations available
 *
 * @category  PHP
 * @package   PSI_Plugin_SMART
 * @author    Antoine Bertin <diaoulael@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
class SMART extends PSI_Plugin
{
    /**
     * variable, which holds the content of the command
     * @var array
     */
    private $_filecontent = array();
    
    /**
     * variable, which holds the result before the xml is generated out of this array
     * @var array
     */
    private $_result = array();
    
    /**
     * variable, which holds PSI_PLUGIN_SMART_IDS well formated datas
     * @var array
     */
    private $_ids = array();
    
    /**
     * read the data into an internal array and also call the parent constructor
     *
     * @param String $enc target encoding
     */
    public function __construct($enc)
    {
        parent::__construct(__CLASS__, $enc);
        switch (PSI_PLUGIN_SMART_ACCESS) {
        case 'command':
            $disks = preg_split('/([\s]+)?,([\s]+)?/', PSI_PLUGIN_SMART_DEVICES, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($disks as $disk) {
                $buffer = "";
                if (CommonFunctions::executeProgram('smartctl', '--all'.((PSI_PLUGIN_SMART_DEVICE) ? ' --device '.PSI_PLUGIN_SMART_DEVICE : '').' '.$disk, $buffer, PSI_DEBUG)) {
                    $this->_filecontent[$disk] = $buffer;
                }
            }
            $fullIds = preg_split('/,/', PSI_PLUGIN_SMART_IDS, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($fullIds as $fullId) {
                $arrFullId = preg_split('/-/', $fullId);
                $this->_ids[intval($arrFullId[0])] = strtolower($arrFullId[1]);
            }
            break;
        default:
            $this->global_error->addError("switch(PSI_PLUGIN_SMART_ACCESS)", "Bad SMART configuration in SMART.config.php");
            break;
        }
    }
    
    /**
     * doing all tasks to get the required informations that the plugin needs
     * result is stored in an internal array
     *
     * @return void
     */
    public function execute()
    {
        if ( empty($this->_filecontent) || empty($this->_ids)) {
            return;
        }
        foreach ($this->_filecontent as $disk=>$result) {
            $newIds = array();
            preg_match('/Vendor Specific SMART Attributes with Thresholds\:\n(.*)\n((.|\n)*)\n\nSMART Error Log Version\:/', $result, $vendorInfos);
            $labels = preg_split('/[\s]+/', $vendorInfos[1]);
            foreach ($labels as $k=>$v) {
                $labels[$k] = str_replace('#', '', strtolower($v));
            }
            $lines = preg_split('/\n/', $vendorInfos[2]);
            $i = 0; // Line number
            foreach ($lines as $line) {
                $line = preg_replace('/^[\s]+/', '', $line);
                $values = preg_split('/[\s]+/', $line);
                if (count($values) > count($labels)) {
                    $values = array_slice($values, 0, count($labels), true);
                }
                $j = 0;
                $found = false;
                foreach ($values as $value) {
                    if (((in_array($value, array_keys($this->_ids)) && $labels[$j] == 'id') || ($found && (in_array($labels[$j], array_values($this->_ids)))) || ($found && $labels[$j] == 'attribute_name'))) {
                        $this->_result[$disk][$i][$labels[$j]] = $value;
                        if ($labels[$j] == 'id') {
                            $newIds[$value] = $this->_ids[$value];
                        }
                        $found = true;
                    }
                    $j++;
                }
                $i++;
            }
            $this->_ids = $newIds;
        }
    }
    
    /**
     * generates the XML content for the plugin
     *
     * @return SimpleXMLObject entire XML content for the plugin
     */
    public function xml()
    {
        if ( empty($this->_result) || empty($this->_ids)) {
            return $this->xml->getSimpleXmlElement();
        }
        
        $columnsChild = $this->xml->addChild('columns');
        // Fill the xml with preferences
        foreach ($this->_ids as $id=>$column_name) {
            $columnChild = $columnsChild->addChild('column');
            $columnChild->addAttribute('id', $id);
            $columnChild->addAttribute('name', $column_name);
        }
        
        $disksChild = $this->xml->addChild('disks');
        // Now fill the xml with S.M.A.R.T datas
        foreach ($this->_result as $diskName=>$diskInfos) {
            $diskChild = $disksChild->addChild('disk');
            $diskChild->addAttribute('name', $diskName);
            foreach ($diskInfos as $lineInfos) {
                $lineChild = $diskChild->addChild('attribute');
                foreach ($lineInfos as $label=>$value) {
                    $lineChild->addAttribute($label, $value);
                }
            }
        }
        return $this->xml->getSimpleXmlElement();
    }
}
?>
