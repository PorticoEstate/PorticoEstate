<?php
/*
* Filename.......: class_xml.php
* Author.........: Troy Wolf [troy@troywolf.com] with major sections ripped from
                   Paul Rose.
* Last Modified..: Date: 2006/03/06 15:30:00
* Description....: XML parsing class.
                   Modified to replace ":" in object names with "_". This was to
                   support Exchange WebDAV stuff.
*/
class xml {
  var $log;
  var $data;
  var $parser;
  var $stack;
  var $index;
  
  /*
  The class constructor. Configure defaults.
  */
  function xml() {
    $this->log = "New xml() object instantiated.<br />\n";
  }

  function fetch($raw_xml) {
    $this->log .= "fetch() called.<br />\n";
    $this->index = 0;
    $this->data = null;
    $this->stack = array();
    $this->stack[] = &$this->data;
    $this->parser = xml_parser_create ("UTF-8");
    xml_set_object($this->parser,$this);
    xml_set_element_handler($this->parser, "tag_open", "tag_close");
    xml_set_character_data_handler($this->parser, "cdata");
    xml_parser_set_option($this->parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
    if (!$parsed_xml = xml_parse($this->parser,$raw_xml, true )) {
      $this->log .= sprintf("XML error: %s at line %d",
        xml_error_string(xml_get_error_code($this->parser)),
        xml_get_current_line_number($this->parser));
      return false;
    }
    xml_parser_free($this->parser);
    return true;
  }

  function tag_open($parser, $tag, $attrs) {
    $tag = str_replace("-", "_", $tag);
    $tag = str_replace(":", "_", $tag);
    
    foreach($attrs as $key => $val) {
      $key = str_replace("-", "_", $key);
      $key = str_replace(":", "_", $key);
      $value = $this->clean($val);
      $object->_attr->$key = $value;
    }
    $temp = &$this->stack[$this->index]->$tag;
    $temp[] = &$object; // push $object onto $tag
    $size = sizeof($temp);
    $this->stack[] = &$temp[$size-1];
    $this->index++;
  }

  function tag_close($parser, $tag) {
    array_pop($this->stack);
    $this->index--;
  }
  
  function cdata($parser, $data) {
    if(trim($data)){
        $this->stack[$this->index]->_text .= $data;
    }
  }

  function clean($string){
    return utf8_decode(trim($string));
  }
}
?>
