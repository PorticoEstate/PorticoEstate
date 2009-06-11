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

<xsl:include href="rental/templates/base/datasource_definition.xsl"/>

<xsl:template match="phpgw" xmlns:php="http://php.net/xsl">
	<script>
		YAHOO.rental.numberOfDatatables = <xsl:value-of select="count(//datatable)"/>;
		YAHOO.rental.setupDatasource = new Array();
	</script>
	
	<div id="rental_user_error">
		<xsl:value-of select="data/error"/>
	</div>
	<div id="rental_user_message">
		<xsl:value-of select="data/message"/>
	</div>
	<h3><xsl:value-of select="php:function('lang', 'rental_rc_rental_composite')" />: <xsl:value-of select="data/composite/name"/></h3>
	<div id="composite_edit_tabview" class="yui-navset">
		<xsl:value-of disable-output-escaping="yes" select="data/tabs" />
		<div class="yui-content">
			<xsl:apply-templates select="data/composite"/>
			<div id="elements">
				<xsl:apply-templates select="data/datatable_included_areas" />
				<xsl:if test="//access = 1">
	    			<xsl:apply-templates select="data/datatable_available_areas" />
	    		</xsl:if>
			</div>
			<div id="contracts">
			    <xsl:apply-templates select="data/datatable_contracts" />
			</div>
			<!--<div id="documents">
				<div id="documents_container">					
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
			-->
		</div>
	</div>		
</xsl:template>

<xsl:template match="composite" xmlns:php="http://php.net/xsl">
	<div id="details">
		<form action="#" method="post">
			<dl class="proplist-col">
				<dt>
					<label for="name"><xsl:value-of select="php:function('lang', 'rental_rc_name')" /></label>
				</dt>
				<dd>
					<input type="text" name="name" id="name">
						<xsl:if test="../access = 0">
							<xsl:attribute name="disabled" value="true"/>
						</xsl:if>
						<xsl:attribute name="value"><xsl:value-of select="name"/></xsl:attribute>
					</input>
				</dd>
				
				<dt><xsl:value-of select="php:function('lang', 'rental_rc_address')" /></dt>
				<dd>
					<xsl:value-of select="adresse1"/>
					<xsl:if test="adresse2 != ''">
						<br /><xsl:value-of select="adresse2"/>
					</xsl:if>
					<br />
					<xsl:if test="postnummer != '0'">
						<br /><xsl:value-of select="postnummer"/>&#160;<xsl:value-of select="poststed"/>
					</xsl:if>
				</dd>
				
				<dt>
					<label for="address_1"><xsl:value-of select="php:function('lang', 'rental_rc_overridden_address')" /></label>
					/ <label for="house_number"><xsl:value-of select="php:function('lang', 'rental_rc_house_number')" /></label>
				</dt>
				<dd>
					<input type="text" name="address_1" id="address_1">
						<xsl:if test="../access = 0">
							<xsl:attribute name="disabled" value="true"/>
						</xsl:if>
						<xsl:attribute name="value"><xsl:value-of select="address_1"/></xsl:attribute>
					</input>
					<input type="text" name="house_number" id="house_number">
						<xsl:if test="../access = 0">
							<xsl:attribute name="disabled" value="true"/>
						</xsl:if>
						<xsl:attribute name="value"><xsl:value-of select="house_number"/></xsl:attribute>
					</input>
				</dd>				
				<dt>
					<label for="postcode"><xsl:value-of select="php:function('lang', 'rental_rc_post_code')" /></label> / <label for="place"><xsl:value-of select="php:function('lang', 'rental_rc_post_place')" /></label>
				</dt>
				<dd>
					<input type="text" name="postcode" id="postcode" class="postcode">
						<xsl:if test="../access = 0">
							<xsl:attribute name="disabled" value="true"/>
						</xsl:if>
						<xsl:attribute name="value"><xsl:value-of select="postcode"/></xsl:attribute>
					</input>
					<input type="text" name="place" id="place">
						<xsl:if test="../access = 0">
							<xsl:attribute name="disabled" value="true"/>
						</xsl:if>
						<xsl:attribute name="value"><xsl:value-of select="place"/></xsl:attribute>
					</input>
				</dd>
			</dl>
			
			<dl class="proplist-col">
				<dt><xsl:value-of select="php:function('lang', 'rental_rc_serial')" /></dt>
				<dd><xsl:value-of select="id"/></dd>
				<dt><xsl:value-of select="php:function('lang', 'rental_rc_area_gros')" /></dt>
				<dd><xsl:value-of select="area_gros"/> m<sup>2</sup></dd>
				<dt><xsl:value-of select="php:function('lang', 'rental_rc_area_net')" /></dt>
				<dd><xsl:value-of select="area_net"/> m<sup>2</sup></dd>
				<dt><xsl:value-of select="php:function('lang', 'rental_rc_propertyident')" /></dt>
				<dd><xsl:value-of select="gab_id"/></dd>
				
				<dt>
					<label for="is_active"><xsl:value-of select="php:function('lang', 'rental_rc_available?')" /></label>
				</dt>
				<dd>
					<input type="checkbox" name="is_active" id="is_active">
						<xsl:if test="../access = 0">
							<xsl:attribute name="disabled" value="true"/>
						</xsl:if>
						<xsl:if test="is_active = 1">
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
						<xsl:if test="../access = 0">
							<xsl:attribute name="disabled" value="true"/>
						</xsl:if>
						<xsl:value-of select="description"/>
					</textarea>
				</dd>
			</dl>
			
			<div class="form-buttons">
				<xsl:choose>
					<xsl:when test="../access = 1">
						<input type="submit">	
							<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'rental_rc_save')"/></xsl:attribute>
						</input>
						<a class="cancel">
						<xsl:attribute name="href"><xsl:value-of select="../cancel_link"></xsl:value-of></xsl:attribute>
	       					<xsl:value-of select="php:function('lang', 'rental_rc_cancel')"/>
	       				 </a>
	       			</xsl:when>
	       			<xsl:otherwise>
	       				<a class="cancel">
						<xsl:attribute name="href"><xsl:value-of select="../cancel_link"></xsl:value-of></xsl:attribute>
	       					<xsl:value-of select="php:function('lang', 'rental_rc_back')"/>
	       				 </a>
	       			</xsl:otherwise>
				</xsl:choose>
			</div>
		</form>
	</div>
</xsl:template>

<xsl:template match="form">
	<form id="queryForm">
		<xsl:attribute name="method">
			<xsl:value-of select="phpgw:conditional(not(method), 'GET', method)"/>
		</xsl:attribute>

		<xsl:attribute name="action">
			<xsl:value-of select="phpgw:conditional(not(action), '', action)"/>
		</xsl:attribute>
        <xsl:for-each select="*">
        	<xsl:if test="./toolbar">
        		<xsl:call-template name="toolbar"/>
        	</xsl:if>
        </xsl:for-each>
	</form>
</xsl:template>

<xsl:template name="toolbar">
    <div id="toolbar"><table class="toolbartable"><tr>
    	<td class="toolbarlabel"><label><b><xsl:value-of select="./label"/></b></label></td>
        <xsl:for-each select="*">
        	<div class="toolbarelement">
	        	<xsl:if test="control = 'input'">
	        		<td class="toolbarcol">
					<label class="toolbar_element_label">
				    <xsl:attribute name="for"><xsl:value-of select="phpgw:conditional(not(id), '', id)"/></xsl:attribute>
				    <xsl:value-of select="phpgw:conditional(not(text), '', text)"/>
				    </label>
				    <input>
			        	<xsl:attribute name="id"><xsl:value-of select="phpgw:conditional(not(id), '', id)"/></xsl:attribute>
			    		<xsl:attribute name="type"><xsl:value-of select="phpgw:conditional(not(type), '', type)"/></xsl:attribute>
			    		<xsl:attribute name="name"><xsl:value-of select="phpgw:conditional(not(name), '', name)"/></xsl:attribute>
			    		<xsl:attribute name="onClick"><xsl:value-of select="phpgw:conditional(not(onClick), '', onClick)"/></xsl:attribute>
			    		<xsl:attribute name="value"><xsl:value-of select="phpgw:conditional(not(value), '', value)"/></xsl:attribute>
			    		<xsl:attribute name="href"><xsl:value-of select="phpgw:conditional(not(href), '', href)"/></xsl:attribute>
			    		<!-- <xsl:attribute name="class">yui-button yui-menu-button yui-skin-sam yui-split-button yui-button-hover button</xsl:attribute> -->
				    </input>
				    </td>
				</xsl:if>
				<xsl:if test="control = 'select'">
					<td class="toolbarcol">
					<label class="toolbar_element_label">
				    <xsl:attribute name="for"><xsl:value-of select="phpgw:conditional(not(id), '', id)"/></xsl:attribute>
				    <xsl:value-of select="phpgw:conditional(not(text), '', text)"/>
				    </label>
				    <select>
					<xsl:attribute name="id"><xsl:value-of select="phpgw:conditional(not(id), '', id)"/></xsl:attribute>
					<xsl:attribute name="name"><xsl:value-of select="phpgw:conditional(not(name), '', name)"/></xsl:attribute>
					<xsl:attribute name="onchange"><xsl:value-of select="phpgw:conditional(not(onchange), '', onchange)"/></xsl:attribute>
			   		<xsl:for-each select="keys">
			   			<xsl:variable name="p" select="position()" />
			   			<option>
			   				<xsl:attribute name="value"><xsl:value-of select="text()"/></xsl:attribute>
			   				<xsl:if test="text() = ../default"><xsl:attribute name="default"/></xsl:if>
			   				<xsl:value-of select="../values[$p]"/>
			   			</option>
			   		</xsl:for-each>
			   		</select>
			   		</td>
				</xsl:if>
			</div>
        </xsl:for-each> 
    </tr></table></div>
</xsl:template>

<xsl:template match="datatable_included_areas" xmlns:php="http://php.net/xsl">
	<h4><xsl:value-of select="php:function('lang', 'rental_rc_added_areas')" /></h4>
	<xsl:apply-templates select="form" />
	<div class="datatable">
		<div id="datatable-container">
			<xsl:call-template name="datasource-definition" >
				<xsl:with-param name="number">1</xsl:with-param>
				<xsl:with-param name="form">queryForm</xsl:with-param>
				<xsl:with-param name="filters">queryForm</xsl:with-param>
				<xsl:with-param name="container_name">datatable-container</xsl:with-param>
				<xsl:with-param name="context_menu_labels">
					<xsl:choose>
						<xsl:when test="../access = 1">
							['<xsl:value-of select="php:function('lang', 'rental_cm_remove')"/>']
						</xsl:when>
						<xsl:otherwise>
							[]
						</xsl:otherwise>
					</xsl:choose>
				</xsl:with-param>
				<xsl:with-param name="context_menu_actions">
					<xsl:choose>
						<xsl:when test="../access = 1">
							['remove_unit']
						</xsl:when>
						<xsl:otherwise>
							[]
						</xsl:otherwise>
					</xsl:choose>
				</xsl:with-param>
			</xsl:call-template>
		</div>
	</div>
</xsl:template>

<xsl:template match="datatable_available_areas" xmlns:php="http://php.net/xsl">
	<h4><xsl:value-of select="php:function('lang', 'rental_rc_add_area')" /></h4>
	<xsl:apply-templates select="form" />
	<div class="datatable">
		<div id="datatable-container2">
			<xsl:call-template name="datasource-definition">
				<xsl:with-param name="number">2</xsl:with-param>
				<xsl:with-param name="form">queryForm</xsl:with-param>
				<xsl:with-param name="filters">queryForm</xsl:with-param>
				<xsl:with-param name="container_name">datatable-container2</xsl:with-param>
				<xsl:with-param name="context_menu_labels">
					['<xsl:value-of select="php:function('lang', 'rental_cm_add')"/>']
				</xsl:with-param>
				<xsl:with-param name="context_menu_actions">
					['add_unit']
				</xsl:with-param>
			</xsl:call-template>
		</div>
	</div>
</xsl:template>

<xsl:template match="datatable_contracts" xmlns:php="http://php.net/xsl">
	<h4><xsl:value-of select="php:function('lang', 'rental_rc_contracts_containing_this_composite')" /></h4>
	<xsl:apply-templates select="form" />
	<div class="datatable">
		<div id="datatable-container-contracts">
			<xsl:call-template name="datasource-definition">
				<xsl:with-param name="number">3</xsl:with-param>
				<xsl:with-param name="form">queryForm</xsl:with-param>
				<xsl:with-param name="filters">queryForm</xsl:with-param>
				<xsl:with-param name="container_name">datatable-container-contracts</xsl:with-param>
			</xsl:call-template>
		</div>
	</div>
</xsl:template>


