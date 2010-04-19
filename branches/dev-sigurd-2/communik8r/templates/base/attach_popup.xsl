<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:phpgw="http://dtds.phpgroupware.org/phpgw.dtd"
	xmlns:phpgwapi="http://dtds.phpgroupware.org/phpgwapi.dtd"
	xmlns:communik8r="http://dtds.phpgroupware.org/communik8r.dtd">
	<xsl:output method="html" indent="yes" />
	<xsl:template match="/">
		<html>
			<head>
				<title>[communik8r]</title>
				<link rel="StyleSheet" type="text/css" href="{/phpgw:response/phpgwapi:info/phpgwapi:base_url}/css/evo.css" media="screen" />
				<link rel="StyleSheet" type="text/css" href="{/phpgw:response/phpgwapi:info/phpgwapi:base_url}/css/evo-print.css" media="print" />
				<link rel="icon" href="templates/default/images/navbar.png" type="image/png" />
				<script type="text/javascript" src="{/phpgw:response/phpgwapi:info/phpgwapi:base_url}/js/base/application.js"></script>
				<script type="text/javascript" src="{/phpgw:response/phpgwapi:info/phpgwapi:base_url}/js/sarissa/sarissa.js"></script>
				<script type="text/javascript" src="{/phpgw:response/phpgwapi:info/phpgwapi:base_url}/js/base/ui.js"></script>
				<script type="text/javascript">

					var strBaseURL = "<xsl:value-of select='/phpgw:response/phpgwapi:info/phpgwapi:base_url' />";
					var strMsgId = "<xsl:value-of select='/phpgw:response/phpgwapi:info/phpgwapi:base_url' />";
					var oApplication = new application();
					var bClosing = false;
					window.onload = function()
					{
						stripe(document.getElementById('attach_list').getElementsByTagName('tr'));;
					}

					function closeWin()
					{
						if ( !bClosing ) //stops double trigger
						{
							setTimeout('window.close()', 2000);//hack to fix focus
							bClosing = true;
							window.opener.focus();
							window.opener.refreshAttachments();
							
						}
					}

					function showHelp()
					{
						alert('Adam Ballai is writing some fine documentation for you to enjoy :P');
					}
				</script>
			</head>
			<body>
				<div id="attach_popup">
					<div id="attach_files_local">
						<form action="#" method="POST" enctype="multipart/form-data" onsubmit="return !!document.forms[0].attachment.value.length;">
							<label for="file1">
								<xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='filename']" />
							</label> <input type="file" id="attachment" name="attachment" />
							<button type="submit">
								<img src="{/phpgw:response/phpgwapi:info/phpgwapi:base_url}/templates/default/images/jump-to-24x24.png" style="vertical-align: middle;" /><xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='attach']" />
							</button>
						</form>
					</div>
					<div id="attached_files_list">
						<table>
							<thead>
								<tr>
									<th>&#160;</th>
									<th class="filename"><xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='filename']" /></th>
									<th class="size"><xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='size']" /></th>
									<th class="remove"><xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='actions']" /></th>
								</tr>
							</thead>
							<tbody id="attach_list">
							<xsl:for-each select="/phpgw:response/communik8r:attachments/communik8r:attachment">
								<xsl:call-template name="attach_rows" />
							</xsl:for-each>
							<tr>
								<td colspan="4">&#160;</td>
								<!-- hack for firefox -->
							</tr>
							</tbody>
						</table>
					</div>
					<button id="button_close" onClick="closeWin();">
						<img src="{/phpgw:response/phpgwapi:info/phpgwapi:base_url}/templates/default/images/close-24x24.png" style="vertical-align: middle;" /><xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='close']"/>
					</button>
					<button id="button_help" onClick="showHelp();">
						<img src="{/phpgw:response/phpgwapi:info/phpgwapi:base_url}/templates/default/images/help-24x24.png" style="vertical-align: middle;" /><xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='help']"/>
					</button>
				</div>
			</body>
		</html>
	</xsl:template>
	<xsl:template name="attach_rows">
			<tr>
				<td><img src="{@icon}" /></td>
				<td class="filename"><xsl:value-of select="." /></td>
				<td class="size"><xsl:value-of select="@size" /></td>
				<td class="remove">Remove</td>
			</tr>
	</xsl:template>
</xsl:stylesheet>
