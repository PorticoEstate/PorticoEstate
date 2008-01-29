<!-- $Id: groups.xsl 16569 2006-03-25 12:39:55Z skwashd $ -->

<xsl:template name="groups">
	<xsl:choose>
		<xsl:when test="group_list">
			<xsl:apply-templates select="group_list" />
		</xsl:when>
		<xsl:when test="group_edit">
			<xsl:apply-templates select="group_edit" />
		</xsl:when>
	</xsl:choose>
</xsl:template>

<!-- BEGIN group_list -->

<xsl:template match="group_list">
	<xsl:call-template name="nextmatchs" />
	<xsl:choose>
		<xsl:when test="search_access = 'yes'">
			<xsl:call-template name="search_field" />
		</xsl:when>
	</xsl:choose>
	<table>
		<col class="admin_group_name" />
		<col class="admin_action" />
		<col class="admin_action" />
		<thead>
			<xsl:apply-templates select="group_header" />
		</thead>
		<tbody>
			<xsl:apply-templates select="group_data" />
		</tbody>
	</table>
	<div class="btngrp">
		<xsl:apply-templates select="group_add" />
	</div>
</xsl:template>

<!-- BEGIN group_header -->
<xsl:template match="group_header">
	<xsl:variable name="sort_name" select="sort_name" />
	<xsl:variable name="lang_sort_statustext"
		select="lang_sort_statustext" />
	<tr>
		<td>
			<a href="{$sort_name}">
				<xsl:value-of select="lang_name" />
			</a>
		</td>
		<td>
			<xsl:value-of select="lang_edit" />
		</td>
		<td>
			<xsl:value-of select="lang_delete" />
		</td>
	</tr>
</xsl:template>

<!-- BEGIN group_data -->
<xsl:template match="group_data">
	<xsl:variable name="lang_edit_statustext">
		<xsl:value-of select="lang_edit_statustext" />
	</xsl:variable>
	<xsl:variable name="lang_delete_statustext">
		<xsl:value-of select="lang_delete_statustext" />
	</xsl:variable>
	<tr>
		<xsl:attribute name="class">
			<xsl:choose>
				<xsl:when test="@class">
					<xsl:value-of select="@class" />
				</xsl:when>
				<xsl:when test="position() mod 2 = 0">
					<xsl:text>row_off</xsl:text>
				</xsl:when>
				<xsl:otherwise>
					<xsl:text>row_on</xsl:text>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:attribute>
		<td>
			<xsl:value-of select="group_name" />
		</td>
		<td>
			<xsl:variable name="edit_url" select="edit_url" />
			<a href="{$edit_url}">
				<xsl:value-of select="lang_edit" />
			</a>
		</td>
		<td>
			<xsl:variable name="delete_url" select="delete_url" />
			<a href="{$delete_url}">
				<xsl:value-of select="lang_delete" />
			</a>
		</td>
	</tr>
</xsl:template>

<!-- BEGIN group_add -->
<xsl:template match="group_add">
	<form method="post" action="{action_url}">
		<xsl:choose>
			<xsl:when test="add_access = 'yes'">
				<button type="submit" name="add"
					onclick="this.value=1;">
					<xsl:value-of select="lang_add" />
				</button>
			</xsl:when>
		</xsl:choose>
		<xsl:variable name="lang_done">
			<xsl:value-of select="lang_done" />
		</xsl:variable>
		<button type="submit" name="done" onclick="this.value=1;">
			<xsl:value-of select="$lang_done" />
		</button>
		<input type="hidden" class="hidden" name="account_id" value="0" />
	</form>
</xsl:template>
<!-- END group_list -->

<!-- BEGIN group_edit -->
<xsl:template match="group_edit">
	<xsl:choose>
		<xsl:when test="msgbox_data != ''">
			<p class="msg">
				<xsl:call-template name="msgbox" />
			</p>
		</xsl:when>
	</xsl:choose>
	<xsl:variable name="edit_url">
		<xsl:value-of select="edit_url" />
	</xsl:variable>
	<xsl:variable name="account_id" select="account_id" />
	<xsl:variable name="select_size" select="select_size" />
	<form action="{$edit_url}" method="post">
		<input type="hidden" name="values[account_id]"
			value="{$account_id}" class="hidden" />
		<br />

		<label for="account_name">
			<xsl:value-of select="lang_account_name" />
		</label>
		<input type="text" name="values[account_name]"
			id="account_name">
			<xsl:attribute name="value">
				<xsl:value-of select="value_account_name" />
			</xsl:attribute>
		</input>
		<br />

		<label for="account_user">
			<xsl:value-of select="lang_include_user" />
		</label>
		<select id="account_user" name="account_user[]"
			multiple="multiple" size="{$select_size}"
			onchange="updateManager()">
			<xsl:apply-templates select="guser_list" />
		</select>
		<br />

		<label for="group_manager">
			<xsl:value-of select="lang_group_manager" />
		</label>
		<select id="group_manager" name="group_manager">
			<xsl:apply-templates select="group_manager" />
		</select>
		<br />

		<label for="quota">
			<xsl:value-of select="lang_file_space" />
		</label>
		<!-- {account_file_space}{account_file_space_select} -->
		<span class="mock_label">
			<xsl:value-of select="lang_permissions" />
		</span>
		<ul class="admin_apps_list">
			<li class="th">
				<span>
					<xsl:value-of select="lang_application" />
				</span>
				<xsl:value-of select="lang_acl" />
			</li>
			<xsl:apply-templates select="app_list" />
		</ul>
		<div class="btngrp">
			<button type="submit" name="values[cancel]"
				onclick="this.value=1;">
				<img src="{img_close}" alt="{lang_close}" />
				<xsl:value-of select="lang_close" />
			</button>

			<button type="submit" name="values[save]"
				onclick="this.value=1;">
				<img src="{img_save}" alt="{lang_save}" />
				<xsl:value-of select="lang_save" />
			</button>
		</div>
	</form>
</xsl:template>

<xsl:template match="guser_list">
	<xsl:variable name="account_id" select="account_id" />
	<xsl:choose>
		<xsl:when test="selected != ''">
			<option value="{$account_id}" selected="selected">
				<xsl:value-of select="account_name" />
			</option>
		</xsl:when>
		<xsl:otherwise>
			<option value="{$account_id}">
				<xsl:value-of select="account_name" />
			</option>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template match="group_manager">
	<xsl:choose>
		<xsl:when test="selected != ''">
			<option value="{account_id}" selected="selected">
				<xsl:value-of select="account_name" />
			</option>
		</xsl:when>
		<xsl:otherwise>
			<option value="{account_id}">
				<xsl:value-of select="account_name" />
			</option>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template match="app_list">
	<li>
		<xsl:attribute name="class">
			<xsl:choose>
				<xsl:when test="@class">
					<xsl:value-of select="@class" />
					<xsl:text>even</xsl:text>
				</xsl:when>
				<xsl:when test="position() mod 2 = 0">
					<xsl:text>row_off even</xsl:text>
				</xsl:when>
				<xsl:otherwise>
					<xsl:text>row_on even</xsl:text>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:attribute>
		<label for="app_{app_name}">
			<xsl:value-of select="app_title" />
		</label>
		<xsl:choose>
			<xsl:when test="checked != ''">
				<input type="checkbox" id="app_{app_name}"
					name="{checkbox_name}" value="1" checked="checked"
					class="cbStyled" />
			</xsl:when>
			<xsl:otherwise>
				<input type="checkbox" id="app_{app_name}"
					name="{checkbox_name}" value="1" class="cbStyled" />
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="acl_url != ''">
				<xsl:variable name="acl_url" select="acl_url" />
				<xsl:variable name="acl_img" select="acl_img" />
				<xsl:variable name="img_name" select="img_name" />
				<a href="{$acl_url}">
					<img src="{$acl_img}" alt="{$img_name}" />
				</a>
			</xsl:when>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="grant_url != ''">
				<a href="{grant_url}">
					<img src="{acl_img}" alt="{grant_img_name}" />
				</a>
			</xsl:when>
		</xsl:choose>

		<br />
	</li>
</xsl:template>
