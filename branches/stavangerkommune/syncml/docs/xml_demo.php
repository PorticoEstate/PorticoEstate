<?php
include "xml_parser.php";

$raw_xml_data = file_get_contents("pkg1_client.xml");

$synchdr = parse_xml(
	$raw_xml_data,
	new xml_offset_mapper(array("SYNCML", "SYNCHDR")));

printf("Message %d from %s\n",
	$synchdr["MSGID"][0][SYNCML_XML_DATA],
	$synchdr["SOURCE"][0]["LOCURI"][0][SYNCML_XML_DATA]);
	
unset($synchdr);

$syncbody = parse_xml(
	$raw_xml_data,
	new xml_offset_mapper(array("SYNCML", "SYNCBODY")));

foreach($syncbody[SYNCML_XML_ORIGINAL_ORDER] as $command) {
	if($command[SYNCML_XML_TAG_NAME] != "FINAL")
		printf("Command ID %d (%s)\n",
			$command["CMDID"][0][SYNCML_XML_DATA],
			$command[SYNCML_XML_TAG_NAME]);
}
?>
