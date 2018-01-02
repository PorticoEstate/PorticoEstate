<!-- $Id$ -->

	<xsl:template name="addressmaster">
		<xsl:choose>
			<xsl:when test="addressmaster_list">
				<xsl:apply-templates select="addressmaster_list"/>
			</xsl:when>
			<xsl:when test="addressmaster_edit">
				<xsl:apply-templates select="addressmaster_edit"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="list"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

<!-- BEGIN addressmaster list -->

	<xsl:template match="addressmaster_list">
		<table width="80%" border="0">
			<thead>
				<tr>
					<th colspan="3"><xsl:value-of select="lang_users"/></th>
				</tr>
				<tr>
					<th width="33%"><xsl:value-of select="sort_lid"/></th>
					<th width="33%"><xsl:value-of select="sort_firstname"/></th>
					<th width="33%"><xsl:value-of select="sort_lastname"/></th>
				</tr>
			</thead>

<!-- BEGIN user_list -->
			<tbody>
				<xsl:if test="addressmaster_user != ''">
					<xsl:apply-templates select="addressmaster_user"/>
				</xsl:if>
			</tbody>
<!-- END user_list -->
		</table>

		<table width="80%" border="0">
			<thead>
				<tr>
					<th colspan="3"><xsl:value-of select="lang_groups"/></th>
				</tr>
				<tr>
					<th width="33%"><xsl:value-of select="sort_lid"/></th>
					<th width="33%">&nbsp;</th>
					<th width="33%">&nbsp;</th>
				</tr>
			</thead>

<!-- BEGIN group_list -->
			<tbody>
				<xsl:if test="addressmaster_group != ''">
					<xsl:apply-templates select="addressmaster_group"/>
				</xsl:if>
			</tbody>
<!-- END group_list -->
		</table>

		<div class="button_group">
			<form method="POST" action="{action_url}">
				<input type="submit" name="edit" value="{lang_edit}"/>
				<input type="submit" name="done" value="{lang_done}"/>
			</form>
		</div>
	</xsl:template>

	<xsl:template match="addressmaster_user">
		<tr>                                                                                                                                             
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
			<td><xsl:value-of select="lid"/></td>
			<td><xsl:value-of select="firstname"/></td>
			<td><xsl:value-of select="lastname"/></td>
		</tr>
	</xsl:template>

	<xsl:template match="addressmaster_group">
		<tr>                                                                                                                                             
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
			<td><xsl:value-of select="lid"/></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
	</xsl:template>

	<xsl:template match="addressmaster_edit">
		<xsl:variable name="action_url" select="action_url"/>
		<xsl:variable name="lang_save" select="lang_save"/>
		<xsl:variable name="lang_cancel" select="lang_cancel"/>
		<div class="msg"><xsl:value-of select="error_message"/></div>
			<form method="POST" action="{$action_url}" name="app_form">
			<h2><xsl:value-of select="lang_select_addressmasters"/></h2>
			<xsl:choose>
				<xsl:when test="select_user != ''">
					<xsl:apply-templates select="select_user"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:apply-templates select="popwin_user"/>
				</xsl:otherwise>
			</xsl:choose>
			<div class="button_group">
				<input type="submit" name="save" value="{$lang_save}"/>
				<input type="submit" name="cancel" value="{$lang_cancel}"/>
			</div>
			</form>
	</xsl:template>

<!-- BEGIN select -->

	<xsl:template match="select_user">
		<fieldset>
			<legend><xsl:value-of select="lang_select_users"/></legend>
			<select name="account_addressmaster[]" multiple="multiple" size="7">
				<xsl:apply-templates select="user_list"/>
			</select>
		</fieldset>

		<fieldset>
			<legend><xsl:value-of select="lang_select_groups"/></legend>
			<select name="group_addressmaster[]" multiple="multiple" size="7">
				<xsl:apply-templates select="group_list"/>
			</select>
		</fieldset>
	</xsl:template>

<!-- END select -->

<!-- BEGIN popwin -->

	<xsl:template match="popwin_user">
		<fieldset>
			<select name="account_addressmaster[]" multiple="multiple" size="7">
				<xsl:if test="user_list != ''">
					<xsl:apply-templates select="user_list"/>
				</xsl:if>
			</select><br />
			<a href="#" onClick="window.open('{url}', 'addressmaster', 'menubar=0,toolbar=0,resizable=1,width={width},height={height}'); return false;"><xsl:value-of select="lang_open_popup" /></a>
		</fieldset>
	</xsl:template>

<!-- END popwin -->

	<xsl:template match="user_list">
		<xsl:variable name="account_id" select="account_id"/>
		<xsl:choose>
			<xsl:when test="select_value != ''">
				<option value="{$account_id}" selected="selected"><xsl:value-of select="fullname"/></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$account_id}"><xsl:value-of select="fullname"/></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="group_list">
		<xsl:variable name="account_id" select="account_id"/>
		<xsl:choose>
			<xsl:when test="select_value != ''">
				<option value="{$account_id}" selected="selected"><xsl:value-of select="fullname"/></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$account_id}"><xsl:value-of select="fullname"/></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
