<!--
	Function
	phpgw:conditional( expression $test, mixed $true, mixed $false )
	Evaluates test expression and returns the contents in the true variable if
	the expression is true and the contents of the false variable if its false

	Returns mixed
-->
<func:function name="phpgw:conditional">
	<xsl:param name="test"/>
	<xsl:param name="true"/>
	<xsl:param name="false"/>

	<func:result>
		<xsl:choose>
			<xsl:when test="$test">
	        	<xsl:value-of select="$true"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$false"/>
			</xsl:otherwise>
		</xsl:choose>
  	</func:result>
</func:function>

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
							<dd><xsl:value-of select="data/id"/></dd>
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
							<xsl:choose>
								<xsl:when test="access = 1">
									<input type="submit">	
										<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'rental_rc_save')"/></xsl:attribute>
									</input>
									<a class="cancel">
									<xsl:attribute name="href"><xsl:value-of select="cancel_link"></xsl:value-of></xsl:attribute>
				       					<xsl:value-of select="php:function('lang', 'rental_rc_cancel')"/>
				       				 </a>
				       			</xsl:when>
				       			<xsl:otherwise>
				       				<a class="cancel">
									<xsl:attribute name="href"><xsl:value-of select="cancel_link"></xsl:value-of></xsl:attribute>
				       					<xsl:value-of select="php:function('lang', 'rental_rc_back')"/>
				       				 </a>
				       			</xsl:otherwise>
							</xsl:choose>
							
						</div>
					</form>
				</div>
				
				<div id="elements">
					<h4><xsl:value-of select="php:function('lang', 'rental_rc_added_areas')" /></h4>
						<div class="datatable">
				    	<div id="datatable-container"/>
				  		<xsl:call-template name="datasource-definition" />
				  	</div>
					<h4><xsl:value-of select="php:function('lang', 'rental_rc_add_area')" /></h4>
				</div>
				
				<div id="contracts">
					<p>kontrakter</p>
				</div>
				
				<div id="documents">
					<div id="documents_container"/>
					
					<script type="text/javascript">
						var  composite_id = <xsl:value-of select="data/id"/>;
						    <![CDATA[
						YAHOO.util.Event.addListener(window, "load", function() {
							var url = 'index.php?menuaction=rental.uidocument_composite.index&sort=name&filter_owner_id=' + composite_id + '&phpgw_return_as=json&';
							var colDefs = [{key: 'name', label: 'Name', formatter: YAHOO.rental.formatLink}, {key: 'category', label: 'Category'}, {key: 'actions', label: 'Actions', formatter: YAHOO.rental.formatGenericLink('Edit', 'Delete')}];
							YAHOO.rental.inlineTableHelper('documents_container', url, colDefs);
						});
						
						]]>
					</script>
					
				</div>
			</div>
		</div>
</xsl:template>

<xsl:template name="datasource-definition">
	<script>
		YAHOO.rental.setupDatasource = function() {
			<xsl:if test="//datatable/source">
	            YAHOO.rental.dataSourceUrl = '<xsl:value-of select="//datatable/source"/>';
	        </xsl:if>

			YAHOO.rental.columnDefs = [
				<xsl:for-each select="//datatable/field">
					{
						key: "<xsl:value-of select="key"/>",
						<xsl:if test="label">
						label: "<xsl:value-of select="label"/>",
					    </xsl:if>
						sortable: <xsl:value-of select="phpgw:conditional(not(sortable = 0), 'true', 'false')"/>,
						<xsl:if test="hidden">
						hidden: <xsl:value-of select="hidden"/>,
					    </xsl:if>
						<xsl:if test="formatter">
						formatter: <xsl:value-of select="formatter"/>,
					    </xsl:if>
						className: "<xsl:value-of select="className"/>"
					}<xsl:value-of select="phpgw:conditional(not(position() = last()), ',', '')"/>
				</xsl:for-each>
			];
		}
	</script>
</xsl:template>
