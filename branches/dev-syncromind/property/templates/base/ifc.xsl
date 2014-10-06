  <!-- $Id$ -->
	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="import">
				<xsl:apply-templates select="import"/>
			</xsl:when>
		</xsl:choose>
	</xsl:template>

	<!-- import -->
	<xsl:template match="import">
		<xsl:choose>
			<xsl:when test="links !=''">
				<xsl:apply-templates select="menu"/>
			</xsl:when>
		</xsl:choose>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:choose>
				<xsl:when test="msgbox_data != ''">
					<tr>
						<td align="left" colspan="3">
							<xsl:call-template name="msgbox"/>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<xsl:variable name="form_action">
				<xsl:value-of select="form_action"/>
			</xsl:variable>
			<form ENCTYPE="multipart/form-data" name="form" method="post" action="{$form_action}">
				<xsl:call-template name="location_form"/>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_subject"/>
					</td>
					<td>
						<input type="text" name="values[subject]" value="{value_subject}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_subject_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_details"/>
					</td>
					<td>
						<textarea cols="60" rows="10" name="values[details]" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_details_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
							<xsl:value-of select="value_details"/>
						</textarea>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_file"/>
					</td>
					<td>
						<input type="file" name="ifcfile" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_file_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<table>
							<tr height="50">
								<td valign="bottom">
									<xsl:variable name="lang_save">
										<xsl:value-of select="lang_save"/>
									</xsl:variable>
									<input type="submit" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_save_statustext"/>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
									</input>
								</td>
								<td valign="bottom">
									<xsl:variable name="lang_apply">
										<xsl:value-of select="lang_apply"/>
									</xsl:variable>
									<input type="submit" name="values[apply]" value="{$lang_apply}" onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_apply_statustext"/>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
									</input>
								</td>
								<td align="right" valign="bottom">
									<xsl:variable name="lang_cancel">
										<xsl:value-of select="lang_cancel"/>
									</xsl:variable>
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
					</td>
				</tr>
			</form>
		</table>
	</xsl:template>
