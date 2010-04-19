<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:phpgw="http://dtds.phpgroupware.org/phpgw.dtd"
	xmlns:phpgwapi="http://dtds.phpgroupware.org/phpgwapi.dtd"
	xmlns:communik8r="http://dtds.phpgroupware.org/communik8r.dtd">
	<xsl:output method="html" indent="yes" />
	<xsl:template match="/">
		<xsl:variable name="app_url"
			select="/phpgw:response/phpgwapi:info/phpgwapi:app_url" />
		<xsl:variable name="skin"
			select="/phpgw:response/phpgwapi:info/phpgwapi:skin" />
		<html>
			<head>
				<title><xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='settings_title']" /></title>
				<link href="{concat($app_url, '/css/evo.css')}" rel="stylesheet" type="text/css" />
				<link rel="icon" href="{concat($app_url, '/templates/', $skin, '/images/navbar.png')}" type="image/png" />
				<script type="text/javascript">

					var strBaseURL = "<xsl:value-of select='/phpgw:response/phpgwapi:info/phpgwapi:base_url' />";
					var strAppURL = "<xsl:value-of select='/phpgw:response/phpgwapi:info/phpgwapi:app_url' />";

					var oAccountsWin = window.self;
				</script>
				<script type="text/javascript" src="{concat($app_url, '/js/base/application.js')}"></script>
				<script type="text/javascript" src="{concat($app_url, '/js/base/ui.js')}"></script>
				<script type="text/javascript" src="{concat($app_url, '/js/sarissa/sarissa.js')}"></script>
				<script type="text/javascript" src="{concat($app_url, '/js/sarissa/sarissa_dhtml.js')}"></script>
				<script type="text/javascript" src="{concat($app_url, '/js/ie7/ie7-standard-p.js')}"></script>
				<script type="text/javascript" src="{concat($app_url, '/js/tabs/tabs.js')}"></script>
				<script type="text/javascript">
				<![CDATA[
					var iSelected = 0;
					var oApplication = new Application();
					var oTabs;

					window.onload = function()
					{
						oTabs = new Tabs(2, 'activetab', 'inactivetab', 'tab', 'content', null, null, switchPanel);
						oTabs.init();
					}

					function switchPanel(iActive)
					{
						alert('Active Panel: ' + iActive);
					}

					function editAccount()
					{
						if ( iSelected > 0 )
						{
							oApplication.editAccount(iSelected);
						}
					}

					function selectAccount(iID)
					{
						if( iID == iSelected )
						{
							return;
						}
						
						var oTRs = document.getElementById('accts_tbl_body').getElementsByTagName('tr');
						for ( var i =0; i > oTRs.length; ++i)
						{
							removeClassName(oTRs.item(i), 'hilite');
						}
						addClassName(document.getElementById('account_' + iID), 'hilite');
						iSelected = iID;
					}

				//]]>
				</script>
			</head>
			<body id="settings">
				<div id="tabbar">
					<ul>
						<li id="tab1">
							<img src="{concat($app_url, '/templates/', $skin, '/images/people-48x48.png')}" alt="{/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='accounts']}" /><br />
							<xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='accounts']" />
						</li>
						<li id="tab2">
							<img src="{concat($app_url, '/templates/', $skin, '/images/settings-48x48.png')}" alt="{/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='settings']}" /><br />
							<xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='settings']" />
						</li>
					</ul>
				</div>
				<div id="content">
					<div id="content1" class="activetab">
						<table>
							<col class="enabled" />
							<col class="name" />
							<col class="type" />
							<thead>
								<tr>
									<th class="enabled"><xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='enabled']" /></th>
									<th class="name"><xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='account_name']" /></th>
									<th class="type"><xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='type']" /></th>
								</tr>
							</thead>
							<tbody id="accts_tbl_body">
								<xsl:for-each select="/phpgw:response/communik8r:response/communik8r:accounts/communik8r:account">
									<tr id="{concat('account_', @id)}" onClick="selectAccount({@id});">
										<xsl:choose>
											<xsl:when test="@enabled=1">
												<td class="enabled"><input type="checkbox" name="enabled[{@enabled}]" checked="checked" /></td>
											</xsl:when>
											<xsl:otherwise>
												<td class="enabled"><input type="checkbox" name="enabled[{@enabled}]" /></td>
											</xsl:otherwise>
										</xsl:choose>
										<td class="name"><xsl:value-of select="@title" /></td>
										<td class="type"><xsl:value-of select="@handler" /></td>
									</tr>
								</xsl:for-each>
							</tbody>
						</table>
						<div id="btns">
							<button onClick="oApplication.editAccount('new');">
								<img src="{concat($app_url, '/templates/', $skin, '/images/add-24x24.png')}" alt="{/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='add']}" />
								<xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='add']" /><br />
							</button>
							<button onClick="editAccount();">
								<img src="{concat($app_url, '/templates/', $skin, '/images/properties-24x24.png')}" alt="{/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='edit']}" />
								<xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='edit']" /><br />
							</button>
						</div>
					</div>
					<div id="content2" class="inactivetab">
					</div>
				</div>
				<div id="ctrl_buttons">
					<button name="help" id="button_help" onClick="oApplication.showHelp('account_email_edit');">
						<img src="{concat($app_url, '/templates/', $skin, '/images/help-24x24.png')}" alt="{/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='help']}" />
						<xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='help']"/>
					</button>
					<button name="cancel" onClick="window.close();">
						<img src="{concat($app_url, '/templates/', $skin, '/images/close-24x24.png')}" alt="{/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='close']}" />
						<xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:langs/phpgwapi:lang[@id='close']"/>
					</button>
				</div>
			</body>
		</html>
	</xsl:template>
</xsl:stylesheet>
