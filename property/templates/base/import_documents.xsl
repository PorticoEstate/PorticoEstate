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

		.processing-import
		{
		display: block;
		margin-left: auto;
		margin-right: auto;
		}

		.select-info
		{
		padding-left: 4px;
		}
	</style>

	<script type="text/javascript">
		var lang = <xsl:value-of select="php:function('js_lang', 'order id', 'building part', 'branch', 'document categories', 'cadastral unit', 'location code', 'building number', 'Missing value', 'Missing info')"/>
		var role = '<xsl:value-of select="role"/>';

	</script>
	<div class="container">

		<h5>
			<xsl:value-of select="role"/>
		</h5>
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
					<div id="input_secret" class="pure-control-group" style="display:none;">
						<label >
							<xsl:value-of select="php:function('lang', 'secret')"/>
						</label>
						<input id="secret" required="required" value="{secret}"></input>
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
				<p id="select_upload_alternative_1">
					<input type="radio" name = "upload_alternative"></input> Alternativ 1: laste opp alle dokumentene som en pakket fil (ZIP eller RAR)</p>
				<p id="select_upload_alternative_2">
					<input  type="radio" name = "upload_alternative"></input> Alternativ 2: laste opp alle dokumentene fra samme katalog</p>

				<fieldset  class="pure-form pure-form-aligned">
					<div id="upload_alternative_1" class="pure-form pure-form-aligned" style="display:none">
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'compressed')"/> (zip, rar)
							</label>
							<input type="file" id="fileupload_zip" class="pure-input-3-4"></input>
							<div class="content_upload_download" id="content_upload_zip"></div>
						</div>
					</div>
					<div id="upload_alternative_2" class="pure-control-group" style="display:none">
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
							<xsl:value-of select="php:function('lang', 'remark')"/>
						</label>

						<input id="remark_detail" class="pure-input-3-4"></input>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'document categories')"/>
						</label>

						<select id='document_category' class="pure-input-3-4" multiple="multiple">
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

						<select id='branch' class="pure-input-3-4" multiple="multiple">
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

					<div class="pure-control-group">
						<div id="message_step_2" class='error' style="display:none; width:80%;"/>
						<img src="{image_loader}" class="processing-import" style="display:none;"></img>
						<div class="pure-input-1" >
							<xsl:for-each select="datatable_def">
								<xsl:if test="container = 'datatable-container_0'">
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
					<div id="validate_message"></div>
					<div class="content_upload_download" id="import_status_wrapper" style="display:none;">
						<p class="file" id="import_status_content">
							<span id="status_value"></span>
						</p>
					</div>
					<div class="pure-controls">
						<button type="button" id="step_2_validate" class="pure-button pure-button-primary" onClick="validate_step_2(0);" style="display:none;">
							<xsl:value-of select="php:function('lang', 'validate')"/>
						</button>
						<button type="button" id="step_2_view_all" class="pure-button pure-button-primary" onClick="refresh_files();" style="display:none;">
							<xsl:value-of select="php:function('lang', 'view all')"/>
						</button>
						<button type="button" id="step_2_next" class="pure-button pure-button-primary" onClick="validate_step_2(1);" style="display:none;">
							<xsl:value-of select="php:function('lang', 'next')"/>
						</button>
						<button type="button" id="step_2_import" class="pure-button pure-button-primary" onClick="step_2_import();" style="display:none;">
							<xsl:value-of select="php:function('lang', 'Import')"/>
						</button>
						<button type="button" id="step_2_import_validate" class="pure-button pure-button-primary" onClick="validate_step_2(2);" style="display:none;">
							<xsl:value-of select="php:function('lang', 'validate import')"/>
						</button>
						<button type="button" id="step_2_import_validate_next" class="pure-button pure-button-primary" onClick="move_to_step_3();" style="display:none;">
							<xsl:value-of select="php:function('lang', 'next')"/>
						</button>
					</div>

				</fieldset>
			</div>
			<div id="step_3">

				<xsl:choose>
					<xsl:when test="role = 'manager'">
						<h1>3) Alt er klart, du kan slette filene fra venterommet</h1>
						<div id="message_step_3" class='msg_good' style="display:none;"/>
						<button type="button" id="step_3_clean_up" class="pure-button pure-button-primary" onClick="step_3_clean_up();">
							<xsl:value-of select="php:function('lang', 'delete files')"/>
						</button>

					</xsl:when>
					<xsl:otherwise>
						<h1>3) Alt er klart, du kan nå lukke vinduet</h1>
					</xsl:otherwise>
				</xsl:choose>
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