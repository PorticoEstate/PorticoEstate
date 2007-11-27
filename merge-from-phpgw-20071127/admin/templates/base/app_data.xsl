<!-- $Id: app_data.xsl 17082 2006-09-05 12:32:21Z skwashd $ -->

	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="list">
				<xsl:apply-templates select="list"/>
			</xsl:when>
			<xsl:when test="cat_list">
				<xsl:call-template name="cats"/>
			</xsl:when>
			<xsl:when test="cat_edit">
				<xsl:call-template name="cats"/>
			</xsl:when>
			<xsl:when test="group_list">
				<xsl:call-template name="groups"/>
			</xsl:when>
			<xsl:when test="group_edit">
				<xsl:call-template name="groups"/>
			</xsl:when>
			<xsl:when test="account_list">
				<xsl:call-template name="users"/>
			</xsl:when>
			<xsl:when test="account_edit">
				<xsl:call-template name="users"/>
			</xsl:when>
			<xsl:when test="account_view">
				<xsl:call-template name="users"/>
			</xsl:when>
			<xsl:when test="new_owner_list">
				<xsl:apply-templates select="new_owner_list"/>
			</xsl:when>
			<xsl:when test="delete">
				<xsl:call-template name="app_delete"/>
			</xsl:when>
			<xsl:when test="addressmaster_list">
				<xsl:call-template name="addressmaster"/>
			</xsl:when>
			<xsl:when test="addressmaster_edit">
				<xsl:call-template name="addressmaster"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="list"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

<!-- BEGIN mainscreen -->

	<xsl:template match="list">
		<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
			var phpinfo;

			function openwindow(url)
			{
				if (phpinfo)
				{
					if (phpinfo.closed)
					{
						phpinfo.stop;
						phpinfo.close;
					}
				}
				phpinfo = window.open(url, "phpinfoWindow","width=700,height=600,location=no,menubar=no,directories=no,toolbar=no,scrollbars=yes,resizable=yes,status=no");
				if (phpinfo.opener == null)
				{
					phpinfo.opener = window;
				}
			}
		</SCRIPT>
		<div id="mainscreen">
			<xsl:choose>
				<xsl:when test="app_row_icon">
					<xsl:apply-templates select="app_row_icon"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:apply-templates select="app_row_noicon"/>
				</xsl:otherwise>
			</xsl:choose>
		</div>
	</xsl:template>

	<xsl:template match="app_row_icon">
		<h2>
		<xsl:choose>
			<xsl:when test="app_icon != ''">
				<img src="{app_icon}" alt="{app_title}" name="{app_title}"/>
			</xsl:when>
		</xsl:choose>
		<a name="{app_name}"><xsl:value-of select="app_title"/></a></h2>
		<ul>
			<xsl:apply-templates select="link_row"/>
		</ul>
	</xsl:template>

	<xsl:template match="link_row">
		<xsl:variable name="pref_link" select="pref_link"/>
		<li>
			<xsl:attribute name="class">
				<xsl:choose>
					<xsl:when test="@class">
						<xsl:value-of select="@class"/>
					</xsl:when>
					<xsl:when test="position() mod 2 = 0">
						<xsl:text>row_off</xsl:text>
					</xsl:when>
					<xsl:otherwise>
						<xsl:text>row_on</xsl:text>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:attribute>
			<a href="{$pref_link}"><xsl:value-of select="pref_text"/></a>
		</li>
	</xsl:template>

<!-- BEGIN newOwnerList -->

	<xsl:template match="new_owner_list">
	<xsl:variable name="l_delete" select="l_delete"/>
		<form method="post">
			<input type="hidden" name="account_id" value="{account_id}" />
			<table style="text-align: center">
				<tr>
					<td colspan="2">
						<xsl:value-of select="lang_new_owner"/><br />
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<select name="account">
							<xsl:apply-templates select="accountlist"/>
						</select><br />
					</td>
				</tr>
				<tr>
					<td>
						<input type="submit" name="deleteAccount" value="{l_delete}" />
					</td>
					<td>
						<input type="submit" name="cancel" value="{l_cancel}" />
					</td>
				</tr>
			</table>
		</form>
	</xsl:template>
	
	<xsl:template match="accountlist">
		<option value="{account_id}"><xsl:value-of select="account_name"/></option>
	</xsl:template>
	
