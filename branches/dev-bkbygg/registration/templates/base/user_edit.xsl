<!-- $Id: control_item.xsl 8913 2012-02-17 10:14:42Z erikhl $ -->
<!-- item  -->

<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div class="identifier-header">
		<h1>
			<xsl:value-of select="php:function('lang', 'edit user')" />
		</h1>
	</div>
	<div id="edit_user_tabview">
		<xsl:value-of disable-output-escaping="yes" select="tabs" />

		<form action="#" method="post" name="form" class="pure-form pure-form-aligned">
			<div id="tab-content">
				<div id="main">

					<input type="hidden" name="id" value = "{value_id}">
					</input>
					<fieldset>
						<xsl:for-each select="user_data">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="text"/>
								</label>
								<xsl:value-of select="value"/>
							</div>
						</xsl:for-each>
						<xsl:call-template name="location_form"/>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'approve')" />
							</label>
							<input type="checkbox" name="values[approve]" value="1">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'approve')"/>
								</xsl:attribute>
								<xsl:if test="value_approved = '1'">
									<xsl:attribute name="checked">
										<xsl:text>checked</xsl:text>
									</xsl:attribute>
								</xsl:if>
							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'process')" />
							</label>
							<input type="checkbox" name="values[pending_users][]" value="{value_id}">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'process approved')"/>
								</xsl:attribute>
							</input>
						</div>
					</fieldset>
				</div>
				<div id="groups">
					<h2>
						<xsl:value-of select="php:function('lang', 'groups')" />
					</h2>
					<ul class="group_list">
						<xsl:apply-templates select="group_list" />
					</ul>
				</div>
				<div id="apps">
					<h2>
						<xsl:value-of select="php:function('lang', 'applications')" />
					</h2>
					<table class="app_list">
						<thead>
							<tr>
								<th>
									<xsl:value-of select="php:function('lang', 'Application')" />
								</th>
								<th>
									<xsl:value-of select="php:function('lang', 'User access')" />
								</th>
								<th>
									<xsl:value-of select="php:function('lang', 'Admin')" />
								</th>
							</tr>
						</thead>
						<tbody>
							<xsl:apply-templates select="app_list" />
						</tbody>
					</table>
				</div>

			</div>
			<div class="form-buttons">
				<xsl:variable name="lang_save">
					<xsl:value-of select="php:function('lang', 'save')" />
				</xsl:variable>
				<xsl:variable name="lang_cancel">
					<xsl:value-of select="php:function('lang', 'cancel')" />
				</xsl:variable>
				<xsl:variable name="lang_delete">
					<xsl:value-of select="php:function('lang', 'delete')" />
				</xsl:variable>
				<input type="submit" name="save" value="{$lang_save}" title = "{$lang_save}" />
				<input type="submit" name="delete" value="{$lang_delete}" title = "{$lang_delete}" />
				<input type="submit" name="cancel" value="{$lang_cancel}" title = "{$lang_cancel}" />
			</div>

		</form>
	</div>
</xsl:template>
	
<!-- BEGIN group_list -->
<xsl:template match="group_list">
	<li>
		<xsl:attribute name="class">
			<xsl:choose>
				<xsl:when test="position() mod 2 = 0">
					<xsl:text>row_off</xsl:text>
				</xsl:when>
				<xsl:otherwise>
					<xsl:text>row_on</xsl:text>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:attribute>
		<xsl:choose>
			<xsl:when test="i_am_admin = 1">
				<input type="checkbox" id="account_groups{account_id}" name="account_groups[]" value="{account_id}">
					<xsl:if test="checked = 1">
						<xsl:attribute name="checked" value="checked" />
					</xsl:if>
				</input>
			</xsl:when>
			<xsl:otherwise>
				<input type="checkbox" readonly='true'>
					<xsl:if test="checked = 1">
						<xsl:attribute name="checked" value="checked" />
					</xsl:if>
				</input>
				<input type="hidden" id="account_groups{account_id}" name="account_groups[]">
					<xsl:if test="checked = 1">
						<xsl:attribute name="value">
							<xsl:value-of select="account_id"/>
						</xsl:attribute>
					</xsl:if>
				</input>
			</xsl:otherwise>
		</xsl:choose>

		<xsl:value-of select="account_lid"/>
	</li>
</xsl:template>

<!-- BEGIN app_list -->

<xsl:template match="app_list">
	<tr>
		<xsl:attribute name="class">
			<xsl:choose>
				<xsl:when test="position() mod 2 = 0">
					<xsl:text>row_off</xsl:text>
				</xsl:when>
				<xsl:otherwise>
					<xsl:text>row_on</xsl:text>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:attribute>
		<td>
			<xsl:value-of select="app_title"/>

		</td>
		<xsl:choose>
			<xsl:when test="i_am_admin = 1">
				<td>
					<input type="checkbox" id="{checkbox_name}" name="{checkbox_name}" value="1">
						<xsl:if test="checked = 1">
							<xsl:attribute name="checked" value="checked" />
						</xsl:if>
					</input>
				</td>
				<td>
					<input type="checkbox" id="{checkbox_name_admin}" name="{checkbox_name_admin}" value="2">
						<xsl:if test="checked_admin = 1">
							<xsl:attribute name="checked" value="checked" />
						</xsl:if>
					</input>
				</td>
			</xsl:when>
			<xsl:otherwise>
				<td>
					<input type="checkbox" readonly='true'>
						<xsl:if test="checked = 1">
							<xsl:attribute name="checked" value="checked" />
						</xsl:if>
					</input>
					<input type="hidden" id="{checkbox_name}" name="{checkbox_name}">
						<xsl:if test="checked = 1">
							<xsl:attribute name="value">
								<xsl:text>1</xsl:text>
							</xsl:attribute>
						</xsl:if>
					</input>
				</td>
				<td>
					<input type="checkbox" readonly='true'>
						<xsl:if test="checked_admin = 1">
							<xsl:attribute name="checked" value="checked" />
						</xsl:if>
					</input>
					<input type="hidden" id="{checkbox_name_admin}" name="{checkbox_name_admin}">
						<xsl:if test="checked_admin = 1">
							<xsl:attribute name="value">
								<xsl:text>2</xsl:text>
							</xsl:attribute>
						</xsl:if>
					</input>
				</td>
			</xsl:otherwise>
		</xsl:choose>
	</tr>
</xsl:template>

<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected != 0">
			<xsl:attribute name="selected" value="selected" />
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>

