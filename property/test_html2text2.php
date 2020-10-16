<?php
	$GLOBALS['phpgw_info']['flags'] = array
		(
		'noheader'	 => true,
		'nonavbar'	 => true,
		'currentapp' => 'property'
	);

	include_once('../header.inc.php');

	$test = <<<HTML
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="ProgId" content="Word.Document">
<meta name="Generator" content="Microsoft Word 15">
<meta name="Originator" content="Microsoft Word 15">
<link rel="File-List" href="cid:filelist.xml@01D5A9CD.9B86CBF0"><!--[if gte mso 9]><xml>
<o:OfficeDocumentSettings>
<o:AllowPNG/>
</o:OfficeDocumentSettings>
</xml><![endif]--><link rel="themeData" href="~~themedata~~"><link rel="colorSchemeMapping" href="~~colorschememapping~~"><!--[if gte mso 9]><xml>
<w:WordDocument>
<w:View>Normal</w:View>
<w:DocumentKind>DocumentEmail</w:DocumentKind>
<w:TrackMoves/>
<w:TrackFormatting/>
<w:HyphenationZone>21</w:HyphenationZone>
<w:EnvelopeVis/>
<w:ValidateAgainstSchemas/>
<w:SaveIfXMLInvalid>false</w:SaveIfXMLInvalid>
<w:IgnoreMixedContent>false</w:IgnoreMixedContent>
<w:AlwaysShowPlaceholderText>false</w:AlwaysShowPlaceholderText>
<w:DoNotPromoteQF/>
<w:LidThemeOther>NO-BOK</w:LidThemeOther>
<w:LidThemeAsian>X-NONE</w:LidThemeAsian>
<w:LidThemeComplexScript>X-NONE</w:LidThemeComplexScript>
<w:Compatibility>
<w:DoNotExpandShiftReturn/>
<w:BreakWrappedTables/>
<w:SplitPgBreakAndParaMark/>
<w:EnableOpenTypeKerning/>
</w:Compatibility>
<m:mathPr>
<m:mathFont m:val="Cambria Math"/>
<m:brkBin m:val="before"/>
<m:brkBinSub m:val="&#45;-"/>
<m:smallFrac m:val="off"/>
<m:dispDef/>
<m:lMargin m:val="0"/>
<m:rMargin m:val="0"/>
<m:defJc m:val="centerGroup"/>
<m:wrapIndent m:val="1440"/>
<m:intLim m:val="subSup"/>
<m:naryLim m:val="undOvr"/>
</m:mathPr></w:WordDocument>
</xml><![endif]--><!--[if gte mso 9]><xml>
<w:LatentStyles DefLockedState="false" DefUnhideWhenUsed="false" DefSemiHidden="false" DefQFormat="false" DefPriority="99" LatentStyleCount="375">
<w:LsdException Locked="false" Priority="0" QFormat="true" Name="Normal"/>
<w:LsdException Locked="false" Priority="9" QFormat="true" Name="heading 1"/>
<w:LsdException Locked="false" Priority="9" SemiHidden="true" UnhideWhenUsed="true" QFormat="true" Name="heading 2"/>
<w:LsdException Locked="false" Priority="9" SemiHidden="true" UnhideWhenUsed="true" QFormat="true" Name="heading 3"/>
<w:LsdException Locked="false" Priority="9" SemiHidden="true" UnhideWhenUsed="true" QFormat="true" Name="heading 4"/>
<w:LsdException Locked="false" Priority="9" SemiHidden="true" UnhideWhenUsed="true" QFormat="true" Name="heading 5"/>
<w:LsdException Locked="false" Priority="9" SemiHidden="true" UnhideWhenUsed="true" QFormat="true" Name="heading 6"/>
<w:LsdException Locked="false" Priority="9" SemiHidden="true" UnhideWhenUsed="true" QFormat="true" Name="heading 7"/>
<w:LsdException Locked="false" Priority="9" SemiHidden="true" UnhideWhenUsed="true" QFormat="true" Name="heading 8"/>
<w:LsdException Locked="false" Priority="9" SemiHidden="true" UnhideWhenUsed="true" QFormat="true" Name="heading 9"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="index 1"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="index 2"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="index 3"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="index 4"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="index 5"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="index 6"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="index 7"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="index 8"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="index 9"/>
<w:LsdException Locked="false" Priority="39" SemiHidden="true" UnhideWhenUsed="true" Name="toc 1"/>
<w:LsdException Locked="false" Priority="39" SemiHidden="true" UnhideWhenUsed="true" Name="toc 2"/>
<w:LsdException Locked="false" Priority="39" SemiHidden="true" UnhideWhenUsed="true" Name="toc 3"/>
<w:LsdException Locked="false" Priority="39" SemiHidden="true" UnhideWhenUsed="true" Name="toc 4"/>
<w:LsdException Locked="false" Priority="39" SemiHidden="true" UnhideWhenUsed="true" Name="toc 5"/>
<w:LsdException Locked="false" Priority="39" SemiHidden="true" UnhideWhenUsed="true" Name="toc 6"/>
<w:LsdException Locked="false" Priority="39" SemiHidden="true" UnhideWhenUsed="true" Name="toc 7"/>
<w:LsdException Locked="false" Priority="39" SemiHidden="true" UnhideWhenUsed="true" Name="toc 8"/>
<w:LsdException Locked="false" Priority="39" SemiHidden="true" UnhideWhenUsed="true" Name="toc 9"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Normal Indent"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="footnote text"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="annotation text"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="header"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="footer"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="index heading"/>
<w:LsdException Locked="false" Priority="35" SemiHidden="true" UnhideWhenUsed="true" QFormat="true" Name="caption"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="table of figures"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="envelope address"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="envelope return"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="footnote reference"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="annotation reference"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="line number"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="page number"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="endnote reference"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="endnote text"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="table of authorities"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="macro"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="toa heading"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="List"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="List Bullet"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="List Number"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="List 2"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="List 3"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="List 4"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="List 5"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="List Bullet 2"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="List Bullet 3"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="List Bullet 4"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="List Bullet 5"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="List Number 2"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="List Number 3"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="List Number 4"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="List Number 5"/>
<w:LsdException Locked="false" Priority="10" QFormat="true" Name="Title"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Closing"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Signature"/>
<w:LsdException Locked="false" Priority="1" SemiHidden="true" UnhideWhenUsed="true" Name="Default Paragraph Font"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Body Text"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Body Text Indent"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="List Continue"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="List Continue 2"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="List Continue 3"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="List Continue 4"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="List Continue 5"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Message Header"/>
<w:LsdException Locked="false" Priority="11" QFormat="true" Name="Subtitle"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Salutation"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Date"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Body Text First Indent"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Body Text First Indent 2"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Note Heading"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Body Text 2"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Body Text 3"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Body Text Indent 2"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Body Text Indent 3"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Block Text"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Hyperlink"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="FollowedHyperlink"/>
<w:LsdException Locked="false" Priority="22" QFormat="true" Name="Strong"/>
<w:LsdException Locked="false" Priority="20" QFormat="true" Name="Emphasis"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Document Map"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Plain Text"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="E-mail Signature"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="HTML Top of Form"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="HTML Bottom of Form"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Normal (Web)"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="HTML Acronym"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="HTML Address"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="HTML Cite"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="HTML Code"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="HTML Definition"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="HTML Keyboard"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="HTML Preformatted"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="HTML Sample"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="HTML Typewriter"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="HTML Variable"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Normal Table"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="annotation subject"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="No List"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Outline List 1"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Outline List 2"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Outline List 3"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Table Simple 1"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Table Simple 2"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Table Simple 3"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Table Classic 1"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Table Classic 2"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Table Classic 3"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Table Classic 4"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Table Colorful 1"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Table Colorful 2"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Table Colorful 3"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Table Columns 1"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Table Columns 2"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Table Columns 3"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Table Columns 4"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Table Columns 5"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Table Grid 1"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Table Grid 2"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Table Grid 3"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Table Grid 4"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Table Grid 5"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Table Grid 6"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Table Grid 7"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Table Grid 8"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Table List 1"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Table List 2"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Table List 3"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Table List 4"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Table List 5"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Table List 6"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Table List 7"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Table List 8"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Table 3D effects 1"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Table 3D effects 2"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Table 3D effects 3"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Table Contemporary"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Table Elegant"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Table Professional"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Table Subtle 1"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Table Subtle 2"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Table Web 1"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Table Web 2"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Table Web 3"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Balloon Text"/>
<w:LsdException Locked="false" Priority="39" Name="Table Grid"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Table Theme"/>
<w:LsdException Locked="false" SemiHidden="true" Name="Placeholder Text"/>
<w:LsdException Locked="false" Priority="1" QFormat="true" Name="No Spacing"/>
<w:LsdException Locked="false" Priority="60" Name="Light Shading"/>
<w:LsdException Locked="false" Priority="61" Name="Light List"/>
<w:LsdException Locked="false" Priority="62" Name="Light Grid"/>
<w:LsdException Locked="false" Priority="63" Name="Medium Shading 1"/>
<w:LsdException Locked="false" Priority="64" Name="Medium Shading 2"/>
<w:LsdException Locked="false" Priority="65" Name="Medium List 1"/>
<w:LsdException Locked="false" Priority="66" Name="Medium List 2"/>
<w:LsdException Locked="false" Priority="67" Name="Medium Grid 1"/>
<w:LsdException Locked="false" Priority="68" Name="Medium Grid 2"/>
<w:LsdException Locked="false" Priority="69" Name="Medium Grid 3"/>
<w:LsdException Locked="false" Priority="70" Name="Dark List"/>
<w:LsdException Locked="false" Priority="71" Name="Colorful Shading"/>
<w:LsdException Locked="false" Priority="72" Name="Colorful List"/>
<w:LsdException Locked="false" Priority="73" Name="Colorful Grid"/>
<w:LsdException Locked="false" Priority="60" Name="Light Shading Accent 1"/>
<w:LsdException Locked="false" Priority="61" Name="Light List Accent 1"/>
<w:LsdException Locked="false" Priority="62" Name="Light Grid Accent 1"/>
<w:LsdException Locked="false" Priority="63" Name="Medium Shading 1 Accent 1"/>
<w:LsdException Locked="false" Priority="64" Name="Medium Shading 2 Accent 1"/>
<w:LsdException Locked="false" Priority="65" Name="Medium List 1 Accent 1"/>
<w:LsdException Locked="false" SemiHidden="true" Name="Revision"/>
<w:LsdException Locked="false" Priority="34" QFormat="true" Name="List Paragraph"/>
<w:LsdException Locked="false" Priority="29" QFormat="true" Name="Quote"/>
<w:LsdException Locked="false" Priority="30" QFormat="true" Name="Intense Quote"/>
<w:LsdException Locked="false" Priority="66" Name="Medium List 2 Accent 1"/>
<w:LsdException Locked="false" Priority="67" Name="Medium Grid 1 Accent 1"/>
<w:LsdException Locked="false" Priority="68" Name="Medium Grid 2 Accent 1"/>
<w:LsdException Locked="false" Priority="69" Name="Medium Grid 3 Accent 1"/>
<w:LsdException Locked="false" Priority="70" Name="Dark List Accent 1"/>
<w:LsdException Locked="false" Priority="71" Name="Colorful Shading Accent 1"/>
<w:LsdException Locked="false" Priority="72" Name="Colorful List Accent 1"/>
<w:LsdException Locked="false" Priority="73" Name="Colorful Grid Accent 1"/>
<w:LsdException Locked="false" Priority="60" Name="Light Shading Accent 2"/>
<w:LsdException Locked="false" Priority="61" Name="Light List Accent 2"/>
<w:LsdException Locked="false" Priority="62" Name="Light Grid Accent 2"/>
<w:LsdException Locked="false" Priority="63" Name="Medium Shading 1 Accent 2"/>
<w:LsdException Locked="false" Priority="64" Name="Medium Shading 2 Accent 2"/>
<w:LsdException Locked="false" Priority="65" Name="Medium List 1 Accent 2"/>
<w:LsdException Locked="false" Priority="66" Name="Medium List 2 Accent 2"/>
<w:LsdException Locked="false" Priority="67" Name="Medium Grid 1 Accent 2"/>
<w:LsdException Locked="false" Priority="68" Name="Medium Grid 2 Accent 2"/>
<w:LsdException Locked="false" Priority="69" Name="Medium Grid 3 Accent 2"/>
<w:LsdException Locked="false" Priority="70" Name="Dark List Accent 2"/>
<w:LsdException Locked="false" Priority="71" Name="Colorful Shading Accent 2"/>
<w:LsdException Locked="false" Priority="72" Name="Colorful List Accent 2"/>
<w:LsdException Locked="false" Priority="73" Name="Colorful Grid Accent 2"/>
<w:LsdException Locked="false" Priority="60" Name="Light Shading Accent 3"/>
<w:LsdException Locked="false" Priority="61" Name="Light List Accent 3"/>
<w:LsdException Locked="false" Priority="62" Name="Light Grid Accent 3"/>
<w:LsdException Locked="false" Priority="63" Name="Medium Shading 1 Accent 3"/>
<w:LsdException Locked="false" Priority="64" Name="Medium Shading 2 Accent 3"/>
<w:LsdException Locked="false" Priority="65" Name="Medium List 1 Accent 3"/>
<w:LsdException Locked="false" Priority="66" Name="Medium List 2 Accent 3"/>
<w:LsdException Locked="false" Priority="67" Name="Medium Grid 1 Accent 3"/>
<w:LsdException Locked="false" Priority="68" Name="Medium Grid 2 Accent 3"/>
<w:LsdException Locked="false" Priority="69" Name="Medium Grid 3 Accent 3"/>
<w:LsdException Locked="false" Priority="70" Name="Dark List Accent 3"/>
<w:LsdException Locked="false" Priority="71" Name="Colorful Shading Accent 3"/>
<w:LsdException Locked="false" Priority="72" Name="Colorful List Accent 3"/>
<w:LsdException Locked="false" Priority="73" Name="Colorful Grid Accent 3"/>
<w:LsdException Locked="false" Priority="60" Name="Light Shading Accent 4"/>
<w:LsdException Locked="false" Priority="61" Name="Light List Accent 4"/>
<w:LsdException Locked="false" Priority="62" Name="Light Grid Accent 4"/>
<w:LsdException Locked="false" Priority="63" Name="Medium Shading 1 Accent 4"/>
<w:LsdException Locked="false" Priority="64" Name="Medium Shading 2 Accent 4"/>
<w:LsdException Locked="false" Priority="65" Name="Medium List 1 Accent 4"/>
<w:LsdException Locked="false" Priority="66" Name="Medium List 2 Accent 4"/>
<w:LsdException Locked="false" Priority="67" Name="Medium Grid 1 Accent 4"/>
<w:LsdException Locked="false" Priority="68" Name="Medium Grid 2 Accent 4"/>
<w:LsdException Locked="false" Priority="69" Name="Medium Grid 3 Accent 4"/>
<w:LsdException Locked="false" Priority="70" Name="Dark List Accent 4"/>
<w:LsdException Locked="false" Priority="71" Name="Colorful Shading Accent 4"/>
<w:LsdException Locked="false" Priority="72" Name="Colorful List Accent 4"/>
<w:LsdException Locked="false" Priority="73" Name="Colorful Grid Accent 4"/>
<w:LsdException Locked="false" Priority="60" Name="Light Shading Accent 5"/>
<w:LsdException Locked="false" Priority="61" Name="Light List Accent 5"/>
<w:LsdException Locked="false" Priority="62" Name="Light Grid Accent 5"/>
<w:LsdException Locked="false" Priority="63" Name="Medium Shading 1 Accent 5"/>
<w:LsdException Locked="false" Priority="64" Name="Medium Shading 2 Accent 5"/>
<w:LsdException Locked="false" Priority="65" Name="Medium List 1 Accent 5"/>
<w:LsdException Locked="false" Priority="66" Name="Medium List 2 Accent 5"/>
<w:LsdException Locked="false" Priority="67" Name="Medium Grid 1 Accent 5"/>
<w:LsdException Locked="false" Priority="68" Name="Medium Grid 2 Accent 5"/>
<w:LsdException Locked="false" Priority="69" Name="Medium Grid 3 Accent 5"/>
<w:LsdException Locked="false" Priority="70" Name="Dark List Accent 5"/>
<w:LsdException Locked="false" Priority="71" Name="Colorful Shading Accent 5"/>
<w:LsdException Locked="false" Priority="72" Name="Colorful List Accent 5"/>
<w:LsdException Locked="false" Priority="73" Name="Colorful Grid Accent 5"/>
<w:LsdException Locked="false" Priority="60" Name="Light Shading Accent 6"/>
<w:LsdException Locked="false" Priority="61" Name="Light List Accent 6"/>
<w:LsdException Locked="false" Priority="62" Name="Light Grid Accent 6"/>
<w:LsdException Locked="false" Priority="63" Name="Medium Shading 1 Accent 6"/>
<w:LsdException Locked="false" Priority="64" Name="Medium Shading 2 Accent 6"/>
<w:LsdException Locked="false" Priority="65" Name="Medium List 1 Accent 6"/>
<w:LsdException Locked="false" Priority="66" Name="Medium List 2 Accent 6"/>
<w:LsdException Locked="false" Priority="67" Name="Medium Grid 1 Accent 6"/>
<w:LsdException Locked="false" Priority="68" Name="Medium Grid 2 Accent 6"/>
<w:LsdException Locked="false" Priority="69" Name="Medium Grid 3 Accent 6"/>
<w:LsdException Locked="false" Priority="70" Name="Dark List Accent 6"/>
<w:LsdException Locked="false" Priority="71" Name="Colorful Shading Accent 6"/>
<w:LsdException Locked="false" Priority="72" Name="Colorful List Accent 6"/>
<w:LsdException Locked="false" Priority="73" Name="Colorful Grid Accent 6"/>
<w:LsdException Locked="false" Priority="19" QFormat="true" Name="Subtle Emphasis"/>
<w:LsdException Locked="false" Priority="21" QFormat="true" Name="Intense Emphasis"/>
<w:LsdException Locked="false" Priority="31" QFormat="true" Name="Subtle Reference"/>
<w:LsdException Locked="false" Priority="32" QFormat="true" Name="Intense Reference"/>
<w:LsdException Locked="false" Priority="33" QFormat="true" Name="Book Title"/>
<w:LsdException Locked="false" Priority="37" SemiHidden="true" UnhideWhenUsed="true" Name="Bibliography"/>
<w:LsdException Locked="false" Priority="39" SemiHidden="true" UnhideWhenUsed="true" QFormat="true" Name="TOC Heading"/>
<w:LsdException Locked="false" Priority="41" Name="Plain Table 1"/>
<w:LsdException Locked="false" Priority="42" Name="Plain Table 2"/>
<w:LsdException Locked="false" Priority="43" Name="Plain Table 3"/>
<w:LsdException Locked="false" Priority="44" Name="Plain Table 4"/>
<w:LsdException Locked="false" Priority="45" Name="Plain Table 5"/>
<w:LsdException Locked="false" Priority="40" Name="Grid Table Light"/>
<w:LsdException Locked="false" Priority="46" Name="Grid Table 1 Light"/>
<w:LsdException Locked="false" Priority="47" Name="Grid Table 2"/>
<w:LsdException Locked="false" Priority="48" Name="Grid Table 3"/>
<w:LsdException Locked="false" Priority="49" Name="Grid Table 4"/>
<w:LsdException Locked="false" Priority="50" Name="Grid Table 5 Dark"/>
<w:LsdException Locked="false" Priority="51" Name="Grid Table 6 Colorful"/>
<w:LsdException Locked="false" Priority="52" Name="Grid Table 7 Colorful"/>
<w:LsdException Locked="false" Priority="46" Name="Grid Table 1 Light Accent 1"/>
<w:LsdException Locked="false" Priority="47" Name="Grid Table 2 Accent 1"/>
<w:LsdException Locked="false" Priority="48" Name="Grid Table 3 Accent 1"/>
<w:LsdException Locked="false" Priority="49" Name="Grid Table 4 Accent 1"/>
<w:LsdException Locked="false" Priority="50" Name="Grid Table 5 Dark Accent 1"/>
<w:LsdException Locked="false" Priority="51" Name="Grid Table 6 Colorful Accent 1"/>
<w:LsdException Locked="false" Priority="52" Name="Grid Table 7 Colorful Accent 1"/>
<w:LsdException Locked="false" Priority="46" Name="Grid Table 1 Light Accent 2"/>
<w:LsdException Locked="false" Priority="47" Name="Grid Table 2 Accent 2"/>
<w:LsdException Locked="false" Priority="48" Name="Grid Table 3 Accent 2"/>
<w:LsdException Locked="false" Priority="49" Name="Grid Table 4 Accent 2"/>
<w:LsdException Locked="false" Priority="50" Name="Grid Table 5 Dark Accent 2"/>
<w:LsdException Locked="false" Priority="51" Name="Grid Table 6 Colorful Accent 2"/>
<w:LsdException Locked="false" Priority="52" Name="Grid Table 7 Colorful Accent 2"/>
<w:LsdException Locked="false" Priority="46" Name="Grid Table 1 Light Accent 3"/>
<w:LsdException Locked="false" Priority="47" Name="Grid Table 2 Accent 3"/>
<w:LsdException Locked="false" Priority="48" Name="Grid Table 3 Accent 3"/>
<w:LsdException Locked="false" Priority="49" Name="Grid Table 4 Accent 3"/>
<w:LsdException Locked="false" Priority="50" Name="Grid Table 5 Dark Accent 3"/>
<w:LsdException Locked="false" Priority="51" Name="Grid Table 6 Colorful Accent 3"/>
<w:LsdException Locked="false" Priority="52" Name="Grid Table 7 Colorful Accent 3"/>
<w:LsdException Locked="false" Priority="46" Name="Grid Table 1 Light Accent 4"/>
<w:LsdException Locked="false" Priority="47" Name="Grid Table 2 Accent 4"/>
<w:LsdException Locked="false" Priority="48" Name="Grid Table 3 Accent 4"/>
<w:LsdException Locked="false" Priority="49" Name="Grid Table 4 Accent 4"/>
<w:LsdException Locked="false" Priority="50" Name="Grid Table 5 Dark Accent 4"/>
<w:LsdException Locked="false" Priority="51" Name="Grid Table 6 Colorful Accent 4"/>
<w:LsdException Locked="false" Priority="52" Name="Grid Table 7 Colorful Accent 4"/>
<w:LsdException Locked="false" Priority="46" Name="Grid Table 1 Light Accent 5"/>
<w:LsdException Locked="false" Priority="47" Name="Grid Table 2 Accent 5"/>
<w:LsdException Locked="false" Priority="48" Name="Grid Table 3 Accent 5"/>
<w:LsdException Locked="false" Priority="49" Name="Grid Table 4 Accent 5"/>
<w:LsdException Locked="false" Priority="50" Name="Grid Table 5 Dark Accent 5"/>
<w:LsdException Locked="false" Priority="51" Name="Grid Table 6 Colorful Accent 5"/>
<w:LsdException Locked="false" Priority="52" Name="Grid Table 7 Colorful Accent 5"/>
<w:LsdException Locked="false" Priority="46" Name="Grid Table 1 Light Accent 6"/>
<w:LsdException Locked="false" Priority="47" Name="Grid Table 2 Accent 6"/>
<w:LsdException Locked="false" Priority="48" Name="Grid Table 3 Accent 6"/>
<w:LsdException Locked="false" Priority="49" Name="Grid Table 4 Accent 6"/>
<w:LsdException Locked="false" Priority="50" Name="Grid Table 5 Dark Accent 6"/>
<w:LsdException Locked="false" Priority="51" Name="Grid Table 6 Colorful Accent 6"/>
<w:LsdException Locked="false" Priority="52" Name="Grid Table 7 Colorful Accent 6"/>
<w:LsdException Locked="false" Priority="46" Name="List Table 1 Light"/>
<w:LsdException Locked="false" Priority="47" Name="List Table 2"/>
<w:LsdException Locked="false" Priority="48" Name="List Table 3"/>
<w:LsdException Locked="false" Priority="49" Name="List Table 4"/>
<w:LsdException Locked="false" Priority="50" Name="List Table 5 Dark"/>
<w:LsdException Locked="false" Priority="51" Name="List Table 6 Colorful"/>
<w:LsdException Locked="false" Priority="52" Name="List Table 7 Colorful"/>
<w:LsdException Locked="false" Priority="46" Name="List Table 1 Light Accent 1"/>
<w:LsdException Locked="false" Priority="47" Name="List Table 2 Accent 1"/>
<w:LsdException Locked="false" Priority="48" Name="List Table 3 Accent 1"/>
<w:LsdException Locked="false" Priority="49" Name="List Table 4 Accent 1"/>
<w:LsdException Locked="false" Priority="50" Name="List Table 5 Dark Accent 1"/>
<w:LsdException Locked="false" Priority="51" Name="List Table 6 Colorful Accent 1"/>
<w:LsdException Locked="false" Priority="52" Name="List Table 7 Colorful Accent 1"/>
<w:LsdException Locked="false" Priority="46" Name="List Table 1 Light Accent 2"/>
<w:LsdException Locked="false" Priority="47" Name="List Table 2 Accent 2"/>
<w:LsdException Locked="false" Priority="48" Name="List Table 3 Accent 2"/>
<w:LsdException Locked="false" Priority="49" Name="List Table 4 Accent 2"/>
<w:LsdException Locked="false" Priority="50" Name="List Table 5 Dark Accent 2"/>
<w:LsdException Locked="false" Priority="51" Name="List Table 6 Colorful Accent 2"/>
<w:LsdException Locked="false" Priority="52" Name="List Table 7 Colorful Accent 2"/>
<w:LsdException Locked="false" Priority="46" Name="List Table 1 Light Accent 3"/>
<w:LsdException Locked="false" Priority="47" Name="List Table 2 Accent 3"/>
<w:LsdException Locked="false" Priority="48" Name="List Table 3 Accent 3"/>
<w:LsdException Locked="false" Priority="49" Name="List Table 4 Accent 3"/>
<w:LsdException Locked="false" Priority="50" Name="List Table 5 Dark Accent 3"/>
<w:LsdException Locked="false" Priority="51" Name="List Table 6 Colorful Accent 3"/>
<w:LsdException Locked="false" Priority="52" Name="List Table 7 Colorful Accent 3"/>
<w:LsdException Locked="false" Priority="46" Name="List Table 1 Light Accent 4"/>
<w:LsdException Locked="false" Priority="47" Name="List Table 2 Accent 4"/>
<w:LsdException Locked="false" Priority="48" Name="List Table 3 Accent 4"/>
<w:LsdException Locked="false" Priority="49" Name="List Table 4 Accent 4"/>
<w:LsdException Locked="false" Priority="50" Name="List Table 5 Dark Accent 4"/>
<w:LsdException Locked="false" Priority="51" Name="List Table 6 Colorful Accent 4"/>
<w:LsdException Locked="false" Priority="52" Name="List Table 7 Colorful Accent 4"/>
<w:LsdException Locked="false" Priority="46" Name="List Table 1 Light Accent 5"/>
<w:LsdException Locked="false" Priority="47" Name="List Table 2 Accent 5"/>
<w:LsdException Locked="false" Priority="48" Name="List Table 3 Accent 5"/>
<w:LsdException Locked="false" Priority="49" Name="List Table 4 Accent 5"/>
<w:LsdException Locked="false" Priority="50" Name="List Table 5 Dark Accent 5"/>
<w:LsdException Locked="false" Priority="51" Name="List Table 6 Colorful Accent 5"/>
<w:LsdException Locked="false" Priority="52" Name="List Table 7 Colorful Accent 5"/>
<w:LsdException Locked="false" Priority="46" Name="List Table 1 Light Accent 6"/>
<w:LsdException Locked="false" Priority="47" Name="List Table 2 Accent 6"/>
<w:LsdException Locked="false" Priority="48" Name="List Table 3 Accent 6"/>
<w:LsdException Locked="false" Priority="49" Name="List Table 4 Accent 6"/>
<w:LsdException Locked="false" Priority="50" Name="List Table 5 Dark Accent 6"/>
<w:LsdException Locked="false" Priority="51" Name="List Table 6 Colorful Accent 6"/>
<w:LsdException Locked="false" Priority="52" Name="List Table 7 Colorful Accent 6"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Mention"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Smart Hyperlink"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Hashtag"/>
<w:LsdException Locked="false" SemiHidden="true" UnhideWhenUsed="true" Name="Unresolved Mention"/>
</w:LatentStyles>
</xml><![endif]--><style><!--
/* Font Definitions */
@font-face
	{font-family:"Cambria Math";
	panose-1:2 4 5 3 5 4 6 3 2 4;
	mso-font-charset:0;
	mso-generic-font-family:roman;
	mso-font-pitch:variable;
	mso-font-signature:-536869121 1107305727 33554432 0 415 0;}
@font-face
	{font-family:Calibri;
	panose-1:2 15 5 2 2 2 4 3 2 4;
	mso-font-charset:0;
	mso-generic-font-family:swiss;
	mso-font-pitch:variable;
	mso-font-signature:-536858881 -1073732485 9 0 511 0;}
/* Style Definitions */
p.MsoNormal, li.MsoNormal, div.MsoNormal
	{mso-style-unhide:no;
	mso-style-qformat:yes;
	mso-style-parent:"";
	margin:0cm;
	margin-bottom:.0001pt;
	mso-pagination:widow-orphan;
	font-size:11.0pt;
	font-family:"Calibri",sans-serif;
	mso-fareast-font-family:Calibri;
	mso-fareast-theme-font:minor-latin;}
a:link, span.MsoHyperlink
	{mso-style-noshow:yes;
	mso-style-priority:99;
	color:#0563C1;
	mso-themecolor:hyperlink;
	text-decoration:underline;
	text-underline:single;}
a:visited, span.MsoHyperlinkFollowed
	{mso-style-noshow:yes;
	mso-style-priority:99;
	color:#954F72;
	mso-themecolor:followedhyperlink;
	text-decoration:underline;
	text-underline:single;}
p.msonormal0, li.msonormal0, div.msonormal0
	{mso-style-name:msonormal;
	mso-style-unhide:no;
	mso-margin-top-alt:auto;
	margin-right:0cm;
	mso-margin-bottom-alt:auto;
	margin-left:0cm;
	mso-pagination:widow-orphan;
	font-size:11.0pt;
	font-family:"Calibri",sans-serif;
	mso-fareast-font-family:Calibri;
	mso-fareast-theme-font:minor-latin;}
span.EpostStil20
	{mso-style-type:personal-reply;
	mso-style-noshow:yes;
	mso-style-unhide:no;
	mso-ansi-font-size:11.0pt;
	mso-bidi-font-size:11.0pt;
	font-family:"Calibri",sans-serif;
	mso-ascii-font-family:Calibri;
	mso-ascii-theme-font:minor-latin;
	mso-fareast-font-family:Calibri;
	mso-fareast-theme-font:minor-latin;
	mso-hansi-font-family:Calibri;
	mso-hansi-theme-font:minor-latin;
	mso-bidi-font-family:"Times New Roman";
	mso-bidi-theme-font:minor-bidi;
	color:windowtext;}
.MsoChpDefault
	{mso-style-type:export-only;
	mso-default-props:yes;
	font-size:10.0pt;
	mso-ansi-font-size:10.0pt;
	mso-bidi-font-size:10.0pt;}
@page WordSection1
	{size:612.0pt 792.0pt;
	margin:70.85pt 70.85pt 70.85pt 70.85pt;
	mso-header-margin:35.4pt;
	mso-footer-margin:35.4pt;
	mso-paper-source:0;}
div.WordSection1
	{page:WordSection1;}
--></style><!--[if gte mso 10]><style>/* Style Definitions */
table.MsoNormalTable
	{mso-style-name:"Vanlig tabell";
	mso-tstyle-rowband-size:0;
	mso-tstyle-colband-size:0;
	mso-style-noshow:yes;
	mso-style-priority:99;
	mso-style-parent:"";
	mso-padding-alt:0cm 5.4pt 0cm 5.4pt;
	mso-para-margin:0cm;
	mso-para-margin-bottom:.0001pt;
	mso-pagination:widow-orphan;
	font-size:10.0pt;
	font-family:"Times New Roman",serif;}
</style><![endif]-->
</head>
<body lang="NO-BOK" link="#0563C1" vlink="#954F72" style="tab-interval:35.4pt">
<div class="WordSection1">
<p class="MsoNormal"><span style="mso-ascii-font-family:Calibri;mso-ascii-theme-font:minor-latin;mso-hansi-font-family:Calibri;mso-hansi-theme-font:minor-latin;mso-bidi-font-family:&quot;Times New Roman&quot;;mso-bidi-theme-font:minor-bidi;mso-fareast-language:EN-US"><o:p>&nbsp;</o:p></span></p>
<p class="MsoNormal"><span style="mso-fareast-language:EN-US"><o:p>&nbsp;</o:p></span></p>
<div>
<div style="border:none;border-top:solid #E1E1E1 1.0pt;padding:3.0pt 0cm 0cm 0cm">
<p class="MsoNormal"><a name="_MailOriginal"><b><span style="mso-fareast-font-family:&quot;Times New Roman&quot;">Fra:</span></b></a><span style="mso-bookmark:_MailOriginal"><span style="mso-fareast-font-family:&quot;Times New Roman&quot;"> UBW økonomi &lt;agresso.epost@bergen.kommune.no&gt;
<br>
<b>Sendt:</b> tirsdag 3. desember 2019 11:34<br>
<b>Til:</b> Postmottak LRS Kunde &lt;LRS.Kunde@bergen.kommune.no&gt;<br>
<b>Emne:</b> Din salgsordre til EHF-kunde - mangler utfylling i feltet &quot;Ekstern referanse - Deres ref&quot;<o:p></o:p></span></span></p>
</div>
</div>
<p class="MsoNormal"><span style="mso-bookmark:_MailOriginal"><o:p>&nbsp;</o:p></span></p>
<p><span style="mso-bookmark:_MailOriginal">UBW-økonomi har fanget opp at&nbsp;din salgsordre ikke er merket med bestillerreferanse/ressursnummer eller referansenavn hos fakturamottaker i feltet «Ekstern referanse&nbsp;/ Deres ref».&nbsp;<o:p></o:p></span></p>
<p><span style="mso-bookmark:_MailOriginal">Denne&nbsp;kunden er mottaker av elektronisk faktura (EHF) fra Bergen kommune, og feltet «Ekstern referanse&nbsp;/ Deres ref»&nbsp;<u>må</u>&nbsp;alltid fylles ut for at mottaker skal kunne lese inn faktura elektronisk i sitt økonomisystem.<o:p></o:p></span></p>
<p><span style="mso-bookmark:_MailOriginal">Dette gjelder salgsordre:<o:p></o:p></span></p>
<table class="MsoNormalTable" border="1" cellpadding="0" style="mso-cellspacing:1.5pt;mso-yfti-tbllook:1184" id="AgrTableEditor2;">
<tbody>
<tr style="mso-yfti-irow:0;mso-yfti-firstrow:yes">
<td style="padding:.75pt .75pt .75pt .75pt">
<p class="MsoNormal" align="center" style="text-align:center"><span style="mso-bookmark:_MailOriginal"><strong><span style="font-size:12.0pt;font-family:&quot;Calibri&quot;,sans-serif;mso-fareast-font-family:&quot;Times New Roman&quot;;color:black">Firma</span></strong></span><span style="mso-bookmark:_MailOriginal"><b><span style="font-family:&quot;Times New Roman&quot;,serif;mso-fareast-font-family:&quot;Times New Roman&quot;;color:black"><o:p></o:p></span></b></span></p>
</td>
<span style="mso-bookmark:_MailOriginal"></span>
<td style="padding:.75pt .75pt .75pt .75pt">
<p class="MsoNormal" align="center" style="text-align:center"><span style="mso-bookmark:_MailOriginal"><strong><span style="font-size:12.0pt;font-family:&quot;Calibri&quot;,sans-serif;mso-fareast-font-family:&quot;Times New Roman&quot;;color:black">Ordrenr</span></strong></span><span style="mso-bookmark:_MailOriginal"><b><span style="font-family:&quot;Times New Roman&quot;,serif;mso-fareast-font-family:&quot;Times New Roman&quot;;color:black"><o:p></o:p></span></b></span></p>
</td>
<span style="mso-bookmark:_MailOriginal"></span>
<td style="padding:.75pt .75pt .75pt .75pt">
<p class="MsoNormal" align="center" style="text-align:center"><span style="mso-bookmark:_MailOriginal"><strong><span style="font-size:12.0pt;font-family:&quot;Calibri&quot;,sans-serif;mso-fareast-font-family:&quot;Times New Roman&quot;;color:black">Ekstern ref / Deres ref</span></strong></span><span style="mso-bookmark:_MailOriginal"><b><span style="font-family:&quot;Times New Roman&quot;,serif;mso-fareast-font-family:&quot;Times New Roman&quot;;color:black"><o:p></o:p></span></b></span></p>
</td>
<span style="mso-bookmark:_MailOriginal"></span>
<td style="padding:.75pt .75pt .75pt .75pt">
<p class="MsoNormal" align="center" style="text-align:center"><span style="mso-bookmark:_MailOriginal"><strong><span style="font-size:12.0pt;font-family:&quot;Calibri&quot;,sans-serif;mso-fareast-font-family:&quot;Times New Roman&quot;;color:black">Kundenr</span></strong></span><span style="mso-bookmark:_MailOriginal"><b><span style="font-family:&quot;Times New Roman&quot;,serif;mso-fareast-font-family:&quot;Times New Roman&quot;;color:black"><o:p></o:p></span></b></span></p>
</td>
<span style="mso-bookmark:_MailOriginal"></span>
<td style="padding:.75pt .75pt .75pt .75pt">
<p class="MsoNormal" align="center" style="text-align:center"><span style="mso-bookmark:_MailOriginal"><strong><span style="font-size:12.0pt;font-family:&quot;Calibri&quot;,sans-serif;mso-fareast-font-family:&quot;Times New Roman&quot;;color:black">Kundenavn</span></strong></span><span style="mso-bookmark:_MailOriginal"><b><span style="font-family:&quot;Times New Roman&quot;,serif;mso-fareast-font-family:&quot;Times New Roman&quot;;color:black"><o:p></o:p></span></b></span></p>
</td>
<span style="mso-bookmark:_MailOriginal"></span>
<td style="padding:.75pt .75pt .75pt .75pt">
<p class="MsoNormal" align="center" style="text-align:center"><span style="mso-bookmark:_MailOriginal"><strong><span style="font-size:12.0pt;font-family:&quot;Calibri&quot;,sans-serif;mso-fareast-font-family:&quot;Times New Roman&quot;;color:black">Dato</span></strong></span><span style="mso-bookmark:_MailOriginal"><b><span style="font-family:&quot;Times New Roman&quot;,serif;mso-fareast-font-family:&quot;Times New Roman&quot;;color:black"><o:p></o:p></span></b></span></p>
</td>
<span style="mso-bookmark:_MailOriginal"></span>
<td style="padding:.75pt .75pt .75pt .75pt">
<p class="MsoNormal" align="center" style="text-align:center"><span style="mso-bookmark:_MailOriginal"><strong><span style="font-size:12.0pt;font-family:&quot;Calibri&quot;,sans-serif;mso-fareast-font-family:&quot;Times New Roman&quot;;color:black">Brukerid</span></strong></span><span style="mso-bookmark:_MailOriginal"><b><span style="font-family:&quot;Times New Roman&quot;,serif;mso-fareast-font-family:&quot;Times New Roman&quot;;color:black"><o:p></o:p></span></b></span></p>
</td>
<span style="mso-bookmark:_MailOriginal"></span>
</tr>
<tr style="mso-yfti-irow:1;mso-yfti-lastrow:yes">
<td style="padding:.75pt .75pt .75pt .75pt">
<p class="MsoNormal"><span style="mso-bookmark:_MailOriginal"><span style="font-family:&quot;Times New Roman&quot;,serif;mso-fareast-font-family:&quot;Times New Roman&quot;;color:black">BY<o:p></o:p></span></span></p>
</td>
<span style="mso-bookmark:_MailOriginal"></span>
<td style="padding:.75pt .75pt .75pt .75pt">
<p class="MsoNormal"><span style="mso-bookmark:_MailOriginal"><span style="font-family:&quot;Times New Roman&quot;,serif;mso-fareast-font-family:&quot;Times New Roman&quot;;color:black">20523757<o:p></o:p></span></span></p>
</td>
<span style="mso-bookmark:_MailOriginal"></span>
<td style="padding:.75pt .75pt .75pt .75pt"><span style="mso-bookmark:_MailOriginal"></span></td>
<span style="mso-bookmark:_MailOriginal"></span>
<td style="padding:.75pt .75pt .75pt .75pt">
<p class="MsoNormal"><span style="mso-bookmark:_MailOriginal"><span style="font-family:&quot;Times New Roman&quot;,serif;mso-fareast-font-family:&quot;Times New Roman&quot;;color:black">919427<o:p></o:p></span></span></p>
</td>
<span style="mso-bookmark:_MailOriginal"></span>
<td style="padding:.75pt .75pt .75pt .75pt">
<p class="MsoNormal"><span style="mso-bookmark:_MailOriginal"><span style="font-family:&quot;Times New Roman&quot;,serif;mso-fareast-font-family:&quot;Times New Roman&quot;;color:black">Foreningen Danielsen ungdomsskole Bergen<o:p></o:p></span></span></p>
</td>
<span style="mso-bookmark:_MailOriginal"></span>
<td style="padding:.75pt .75pt .75pt .75pt">
<p class="MsoNormal"><span style="mso-bookmark:_MailOriginal"><span style="font-family:&quot;Times New Roman&quot;,serif;mso-fareast-font-family:&quot;Times New Roman&quot;;color:black">03.12.2019<o:p></o:p></span></span></p>
</td>
<span style="mso-bookmark:_MailOriginal"></span>
<td style="padding:.75pt .75pt .75pt .75pt">
<p class="MsoNormal"><span style="mso-bookmark:_MailOriginal"><span style="font-family:&quot;Times New Roman&quot;,serif;mso-fareast-font-family:&quot;Times New Roman&quot;;color:black">MY616<o:p></o:p></span></span></p>
</td>
<span style="mso-bookmark:_MailOriginal"></span>
</tr>
</tbody>
</table>
<p><span style="mso-bookmark:_MailOriginal">Kontakt kunde/mottaker for å avklare merking av faktura dersom bestillerreferanse/ressursnummer eller referansenavn ikke fremgår på grunnlaget for kravet.&nbsp;<o:p></o:p></span></p>
<p><span style="mso-bookmark:_MailOriginal">Gå deretter tilbake til salgsordrebildet, velg åpne og lukk deretter felthjelpvinduet. Legg inn ordrenummer (&#43; tab). Legg inn ønsket merking hos mottaker i feltet «Ekstern referanse&nbsp;/ Deres ref» og lagre ordren på
 nytt.<o:p></o:p></span></p>
<p><span style="mso-bookmark:_MailOriginal">Se evt. Økonomihåndboken&nbsp;- Driftsinntekter - Registrere fakturagrunnlag/salgsordre for informasjon om hvordan du endrer ordrer som ikke er fakturert.<o:p></o:p></span></p>
<p><span style="mso-bookmark:_MailOriginal"><span style="color:black">Kontakt LRS servicesenter via LRS hjelper deg eller på telefon 5556 9090 hvis du trenger hjelp.</span><o:p></o:p></span></p>
<p><span style="mso-bookmark:_MailOriginal">&nbsp;<o:p></o:p></span></p>
<p><span style="mso-bookmark:_MailOriginal">Hilsen Lønns- og regnskapssenteret<o:p></o:p></span></p>
<p><span style="mso-bookmark:_MailOriginal">IA-mal: UF - Varsel om blank &quot;Deres ref&quot; i salgsordre til EHF-kunder<o:p></o:p></span></p>
<p><span style="mso-bookmark:_MailOriginal">&nbsp;</span><o:p></o:p></p>
</div>
</body>
</html>
HTML;

	$tidy_options = array(
		'indent'						 => 2,
		'output-xhtml'					 => true,
		'drop-font-tags'				 => true,
		'clean'							 => true,
		'merge-spans'					 => true,
		'drop-proprietary-attributes'	 => true,
		'char-encoding'					 => 'utf8'
	);

	if (class_exists('tidy'))
	{
		$tidy	 = new tidy;
		$test	 = $tidy->repairString($test);
		$tidy->parseString($test, $tidy_options, 'utf8');
		$test	 = $tidy->html();
	}

	$dom			 = new DOMDocument();
	$dom->recover	 = true;
	$dom->loadHTML($test);//, LIBXML_NOBLANKS | LIBXML_XINCLUDE  );
	$xpath			 = new DOMXPath($dom);
	$nodes			 = $xpath->query('//*[@style]');  // Find elements with a style attribute
	foreach ($nodes as $node)
	{
		$node->removeAttribute('style'); // Remove style attribute
	}
	unset($node);
	$nodes = $xpath->query('//*[@class]');  // Find elements with a class attribute
	foreach ($nodes as $node)
	{
		$node->removeAttribute('class'); // Remove class attribute
	}
	unset($node);
	$nodes = $xpath->query('//*[@lang]');  // Find elements with a lang attribute
	foreach ($nodes as $node)
	{
		$node->removeAttribute('lang'); // Remove lang attribute
	}
	unset($node);
	$nodes = $xpath->query('//*[@align]');  // Find elements with a align attribute
	foreach ($nodes as $node)
	{
		$node->removeAttribute('align'); // Remove align attribute
	}
	unset($node);
	$nodes = $xpath->query('//*[@size]');  // Find elements with a size attribute
	foreach ($nodes as $node)
	{
		$node->removeAttribute('size'); // Remove size attribute
	}
	unset($node);

//	$body = $dom->getElementsByTagName('body');
//   $body = $body->item(0);


	$test = $dom->saveHTML();

	if (class_exists('tidy'))
	{
		$tidy	 = new tidy;
		$tidy->parseString($test);
		$test	 = $tidy->body();
		$test =  phpgw::clean_html($test);
	}





	echo $test;
	die();

