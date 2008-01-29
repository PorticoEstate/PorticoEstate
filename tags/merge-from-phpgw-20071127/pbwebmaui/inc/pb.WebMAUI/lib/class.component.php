<?php
/**
 * pbWebMAUI
 * @author Andreas Schiller <aschiller@probusiness.de>
 * @copyright Copyright (C) 2002-2003 probusiness AG
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package pbWebMAUI
 */

    /**
     * IT include file
     * @see IT
     */
    require_once "IT.php";
    require_once "PEAR.php";
    require_once "Mail.php";
    /**
     * debug include file
     * @see debug
     */
    require_once "debug.inc.php";
    /**
     * dialog include file
     * @see dialog
     */
    require_once "class.dialog.php";


    /**
     * Component
     * @package pbWebMAUI
     */
    class Component {
        /**
         * Holds the template
         *
         * @var string $_template 
         * @access private
         */
        var $_template;                

        /**
         * Application
         *
         * @var string $_application 
         * @access private
         */
        var $_application;            

        /**
         * Parent showing this component
         *
         * @var string $_parent 
         * @access private
         */
        var $_parent;                   


        /**
         * Name of
         *
         * @var string $_name
         * @access private
         */
        var $_name;

        /**
         * Array of children
         *
         * @var string $_children
         * @access private
         */
        var $_children; 

        /**
         * Name of block (template) to fill while preparing and to output
         *
         * @var string $_blockName
         * @access private
         */
        var $_blockName;                 

        /**
         * OnShow
         *
         * @var string $_OnShow
         * @access private
         */
        var $_OnShow;

        /**
         * Constructor
         *
         * @param $application
         * @param $parent
         * @param $name
         * @param $templatefile
         */
        function Component(&$application, &$parent, $name, $templatefile) {
            debug(dbgComponent, 3, "construct component",array("name"=>$name,"template"=>$templatefile));
            $this->setApplication($application);
            $this->setName($name);
            $this->setParent($parent);

            $this->setBlockName("Content");

            $this->_template = new HTML_Template_IT ($application->TemplateDirectory);

            if (strlen($templatefile)) { //should components wizhout template file really exist?
                if (file_exists($application->TemplateDirectory."/".$application->Style."/".$templatefile)) {
                    if (!$this->_template->loadTemplatefile($application->Style."/".$templatefile, true, true)) {
                        echo get_debug();
                        die("error loading template (style=".$application->Style.", file=".$templatefile.")");
                    }
                }
                else {
                    if (!$this->_template->loadTemplatefile("default/".$templatefile, true, true)) {
                        die("error loading default template (file=".$templatefile.")");
                    }
                }
            }
        }

        /**
         * Property application
         *
         * @param $value
         */
        function setApplication(&$value) {
            $this->_application =& Application::getInstance(null);
        }


        /**
         * Property application
         *
         * @param $value
         * @return object Application
         */
        function &getApplication() {
            return $this->_application;
        }

        /**
         * Property parent
         *
         * @param $value
         */
        function setParent(&$value) {
            $this->_parent = &$value;
            if (!empty($value)) {
                $value->addChild($this);
            }
        }

        /**
         * Get parent
         *
         * @return mixed Parent object or false
         */
        function getParent() {
            if (empty($this->_parent))
                return false;
            else return $this->_parent;
        }

        /**
         * Property name
         *
         * @param $value
         */
        function setName($value) {
            $this->_name = $value;
        }

        /**
         * Get name
         *
         * @return string Name
         */
        function getName() {
            return $this->_name;
        }

        /**
         * Property OnShow
         *
         * @param string $funcname
         */
        function setOnShow($funcname) {
            debug(dbgComponent,2,sprintf("setOnShow(%s) in class %s", $funcname, get_class($this)));
            $this->_OnShow = $funcname;
        }

        /**
         * Property Blockname
         *
         * @param $value
         */
        function setBlockName($value) {
            $this->_blockName = $value;
        }

        /**
         * Get blockname
         *
         * @return string Block name
         */
        function getBlockName() {
            return $this->_blockName;
        }

        /**
         * Adds a child component to this. is called from child component when setting parent
         *
         * @param $component
         */
        function addChild(&$component) {
            $this->_children[$component->getName()] = &$component;
        }

        /**
         * Parses this components template and returns the content of this components template or its given block
         *
         * @return string Content of this components template or its given block
         */
        function getContent() {
            $this->_template->parse($this->getBlockName());
            return $this->_template->get($this->getBlockName());
        }

        /**
         * Set template variable;
         * to call from descendants of this class instead of using $this->_template->setVariable
         *
         * @param mixed $placeholder
         * @param string $value value of variable to show
         */
        function setVariable($placeholder, $value="") {
            if (is_array($placeholder)) {
                $this->_template->setVariable($placeholder);
            }
            else {
                    $this->_template->setVariable($placeholder, $value);
            }
        }

        /**
         * Set current block
         *
         * @param $block
         */
        function setCurrentBlock($block) {
            $this->_template->setCurrentBlock($block);
        }

        /**
         * Parse block
         *
         * @param $block
         */
        function parse($block) {
            $this->_template->parse($block);
        }

        /**
         * Called at beginning of show(), override in your component
         */
        function OnPrepare() {
        }

        /**
         * Called at end of show(), befor output, override in your component
         */
        function OnShow() {
        }

        /**
         * Get content of children and possibile print it
         *
         * @param boolean $print
         * @return mixed
         */
        function show($print=true) {
            debug (dbgComponent, 1, sprintf("show %s, childcount=%d, print=%s, _OnShow=%s, class=%s",$this->getName() , count($this->_children), $print, $this->_OnShow, get_class($this)));
            $this->OnPrepare();

            //set block of template if required
            if ($this->getBlockName() <> "")
                $this->_template->setCurrentBlock($this->getBlockName());

            //show children, if any
            //this will be done by substituting their content into this templates corresponding variable
            if (!empty($this->_children)) {
                reset($this->_children);

                foreach(array_keys($this->_children) as $childname) {
                    $child = &$this->_children[$childname];

                    debug (dbgComponent, 1, sprintf("show child %s", $childname));
                    $this->_template->setVariable($childname, $child->show(false));
                }
            }

            if (isset($this->_OnShow)) {
                debug(dbgComponent, 1, "calling OnShow Event", array("Event"=>$this->_OnShow,
                                                                    "class"=>get_class($this),
                                                                    ));
                $f = $this->_OnShow;
                eval("$f(\$this);");
            }
            else {
                $this->OnShow();
            }

            $this->_template->touchBlock($this->getBlockName());

            debug (dbgApplication, 2, "after Component::show()", array("name"=>$this->_name, "getUsername"=>$this->_application->getUsername()) );
            
            //print content or return as string
            if ($print) {
                $this->_template->show();
                return true;
            }
            else {
                    return $this->getContent();
            }
        }
    }
?>
