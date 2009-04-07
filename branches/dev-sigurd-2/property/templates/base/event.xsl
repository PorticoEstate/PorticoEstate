<!-- $Id: category.xsl,v 1.1 2005/01/17 10:03:18 sigurdne Exp $ -->

	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="edit">
				<xsl:apply-templates select="edit"/>
			</xsl:when>
		</xsl:choose>
	</xsl:template>

<!-- add / edit  -->
	<xsl:template match="edit">
		<script language="JavaScript">
			self.name="first_Window";
			<xsl:value-of select="lookup_functions"/>
		</script>
	
		<xsl:choose>
			<xsl:when test="msgbox_data != ''">
				<xsl:call-template name="msgbox"/>
			</xsl:when>
		</xsl:choose>

		<div class="yui-navset" id="general_edit_tabview" align="left">
			<xsl:variable name="form_action"><xsl:value-of select="form_action"/></xsl:variable>
			<form method="post" action="{$form_action}">
			<xsl:value-of disable-output-escaping="yes" select="tabs" />
			<div class="yui-content">		
				<div id="general">
					<table cellpadding="2" cellspacing="2" width="79%" align="center">
						<xsl:choose>
							<xsl:when test="value_id != ''">
								<tr>
									<td valign="top">
										<xsl:value-of select="lang_id"/>
									</td>
									<td>
										<xsl:value-of select="value_id"/>
									</td>
								</tr>
							</xsl:when>
						</xsl:choose>	
						<tr>
							<td valign="top">
								<xsl:value-of select="lang_start_date"/>
							</td>
							<td>
								<input type="text" id="values_start_date" name="values[start_date]" size="10" value="{value_start_date}" readonly="readonly" onMouseout="window.status='';return true;" >
									<xsl:attribute name="title">
										<xsl:value-of select="lang_start_date_statustext"/>
									</xsl:attribute>
								</input>
								<img id="values_start_date-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;" />
							</td>
						</tr>
						<tr>
							<td valign="top">
								<xsl:value-of select="lang_responsible"/>
							</td>
							<td>
								<xsl:value-of disable-output-escaping="yes" select="responsible"/>
							</td>
						</tr>
						<tr>
							<td valign="top">
								<xsl:value-of select="lang_action"/>
							</td>
						</tr>

					</table>
				</div>
				<div id="repeat">
					<table cellpadding="2" cellspacing="2" width="79%" align="center">
						<tr>
							<td valign="top">
								<xsl:value-of select="lang_end_date"/>
							</td>
							<td>
								<input type="text" id="values_end_date" name="values[end_date]" size="10" value="{value_end_date}" readonly="readonly" onMouseout="window.status='';return true;" >
									<xsl:attribute name="title">
										<xsl:value-of select="lang_end_date_statustext"/>
									</xsl:attribute>
								</input>
								<img id="values_end_date-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;" />
							</td>
						</tr>
						<tr>
							<td valign="top">
								<xsl:value-of select="lang_rpt_type"/>
							</td>
							<td>
								<xsl:value-of disable-output-escaping="yes" select="rpt_type"/>
							</td>
						</tr>
						<tr>
							<td valign="top">
								<xsl:value-of select="lang_rpt_day"/>
							</td>
							<td>
								<xsl:value-of disable-output-escaping="yes" select="rpt_day"/>
							</td>
						</tr>
						<tr>
							<td valign="top">
								<xsl:value-of select="lang_interval"/>
							</td>
							<td>
								<input type="text" id="values_interval" name="values[interval]" size="4" value="{value_interval}">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_interval_statustext"/>
									</xsl:attribute>
								</input>
							</td>
						</tr>			
					</table>
				</div>

		<table cellpadding="2" cellspacing="2" width="80%" align="center">
			<tr height="50">
				<td valign="bottom">
					<input type="submit" name="values[save]" value="{lang_save}" onMouseout="window.status='';return true;">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_save_statustext"/>
						</xsl:attribute>
					</input>
				</td>
				<td valign="bottom">
					<input type="submit" name="values[apply]" value="{lang_apply}" onMouseout="window.status='';return true;">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_apply_statustext"/>
						</xsl:attribute>
					</input>
				</td>
				<td align="right" valign="bottom">
					<input type="submit" name="values[cancel]" value="{lang_cancel}" onMouseout="window.status='';return true;">
						<xsl:attribute name="title">
								<xsl:value-of select="lang_cancel_statustext"/>
						</xsl:attribute>
					</input>
				</td>
			</tr>
		</table>
		</div>
		</form>
		</div>
	</xsl:template>
