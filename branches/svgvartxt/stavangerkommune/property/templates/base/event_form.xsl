  <!-- $Id$ -->
	<xsl:template name="event_form">
		<xsl:apply-templates select="event_data"/>
	</xsl:template>

	<!-- New template-->
	<xsl:template xmlns:php="http://php.net/xsl" match="event_data">
		<script type="text/javascript">
			self.name="first_Window";
			function event_lookup_<xsl:value-of select="name"/>()
			{
				var oArgs = <xsl:value-of select="event_link"/>;
				if(document.form.<xsl:value-of select="name"/>.value)
				{
					oArgs['id'] = document.form.<xsl:value-of select="name"/>.value;
				}

				var strURL = phpGWLink('index.php', oArgs);
				Window1=window.open(strURL,"Search","left=50,top=100,width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");
			}
		</script>
		<tr>
			<td valign="top">
				<xsl:value-of select="event_name"/>
				<!--<a href="javascript:event_lookup_{name}()" title="{lang_select_event_help}"><xsl:value-of select="event_name"/></a> -->
			</td>
			<td>
				<xsl:choose>
					<xsl:when test="warning!=''">
						<xsl:value-of select="warning"/>
					</xsl:when>
					<xsl:otherwise>
						<xsl:variable name="event_descr">
							<xsl:value-of select="name"/>
							<xsl:text>_descr</xsl:text>
						</xsl:variable>
						<xsl:variable name="lookup_function">
							<xsl:text>event_lookup_</xsl:text>
							<xsl:value-of select="name"/>
							<xsl:text>();</xsl:text>
						</xsl:variable>
						<table>
							<tr>
								<td>
									<input type="text" name="{name}" value="{value}" onClick="{$lookup_function}" readonly="readonly" size="6"/>
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
											<xsl:value-of select="php:function('lang', 'responsible')"/>
											<xsl:text>: </xsl:text>
											<xsl:value-of select="responsible"/>
										</td>
									</tr>
									<tr>
										<td>
											<xsl:value-of select="lang_next_run"/>
											<xsl:text>: </xsl:text>
											<xsl:value-of select="next"/>
										</td>
									</tr>
									<tr>
										<td>
											<xsl:value-of select="lang_enabled"/>
											<xsl:text>: </xsl:text>
											<xsl:value-of select="enabled"/>
										</td>
									</tr>
									<tr>
										<td>
											<xsl:value-of select="php:function('lang', 'count')"/>
											<xsl:text>: </xsl:text>
											<xsl:value-of select="count"/>
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
