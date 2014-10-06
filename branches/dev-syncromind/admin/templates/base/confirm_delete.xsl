<!-- $Id$ -->

	<xsl:template name="confirm_delete">
		<xsl:apply-templates select="delete"/>
	</xsl:template>

	<xsl:template match="delete" xmlns:php="http://php.net/xsl">
		<xsl:choose>
			<xsl:when test="msgbox_data != ''">
				<tr>
					<td align="left" colspan="3">
						<xsl:call-template name="msgbox"/>
					</td>
				</tr>
			</xsl:when>
		</xsl:choose>

			<h1><xsl:value-of select="lang_confirm_msg"/></h1>
			<div class="button_group">
				<form method="post" action="{form_action}">
					<xsl:choose>
						<xsl:when test="subs != ''">
							<select name="subs" >
								<option value="none">
									<xsl:value-of select="php:function('lang', 'select')" />
								</option>
								<option value="move">
									<xsl:value-of select="lang_sub_select_move"/>
								</option>
								<option value="drop">
									<xsl:value-of select="lang_sub_select_drop"/>
								</option>
							</select>
						</xsl:when>
					</xsl:choose>
					<input type="submit" name="cancel" value="{lang_no}" title="{lang_no_statustext}"/>
					<xsl:choose>
						<xsl:when test="show_done">
							<xsl:choose>
								<xsl:when test="show_done = ''">
									<input type="submit" name="confirm" value="{lang_yes}" title="{lang_yes_statustext}"/>
								</xsl:when>
							</xsl:choose>
						</xsl:when>
						<xsl:otherwise>
							<input type="submit" name="confirm" value="{lang_yes}" title="{lang_yes_statustext}"/>
						</xsl:otherwise>
					</xsl:choose>
				</form>
			</div>
	</xsl:template>
