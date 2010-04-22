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
				<link rel="StyleSheet" type="text/css" href="css/evo.css" media="screen" />
				<link rel="StyleSheet" type="text/css" href="css/evo-print.css" media="print" />
				<link rel="icon" href="templates/default/images/navbar.png" type="image/png" />
				<!-- Basic onLoad functionality -->
				<script type="text/javascript">

					var strBaseURL = "<xsl:value-of select='/phpgw:response/phpgwapi:info/phpgwapi:base_url' />";
					var strAppURL = "<xsl:value-of select='/phpgw:response/phpgwapi:info/phpgwapi:app_url' />";
					var strCurSelection = "<xsl:value-of select='/phpgw:response/communik8r:info/communik8r:current_selection' />";
					var oCurMsgs = new Object;
					<xsl:for-each select="/phpgw:response/communik8r:info/communik8r:current_message">
						oCurMsgs['<xsl:value-of select="@id" />'] = <xsl:value-of select="." />;
					</xsl:for-each>
					window.onload = function()
					{
						oApplication = new Application();
						oMenu = new Menu('menu_holder');
						oButtons = new Buttons('buttons');
						oSummary = new Summary('summary');
						oMessage = new Message('message');
						oScreenEvents = new screenEvents();
						oAccounts = new Accounts('accounts');
						eventsLocked = false;
					}
				</script>
				<!-- CSS compliance fixes -->
				<script src="js/ie7/ie7-standard-p.js" type="text/javascript"></script>
				<script type="text/javascript" src="js/sarissa/sarissa.js"></script>
				<script type="text/javascript" src="js/sarissa/sarissa_dhtml.js"></script>
				<script type="text/javascript" src="js/base/application.js"></script>
				<script type="text/javascript" src="js/jsdommenubar/jsdomenu_compressed.js"></script>
				<script type="text/javascript" src="js/jsdommenubar/jsdomenubar_compressed.js"></script>
				<script type="text/javascript" src="js/sortabletable/js/sortabletable.js"></script>
				<script type="text/javascript" src="js/base/summary.js"></script>
				<script type="text/javascript" src="js/base/buttons.js"></script>
				<script type="text/javascript" src="js/dhtmlX/dhtmlXCommon.js"></script>
				<script type="text/javascript" src="js/dhtmlX/dhtmlXTree.js"></script>
				<script type="text/javascript" src="js/base/menu.js"></script>
				<script type="text/javascript" src="js/base/accounts.js"></script>
				<script type="text/javascript" src="js/base/message.js"></script>
				<script type="text/javascript" src="js/base/attachment.js"></script>
				<script type="text/javascript" src="js/base/ui.js"></script>
			</head>
			<body>
				<div id="content">
					<div id="menu_holder"></div>
					<div id="buttons">&#160;</div>
					<div id="accounts"></div>
					<div id="search">&#160;</div>
					<div id="summary">&#160;</div>
					<div id="message">&#160;</div>
				</div>
				<div id="msg_loading">
					<div class="hilite">[communik8r]</div>
					<span>Loading, please wait ....</span>
					<object width="48" height="48">
						<param name="movie" value="templates/base/images/c8.swf" />
						<embed src="templates/base/images/c8.swf" width="48" height="48" />
					</object>
				</div>
			</body>
		</html>
	</xsl:template>
</xsl:stylesheet>
