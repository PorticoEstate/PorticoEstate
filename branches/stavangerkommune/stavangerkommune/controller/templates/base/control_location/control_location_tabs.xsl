<!-- $Id$ -->
<!-- separate tabs and  inline tables-->

<xsl:template match="data" xmlns:php="http://php.net/xsl">
<div class="yui-navset yui-navset-top" id="control_location_tabview">

<div id="choose_control" class="select-box">
				
				<!-- When control area is chosen, an ajax request is executed. 
					 The operation fetches controls from db and populates the control list.
					 The ajax operation is handled in ajax.js 
				 --> 
				 <label>Velg kontrollen du vil vise bygg for</label>
				 <select id="control_area_list" name="control_area_list">
					<option value="">Velg kontrollområde</option>
					<xsl:for-each select="control_areas_array">
						<option value="{id}">
							<xsl:value-of disable-output-escaping="yes" select="name"/>
						</option>
					</xsl:for-each>
				  </select>
				 
				 <form id="loc_form" action="" method="GET">
					<select id="control_id" name="control_id">
						<xsl:choose>
							<xsl:when test="control_array/child::node()">
								<xsl:for-each select="control_array">
									<xsl:variable name="control_id"><xsl:value-of select="id"/></xsl:variable>
									<option value="{$control_id}">
										<xsl:value-of select="title"/>
									</option>
								</xsl:for-each>
							</xsl:when>
							<xsl:otherwise>
								<option>
									Ingen kontroller
								</option>
							</xsl:otherwise>
						</xsl:choose>
					</select>
				</form>
			</div>

	<xsl:choose>
		<xsl:when test="view = 'view_locations_for_control'">
			<div class="identifier-header">
				<h1><xsl:value-of select="php:function('lang', 'Locations_for_control')"/></h1>
			</div>
			<!-- Prints tabs array -->
			<xsl:value-of disable-output-escaping="yes" select="tabs" />
			 
			<xsl:call-template name="view_locations_for_control" />
		</xsl:when>
		<xsl:when test="view = 'register_control_to_location'">
			<div class="identifier-header">
				<h1>Legg kontroll til bygg</h1>
			</div>
			<!-- Prints tabs array -->
			<xsl:value-of disable-output-escaping="yes" select="tabs" />
			<xsl:call-template name="register_control_to_location" />
		</xsl:when>
	</xsl:choose>
</div>
	
</xsl:template>
