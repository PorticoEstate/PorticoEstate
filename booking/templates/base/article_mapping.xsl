
<!-- $Id: price_item.xsl 12604 2015-01-15 17:06:11Z nelson224 $ -->
<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="edit">
			<xsl:apply-templates select="edit" />
		</xsl:when>
	</xsl:choose>

</xsl:template>

<!-- add / edit  -->
<xsl:template xmlns:php="http://php.net/xsl" match="edit">
	<xsl:variable name="date_format">
		<xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')" />
	</xsl:variable>

	<div class="content">

		<div id='receipt'></div>
		<div>
			<xsl:variable name="form_action">
				<xsl:value-of select="form_action"/>
			</xsl:variable>

			<script type="text/javascript">
				var lang = <xsl:value-of select="php:function('js_lang', 'Name or company is required', 'next', 'save', 'Name', 'Resource Type', 'Select', 'Active')"/>;
				var initialSelection = <xsl:value-of select="resources_json"/>;

			</script>
			<form id="form" name="form" method="post" action="{$form_action}" class="pure-form pure-form-aligned">
				<div id="tab-content">
					<xsl:value-of disable-output-escaping="yes" select="tabs"/>

					<input type="hidden" id="active_tab" name="active_tab" value="{value_active_tab}"/>
					<div id="first_tab">
						<fieldset>
							<legend>
								<xsl:value-of select="php:function('lang', 'article mapping')"/>
							</legend>
							<xsl:if test="article/id > 0">
								<div class="pure-control-group">
									<label>
										<xsl:value-of select="php:function('lang', 'id')"/>
									</label>
									<xsl:value-of select="article/id"/>
								</div>
							</xsl:if>
							<input type="hidden" id="id" name="id" value="{article/id}"/>
							<input type="hidden" id="article_id" name="article_id" value="{article/article_id}"/>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'category')"/>
								</label>
								<select id="field_article_cat_id" name="article_cat_id" class="pure-input-1-2">
									<xsl:if test="article/id > 0">
										<xsl:attribute name="readonly">
											<xsl:text>readonly</xsl:text>
										</xsl:attribute>
										<xsl:attribute name="style">
											<xsl:text>pointer-events: none;</xsl:text>
										</xsl:attribute>
									</xsl:if>
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:apply-templates select="article_categories/options"/>
								</select>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'article code')"/>
								</label>
								<input type="text" id="article_code" name="article_code" value="{article/article_code}" class="pure-input-1-2" >
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="placeholder">
										<xsl:value-of select="php:function('lang', 'article_code')"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'tax code')"/>
								</label>
								<select id="tax_code" name="tax_code" class="pure-input-1-2" required="required">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<option value="">
										<xsl:value-of select="php:function('lang', 'select')"/>
									</option>
									<xsl:apply-templates select="tax_code_list/options"/>
								</select>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'unit')"/>
								</label>
								<select id="unit" name="unit" class="pure-input-1-2" required="required">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<option value="">
										<xsl:value-of select="php:function('lang', 'select')"/>
									</option>
									<xsl:apply-templates select="unit_list/options"/>
								</select>
							</div>
							<div id="resource_selector" style="display:none;">
								<div class="pure-control-group">
									<label for="field_building_name">
										<xsl:value-of select="php:function('lang', 'Building')" />
									</label>
									<input id="field_building_id" name="building_id" type="hidden">
										<xsl:attribute name="value">
											<xsl:value-of select="article/building_id"/>
										</xsl:attribute>
									</input>
									<input id="field_building_name" name="building_name" type="text" class="pure-input-1-2" >
										<xsl:attribute name="value">
											<xsl:value-of select="article/building_name"/>
										</xsl:attribute>
									</input>
									<div id="building_container"></div>
								</div>
								<div class="pure-control-group">
									<label style="vertical-align:top;">
										<xsl:value-of select="php:function('lang', 'Resources')" />
									</label>
									<div id="resources_container" style="display:inline-block;">
										<span class="select_first_text">
											<xsl:value-of select="php:function('lang', 'Select a building first')" />
										</span>
									</div>
								</div>
							</div>

							<div id="service_container" class="pure-control-group" style="display:none;">
								<label>
									<xsl:value-of select="php:function('lang', 'service')"/>
								</label>
								<select id="field_service_id" name="service_id" class="pure-input-1-2" >
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:apply-templates select="service_list/options"/>
								</select>
							</div>

						</fieldset>
					</div>
					<div id="prizing">
						<fieldset>
							<legend>
								<xsl:value-of select="php:function('lang', 'prizing')"/>
							</legend>

							<div class="pure-control-group">
								<xsl:variable name="lang_date_from">
									<xsl:value-of select="php:function('lang', 'date from')"/>
								</xsl:variable>
								<label>
									<xsl:value-of select="$lang_date_from"/>
								</label>
								<input type="text" id="date_from" name="article_prizing[date_from]" size="10" readonly="readonly" >
									<xsl:if test="article_prizing/date_from != 0 and article_prizing/date_from != ''">
										<xsl:attribute name="value">
											<xsl:value-of select="php:function('date', $date_format, number(article_prizing/date_from))"/>
										</xsl:attribute>
									</xsl:if>
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="$lang_date_from"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'price')"/>
								</label>
								<input type="text" id="price" name="article_prizing[price]" size="10" value="{article_prizing/price}" >
									<xsl:attribute name="data-validation">
										<xsl:text>number</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-allowing">
										<xsl:text>float</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-decimal-separator">
										<xsl:text>,</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'price')"/>
									</xsl:attribute>
									<xsl:attribute name="placeholder">
										<xsl:value-of select="php:function('lang', 'float')"/>
									</xsl:attribute>

								</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'remark')"/>
								</label>
								<input type="text" id="remark" name="article_prizing[remark]" value="{article_prizing/remark}" class="pure-input-1-2" >
								</input>
							</div>

							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'history')"/>
								</label>
								<div class="pure-custom">
									<xsl:for-each select="datatable_def">
										<xsl:if test="container = 'datatable-container_0'">
											<xsl:call-template name="table_setup">
												<xsl:with-param name="container" select ='container'/>
												<xsl:with-param name="requestUrl" select ='requestUrl'/>
												<xsl:with-param name="ColumnDefs" select ='ColumnDefs'/>
												<xsl:with-param name="data" select ='data'/>
												<xsl:with-param name="config" select ='config'/>
											</xsl:call-template>
										</xsl:if>
									</xsl:for-each>
								</div>
							</div>

						</fieldset>
					</div>

					<div id='files'>
						<fieldset>
							<legend>
								<xsl:value-of select="php:function('lang', 'documents')"/>
							</legend>

							<xsl:choose>
								<xsl:when test="fileupload = 1">
									<div class="pure-control-group">
										<label>
											<xsl:value-of select="php:function('lang', 'upload files')"/>
										</label>

										<xsl:call-template name="multi_upload_file_inline">
											<xsl:with-param name="class">pure-input-3-4 pure-custom</xsl:with-param>
											<xsl:with-param name="multi_upload_action">
												<xsl:value-of select="multi_upload_action"/>
											</xsl:with-param>
											<xsl:with-param name="section">documents</xsl:with-param>
										</xsl:call-template>
									</div>

								</xsl:when>
							</xsl:choose>

							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'files')"/>
								</label>
								<div class="pure-custom pure-input-3-4">
									<xsl:for-each select="datatable_def">
										<xsl:if test="container = 'datatable-container_1'">
											<xsl:call-template name="table_setup">
												<xsl:with-param name="container" select ='container'/>
												<xsl:with-param name="requestUrl" select ='requestUrl'/>
												<xsl:with-param name="ColumnDefs" select ='ColumnDefs'/>
												<xsl:with-param name="tabletools" select ='tabletools' />
												<xsl:with-param name="data" select ='data'/>
												<xsl:with-param name="config" select ='config'/>
											</xsl:call-template>
										</xsl:if>
									</xsl:for-each>
								</div>
							</div>

						</fieldset>



					</div>



				</div>
				<div id="submit_group_bottom" class="proplist-col">
					<xsl:variable name="lang_save">
						<xsl:value-of select="php:function('lang', 'next')"/>
					</xsl:variable>
					<input type="button" class="pure-button pure-button-primary" name="save" id="save_button_bottom" onClick="validate_submit();">
						<xsl:attribute name="value">
							<xsl:value-of select="$lang_save"/>
						</xsl:attribute>
						<xsl:attribute name="title">
							<xsl:value-of select="$lang_save"/>
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


