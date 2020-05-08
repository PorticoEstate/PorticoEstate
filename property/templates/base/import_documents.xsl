<!-- $Id$ -->
<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="jquery_phpgw_i18n"/>
	<style>
		.delete_file
		{
		float: right;
		}
		.remove_tag
		{
		float: right;
		}
		.dt-buttons
		{
		width:100%;
		}
	</style>

	<script type="text/javascript">
		var lang = <xsl:value-of select="php:function('js_lang', 'order id', 'building part', 'branch', 'document categories', 'cadastral unit', 'location code', 'building number', 'Missing value', 'Missing info')"/>
	</script>
	<div class="container">
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="tabs"/>
			<div id="step_1">
				<fieldset class="pure-form pure-form-aligned">
					<!--<h1>Step 1 - Order reference</h1>-->
					<div id="message_step_1" class='error' style="display:none;"/>
					<div class="pure-control-group">
						<label >
							<xsl:value-of select="php:function('lang', 'order id')"/>
						</label>
						<input id="order_id" required="required" value="{order_id}"></input>
					</div>
					<div id="order_info" style="display:none;">

						<div class="pure-control-group">
							<label >
								<xsl:value-of select="php:function('lang', 'vendor')"/>
							</label>
							<div class="pure-custom" id="vendor_name"></div>
						</div>
						<div class="pure-control-group">
							<label >
								<xsl:value-of select="php:function('lang', 'cadastral unit')"/>
							</label>
							<input id="cadastral_unit" required="required"></input>
						</div>
						<div class="pure-control-group">
							<label >
								<xsl:value-of select="php:function('lang', 'location code')"/>
							</label>
							<input id="location_code" required="required"></input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'building number')"/>
							</label>
							<input id="building_number" required="required"></input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'remark')"/>
							</label>
							<input id="remark"></input>
						</div>
					</div>
					<div class="pure-controls">
						<button type="button" id="get_order_info" class="pure-button pure-button-primary" onClick="get_order_info();">
							<xsl:value-of select="php:function('lang', 'get info')"/>
						</button>
						<button type="button" id="validate_step_1" class="pure-button pure-button-primary" onClick="validate_step_1();" style="display:none;">
							<xsl:value-of select="php:function('lang', 'validate')"/>
						</button>
					</div>

				</fieldset>
			</div>
			<div id="step_2" style="display:none;">
				<h1>2) laste opp alle dokumentene til venterommet</h1>

				<fieldset id="fieldset_file_input" class="pure-form pure-form-aligned">
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'upload files')"/>
						</label>

						<xsl:call-template name="multi_upload_file_inline">
							<xsl:with-param name="class">pure-input-3-4 pure-custom</xsl:with-param>
							<xsl:with-param name="multi_upload_action">
								<xsl:value-of select="multi_upload_action"/>
							</xsl:with-param>
						</xsl:call-template>
					</div>


					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'document categories')"/>
						</label>

						<select id='document_category' multiple="multiple">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'select')"/>
							</xsl:attribute>
							<xsl:apply-templates select="document_category_list/options"/>
						</select>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'branch')"/>
						</label>

						<select id='branch' multiple="multiple">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'select')"/>
							</xsl:attribute>
							<xsl:apply-templates select="branch_list/options"/>
						</select>
					</div>
					<div class="pure-control-group">
						<xsl:variable name="lang_building_part">
							<xsl:value-of select="php:function('lang', 'building part')"/>
						</xsl:variable>
						<label>
							<xsl:value-of select="$lang_building_part"/>
						</label>

						<select id="building_part" class="pure-input-3-4"  multiple="multiple">
							<xsl:attribute name="title">
								<xsl:value-of select="$lang_building_part"/>
							</xsl:attribute>
							<xsl:attribute name="data-validation">
								<xsl:text>required</xsl:text>
							</xsl:attribute>
							<xsl:attribute name="data-validation-error-msg">
								<xsl:value-of select="$lang_building_part"/>
							</xsl:attribute>
							<xsl:apply-templates select="building_part_list/options"/>
						</select>
					</div>

					<xsl:for-each select="datatable_def">
						<xsl:if test="container = 'datatable-container_0'">
							<div class="pure-control-group">
								<div id="message_step_2" class='error' style="display:none; width:80%;"/>
								<label>
									<xsl:value-of select="php:function('lang', 'files')"/>
								</label>
								<div class="pure-custom pure-u-md-3-4" >
									<xsl:call-template name="table_setup">
										<xsl:with-param name="container" select ='container'/>
										<xsl:with-param name="requestUrl" select ='requestUrl'/>
										<xsl:with-param name="ColumnDefs" select ='ColumnDefs'/>
										<xsl:with-param name="data" select ='data'/>
										<xsl:with-param name="tabletools" select ='tabletools' />
										<xsl:with-param name="config" select ='config'/>
									</xsl:call-template>
								</div>
							</div>
						</xsl:if>
					</xsl:for-each>
					<div class="pure-controls">
						<div id="validate_message"></div>
						<button type="button" class="pure-button pure-button-primary" onClick="validate_step_2(false);">
							<xsl:value-of select="php:function('lang', 'validate')"/>
						</button>
						<button type="button" id="step_2_view_all" class="pure-button pure-button-primary" onClick="refresh_files();" style="display:none;">
							<xsl:value-of select="php:function('lang', 'view all')"/>
						</button>
						<button type="button" id="step_2_next" class="pure-button pure-button-primary" onClick="validate_step_2(true);" style="display:none;">
							<xsl:value-of select="php:function('lang', 'next')"/>
						</button>
						<button type="button" id="step_2_import" class="pure-button pure-button-primary" onClick="step_2_import();" style="display:none;">
							<xsl:value-of select="php:function('lang', 'Import')"/>
						</button>
					</div>

				</fieldset>
			</div>
			<div id="step_3">
				<h1>3) Alt er klart, du kan nå lukke vinduet</h1>
			</div>
		</div>
	</div>

</xsl:template>


<!-- END cat_list -->


<xsl:template match="options">
	<xsl:param name="selected"/>
	<option value="{id}">
		<!--<xsl:if test="selected = 1 or id = $selected or contains($selected, id )">-->
		<xsl:if test="selected = 1 or id = $selected">
			<xsl:attribute name="selected" value="selected"/>
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>