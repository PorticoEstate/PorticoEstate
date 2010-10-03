<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:phpgw="http://dtds.phpgroupware.org/phpgw.dtd"
	xmlns:phpgwapi="http://dtds.phpgroupware.org/phpgwapi.dtd"
	xmlns:communik8r="http://dtds.phpgroupware.org/communik8r.dtd">
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
				<title><xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='accounts_title']" /></title>
				<link href="{concat($app_url, '/css/evo.css')}" rel="stylesheet" type="text/css" />
				<link rel="icon" href="{concat($app_url, '/templates/', $skin, '/images/navbar.png')}" type="image/png" />
				<script type="text/javascript">

					var strBaseURL = "<xsl:value-of select='$base_url' />";
					var strAppURL = "<xsl:value-of select='$app_url' />";
					var oAccountsWin = window.self;
				</script>
				<script type="text/javascript" src="{concat($app_url, '/js/base/application.js')}"></script>
				<script type="text/javascript" src="{concat($app_url, '/js/base/ui.js')}"></script>
				<script type="text/javascript" src="{concat($api_url, '/js/sarissa/sarissa-compressed.js')}"></script>
			<!--	<script type="text/javascript" src="{concat($base_url, '/js/sarissa/sarissa_dhtml.js')}"></script> -->
				<script type="text/javascript" src="{concat($app_url, '/js/tabs/tabs.js')}"></script>
				<script type="text/javascript" src="{concat($app_url, '/js/base/emailaccounts.js')}"></script>
				<script type="text/javascript">
				<![CDATA[
					var oApplication = new Application();
					var oTabs;

					window.onload = function()
					{
						oTabs = new Tabs(3, 'activetab', 'inactivetab', 'tab', 'content');
						oTabs.init();
					}

					function updateAccount()
					{
						if ( !isValid() )
						{
							return alert('Invalid Account');
						}

						var strID = document.getElementById('acct_id').value;
						var oXML = Sarissa.getDomDocument('http://dtds.phpgroupware.org/phpgw.dtd', 'phpgw');

						if ( !document.createElementNS ) // Yes IE is a fucked piece of shit!
						{
							var ophpGWResponse = oXML.createElement('phpgw:response');

							var ophpGWapiInfo = oXML.createElement('phpgwapi:info');
							ophpGWResponse.appendChild(ophpGWapiInfo);

							var oCommunik8rResponse = oXML.createElement('communik8r:response');
						}
						else
						{
							var ophpGWResponse = oXML.createElementNS('http://dtds.phpgroupware.org/phpgw.dtd', 
												'phpgw:response');

							var ophpGWapiInfo = oXML.createElementNS('http://dtds.phpgroupware.org/phpgwapi.dtd', 
												'phpgwapi:info');
							ophpGWResponse.appendChild(ophpGWapiInfo);

							var oCommunik8rResponse = oXML.createElementNS('http://dtds.phpgroupware.org/communik8r.dtd', 
													'communik8r:response');
						}

						var oCommunik8rAcct = oXML.createElement('communik8r:account');
						oCommunik8rAcct.setAttribute('id', strID);

						var arInputText = Array('acct_name', 'display_name', 'acct_uri', 'org', 'acct_type_id', 'username', 'password', 'hostname', 'port', 'extra_server_prefix');
						var iLenAIT = arInputText.length;
						for ( i = 0; i < iLenAIT; ++i )
						{
							var oElm = oXML.createElement('communik8r:' + arInputText[i]);
							oElm.appendChild( oXML.createTextNode( document.getElementById(arInputText[i]).value ) );
							oCommunik8rAcct.appendChild(oElm);
						}
						
						oCommunik8rResponse.appendChild(oCommunik8rAcct);
						ophpGWResponse.appendChild(oCommunik8rResponse);

						var xmlhttp = new XMLHttpRequest();
						xmlhttp.open('PUT', oApplication.strBaseURL + '/accounts/' + strID + oApplication.strGET, false);
						//xmlhttp.async = false;
						xmlhttp.send( Sarissa.serialize(ophpGWResponse) );

						if ( xmlhttp.status == 200 )
						{
							window.close();
						}
						else
						{
							alert( xmlhttp.responseText );
						}
					}

					function isValid()
					{
						return true;
					}
				//]]>
				</script>
			</head>
			<body id="account_edit">
				<form name="accountsForm" id="accountsForm" method="GET" action="javascript:updateAccount();">
					<div id="tabbar">
						<ul>
							<li id="tab1"><xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='identity']" /></li>
							<li id="tab2"><xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='receiving']" /></li>
							<li id="tab3"><xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='sending']" /></li>
						</ul>
					</div>
					<div id="content1" class="inactivetab">
						<h1><xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='identity']" /></h1>
						<h2><xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='acct_info']" /></h2>

						<label for="acct_name"><xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='acct_name']"/>:</label>
						<input type="text" name="acct_name" id="acct_name" value="{/phpgw:response/communik8r:response/communik8r:account/communik8r:acct_name}" /><br />

						<h2><xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='required_info']" /></h2>
						<label for="display_name"><xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='full_name']"/>:</label>
						<input type="text" name="display_name" id="display_name" value="{/phpgw:response/communik8r:response/communik8r:account/communik8r:display_name}" /><br />

						<label for="acct_uri"><xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='email_address']"/>:</label>
						<input type="text" name="acct_uri" id="acct_uri" value="{/phpgw:response/communik8r:response/communik8r:account/communik8r:acct_uri}" /><br />

						<label for="org"><xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='org']"/>:</label>
						<input type="text" name="org" id="org" value="{/phpgw:response/communik8r:response/communik8r:account/communik8r:org}" /><br />

					</div>
					<div id="content2" class="inactivetab">
						<h1><xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='receiving']" /></h1>
						<label for="acct_type_id"><xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='server_type']"/>:</label>
						<!-- TODO Put select here -->
						IMAP<input type="hidden" name="acct_type_id" id="acct_type_id" value="1" /><br />

						<h2><xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='config']" /></h2>

						<label for="username"><xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='username']"/>:</label>
						<input type="text" name="username" id="username" value="{/phpgw:response/communik8r:response/communik8r:account/communik8r:username}" /><br />

						<label for="password"><xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='password']"/>:</label>
						<input type="password" name="password" id="password" /><br />

						<!-- TODO switch to hostname instead of server, needs a schema change -->
						<label for="hostname"><xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='hostname']"/>:</label>
						<input type="text" name="hostname" id="hostname" value="{/phpgw:response/communik8r:response/communik8r:account/communik8r:hostname}" /><br />

						<label for="port"><xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='port']"/>:</label>
						<input type="text" name="port" id="port" value="{/phpgw:response/communik8r:response/communik8r:account/communik8r:port}" /><br />

						<label for="extra_server_prefix"><xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='server_prefix']"/>:</label>
						<input type="text" name="extra_server_prefix" id="extra_server_prefix" value="{/phpgw:response/communik8r:response/communik8r:account/communik8r:extra_server_prefix}" /><br />
					</div>
					<div id="content3" class="inactivetab">
						<h1><xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='sending']"/></h1>
					</div>
					<div id="ctrl_buttons">
						<button name="help" id="button_help" onClick="oApplication.showHelp('account_email_edit');">
							<img src="{concat($app_url, '/templates/', $skin, '/images/help-24x24.png')}" alt="{/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='help']}" />
							<xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='help']"/>
						</button>
						<button name="cancel" onClick="oApplication.confirmClose('{/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='confirm_cancel_msg']}');">
							<img src="{concat($app_url, '/templates/', $skin, '/images/cancel-24x24.png')}" alt="{/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='cancel']}" />
							<xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='cancel']"/>
						</button>
						<button type="submit" name="ok">
							<img src="{concat($app_url, '/templates/', $skin, '/images/ok-24x24.png')}" alt="{/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='ok']}" />
							<xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='ok']"/>
						</button>
					</div>
					<input type="hidden" id="acct_id" name="acct_id" value="{/phpgw:response/communik8r:response/communik8r:account/@id}" />
				</form>
			</body>
		</html>
	</xsl:template>

	<xsl:template name="account_option">
		<option value="{@id}"><xsl:value-of select="." /></option>
	</xsl:template>
</xsl:stylesheet>
