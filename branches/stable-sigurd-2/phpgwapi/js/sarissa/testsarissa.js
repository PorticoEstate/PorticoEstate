/**
 * ====================================================================
 * About
 * ====================================================================
 * Sarissa cross browser XML library - unit tests
 * @version 0.9.6.1
 * @author: Manos Batsis, mailto: mbatsis at users full stop sourceforge full stop net
 *
 * This module contains unit tests for Sarissa that run using EcmaUnit by Guido Wesdorp and
 * Philipp von Weitershausen, see http http://kupu.oscom.org/download/ecmaunit-0.3.html
 * Thanks for the great work guys!
 *
 * ====================================================================
 * Licence
 * ====================================================================
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2 or
 * the GNU Lesser General Public License version 2.1 as published by
 * the Free Software Foundation (your choice of the two).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License or GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * or GNU Lesser General Public License along with this program; if not,
 * write to the Free Software Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
 * or visit http://www.gnu.org
 *
 */

/** @constructor */
function SarissaTestCase() {
    /** @final */
    this.name = 'SarissaTestCase';
    
    /** Test the <code>Sarissa.getDomDocument()</code> method */
    this.testGetEmptyDomDocument = function(){
        this.assert(Sarissa.getDomDocument());
    };
    
    /** Test the <code>Sarissa.getDomDocument()</code> method */
    this.testGetDomDocument = function(){
        this.assert(Sarissa.getDomDocument("http://foo.bar/","foo", null));
    };
    
    
    /** Test the <code>Sarissa.serialize()</code> method */
    this.testSerialize = function(){
        var oDom = Sarissa.getDomDocument("","foo", null);
        this.assert(Sarissa.serialize(oDom));
    };

    /** Test the <code>Sarissa.stripTags()</code> method */
    this.testStripTags = function(){
        this.assertEquals(Sarissa.stripTags("<root>this<s> could</s> be <a>wron</a>g</root>"), "this could be wrong");
    };

    /** Test the <code>Sarissa.getParseErrorText()</code> method when there is no parsing error */
    this.testGetParseErrorTextNoError = function(){
        var oDom = Sarissa.getDomDocument("","foo", null);
        this.assertEquals(Sarissa.getParseErrorText(oDom), Sarissa.PARSED_OK);
    };
    
    /** Test the <code>Sarissa.getParseErrorText()</code> */
    this.testGetParseErrorTextError = function(){;
        var oDom = Sarissa.getDomDocument("","foo", null);
        oDom.async = false;
        oDom.load("test-bad.xml");
        document.getElementById("parseError").appendChild(document.createTextNode(Sarissa.getParseErrorText(oDom)));
    this.assert(Sarissa.getParseErrorText(oDom));
    };
    
    /** Test the <code>Sarissa.copyChildNodes()</code> method */
    this.testCopyChildNodes = function(){
        var from = Sarissa.getDomDocument("","foo", null);
        from.documentElement.appendChild(from.createElement("elementName"));
        var to = Sarissa.getDomDocument("","bar", null);
        Sarissa.copyChildNodes(from, to);
        this.assertEquals(from.documentElement.tagName, to.documentElement.tagName);
    };
    
    /** Test the <code>Sarissa.moveChildNodes()</code> method */
    this.testMoveChildNodes = function(){
        var from = Sarissa.getDomDocument("","root", null);
        for(i=0;i<4;i++){
            from.documentElement.appendChild(from.createElement("elem"));
        };
        var to = Sarissa.getDomDocument("","bar", null);
        Sarissa.moveChildNodes(from.documentElement, to.documentElement);
        this.assertEquals(to.getElementsByTagName("elem").length, 4);
        this.assertEquals(from.getElementsByTagName("elem").length, 0);
    };
    
    /** Test the <code>Sarissa.clearChildNodes()</code> method */
    this.testClearChildNodes = function(){
        var from = Sarissa.getDomDocument("","foo", null);
        Sarissa.clearChildNodes(from);
    this.assertEquals(from.childNodes.length, 0);
    };

    /** Test the <code>Sarissa.getText()</code>*/ 
    this.testGetTextWithCdata = function(){
        var oDom = (new DOMParser()).parseFromString("<root xml:space='preserve'>This t<elem>ext has</elem> <![CDATA[ CDATA ]]>in<elem /> it</root>", "text/xml");
        this.assertEquals(Sarissa.getText(oDom.documentElement, true), "This text has  CDATA in it");
        this.assertEquals(Sarissa.getText(oDom, true), "This text has  CDATA in it");
    };

    /** Test the <code>Sarissa.getParseErrorText()</code> */
    this.testGetText = function(){
        var oDom = (new DOMParser()).parseFromString("<root xml:space='preserve'>This t<elem>ext has </elem>no CDATA in<elem /> it</root>", "text/xml");
        this.assertEquals(Sarissa.getText(oDom.documentElement, true), "This text has no CDATA in it");
        this.assertEquals(Sarissa.getText(oDom, true), "This text has no CDATA in it");
    };
    
    this.testXmlize = function(){
        var book = new Object();
        book.chapters = new Array();
        book.chapters[0] = "Kingdom of fools";
        book.chapters[1] = "Fall";
        book.chapters[2] = "Final battle";
        book.chapters[3] = "Characters that need to be escaped: << << \"' \"\"\"&&'' < > & ' \" ";
        book.chapters[4] = "Epilogue";
        book.editor = "Manos Batsis";
        var publisher = new Object();
        publisher.name = "Some Publisher";
        book.publisher = publisher;

        var s = Sarissa.xmlize(book, "book");
        document.getElementById("xmlization").appendChild(document.createTextNode(s));
    };
};
SarissaTestCase.prototype = new TestCase;

/** @constructor */
function XMLHttpRequestTestCase(){
    /** @final */
    this.name = 'XmlHttpRequestTestCase';

    /** Test the XMLHttpRequest constructor exists (natively or not) */
    this.test = function(){
        this.assert(new XMLHttpRequest());
    };
};
XMLHttpRequestTestCase.prototype = new TestCase;

/** @constructor */
function XMLSerializerTestCase(){
    /** @final */
    this.name = 'XMLSerializerTestCase';

    /** Test the serializeToString method */
    this.testSerializeToString = function(){
        var serializer = new XMLSerializer();
        var oDoc = Sarissa.getDomDocument("","foo", null);
        // TODO: validate with a regexp 
        this.assert(serializer.serializeToString(oDoc));
    };
};
XMLSerializerTestCase.prototype = new TestCase;


/** @constructor */
function DOMParserTestCase(){
    /** @final */
    this.name = 'DOMParserTestCase';

    /** Test the serializeToString method */
    this.testParseFromString = function(){
        var parser = new DOMParser();
        var oDoc = parser.parseFromString("<root />", "text/xml");
        // TODO: validate with a regexp 
        this.assertEquals(oDoc.documentElement.tagName, "root");
    };
};
DOMParserTestCase.prototype = new TestCase;

/** Test the <code>XMLDocument.selectNodes()</code> method */
testSelectNodes = function() {
    this.xmlDoc = (new DOMParser()).parseFromString("<root/>");
    var nodeList = this.xmlDoc.selectNodes("*");
    this.assertEquals(nodeList.length, 1);
    this.assertEquals(nodeList.item(0), nodeList[0]);
};

/** Test the <code>XMLDocument.selectSingleNode()</code> method */
testSelectSingleNode = function() {
    this.xmlDoc = Sarissa.getDomDocument("", "root", null);
    var node = this.xmlDoc.selectSingleNode("*");
    this.assert(node);
    this.assertEquals(node.tagName, "root");
};

var isXmlDocumentAsyncLoadOK = false;
/** @constructor */
function XMLDocumentTestCase() {
    if(window.XMLDocument){
       /** @final */
       this.name = 'XMLDocumentTestCase';
       
       this.xmlDoc = null;
       
       this.setUp = function() {
            this.xmlDoc = Sarissa.getDomDocument();
       };
        
        
        /** Test the <code>XMLDocument.load()</code> method */
        this.testLoad = function() {
            this.xmlDoc.async = false;
            this.xmlDoc.load("test.xml");
            this.assertEquals(this.xmlDoc.documentElement.tagName, "root");
        };

        if(Sarissa.IS_ENABLED_SELECT_NODES){
            /** Test the <code>XMLDocument.selectNodes()</code> method */
            this.testSelectNodes = testSelectNodes
            
            /** Test the <code>XMLDocument.selectSingleNode()</code> method */
            this.testSelectSingleNode = testSelectSingleNode;
        };
    };
    
};
XMLDocumentTestCase.prototype = new TestCase;


/** @constructor */
function XMLElementTestCase() {
    if(window.XMLElement){
       /** @final */
       this.name = 'XMLElementTestCase';
       
       this.xmlDoc = null;
       
       this.setUp = function() {
            this.xmlDoc = Sarissa.getDomDocument();
       };
    
        if(Sarissa.IS_ENABLED_SELECT_NODES){
            /** Test the <code>XMLElement.selectNodes()</code> method */
            this.testSelectNodes = testSelectNodes
            
            /** Test the <code>XMLElement.selectSingleNode()</code> method */
            this.testSelectSingleNode = testSelectSingleNode;
        };
    };
};
XMLElementTestCase.prototype = new TestCase;
