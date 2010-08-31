<!-- $Id$ -->

	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="edit">
				<xsl:apply-templates select="edit"/>
			</xsl:when>
		</xsl:choose>
	</xsl:template>
	

<!-- add / edit  -->
	<xsl:template match="edit" xmlns:php="http://php.net/xsl">
		<div align="left">
		
		<table cellpadding="2" cellspacing="2" width="80%" align="center">
			<xsl:choose>
				<xsl:when test="msgbox_data != ''">
					<tr>
						<td align="left" colspan="3">
							<xsl:call-template name="msgbox"/>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<xsl:variable name="form_action"><xsl:value-of select="form_action"/></xsl:variable>
			<form ENCTYPE="multipart/form-data" name="form" method="post" action="{$form_action}">
			<tr>
				<td valign="top">
					<xsl:value-of select="php:function('lang', 'id')" />
				</td>
				<td>
					<xsl:choose>
						<xsl:when test="value_id != ''">
							<xsl:value-of select="value_id"/>
						</xsl:when>
						<xsl:otherwise>
							<input type="text" name="values[id]" value="{value_id}">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'enter id')" />
								</xsl:attribute>
							</input>
						</xsl:otherwise>
					</xsl:choose>	
				</td>
			</tr>
			<tr>
				<td>
					<xsl:value-of select="lang_category"/>
				</td>
				<td>
			<!--		<xsl:call-template name="cat_select"/> -->
				</td>
			</tr>

			<tr>
				<td>
					<xsl:value-of select="lang_responsible"/>
				</td>
				<td>
					<xsl:call-template name="user_id_select"/>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="php:function('lang', 'descr')" />
				</td>
				<td>
					<textarea cols="60" rows="10" name="values[descr]" wrap="virtual">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_descr_b_accounttext"/>
						</xsl:attribute>
						<xsl:value-of select="value_descr"/>		
					</textarea>

				</td>
			</tr>
			<tr height="50">
				<td>
					<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
					<input type="submit" name="values[save]" value="{$lang_save}">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_save_b_accounttext"/>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			</form>
			<tr>
				<td>
					<xsl:variable name="done_action"><xsl:value-of select="done_action"/></xsl:variable>
					<xsl:variable name="lang_done"><xsl:value-of select="php:function('lang', 'done')" /></xsl:variable>
					<form method="post" action="{$done_action}">
						<input type="submit" name="done" value="{$lang_done}">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_done_b_accounttext"/>
							</xsl:attribute>
						</input>
					</form>
				</td>
			</tr>
		</table>
		</div>
	</xsl:template>
