  <!-- $Id$ -->
	<!-- add / edit  -->
	<xsl:template xmlns:php="http://php.net/xsl" match="reporting">
		<xsl:variable name="lang_download">
			<xsl:value-of select="php:function('lang', 'download')"/>
		</xsl:variable>
		<xsl:variable name="form_action">
			<xsl:value-of select="form_action"/>
		</xsl:variable>
		<form method="post" action="{$form_action}">
			<div class="yui-navset yui-navset-top" id="reporting_tabview">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<xsl:choose>
					<xsl:when test="msgbox_data != ''">
						<table cellpadding="2" cellspacing="2" width="90%" align="center">
							<tr>
								<td align="left">
									<xsl:call-template name="msgbox"/>
								</td>
							</tr>
						</table>
					</xsl:when>
				</xsl:choose>
				<div class="yui-content">
					<div id="deposition">
						<table cellpadding="2" cellspacing="2" width="90%" align="center">
							<tr>
								<td>
									<xsl:value-of select="php:function('lang', 'deposition')"/>
									<xsl:text>  </xsl:text>
									<input type="checkbox" name="values[deposition]" value="True">
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'deposition')"/>
										</xsl:attribute>
									</input>
								</td>
							</tr>
							<tr height="50">
								<td valign="bottom">
									<input type="submit" name="values[export_deposition]" value="{$lang_download}">
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'save')"/>
										</xsl:attribute>
									</input>
								</td>
							</tr>
						</table>
					</div>
					<div id="reconciliation">
						<table cellpadding="2" cellspacing="2" width="90%" align="center">
							<tr>
								<td valign="top">
									<xsl:value-of select="php:function('lang', 'periods')"/>
									<p style="height: 150px; overflow: auto; border: 5px solid #eee; background: #eee; color: #000; margin-bottom: 1.5em;">
										<xsl:apply-templates select="accounting_periods/options"/>
									</p>
								</td>
							</tr>
							<tr height="50">
								<td valign="bottom">
									<input type="submit" name="values[export_reconciliation]" value="{$lang_download}">
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'save')"/>
										</xsl:attribute>
									</input>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</form>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="options">
		<label>
			<input type="checkbox" name="values[periods][]" value="{id}"/>
			<xsl:value-of disable-output-escaping="yes" select="name"/>
		</label>
		<br/>
	</xsl:template>
