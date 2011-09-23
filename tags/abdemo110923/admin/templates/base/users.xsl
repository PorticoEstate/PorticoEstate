<!-- $Id$ -->

	<xsl:template name="users">
		<xsl:choose>
			<xsl:when test="account_list">
				<xsl:apply-templates select="account_list"/>
			</xsl:when>
			<xsl:when test="account_edit">
				<xsl:apply-templates select="account_edit"/>
			</xsl:when>
			<xsl:when test="account_view">
				<xsl:apply-templates select="account_view"/>
			</xsl:when>
		</xsl:choose>
	</xsl:template>

<!-- BEGIN user_list -->

	<xsl:template match="account_list">
		<div id="admin_accounts_list">
			<xsl:if test="search_access = 1">
				<div class="search">
					<xsl:call-template name="search_field"/>
				</div>
			</xsl:if>

			<div>
				<xsl:call-template name="nextmatchs"/>
			</div>

			<table>
				<thead>
					<xsl:apply-templates select="user_header"/>
				</thead>
				<tbody>
					<xsl:apply-templates select="user_data"/>
				</tbody>
			</table>
			<xsl:apply-templates select="user_add"/>
		</div>
	</xsl:template>

<!-- BEGIN user_header -->

	<xsl:template match="user_header">
		<tr>
			<th><a href="{sort_lid}"><xsl:value-of select="lang_lid"/></a></th>
			<th><a href="{sort_firstname}"><xsl:value-of select="lang_firstname"/></a></th>
			<th><a href="{sort_lastname}"><xsl:value-of select="lang_lastname"/></a></th>
			<th><a href="{sort_status}"><xsl:value-of select="lang_status"/></a></th>
			<th><xsl:value-of select="lang_view" /></th>
			<th><xsl:value-of select="lang_edit" /></th>
			<th><xsl:value-of select="lang_delete" /></th>
		</tr>
	</xsl:template>

<!-- BEGIN user_data -->

	<xsl:template match="user_data">
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
				<xsl:choose>
					<xsl:when test="status != 1">
						<xsl:text> inactive</xsl:text>
					</xsl:when>
				</xsl:choose>
			</xsl:attribute>
			<td align = 'center'><xsl:value-of select="lid" /></td>
			<td align = 'center'><xsl:value-of select="firstname" /></td>
			<td align = 'center'><xsl:value-of select="lastname" /></td>
		<!--	<td class="icon"><img src="{status_img}" alt="{status_text}" /></td> -->
			<td align = 'center'><xsl:value-of select="status_text"/></td>
			<td class="action">
				<a href="{view_url}"><xsl:value-of select="lang_view"/></a>
			</td>
			<td class="action">
				<a href="{edit_url}"><xsl:value-of select="lang_edit"/></a>
			</td>
			<td class="action">
				<a href="{delete_url}"><xsl:value-of select="lang_delete"/></a>
			</td>
		</tr>
	</xsl:template>

<!-- BEGIN user_add -->

	<xsl:template match="user_add">
		<div class="btngrp">
			<xsl:choose>
				<xsl:when test="add_access = 1">
					<a href="{url_add}"><xsl:value-of select="lang_add" /></a>
				</xsl:when>
			</xsl:choose>
			<a href="{url_done}"><xsl:value-of select="lang_done" /></a>
		</div>
	</xsl:template>

<!-- END user_list -->

<!-- BEGIN account_view -->

	<xsl:template match="account_view">
		<div id="admin_view_user">
			<h2><xsl:value-of select="l_user" /></h2>
			<span class="label"><xsl:value-of select="l_loginid" /></span>
			<span class="value"><xsl:value-of select="lid"/></span><br />

			<span class="label"><xsl:value-of select="l_firstname" /></span>
			<span class="value"><xsl:value-of select="firstname"/></span><br />

			<span class="label"><xsl:value-of select="l_lastname" /></span>
			<span class="value"><xsl:value-of select="lastname"/></span><br />

			<span class="label"><xsl:value-of select="l_status" /></span>
			<span class="value"><xsl:value-of select="account_status"/></span><br />

			<span class="label"><xsl:value-of select="l_expires" /></span>
			<span class="value"><xsl:value-of select="input_expires"/></span><br />

			<span class="label"><xsl:value-of select="l_lastlogin" /></span>
			<span class="value"><xsl:value-of select="account_lastlogin"/></span><br />

			<span class="label"><xsl:value-of select="l_pwchange" /></span>
			<span class="value"><xsl:value-of select="account_lastpasswd_change"/></span><br />

			<h2><xsl:value-of select="l_applications" /></h2>
			<div>
				<ul>
					<xsl:apply-templates select="permissions" />
				</ul>
			</div>
		</div>
		<div><a href="{i_back}"><xsl:value-of select="l_back" /></a></div>
	</xsl:template>

<!-- BEGIN account_edit -->

	<xsl:template match="account_edit" xmlns:php="http://php.net/xsl">
		<div id="admin_account_edit">
			<h1><xsl:value-of select="page_title" /></h1>
			<xsl:if test="msgbox_data != ''">
				<xsl:call-template name="msgbox"/>
			</xsl:if>

			<div class="yui-navset" id="account_edit_tabview">
				<xsl:value-of disable-output-escaping="yes" select="tabs" />

				<form method="post" action="{edit_url}">
					<div class="yui-content">
						<div id="user">
							<input type="hidden" name="values[id]" value="{account_id}" />
							<ul id="admin_account_form">
								<li>
									<input type="checkbox" name="values[enabled]" value="1" class="check">
										<xsl:if test="account_enabled = 1">
											<xsl:attribute name="checked" value="checked" />
										</xsl:if>
									</input>
									<label for="account_active"><xsl:value-of select="lang_account_active" class="check" /></label><br />
								</li>

								<li>
									<label for="account_lid"><xsl:value-of select="lang_lid"/></label>
									<input type="text" id="account_lid" name="values[lid]" value="{account_lid}" /><br />
								</li>

								<li>
									<label for="account_firstname"><xsl:value-of select="lang_firstname"/></label>
									<input type="text" id="account_firstname" name="values[firstname]" value="{account_firstname}"/><br />
								</li>

								<li>
									<label for="account_lastname"><xsl:value-of select="lang_lastname"/></label>
									<input type="text" id="account_lastname" name="values[lastname]" value="{account_lastname}"/><br />
								</li>

								<li>
									<label for="account_password"><xsl:value-of select="lang_password"/></label>
									<input type="password" id="account_password" name="values[passwd]" value="{account_passwd}"/><br />
								</li>

								<li>
									<label for="account_password2"><xsl:value-of select="lang_reenter_password"/></label>
									<input type="password" id="account_password2" name="values[passwd_2]" value="{account_passwd_2}"/><br />
								</li>

								<li>
									<span class="label"><xsl:value-of select="lang_contact" /></span>
									<span class="value"><a href="{url_contacts}"><xsl:value-of select="url_contacts_text" /></a></span><br />
								</li>

								<li>
									<input type="checkbox" name="values[changepassword]" value="1" class="check">
										<xsl:if test="changepassword = 1">
											<xsl:attribute name="checked" value="checked" />
										</xsl:if>
									</input>
									<label for="changepassword"><xsl:value-of select="lang_changepassword" class="check" /></label><br />
								</li>
								<li>
									<input type="checkbox" name="values[anonymous]" value="1" class="check">
										<xsl:if test="anonymous = 1">
											<xsl:attribute name="checked" value="checked" />
										</xsl:if>
									</input>
									<label for="anonymous"><xsl:value-of select="lang_anonymous" class="check" /></label><br />
								</li>

								<li>
									<label for="account_expires"><xsl:value-of select="lang_expires"/></label>
									<span class="dates" id="account_expires">
										<xsl:value-of disable-output-escaping="yes" select="select_expires" />
									</span><br />

									<input type="checkbox" name="values[expires_never]" value="1" class="check">
										<xsl:if test="expires_never = 1">
											<xsl:attribute name="checked" value="checked" />
										</xsl:if>
									</input>
									<label for="expires_never"><xsl:value-of select="lang_never" class="check" /></label><br />
								</li>

								<li>
									<label for="account_quota"><xsl:value-of select="lang_quota"/></label>
									<input type="text" name="values[quota]" id="values_quota" value="{account_quota}" />Mb<br />
								</li>
							</ul>
						</div>
						<div id="groups">
							<h2><xsl:value-of select="lang_groups" /></h2>
							<ul class="group_list">
								<xsl:apply-templates select="group_list" />
							</ul>
						</div>
						<div id="apps">
							<h2><xsl:value-of select="lang_applications" /></h2>
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
					<div class="button_group">
						<input type="submit" name="save" value="{lang_save}"/>
						<input type="submit" name="cancel" value="{lang_cancel}"/>
					</div>
				</form>
			</div>
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
						<xsl:choose>
							<xsl:when test="selected != ''">
								<xsl:attribute name="checked" value="checked" />
							</xsl:when>
						</xsl:choose>
					</input>
				</xsl:when>
				<xsl:otherwise>
					<input type="checkbox" readonly='true'>
						<xsl:choose>
							<xsl:when test="selected != ''">
								<xsl:attribute name="checked" value="checked" />
							</xsl:when>
						</xsl:choose>
					</input>
					<input type="hidden" id="account_groups{account_id}" name="account_groups[]">
						<xsl:if test="selected != ''">
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
							<xsl:choose>
								<xsl:when test="checked = '1'">
									<xsl:attribute name="checked" value="checked" />
								</xsl:when>
							</xsl:choose>
						</input>
					</td>
					<td>
						<input type="checkbox" id="{checkbox_name_admin}" name="{checkbox_name_admin}" value="2">
							<xsl:choose>
								<xsl:when test="checked_admin = '1'">
									<xsl:attribute name="checked" value="checked" />
								</xsl:when>
							</xsl:choose>
						</input>
					</td>
				</xsl:when>
				<xsl:otherwise>
					<td>
						<input type="checkbox" readonly='true'>
							<xsl:choose>
								<xsl:when test="checked = '1'">
									<xsl:attribute name="checked" value="checked" />
								</xsl:when>
							</xsl:choose>
						</input>
						<input type="hidden" id="{checkbox_name}" name="{checkbox_name}">
							<xsl:if test="checked = '1'">
								<xsl:attribute name="value">
									<xsl:text>1</xsl:text>
								</xsl:attribute>
							</xsl:if>
						</input>
					</td>
					<td>
						<input type="checkbox" readonly='true'>
							<xsl:choose>
								<xsl:when test="checked_admin = '1'">
									<xsl:attribute name="checked" value="checked" />
								</xsl:when>
							</xsl:choose>
						</input>
						<input type="hidden" id="{checkbox_name_admin}" name="{checkbox_name_admin}">
							<xsl:if test="checked_admin = '1'">
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

<!-- permissions - applist for view -->
	<xsl:template match="permissions">
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
			<img src="{img}" alt="{alt}" />
			<xsl:value-of select="name" />
		</li>
	</xsl:template>
