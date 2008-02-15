<!-- $Id: app_data.xsl 15659 2005-01-17 16:23:42Z ceb $ -->

	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="edit">
				<xsl:apply-templates select="edit"/>
			</xsl:when>
			<xsl:when test="view">
				<xsl:apply-templates select="view"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="list"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="list">
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<tr>
				<td>
					<xsl:call-template name="categories"/>
				</td>
				<td align="center">
					<xsl:call-template name="filter_select"/>
				</td>
				<td align="right">
					<xsl:call-template name="search_field"/>
				</td>
			</tr>
			<tr>
				<td colspan="3" width="100%">
					<xsl:call-template name="nextmatchs"/>
					<!--	<xsl:with-param name="nextmatchs_params"/>
					</xsl:call-template> -->
				</td>
			</tr>
		</table>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
				<xsl:apply-templates select="table_header"/>
				<xsl:apply-templates select="values"/>
				<xsl:apply-templates select="table_add"/>
		</table>
	</xsl:template>

	<xsl:template match="table_header">
		<xsl:variable name="sort_time_created" select="sort_time_created"/>
		<xsl:variable name="sort_note_id" select="sort_note_id"/>
			<tr class="th">
				<td width="10%" align="right">
					<a href="{$sort_note_id}" class="th_text"><xsl:value-of select="lang_note_id"/></a>
				</td>
				<td width="40%">
					<xsl:value-of select="lang_content"/>
				</td>
				<td width="20%" align="center">
					<a href="{$sort_time_created}" class="th_text"><xsl:value-of select="lang_time_created"/></a>
				</td>
				<td width="10%" align="center">
					<xsl:value-of select="lang_owner"/>
				</td>
				<td width="5%" align="center">
					<xsl:value-of select="lang_view"/>
				</td>
				<td width="5%" align="center">
					<xsl:value-of select="lang_edit"/>
				</td>
				<td width="5%" align="center">
					<xsl:value-of select="lang_delete"/>
				</td>
			</tr>
	</xsl:template>

	<xsl:template match="values">
		<xsl:variable name="lang_view_statustext"><xsl:value-of select="lang_view_statustext"/></xsl:variable>
		<xsl:variable name="lang_edit_statustext"><xsl:value-of select="lang_edit_statustext"/></xsl:variable>
		<xsl:variable name="lang_delete_statustext"><xsl:value-of select="lang_delete_statustext"/></xsl:variable>
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

				<td align="right">
					<xsl:value-of select="note_id"/>
				</td>
				<td>
					<xsl:value-of select="first"/>
				</td>
				<td align="center">
					<xsl:value-of select="date"/>
				</td>
				<td align="center">
					<xsl:value-of select="owner"/>
				</td>
				<td align="center">
					<xsl:variable name="link_view"><xsl:value-of select="link_view"/></xsl:variable>
					<a href="{$link_view}" onMouseover="window.status='{$lang_view_statustext}';return true;" onMouseout="window.status='';return true;" class="th_text"><xsl:value-of select="text_view"/></a>
				</td>
				<td align="center">
					<xsl:variable name="link_edit"><xsl:value-of select="link_edit"/></xsl:variable>
					<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_statustext}';return true;" onMouseout="window.status='';return true;" class="th_text"><xsl:value-of select="text_edit"/></a>
				</td>
				<td align="center">
					<xsl:variable name="link_delete"><xsl:value-of select="link_delete"/></xsl:variable>
					<a href="{$link_delete}" onMouseover="window.status='{$lang_delete_statustext}';return true;" onMouseout="window.status='';return true;" class="th_text"><xsl:value-of select="text_delete"/></a>
				</td>
			</tr>
	</xsl:template>

	<xsl:template match="table_add">
			<tr>
				<td height="50">
					<xsl:variable name="add_action"><xsl:value-of select="add_action"/></xsl:variable>
					<xsl:variable name="lang_add"><xsl:value-of select="lang_add"/></xsl:variable>
					<form method="post" action="{$add_action}">
						<input type="submit" name="add" value="{$lang_add}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_add_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</form>
				</td>
			</tr>
	</xsl:template>

<!-- add / edit -->

	<xsl:template match="edit">
		<xsl:variable name="edit_url"><xsl:value-of select="edit_url"/></xsl:variable>
		<form method="post" action="{$edit_url}">
		<table cellpadding="2" cellspacing="2" width="79%" align="center">
			<xsl:choose>
				<xsl:when test="msgbox_data != ''">
					<tr>
						<td align="center" colspan="3"><xsl:call-template name="msgbox"/></td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<tr class="row_off">
				<td colspan="2">
					<xsl:value-of select="lang_category"/>
				</td>
				<td>
					<xsl:call-template name="categories"/>
				</td>
			</tr>
			<tr class="row_on">
				<td valign="top" colspan="2">
					<xsl:value-of select="lang_content"/>
				</td>
				<td>
					<textarea cols="60" rows="10" name="values[content]" wrap="virtual" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_content_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
						<xsl:value-of select="value_content"/>		
					</textarea>
				</td>
			</tr>
			<tr class="row_off">
				<td colspan="2">
					<xsl:value-of select="lang_access"/>
				</td>
				<td>
					<xsl:choose>
							<xsl:when test="value_access = 'private'">
								<input type="checkbox" name="values[access]" value="True" checked="checked" onMouseout="window.status='';return true;">
									<xsl:attribute name="onMouseover">
										<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_access_on_statustext"/>
										<xsl:text>'; return true;</xsl:text>
									</xsl:attribute>
								</input>
							</xsl:when>
							<xsl:otherwise>
								<input type="checkbox" name="values[access]" value="True" onMouseout="window.status='';return true;">
									<xsl:attribute name="onMouseover">
										<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_access_off_statustext"/>
										<xsl:text>'; return true;</xsl:text>
									</xsl:attribute>
								</input>
							</xsl:otherwise>
					</xsl:choose>
				</td>
			</tr>
			<tr height="50">
				<td valign="bottom">
					<xsl:variable name="lang_save"><xsl:value-of select="lang_save"/></xsl:variable>
					<input type="submit" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_save_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
				<td valign="bottom">
					<xsl:variable name="lang_apply"><xsl:value-of select="lang_apply"/></xsl:variable>
					<input type="submit" name="values[apply]" value="{$lang_apply}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_apply_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
				<td align="right" valign="bottom">
					<xsl:variable name="lang_cancel"><xsl:value-of select="lang_cancel"/></xsl:variable>
					<input type="submit" name="values[cancel]" value="{$lang_cancel}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_cancel_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
		</table>
		</form>
	</xsl:template>

<!-- view -->

	<xsl:template match="view">
		<table cellpadding="2" cellspacing="2" width="79%" align="center">
			<tr class="row_off">
				<td width="19%">
					<xsl:value-of select="lang_time_created"/>
				</td>
				<td width="81%">
					<xsl:value-of select="value_date"/>
				</td>
			</tr>
			<tr class="row_on">
				<td>
					<xsl:value-of select="lang_category"/>
				</td>
				<td>
					<xsl:value-of select="value_cat"/>
				</td>
			</tr>
			<tr class="row_off">
				<td valign="top">
					<xsl:value-of select="lang_content"/>
				</td>
				<td>
					<xsl:value-of select="value_content"/>
				</td>
			</tr>
			<tr class="row_on">
				<td>
					<xsl:value-of select="lang_access"/>
				</td>
				<td>
					<xsl:value-of select="value_access"/>
				</td>
			</tr>
			<tr height="50">
				<td>
					<xsl:variable name="done_action"><xsl:value-of select="done_action"/></xsl:variable>
					<xsl:variable name="lang_done"><xsl:value-of select="lang_done"/></xsl:variable>
					<form method="post" action="{$done_action}">
					<input type="submit" class="forms" name="done" value="{$lang_done}" onMouseover="window.status='Back to the list.';return true;" onMouseout="window.status='';return true;"/>
					</form>
				</td>
			</tr>
		</table>
	</xsl:template>
