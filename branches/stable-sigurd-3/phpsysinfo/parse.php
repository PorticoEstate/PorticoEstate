<?php
require("./includes/common_functions.php");

$parser = new ioreg_parse( rfts( "ioreg-with.txt" ) );
$parser->parse();

class ioreg_parse {

    var $strIoreg_input;
    var $XPath;
    var $intPos = 0;
    var $last = array();
    var $posi = 0;
    
    function ioreg_parse( $strInput ) {
        $this->XPath = new DOMDocument('1.0', 'iso-8859-1');
        $this->strIoreg_input = split( "\n", $strInput );
    }
    
    function detailtonode( $arrDetails ) {
        $element = $this->last[$this->posi-1][1];
        foreach( $arrDetails as $strLine ) {
    	    preg_match( '/(.*)"(.*)" = (.*)/', $strLine, $arrMatches );

	    if(count($arrMatches) != 4) { 
	        return;
	    }
	    
	    $enew = $this->XPath->createElement( "property" );
	    $enew->setAttributeNode( new DOMAttr( "name", $arrMatches[2] ) );
	    $enew->appendChild( $this->XPath->createCDATASection( $arrMatches[3] ) );
	    $element->appendChild($enew);
	}
    }
    
    function parse() {
	$this->parsechild( $this->strIoreg_input, "+-o" );
	header("Content-Type: text/xml\n\n");
	print $this->XPath->savexml();
    }

    function parsechild( $arrLines, $strParent ) {
	$booInode = false;
	$arrDetail = array();
	$strNode = "";
	
	do {
	    $strLine = $arrLines[$this->intPos];
	    $this->intPos++;

	    if( preg_match( '/(.*)\+-o(.*)/', $strLine, $arrMatches ) ) {
		preg_match( '/(.*)<(.*)>/', $arrMatches[2], $arrMatchesLine );
		$strNode = trim( $arrMatchesLine[1] );

		preg_match( '/(.*)\+-o(.*)/', $strParent, $arrParentmatches );

		$a = strlen( $arrMatches[1] );
		$b = strlen( $arrParentmatches[1] );
		$r = array();

		if( $a - $b < 0 ) {
		    $this->posi = count( $this->last ) - 1 - ( $b -$a ) / 2;
		} elseif( $a > 0 && $a == $b) {
		    $this->posi = count( $this->last ) - 1;
		}

		for($i=0;$i<$this->posi;$i++) {
		    $r[] = array( $this->last[$i][0], $this->last[$i][1]);
		}
		$this->last = $r;
		$this->last[$this->posi][0] = $strLine;
		$this->posi++;

		$this->addnode();

		$this->parsechild( $arrLines, $this->last[$this->posi-1][0] );
		$this->parsechild( $arrLines, $this->last[$this->posi-1][0] );

	    } else {
	        if( preg_match( '/([ |])+}$/', $strLine ) ) {
	    	    $booInode = false;
		}
		if( $booInode ) {
	    	    $arrDetail[] = $strLine;
		} 
		if( preg_match( '/([ |])+{$/', $strLine ) ) {
	    	    $booInode = true;
		}
		if( ! $booInode) {
		    $this->detailtonode( $arrDetail );
		    $this->intPos++;
		    return;
		}
	    }
	} while( $this->intPos < count( $arrLines ) );
    }
    
    function addnode() {
        if( isset( $this->last[$this->posi - 2][0] ) ) {
            preg_match( '/(.*)\+-o(.*)/', $this->last[$this->posi-2][0], $arrMatches );
    	    preg_match( '/(.*)<(.*)>/', $arrMatches[2], $arrMatchesLine );
    	    $strQuery = "//*[@name='" . trim( $arrMatchesLine[1] ) . "']";
	    $xpath = new DOMXPath($this->XPath);
	    $parent = $xpath->query($strQuery)->item(0);
        } else {
	    $parent = $this->XPath;
	}

	preg_match( '/(.*)\+-o(.*)/', $this->last[$this->posi-1][0], $arrMatches );
	preg_match( '/(.*)<(.*)>/', $arrMatches[2], $arrMatchesLine );
        $strNode = trim( $arrMatchesLine[1] );

	$element = $this->XPath->createElement("device");
	$element->setAttributeNode( new DOMAttr( "name", $strNode) );
	$element->appendChild( $this->XPath->createCDATASection( $arrMatchesLine[2] ) );
	
	$this->last[$this->posi-1][1] = $parent->appendChild($element);
    }
}

?>
