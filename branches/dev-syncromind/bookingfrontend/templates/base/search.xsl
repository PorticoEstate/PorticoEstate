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
		<div>
			<div class="heading">
				<xsl:value-of select="php:function('lang', 'type')" />
			</div>
			<ul id="search_type">
				<li>
					<label>
						<input type="checkbox" name="search_type[]" value="building"/>
						<xsl:value-of select="php:function('lang', 'building')" />
					</label>
				</li>
				<li>
					<label>
						<input type="checkbox" name="search_type[]" value="resource" checked="checked"/>
						<xsl:value-of select="php:function('lang', 'resource')" />
					</label>
				</li>
				<!--li>
					<label>
						<input type="checkbox" name="search_type[]" value="organization"/>
						<xsl:value-of select="php:function('lang', 'organization')" />
					</label>
				</li>
				<li>
					<label>
						<input type="checkbox" name="search_type[]" value="event"/>
						<xsl:value-of select="php:function('lang', 'event')" />
					</label>
				</li-->
			</ul>
		</div>

		<div id="activity_tree">
			<fieldset>
				<!-- Some style for the expand/contract section-->
				<style>
					#expandcontractdiv {border:1px dotted #dedede; margin:0 0 .5em 0; padding:0.4em;}
					#treeDiv1 { background: #fff; padding:1em; margin-top:1em; }
					.no_checkbox>i.jstree-checkbox{ display:none}
				</style>
				<script type="text/javascript">
					filter_tree = <xsl:value-of select="filter_tree"/>;
				</script>
				<!-- markup for expand/contract links -->
				<div id="treecontrol">
					<a id="collapse1" title="Collapse the entire tree below" href="#">
						<xsl:value-of select="php:function('lang', 'collapse all')"/>
					</a>
					<xsl:text> | </xsl:text>
					<a id="expand1" title="Expand the entire tree below" href="#">
						<xsl:value-of select="php:function('lang', 'expand all')"/>
					</a>
				</div>
				<div id="treeDiv1"></div>
			</fieldset>
		</div>

		<div id="no_result">
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
		</div>
		<div id="result"></div>
	</div>
</xsl:template>
