<!--
 * FCKeditor - The text editor for internet
 * Copyright (C) 2003-2004 Frederico Caldeira Knabben
 * 
 * Licensed under the terms of the GNU Lesser General Public License:
 * 		http://www.opensource.org/licenses/lgpl-license.php
 * 
 * For further information visit:
 * 		http://www.fckeditor.net/
 * 
 * File Name: util.asp
 * 	This file include generic functions used by the ASP Connector.
 * 
 * Version:  2.0 RC3
 * Modified: 2005-02-11 16:00:27
 * 
 * File Authors:
 * 		Frederico Caldeira Knabben (fredck@fckeditor.net)
-->
<%
Function RemoveFromStart( sourceString, charToRemove )
	Dim oRegex
	Set oRegex = New RegExp
	oRegex.Pattern = "^" & charToRemove & "+"

	RemoveFromStart = oRegex.Replace( sourceString, "" )
End Function

Function RemoveFromEnd( sourceString, charToRemove )
	Dim oRegex
	Set oRegex = New RegExp
	oRegex.Pattern = charToRemove & "+$"

	RemoveFromEnd = oRegex.Replace( sourceString, "" )
End Function

Function ConvertToXmlAttribute( value )
	ConvertToXmlAttribute = Replace( value, "&", "&amp;" )
End Function

Function InArray( value, sourceArray )
	Dim i
	For i = 0 to UBound( sourceArray )
		If sourceArray(i) = value Then
			InArray = True
			Exit Function
		End If
	Next
	InArray = False
End Function

%>