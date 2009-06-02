<xsl:preserve-space elements="data"/>

<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <h3><xsl:value-of select="php:function('lang', 'rental_rc_rental_composite')" />: <xsl:value-of select="data/name"/></h3>

		<div id="composite_edit_tabview" class="yui-navset">
			<xsl:value-of disable-output-escaping="yes" select="tabs" />
			<div class="yui-content">
				
				<div id="details">
					<form action="#" method="post">
						<dl class="proplist-col">
							<dt>
								<label for="name"><xsl:value-of select="php:function('lang', 'rental_rc_name')" /></label>
							</dt>
							<dd>
								<input type="text" name="name" id="name">
									<xsl:if test="access = 0">
										<xsl:attribute name="disabled" value="true"/>
									</xsl:if>
									<xsl:attribute name="value"><xsl:value-of select="data/name"/></xsl:attribute>
								</input>
							</dd>
							
							<dt><xsl:value-of select="php:function('lang', 'rental_rc_address')" /></dt>
							<dd>
								<xsl:value-of select="data/adresse1"/>
								<xsl:if test="data/adresse2 != ''">
									<br /><xsl:value-of select="data/adresse2"/>
								</xsl:if>
								<xsl:if test="data/postnummer != ''">
									<br /><xsl:value-of select="data/postnummer"/>&#160;<xsl:value-of select="data/poststed"/>
								</xsl:if>
							</dd>
							
							<dt>
								<label for="address_1"><xsl:value-of select="php:function('lang', 'rental_rc_address')" /></label>
								/ <label for="house_number"><xsl:value-of select="php:function('lang', 'rental_rc_house_number')" /></label>
							</dt>
							<dd>
								<input type="text" name="address_1" id="address_1">
									<xsl:if test="access = 0">
										<xsl:attribute name="disabled" value="true"/>
									</xsl:if>
									<xsl:attribute name="value"><xsl:value-of select="data/address_1"/></xsl:attribute>
								</input>
								<input type="text" name="house_number" id="house_number">
									<xsl:if test="access = 0">
										<xsl:attribute name="disabled" value="true"/>
									</xsl:if>
									<xsl:attribute name="value"><xsl:value-of select="data/house_number"/></xsl:attribute>
								</input>
							</dd>
							<dd>
								<input type="text" name="address_2" id="address_2">
									<xsl:if test="access = 0">
										<xsl:attribute name="disabled" value="true"/>
									</xsl:if>
									<xsl:attribute name="value"><xsl:value-of select="data/address_2"/></xsl:attribute>
								</input>
							</dd>
							
							<dt>
								<label for="postcode"><xsl:value-of select="php:function('lang', 'rental_rc_post_code')" /></label> / <label for="place"><xsl:value-of select="php:function('lang', 'rental_rc_post_place')" /></label>
							</dt>
							<dd>
								<input type="text" name="postcode" id="postcode" class="postcode">
									<xsl:if test="access = 0">
										<xsl:attribute name="disabled" value="true"/>
									</xsl:if>
									<xsl:attribute name="value"><xsl:value-of select="data/postcode"/></xsl:attribute>
								</input>
								<input type="text" name="place" id="place">
									<xsl:if test="access = 0">
										<xsl:attribute name="disabled" value="true"/>
									</xsl:if>
									<xsl:attribute name="value"><xsl:value-of select="data/place"/></xsl:attribute>
								</input>
							</dd>
						</dl>
						
						<dl class="proplist-col">
							<dt><xsl:value-of select="php:function('lang', 'rental_rc_serial')" /></dt>
							<dd><xsl:value-of select="data/composite_id"/></dd>
							<dt><xsl:value-of select="php:function('lang', 'rental_rc_area_gros')" /></dt>
							<dd><xsl:value-of select="data/area_gros"/> m<sup>2</sup></dd>
							<dt><xsl:value-of select="php:function('lang', 'rental_rc_area_net')" /></dt>
							<dd><xsl:value-of select="data/area_net"/> m<sup>2</sup></dd>
							<dt><xsl:value-of select="php:function('lang', 'rental_rc_propertyident')" /></dt>
							<dd><xsl:value-of select="data/gab_id"/></dd>
							
							<dt>
								<label for="is_active"><xsl:value-of select="php:function('lang', 'rental_rc_available?')" /></label>
							</dt>
							<dd>
								<input type="checkbox" name="is_active" id="is_active">
									<xsl:if test="access = 0">
										<xsl:attribute name="disabled" value="true"/>
									</xsl:if>
									<xsl:if test="data/is_active = 1">
										<xsl:attribute name="checked">checked</xsl:attribute>
									</xsl:if>
								</input>
							</dd>
						</dl>
						
						<dl class="rental-description-edit">
							<dt>
								<label for="description"><xsl:value-of select="php:function('lang', 'rental_rc_description')" /></label>
							</dt>
							<dd>
								<textarea name="description" id="description" rows="10" cols="50">
									<xsl:if test="access = 0">
										<xsl:attribute name="disabled" value="true"/>
									</xsl:if>
									<xsl:value-of select="data/description"/>
								</textarea>
							</dd>
						</dl>
						
						<div class="form-buttons">
							<xsl:if test="access = 1">
								<input type="submit">	
									<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'rental_rc_save')"/></xsl:attribute>
								</input>
								<a class="cancel">
								<xsl:attribute name="href"><xsl:value-of select="cancel_link"></xsl:value-of></xsl:attribute>
			       					<xsl:value-of select="php:function('lang', 'rental_rc_cancel')"/>
			       				 </a>
							</xsl:if>
						</div>
					</form>
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
