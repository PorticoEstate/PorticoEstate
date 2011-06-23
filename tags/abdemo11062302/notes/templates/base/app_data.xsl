<!-- $Id$ -->

	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="edit">
				<xsl:apply-templates select="edit" />
			</xsl:when>
			<xsl:when test="view">
				<xsl:apply-templates select="view" />
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="list" />
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="list">
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<tr>
				<td>
					<xsl:call-template name="categories" />
				</td>
				<td align="center">
					<xsl:call-template name="filter_select" />
				</td>
				<td align="right">
					<xsl:call-template name="search_field" />
				</td>
			</tr>
			<tr>
				<td colspan="3" width="100%">
					<xsl:call-template name="nextmatchs" />
				</td>
			</tr>
		</table>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
				<xsl:apply-templates select="table_header" />
				<xsl:apply-templates select="values" />
		</table>
		<xsl:apply-templates select="table_add" />
	</xsl:template>

	<xsl:template match="table_header" xmlns:php="http://php.net/xsl">
		<xsl:variable name="sort_time_created" select="sort_time_created" />
		<xsl:variable name="sort_note_id" select="sort_note_id" />
			<tr class="th">
				<td width="10%" align="right">
					<a href="{$sort_note_id}" class="th_text"><xsl:value-of select="php:function('lang', 'note id')" /></a>
				</td>
				<td width="40%">
					<xsl:value-of select="php:function('lang', 'content')" />
				</td>
				<td width="20%" align="center">
					<a href="{$sort_time_created}" class="th_text"><xsl:value-of select="php:function('lang', 'time created')" /></a>
				</td>
				<td width="10%" align="center">
					<xsl:value-of select="php:function('lang', 'owner')" />
				</td>
				<td width="5%" align="center">
					<xsl:value-of select="php:function('lang', 'view')" />
				</td>
				<td width="5%" align="center">
					<xsl:value-of select="php:function('lang', 'edit')" />
				</td>
				<td width="5%" align="center">
					<xsl:value-of select="php:function('lang', 'delete')" />
				</td>
			</tr>
	</xsl:template>

	<xsl:template match="values">
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

				<td align="right">
					<xsl:value-of select="note_id" />
				</td>
				<td>
					<xsl:value-of select="first" />
				</td>
				<td align="center">
					<xsl:value-of select="date" />
				</td>
				<td align="center">
					<xsl:value-of select="owner" />
				</td>
				<td align="center">
					<xsl:variable name="link_view"><xsl:value-of select="link_view" /></xsl:variable>
					<a href="{$link_view}"><xsl:value-of select="text_view" /></a>
				</td>
				<td align="center">
					<xsl:variable name="link_edit"><xsl:value-of select="link_edit" /></xsl:variable>
					<a href="{$link_edit}"><xsl:value-of select="text_edit" /></a>
				</td>
				<td align="center">
					<xsl:variable name="link_delete"><xsl:value-of select="link_delete" /></xsl:variable>
					<a href="{$link_delete}"><xsl:value-of select="text_delete" /></a>
				</td>
			</tr>
	</xsl:template>

	<xsl:template match="table_add" xmlns:php="http://php.net/xsl">
		<div>
			<xsl:variable name="lang_add_statustext"><xsl:value-of select="php:function('lang', 'add a note')" /></xsl:variable>
			<a href="{add_action}" title="{$lang_add_statustext}"><xsl:value-of select="php:function('lang', 'add')" /></a>
		</div>
	</xsl:template>

<!-- add / edit -->

	<xsl:template match="edit">
		<xsl:if test="msgbox_data != ''">
			<div><xsl:call-template name="msgbox" /></div>
		</xsl:if>
			<form method="post" action="{edit_url}">
			<fieldset>
				<legend><xsl:value-of select="lang_content" /></legend>
				<textarea cols="100" rows="10" name="note_content" id="note_content" wrap="soft">
					<xsl:value-of select="value_content" />&nbsp;
				</textarea>
			</fieldset>

			<fieldset>
				<legend><xsl:value-of select="lang_advanced" /></legend>
				<table cellpadding="2" cellspacing="2" width="79%" align="center">
					<tr class="row_on">
						<td colspan="2">
							<xsl:value-of select="lang_category" />
						</td>
						<td>
							<xsl:call-template name="categories" />
						</td>
					</tr>

					<tr class="row_off">
						<td colspan="2">
							<xsl:value-of select="lang_access" />
						</td>
						<td>
							<input type="checkbox" name="values[access]" value="True">
								<xsl:if test="value_access = 'private'">
									<xsl:attribute name="checked">
										checked
									</xsl:attribute>
								</xsl:if>
							</input>
						</td>
					</tr>
				</table>
			</fieldset>
			<div class="button_group">
				<input type="submit" name="apply" value="{lang_apply}" /> 

				<input type="submit" name="cancel" value="{lang_cancel}" /> 

				<input type="submit" name="save" value="{lang_save}" /> 
			</div>
		</form>
	</xsl:template>

<!-- view -->

	<xsl:template match="view">
		<div id="note_view">
			<div id="note_content">
				<xsl:value-of select="value_content" disable-output-escaping="yes" />
				<p><xsl:value-of select="lang_category" /></p>
			</div>
			<div id="note_created"><xsl:value-of select="lang_created" /></div>
		</div>
		<div>
			<a href="{done_action}"><xsl:value-of select="lang_done" /></a>
		</div>
	</xsl:template>
