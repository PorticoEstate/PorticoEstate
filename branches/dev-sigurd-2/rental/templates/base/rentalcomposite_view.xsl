<xsl:preserve-space elements="data"/>

<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <h3><xsl:value-of select="php:function('lang', 'Showing')" />: <xsl:value-of select="data/name"/></h3>

		<div id="composite_edit_tabview" class="yui-navset">
			<xsl:value-of disable-output-escaping="yes" select="tabs" />
			<div class="yui-content">
				
				<div id="details">
					<dl class="proplist-col">
						<dt><xsl:value-of select="php:function('lang', 'Name')" /></dt>
						<dd><xsl:value-of select="data/name"/></dd>
						<dt><xsl:value-of select="php:function('lang', 'Address')" /></dt>
						<dd>
							<xsl:choose>
									<xsl:when test="data/has_custom_address = '1'">
										<xsl:value-of select="data/address_1"/>&#160;<xsl:value-of select="data/house_number"/>
										<xsl:if test="data/address_2 != ''">
											<br /><xsl:value-of select="data/address_2"/>
										</xsl:if>
										<xsl:if test="data/postcode != ''">
											<br /><xsl:value-of select="data/postcode"/>&#160;<xsl:value-of select="data/place"/>
										</xsl:if>
									</xsl:when>
									<xsl:otherwise>
										<xsl:value-of select="data/adresse1"/>
										<xsl:if test="data/adresse2 != ''">
											<br /><xsl:value-of select="data/adresse2"/>
										</xsl:if>
										<xsl:if test="data/postnummer != ''">
											<br /><xsl:value-of select="data/postnummer"/>&#160;<xsl:value-of select="data/poststed"/>
										</xsl:if>
									</xsl:otherwise>
								</xsl:choose>
						</dd>
					</dl>
					<dl class="proplist-col">
						<dt><xsl:value-of select="php:function('lang', 'Number')" /></dt>
						<dd><xsl:value-of select="data/composite_id"/></dd>
						<dt><xsl:value-of select="php:function('lang', 'Area')" /></dt>
						<dd><xsl:value-of select="data/area"/> m<sup>2</sup></dd>
						<dt><xsl:value-of select="php:function('lang', 'Property id')" /></dt>
						<dd><xsl:value-of select="data/gab_id"/></dd>
						<dt><xsl:value-of select="php:function('lang', 'Is active')" /></dt>
						<dd>
							<xsl:choose>
								<xsl:when test="data/is_active = 1"><xsl:value-of select="php:function('lang', 'Yes')" /></xsl:when>
								<xsl:otherwise><xsl:value-of select="php:function('lang', 'No')" /></xsl:otherwise>
							</xsl:choose>
						</dd>
					</dl>
					
					<dl class="rental-description">
						<dt><xsl:value-of select="php:function('lang', 'Description')" /></dt>
						<dd><xsl:value-of select="data/description"/></dd>
					</dl>
				</div>
				
				<div id="elements">
					<p>elementer</p>
				</div>
				
				<div id="contracts">
					<p>kontrakter</p>
				</div>
				
				<div id="documents">
					<p>dokumenter</p>
				</div>
			</div>
		</div>
</xsl:template>
