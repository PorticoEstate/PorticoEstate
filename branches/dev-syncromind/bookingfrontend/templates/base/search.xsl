<xsl:template name="strip-tags" xmlns:php="http://php.net/xsl">
	<xsl:param name="text"/>
	<xsl:choose>
		<xsl:when test="contains($text, '&lt;')">
			<xsl:value-of select="substring-before($text, '&lt;')"/>
			<xsl:call-template name="strip-tags">
				<xsl:with-param name="text" select="concat(' ', substring-after($text, '&gt;'))"/>
			</xsl:call-template>
		</xsl:when>
		<xsl:otherwise>
			<xsl:value-of select="$text"/>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<script type="text/javascript">
//		var selected_part_of_towns = "<xsl:value-of select="selected_part_of_towns"/>";
	</script>
	<div id="content">
		<form action="" method="GET" id="search">
			<input type="hidden" id="menuaction" name="menuaction" value="bookingfrontend.uisearch.index" />
			<input type="hidden" id="activity_top_level" name="activity_top_level" value="{activity_top_level}" />
			<div id="building_container">
				<input id="field_building_id" name="building_id" type="hidden">
					<xsl:attribute name="value">
						<xsl:value-of select="building_id"/>
					</xsl:attribute>
				</input>
				<input id="field_building_name" name="building_name" type="text">
					<xsl:attribute name="value">
						<xsl:value-of select="building_name"/>
					</xsl:attribute>
					<xsl:attribute name="placeholder">
						<xsl:text> Søk bygning</xsl:text>
					</xsl:attribute>

				</input>
			</div>
			<!--xsl:text> </xsl:text><input type="submit" value="{php:function('lang', 'Search')}"/-->
			<div class="hint">
				F.eks. "<i>Haukelandshallen</i>", "<i>Nordnes bydelshus</i>", "<i>idrett</i>" eller "<i>kor</i>".
			</div>
		</form>
		<div>
			<div class="heading">
				<xsl:value-of select="php:function('lang', 'part of town')" />
			</div>
			<ul id="part_of_town">
				<xsl:for-each select="part_of_towns">
					<li>
						<label>
							<input type="checkbox" name="part_of_town[]">
								<xsl:attribute name="value">
									<xsl:value-of select="id"/>
								</xsl:attribute>
								<xsl:if test="checked = 1">
								<xsl:attribute name="checked">
									<xsl:text>checked</xsl:text>
								</xsl:attribute>
								</xsl:if>
							</input>
							<xsl:value-of select="name"/>
						</label>
					</li>
				</xsl:for-each>
			</ul>
		</div>

		<ul>
			<xsl:for-each select="activities">
				<li>
					<a href="{search_url}">
						<xsl:choose>
							<xsl:when test="../activity_top_level = id">
								<xsl:text>[</xsl:text>
								<xsl:value-of select="name"/>
								<xsl:text>]</xsl:text>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="name"/>
							</xsl:otherwise>
						</xsl:choose>
					</a>
				</li>
			</xsl:for-each>
		</ul>
		<xsl:if test="not(search)">
			<div id="cloud">
				<div class="frontpagetext">
					<xsl:value-of disable-output-escaping="yes" select="frontpagetext"/>
				</div>
			</div>
			<div style="text-align:center;">
				<img alt="" >
					<xsl:attribute name="src">
						<xsl:value-of select="frontimage"/>
					</xsl:attribute>
				</img>
			</div>
		</xsl:if>
		<xsl:if test="search">
			<div id="result">
				<h5>
					<u>
						<strong>
							<xsl:value-of select="php:function('lang', 'Found %1 results', search/results/total_records_sum)" />
						</strong>
					</u>
				</h5>
				<br />
				<br />
				<xsl:if test="search/results/total_records_sum &gt; 0">
					<ol id="result">
						<xsl:for-each select="search/results/results">
							<li>
								<div class="header">
									<a class="bui_single_view_link">
										<xsl:attribute name="href">
											<xsl:value-of select="link"/>
										</xsl:attribute>
										<xsl:value-of select="name"/>
									</a>
									(<xsl:value-of select="php:function('lang', string(type))"/>)
								</div>
								<div class="details">
									<div>
										<dl>
											<dt>
												<h4>
													<xsl:value-of select="php:function('lang', 'Description')" />
												</h4>
											</dt>
											<dd class="description">
												<xsl:variable name="tag_stripped_description">
													<xsl:call-template name="strip-tags">
														<xsl:with-param name="text" select="description"/>
													</xsl:call-template>
												</xsl:variable>
												<xsl:choose>
													<xsl:when test="string-length($tag_stripped_description) &gt; 1">
														<xsl:choose>
															<xsl:when test="string-length($tag_stripped_description) &gt; 100">
																<xsl:value-of select="substring($tag_stripped_description, 0, 97)"/>...
															</xsl:when>
															<xsl:otherwise>
																<xsl:value-of select="$tag_stripped_description"/>
															</xsl:otherwise>
														</xsl:choose>
													</xsl:when>
													<xsl:otherwise>
														<xsl:value-of select="php:function('lang', 'No description yet')" />
													</xsl:otherwise>
												</xsl:choose>
												<div id="{img_container}"/>
												<script type="text/javascript">
													$(window).load(function() {
													JqueryPortico.booking.inlineImages('<xsl:value-of select="img_container"/>', '<xsl:value-of select="img_url"/>');
													});
												</script>
											</dd>
											<xsl:if test="string-length(homepage) &gt; 1">
												<dt>
													<h4>
														<xsl:value-of select="php:function('lang', 'Homepage')" />
													</h4>
												</dt>
												<dd class="description">
													<a>
														<xsl:attribute name="href">
															<xsl:value-of select="homepage"/>
														</xsl:attribute>
														<xsl:value-of select="homepage"/>
													</a>
												</dd>
											</xsl:if>
										</dl>
									</div>
									<div class="clr"></div>
								</div>
							</li>
						</xsl:for-each>
					</ol>
				</xsl:if>
			</div>
		</xsl:if>
	</div>
</xsl:template>
