
<!-- $Id: composite.xsl 12604 2015-01-15 17:06:11Z nelson224 $ -->
<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="edit">
			<xsl:apply-templates select="edit"/>
		</xsl:when>
		<xsl:when test="view">
			<xsl:apply-templates select="view"/>
		</xsl:when>
	</xsl:choose>
	<xsl:call-template name="jquery_phpgw_i18n"/>
</xsl:template>

<xsl:template name="top-toolbar">
	<div class="toolbar-container">
		<div class="pure-g">
			<div class="pure-u-1-3">
				<div>
					<xsl:value-of select="php:function('lang', 'name')"/> : <xsl:value-of select="value_name"/>
				</div>
			</div>
		</div>
	</div>
</xsl:template>

<!-- add / edit  -->
<xsl:template xmlns:php="http://php.net/xsl" match="edit">

	<xsl:call-template name="top-toolbar" />
	<div>
		<xsl:variable name="form_action">
			<xsl:value-of select="form_action"/>
		</xsl:variable>

		<form id="form" name="form" method="post" ENCTYPE="multipart/form-data" action="{$form_action}" class="pure-form pure-form-stacked">
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<div id="details" class="pure-g">
					<div class="pure-u-1 pure-u-lg-1-2">
						<input type="hidden" name="id" value="{composite_id}"/>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'name')"/>
							</label>
							<input type="text" name="name" id="name" value="{value_name}">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'address')"/>
							</label>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'location')"/>
							</label>
							<xsl:if test="value_unit_count > 0">
								<input type="hidden" name="part_of_town_id"  value="{value_part_of_town_id}"/>
							</xsl:if>
							<select id="part_of_town_id" name="part_of_town_id">
								<xsl:choose>
									<xsl:when test="value_unit_count > 0">
										<xsl:attribute name="disabled">
											<xsl:text>disabled</xsl:text>
										</xsl:attribute>
									</xsl:when>
									<xsl:otherwise>
										<xsl:attribute name="data-validation">
											<xsl:text>required</xsl:text>
										</xsl:attribute>
									</xsl:otherwise>
								</xsl:choose>
								<xsl:apply-templates select="list_part_of_town/options"/>
							</select>
						</div>
						<xsl:if test="contract_furnished_status = 1">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'custom price')"/>
								</label>
								<input type="text" name="custom_price" id="custom_price" value="{value_custom_price}"/>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'price type')"/>
								</label>
								<xsl:if test="count(//list_price_type/options) > 0">
									<select id="price_type_id" name="price_type_id">
										<xsl:apply-templates select="list_price_type/options"/>
									</select>
								</xsl:if>
							</div>

							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'custom price factor')"/>
								</label>
								<input type="text" name="custom_price_factor" id="custom_price_factor" value="{value_custom_price_factor}"/>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'composite standard')"/>
								</label>
								<xsl:if test="count(//list_composite_standard/options) > 0">
									<select id="composite_standard_id" name="composite_standard_id">
										<xsl:apply-templates select="list_composite_standard/options"/>
									</select>
								</xsl:if>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'composite type')"/>
								</label>
								<xsl:if test="count(//list_composite_type/options) > 0">
									<select id="composite_type_id" name="composite_type_id">
										<xsl:apply-templates select="list_composite_type/options"/>
									</select>
								</xsl:if>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'furnish_type')"/>
								</label>
								<select id="furnish_type_id" name="furnish_type_id">
									<xsl:apply-templates select="list_furnish_type/options"/>
								</select>
							</div>
						</xsl:if>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'has_custom_address')"/>
							</label>
							<input type="checkbox" name="has_custom_address" id="has_custom_address">
								<xsl:if test="has_custom_address = 1">
									<xsl:attribute name="checked" value="checked"/>
								</xsl:if>
							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'overridden_address')"/> / <xsl:value-of select="php:function('lang', 'house_number')"/>
							</label>
							<input type="text" name="address_1" id="address_1" value="{value_custom_address_1}"/>
							<input type="text" name="house_number" id="house_number" value="{value_custom_house_number}"/>
							<input type="text" name="address_2" id="address_2" value="{value_custom_address_2}"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'post_code')"/> / <xsl:value-of select="php:function('lang', 'post_place')"/>
							</label>
							<input type="text" name="postcode" id="postcode" value="{value_custom_postcode}"/>
							<input type="text" name="place" id="place" value="{value_custom_place}"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'description')"/>
							</label>
							<textarea name="description" id="description" rows="10" cols="50">
								<xsl:value-of select="value_description"/>
							</textarea>
						</div>
					</div>
					<div class="pure-u-1 pure-u-lg-1-2">

						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'area_gros')"/>
							</label>
							<xsl:value-of select="value_area_gros"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'area_net')"/>
							</label>
							<xsl:value-of select="value_area_net"/>
						</div>
						<!--div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'available ?')"/>
							</label>
							<input type="checkbox" name="is_active" id="is_active">
								<xsl:if test="is_active = 1">
									<xsl:attribute name="checked" value="checked"/>
								</xsl:if>
							</input>
						</div-->
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'available ?')"/>
							</label>
							<xsl:if test="count(//list_status_id/options) > 0">
								<select id="status_id" name="status_id">
									<xsl:apply-templates select="list_status_id/options"/>
								</select>
							</xsl:if>
						</div>

					</div>
					<xsl:choose>
						<xsl:when test="fileupload = 1">
							<div class="pure-u-1">
								<div class="pure-control-group">
									<label>
										<xsl:value-of select="php:function('lang', 'files')"/>
									</label>
									<div  >
										<xsl:for-each select="datatable_def">
											<xsl:if test="container = 'datatable-container_4'">
												<xsl:call-template name="table_setup">
													<xsl:with-param name="container" select ='container'/>
													<xsl:with-param name="requestUrl" select ='requestUrl'/>
													<xsl:with-param name="ColumnDefs" select ='ColumnDefs'/>
													<xsl:with-param name="data" select ='data'/>
													<xsl:with-param name="tabletools" select ='tabletools' />
													<xsl:with-param name="config" select ='config'/>
												</xsl:call-template>
											</xsl:if>
										</xsl:for-each>
									</div>
								</div>
								<script type="text/javascript">
									var multi_upload_parans = <xsl:value-of select="multi_upload_parans"/>;
								</script>
								<xsl:call-template name="file_upload"/>
							</div>
						</xsl:when>
					</xsl:choose>

				</div>

				<xsl:choose>
					<xsl:when test="composite_id > 0">
						<div id="units">
							<div>
								<xsl:for-each select="datatable_def">
									<xsl:if test="container = 'datatable-container_0'">
										<xsl:call-template name="table_setup">
											<xsl:with-param name="container" select ='container'/>
											<xsl:with-param name="requestUrl" select ='requestUrl' />
											<xsl:with-param name="ColumnDefs" select ='ColumnDefs' />
											<xsl:with-param name="tabletools" select ='tabletools' />
											<xsl:with-param name="data" select ='data' />
											<xsl:with-param name="config" select ='config' />
										</xsl:call-template>
									</xsl:if>
								</xsl:for-each>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'search_options')"/>
								</label>
								<div class="pure-custom">
									<div class="pure-control-group">
										<label>
											<xsl:value-of select="php:function('lang', 'search_for')"/>
										</label>
										<input type="text" id="query" name="query" value=""></input>
										<label>
											<xsl:value-of select="php:function('lang', 'search_where')"/>
										</label>
										<select id="search_option" name="search_option">
											<xsl:apply-templates select="list_search_option/options"/>
										</select>
									</div>
									<div class="pure-control-group">
										<label>
											<xsl:value-of select="php:function('lang', 'level')"/>
										</label>
										<select id="type_id" name="type_id">
											<xsl:apply-templates select="list_type_id/options"/>
										</select>
									</div>
								</div>
							</div>
							<div>
								<xsl:for-each select="datatable_def">
									<xsl:if test="container = 'datatable-container_1'">
										<xsl:call-template name="table_setup">
											<xsl:with-param name="container" select ='container'/>
											<xsl:with-param name="requestUrl" select ='requestUrl' />
											<xsl:with-param name="ColumnDefs" select ='ColumnDefs' />
											<xsl:with-param name="tabletools" select ='tabletools' />
											<xsl:with-param name="data" select ='data' />
											<xsl:with-param name="config" select ='config' />
										</xsl:call-template>
									</xsl:if>
								</xsl:for-each>
							</div>
						</div>
						<div id="contracts">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'search_options')"/>
								</label>
								<div class="pure-custom">
									<div class="pure-control-group">
										<label>
											<xsl:value-of select="php:function('lang', 'search_for')"/>
										</label>
										<input type="text" id="contracts_query" name="contracts_query" value=""></input>
										<label>
											<xsl:value-of select="php:function('lang', 'search_where')"/>
										</label>
										<select id="contracts_search_option" name="contracts_search_option">
											<xsl:apply-templates select="list_contracts_search_options/options"/>
										</select>
									</div>
									<div class="pure-control-group">
										<label>
											<xsl:value-of select="php:function('lang', 'status')"/>
										</label>
										<select id="contract_status" name="contract_status">
											<xsl:apply-templates select="list_status_options/options"/>
										</select>
										<label>
											<xsl:value-of select="php:function('lang', 'date')"/>
										</label>
										<input type="text" id="status_date" name="status_date" value=""></input>
									</div>
									<div class="pure-control-group">
										<label>
											<xsl:value-of select="php:function('lang', 'field_of_responsibility')"/>
										</label>
										<select id="contract_type" name="contract_type">
											<xsl:apply-templates select="list_fields_of_responsibility_options/options"/>
										</select>
									</div>
								</div>
							</div>
							<div>
								<xsl:for-each select="datatable_def">
									<xsl:if test="container = 'datatable-container_2'">
										<xsl:call-template name="table_setup">
											<xsl:with-param name="container" select ='container'/>
											<xsl:with-param name="requestUrl" select ='requestUrl' />
											<xsl:with-param name="ColumnDefs" select ='ColumnDefs' />
											<xsl:with-param name="tabletools" select ='tabletools' />
											<xsl:with-param name="data" select ='data' />
											<xsl:with-param name="config" select ='config' />
										</xsl:call-template>
									</xsl:if>
								</xsl:for-each>
							</div>

							<br />
							<br />

							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'search_options')"/>
								</label>
								<div class="pure-custom">
									<div class="pure-control-group">
										<label>
											<xsl:value-of select="php:function('lang', 'search_for')"/>
										</label>
										<input type="text" id="applications_query" name="applications_query" value=""></input>
									</div>
									<div class="pure-control-group">
										<label>
											<xsl:value-of select="php:function('lang', 'status')"/>
										</label>
										<select id="application_status" name="application_status">
											<xsl:apply-templates select="list_status_application_options/options"/>
										</select>
									</div>
								</div>
							</div>
							<div>
								<xsl:for-each select="datatable_def">
									<xsl:if test="container = 'datatable-container_3'">
										<xsl:call-template name="table_setup">
											<xsl:with-param name="container" select ='container'/>
											<xsl:with-param name="requestUrl" select ='requestUrl' />
											<xsl:with-param name="ColumnDefs" select ='ColumnDefs' />
											<xsl:with-param name="tabletools" select ='tabletools' />
											<xsl:with-param name="data" select ='data' />
											<xsl:with-param name="config" select ='config' />
										</xsl:call-template>
									</xsl:if>
								</xsl:for-each>
							</div>
						</div>
					</xsl:when>
				</xsl:choose>
			</div>
			<div class="proplist-col">
				<input type="submit" class="pure-button pure-button-primary" name="save">
					<xsl:attribute name="value">
						<xsl:value-of select="php:function('lang', 'save')"/>
					</xsl:attribute>
				</input>
				<xsl:variable name="cancel_url">
					<xsl:value-of select="cancel_url"/>
				</xsl:variable>
				<input type="button" class="pure-button pure-button-primary" name="cancel" onClick="window.location = '{cancel_url}';">
					<xsl:attribute name="value">
						<xsl:value-of select="php:function('lang', 'cancel')"/>
					</xsl:attribute>
				</input>
			</div>
		</form>
	</div>
</xsl:template>

<!-- view -->
<xsl:template xmlns:php="http://php.net/xsl" match="view">

	<xsl:call-template name="top-toolbar" />
	<div>
		<form id="form" name="form" method="post" action="" class="pure-form pure-form-stacked">
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<div id="details" class="pure-g">
					<div class="pure-u-1 pure-u-lg-1-2">
						<input type="hidden" name="id" value="{composite_id}"/>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'name')"/>
							</label>
							<xsl:value-of select="value_name"/>
						</div>
						<div class="pure-control-group">
							<xsl:if test="has_custom_address = 1">
								<label>
									<xsl:value-of select="php:function('lang', 'custom_address')"/>
								</label>
								<div class="pure-custom">
									<div>
										<xsl:value-of select="value_custom_address_1"/>
									</div>
									<xsl:if test="value_custom_address_2 != ''">
										<div>
											<xsl:value-of select="value_custom_address_2"/>
										</div>
									</xsl:if>
									<xsl:if test="value_custom_house_number != ''">
										<div>
											<xsl:value-of select="value_custom_house_number"/>
										</div>
									</xsl:if>
									<xsl:if test="value_custom_postcode != ''">
										<div>
											<xsl:value-of select="value_custom_postcode"/>
											<xsl:text> </xsl:text>
											<xsl:value-of select="value_custom_place"/>
										</div>
									</xsl:if>
								</div>
							</xsl:if>
							<xsl:if test="has_custom_address = 0">
								<label>
									<xsl:value-of select="php:function('lang', 'address')"/>
								</label>
								<xsl:value-of select="value_address_1"/>
							</xsl:if>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'composite standard')"/>
							</label>
							<xsl:value-of select="value_composite_standard_name"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'composite type')"/>
							</label>
							<xsl:value-of select="value_composite_type_name"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'furnish_type')"/>
							</label>
							<xsl:value-of select="value_furnish_type_name"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'description')"/>
							</label>
							<xsl:value-of select="value_description"/>
						</div>
					</div>
					<div class="pure-u-1 pure-u-lg-1-2">

						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'area_gros')"/>
							</label>
							<xsl:value-of select="value_area_gros"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'area_net')"/>
							</label>
							<xsl:value-of select="value_area_net"/>
						</div>
						<!--div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'available ?')"/>
							</label>
							<input type="checkbox" name="is_active" id="is_active" disabled="disabled">
								<xsl:if test="is_active = 1">
									<xsl:attribute name="checked" value="checked"/>
								</xsl:if>
							</input>
						</div-->
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'available ?')"/>
							</label>
							<xsl:for-each select="list_status_id/options">
								<xsl:if test="selected != 0">
									<xsl:value-of disable-output-escaping="yes" select="name"/>
								</xsl:if>
							</xsl:for-each>
						</div>
					</div>
				</div>
				<div id="units">
					<div>
						<xsl:for-each select="datatable_def">
							<xsl:if test="container = 'datatable-container_0'">
								<xsl:call-template name="table_setup">
									<xsl:with-param name="container" select ='container'/>
									<xsl:with-param name="requestUrl" select ='requestUrl' />
									<xsl:with-param name="ColumnDefs" select ='ColumnDefs' />
									<xsl:with-param name="tabletools" select ='tabletools' />
									<xsl:with-param name="data" select ='data' />
									<xsl:with-param name="config" select ='config' />
								</xsl:call-template>
							</xsl:if>
						</xsl:for-each>
					</div>
				</div>
				<div id="contracts">
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'search_options')"/>
						</label>
						<div class="pure-custom">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'search_for')"/>
								</label>
								<input type="text" id="contracts_query" name="contracts_query" value=""></input>
								<label>
									<xsl:value-of select="php:function('lang', 'search_where')"/>
								</label>
								<select id="contracts_search_option" name="contracts_search_option">
									<xsl:apply-templates select="list_contracts_search_options/options"/>
								</select>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'status')"/>
								</label>
								<select id="contract_status" name="contract_status">
									<xsl:apply-templates select="list_status_options/options"/>
								</select>
								<label>
									<xsl:value-of select="php:function('lang', 'date')"/>
								</label>
								<input type="text" id="status_date" name="status_date" value=""></input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'field_of_responsibility')"/>
								</label>
								<select id="contract_type" name="contract_type">
									<xsl:apply-templates select="list_fields_of_responsibility_options/options"/>
								</select>
							</div>
						</div>
					</div>
					<div>
						<xsl:for-each select="datatable_def">
							<xsl:if test="container = 'datatable-container_1'">
								<xsl:call-template name="table_setup">
									<xsl:with-param name="container" select ='container'/>
									<xsl:with-param name="requestUrl" select ='requestUrl' />
									<xsl:with-param name="ColumnDefs" select ='ColumnDefs' />
									<xsl:with-param name="tabletools" select ='tabletools' />
									<xsl:with-param name="data" select ='data' />
									<xsl:with-param name="config" select ='config' />
								</xsl:call-template>
							</xsl:if>
						</xsl:for-each>
					</div>
					
					<br />
					<br />
					
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'search_options')"/>
						</label>
						<div class="pure-custom">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'search_for')"/>
								</label>
								<input type="text" id="applications_query" name="applications_query" value=""></input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'status')"/>
								</label>
								<select id="application_status" name="application_status">
									<xsl:apply-templates select="list_status_application_options/options"/>
								</select>
							</div>
						</div>
					</div>
					<div>
						<xsl:for-each select="datatable_def">
							<xsl:if test="container = 'datatable-container_2'">
								<xsl:call-template name="table_setup">
									<xsl:with-param name="container" select ='container'/>
									<xsl:with-param name="requestUrl" select ='requestUrl' />
									<xsl:with-param name="ColumnDefs" select ='ColumnDefs' />
									<xsl:with-param name="tabletools" select ='tabletools' />
									<xsl:with-param name="data" select ='data' />
									<xsl:with-param name="config" select ='config' />
								</xsl:call-template>
							</xsl:if>
						</xsl:for-each>
					</div>
				</div>
			</div>
			<div class="proplist-col">
				<xsl:variable name="cancel_url">
					<xsl:value-of select="cancel_url"/>
				</xsl:variable>
				<input type="button" class="pure-button pure-button-primary" name="cancel" value="{lang_cancel}" onMouseout="window.status='';return true;" onClick="window.location = '{cancel_url}';"/>
			</div>
		</form>
	</div>
</xsl:template>

<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected != 0">
			<xsl:attribute name="selected" value="selected"/>
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>