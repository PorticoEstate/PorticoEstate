<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:phpgw="http://dtds.phpgroupware.org/phpgw.dtd"
	xmlns:phpgwapi="http://dtds.phpgroupware.org/phpgwapi.dtd"
	xmlns:communik8r="http://dtds.phpgroupware.org/communik8r.dtd">
	<xsl:include href="./buttons" />
	<xsl:output method="html" indent="yes" />
	<xsl:template match="/">
		<xsl:variable name="api_url"
			select="/phpgw:response/phpgwapi:info/phpgwapi:api_url" />
		<xsl:variable name="base_url"
			select="/phpgw:response/phpgwapi:info/phpgwapi:base_url" />
		<xsl:variable name="app_url"
			select="/phpgw:response/phpgwapi:info/phpgwapi:app_url" />

		<xsl:variable name="skin"
			select="/phpgw:response/phpgwapi:info/phpgwapi:skin" />
		<html>
			<head>
				<title><xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='compose_title']" /></title>
				<link href="{concat($app_url, '/css/evo.css')}" rel="stylesheet" type="text/css" />
				<link rel="icon" href="{concat($app_url, '/communik8r/templates/default/images/navbar.png')}" type="image/png" />
				<script type="text/javascript">

					var strBaseURL = "<xsl:value-of select='$base_url' />";
					var strAppURL = "<xsl:value-of select='$app_url' />";
					var strMsgID = "<xsl:value-of select='/phpgw:response/communik8r:response/communik8r:message/@id' />";
					var oComposeWin = window.self;
				</script>
				<script type="text/javascript" src="{concat($app_url, '/js/base/application.js')}"></script>
				<script type="text/javascript" src="{concat($api_url, '/js/ckeditor/ckeditor.js')}"></script>
				<script type="text/javascript" src="{concat($api_url, '/js/json/json.js')}"></script>
				<script type="text/javascript" src="{concat($api_url, '/js/sarissa/sarissa-compressed.js')}"></script>
			<!--	<script type="text/javascript" src="{concat($app_url, '/js/sarissa/sarissa_dhtml.js')}"></script>-->
				<script type="text/javascript" src="{concat($app_url, '/js/autocomplete/autocomplete.js')}"></script>
				<script type="text/javascript" src="{concat($app_url, '/js/base/compose.js')}"></script>
			</head>
			<body>
				<form name="composeForm" id="composeForm" method="GET" action="javascript:sendMsg('composeForm');">
					<input type="hidden" id="msg_id" name="msg_id" value="{/phpgw:response/communik8r:response/communik8r:message/@id}" />
					<div id="menubar">
						&#160;
						<!--Add menu logic here - still to come :(-->
					</div>
					<div id="buttons">
					<xsl:for-each select="/phpgw:response/communik8r:response/communik8r:buttons/communik8r:button">
						<xsl:call-template name="button"/>
					</xsl:for-each>
					</div>
					<div id="fields">
						<label for="account">From:</label>
						<select id="account" name="account">
							<xsl:for-each select="/phpgw:response/communik8r:response/communik8r:accounts/communik8r:account">
								<xsl:call-template name="account_option" />
							</xsl:for-each>
						</select>
						<label for="signature_list">Signature:</label>
						<select id="signature_list" name="signature_list">
							<option value="1">Personal</option>
							<option value="2">Business</option>
						</select><br />
						
						<input type="button" id="btn_to" name="btn_to" 
							value="{/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='to']}:" class="btn" />
						<input type="text" id="to" name="to" autocomplete="off" value="{/phpgw:response/communik8r:response/communik8r:message/communik8r:headers/communik8r:message_to}" /><br />

						<input type="button" id="btn_cc" name="btn_cc" 
							value="{/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='cc']}:" class="btn" />
						<input type="text" id="cc" name="cc" autocomplete="off" value="{/phpgw:response/communik8r:response/communik8r:message/communik8r:headers/communik8r:message_cc}"/><br />

						<input type="button" id="btn_bcc" name="btn_bcc" 
							value="{/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='bcc']}:" 
							class="btn" />
						<input type="text" id="bcc" name="bcc" autocomplete="off" value="{/phpgw:response/communik8r:response/communik8r:message/communik8r:headers/communik8r:message_bcc}" /><br />

						<label for="subject"><xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='subject']" />:</label>
						<input id="subject" type="text" name="subject" autocomplete="off" value="{/phpgw:response/communik8r:response/communik8r:message/communik8r:headers/communik8r:message_subject}" />
					</div>
					<div id="compose">
						<textarea id="msgbody" name="msgbody" rows="6" cols="80" wrap="soft"><xsl:value-of select="/phpgw:response/communik8r:response/communik8r:message/communik8r:body" /></textarea>
					</div>
					<div id="signature" class="hidden">
						<div id="signature_selector"><xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='view_attachments']" /></div>	
						<textarea id="signature_content" name="signature_content" rows="5" cols="80"></textarea>
					</div>
					<div id="attachments">
						<div id="attachments_selector"><xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='view_signature']" /></div>
						<div id="attachments_list">&#160;</div>
					</div>
				</form>
			</body>
		</html>
	</xsl:template>

	<xsl:template name="account_option">
		<option value="{@id}"><xsl:value-of select="." /></option>
	</xsl:template>
</xsl:stylesheet>
