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
				TINY.box.show({iframe:strURL, boxid:"frameless",width:750,height:450,fixed:false,maskid:"darkmask",maskopacity:40, mask:true, animate:true, close: true});
			}
		</script>
		<div class="pure-control-group">
			<label for="name">
				<xsl:value-of select="event_name"/>
			</label>
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
					<div class="pure-custom">
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
					</div>
				</xsl:otherwise>
			</xsl:choose>
		</div>
	</xsl:template>
