<!-- $Id$ -->

	<xsl:template name="event_form">
		<xsl:apply-templates select="event_data"></xsl:apply-templates>
	</xsl:template>

	<xsl:template xmlns:php="http://php.net/xsl" match="event_data">
		<script type="text/javascript">
			self.name="first_Window";
			function event_lookup_<xsl:value-of select="name"></xsl:value-of>()
			{
				var oArgs = <xsl:value-of select="event_link"></xsl:value-of>;
				if(document.form.<xsl:value-of select="name"></xsl:value-of>.value)
				{
					oArgs['id'] = document.form.<xsl:value-of select="name"></xsl:value-of>.value;
				}

				var strURL = phpGWLink('index.php', oArgs);
				Window1=window.open(strURL,"Search","left=50,top=100,width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");
			}		
		</script>
		<tr>
			<td valign="top">
				<xsl:value-of select="event_name"></xsl:value-of>
				<!--	<a href="javascript:event_lookup_{name}()" title="{lang_select_event_help}"><xsl:value-of select="event_name"/></a> -->
			</td>
			<td>
				<xsl:choose>
					<xsl:when test="warning!=''">
						<xsl:value-of select="warning"></xsl:value-of>
					</xsl:when>
					<xsl:otherwise>
						<xsl:variable name="event_descr"><xsl:value-of select="name"></xsl:value-of><xsl:text>_descr</xsl:text></xsl:variable>
						<xsl:variable name="lookup_function"><xsl:text>event_lookup_</xsl:text><xsl:value-of select="name"></xsl:value-of><xsl:text>();</xsl:text></xsl:variable>
						<table>
							<tr>
								<td>
									<input type="text" name="{name}" value="{value}" onClick="{$lookup_function}" readonly="readonly" size="6"></input>
									<input size="30" type="text" name="{$event_descr}" value="{descr}" onClick="{$lookup_function}" readonly="readonly"> 
										<xsl:choose>
											<xsl:when test="disabled!=''">
												<xsl:attribute name="disabled">
													<xsl:text> disabled</xsl:text>
												</xsl:attribute>
											</xsl:when>
										</xsl:choose>
									</input>
								</td>
							</tr>
							<xsl:choose>
								<xsl:when test="next!=''">
									<tr>
										<td>
											<xsl:value-of select="php:function('lang', 'responsible')"></xsl:value-of>
											<xsl:text>: </xsl:text>
											<xsl:value-of select="responsible"></xsl:value-of>
										</td>
									</tr>
									<tr>
										<td>
											<xsl:value-of select="lang_next_run"></xsl:value-of>
											<xsl:text>: </xsl:text>
											<xsl:value-of select="next"></xsl:value-of>
										</td>
									</tr>
									<tr>
										<td>
											<xsl:value-of select="lang_enabled"></xsl:value-of>
											<xsl:text>: </xsl:text>
											<xsl:value-of select="enabled"></xsl:value-of>
										</td>
									</tr>
									<tr>
										<td>
											<xsl:value-of select="php:function('lang', 'count')"></xsl:value-of>
											<xsl:text>: </xsl:text>
											<xsl:value-of select="count"></xsl:value-of>
										</td>
									</tr>
								</xsl:when>
							</xsl:choose>
						</table>
					</xsl:otherwise>
				</xsl:choose>
			</td>
		</tr>
	</xsl:template>
