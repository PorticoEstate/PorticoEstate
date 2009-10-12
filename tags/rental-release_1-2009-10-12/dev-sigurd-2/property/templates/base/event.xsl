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
								<tr>
									<td valign="top">
										<xsl:value-of select="lang_next_run"/>
									</td>
									<td>
										<xsl:value-of select="value_next_run"/>
									</td>
								</tr>
							</xsl:when>
						</xsl:choose>	
						<tr>
							<td valign="top">
								<xsl:value-of select="lang_descr"/>
							</td>
							<td>
								<textarea cols="{textareacols}" rows="{textarearows}" name="values[descr]" wrap="virtual">
									<xsl:value-of select="value_descr"/>		
								</textarea>
							</td>
						</tr>
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
							<td>
								<xsl:value-of disable-output-escaping="yes" select="action"/>
							</td>
						</tr>

			<tr>
				<td valign="top">
					<xsl:value-of select="lang_enabled"/>
				</td>
				<td>
					<xsl:choose>
						<xsl:when test="value_enabled = '1'">
							<input type="checkbox" name="values[enabled]" value="1" checked="checked" onMouseout="window.status='';return true;">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_enabled_on_statustext"/>
								</xsl:attribute>
							</input>
						</xsl:when>
						<xsl:otherwise>
							<input type="checkbox" name="values[enabled]" value="1" onMouseout="window.status='';return true;">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_enabled_off_statustext"/>
								</xsl:attribute>
							</input>
						</xsl:otherwise>
					</xsl:choose>
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
								<xsl:value-of select="lang_repeat_type"/>
							</td>
							<td>
								<xsl:value-of disable-output-escaping="yes" select="repeat_type"/>
							</td>
						</tr>
						<tr>
							<td valign="top">
								<xsl:value-of select="lang_repeat_day"/>
							</td>
							<td>
								<xsl:value-of disable-output-escaping="yes" select="repeat_day"/>
							</td>
						</tr>
						<tr>
							<td valign="top">
								<xsl:value-of select="lang_repeat_interval"/>
							</td>
							<td>
								<input type="text" id="values_repeat_interval" name="values[repeat_interval]" size="4" value="{value_repeat_interval}">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_repeat_interval_statustext"/>
									</xsl:attribute>
								</input>
							</td>
						</tr>			
						<tr>
							<td valign="top">
				                <a>
            				        <xsl:attribute name="href"><xsl:value-of select="link_schedule"/></xsl:attribute>
            				        <xsl:value-of select="php:function('lang', 'plan')" />
            				    </a>
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
						<xsl:choose>
							<xsl:when test="value_id != ''">
								<td align="right" valign="bottom">
									<input type="submit" name="values[delete]" value="{lang_delete}">
										<xsl:attribute name="title">
											<xsl:value-of select="lang_delete_statustext"/>
										</xsl:attribute>
									</input>
								</td>
							</xsl:when>
						</xsl:choose>	
					</tr>
				</table>
			</div>
		</form>
		</div>
	</xsl:template>
