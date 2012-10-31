<!-- $Id: activity_item.xsl 10096 2012-10-03 07:10:49Z vator $ -->
<!-- item  -->

<xsl:template match="data" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format"><xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')"/></xsl:variable>

<xsl:call-template name="yui_phpgw_i18n"/>
<div id="resource-allocation" class="yui-navset yui-navset-top">
	<h1>
		<xsl:value-of select="php:function('lang', 'Allocation of requirement for')"/> <xsl:value-of select="activity/name"/>
	</h1>
	
	<div class="content-wrp">
		
		<div id="requirement-wrp">
			<ul>
				<li>
					<label for="start_date">Startdato</label><span><xsl:value-of select="php:function('date', $date_format, number(requirement/start_date))"/></span>
				</li>
				<li>
					<label for="end_date">Sluttdato</label>
					<span><xsl:value-of select="php:function('date', $date_format, number(requirement/end_date))"/></span>
				</li>
				<li>
					<label for="no_of_items">Antall</label>
					<span><xsl:value-of select="requirement/no_of_items" /></span>
				</li>
			</ul>
			
			<h3>Kriterier</h3>
				<xsl:for-each select="view_criterias_array">
					<ul>
						<li>
							<label><xsl:value-of select="cust_attribute_data/input_text"/></label>
							<span style="margin-right:5px;"><xsl:value-of select="operator"/></span>
							<xsl:choose>
								<xsl:when test="cust_attribute_data/column_info/type = 'LB'">
									<xsl:for-each select="cust_attribute_data/choice">
										<xsl:if test="//value = id">
											<span><xsl:value-of select="value"/></span>
										</xsl:if>
									</xsl:for-each>
								</xsl:when>
								<xsl:otherwise>
									<span><xsl:value-of select="value"/></span>
								</xsl:otherwise>
							</xsl:choose>
						</li>
					</ul>
				</xsl:for-each>
		</div>
			
			<xsl:variable name="action_url">
				<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:logistic.uirequirement_resource_allocation.save')" />
			</xsl:variable>
			<form action="{$action_url}" method="post">
				<input type="hidden" name="requirement_id" value="{requirement/id}" />

				<div id="resource-list">
					<div class="resource heading">
							<span class="desc">Kort beskrivelse</span>
							<span class="loc_id">Lokasjons id</span>
							<span class="type">Type</span>
							<span class="loc_code">Lokasjons kode</span>
					</div>
					<xsl:for-each select="allocation_suggestions">
						
						<div>				
							<xsl:choose>
						  	<xsl:when test="(position() mod 2) != 1">
						    	<xsl:attribute name="class">resource odd</xsl:attribute>
						    </xsl:when>
						    <xsl:otherwise>
						    	<xsl:attribute name="class">resource even</xsl:attribute>
						    </xsl:otherwise>
						  </xsl:choose>
						
							<input type="checkbox" value="{id}" name="chosen_resources[]" />
							<span class="desc"><xsl:value-of select="short_description" /></span>
							<span class="loc_id"><xsl:value-of select="location_id" /></span>
							<span class="type"><xsl:value-of select="type_lokale" /></span>
							<span class="loc_code"><xsl:value-of select="location_code" /></span>
						</div>
					</xsl:for-each>
				</div>			
				
				<input type="submit" value="Lagre" />
			</form>
	</div>
</div>
</xsl:template>