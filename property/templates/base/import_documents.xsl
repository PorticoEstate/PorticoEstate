<!-- $Id$ -->


<xsl:template match="data" xmlns:php="http://php.net/xsl">
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

					<div id="message_step_2" class='error' style="display:none;"/>
					<div class="pure-control-group">
						<xsl:for-each select="datatable_def">
							<xsl:if test="container = 'datatable-container_0'">
								<div class="pure-control-group">
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
					</div>
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


<xsl:template match="user_data" xmlns:php="http://php.net/xsl">
	<tr>
		<td>
			<xsl:value-of disable-output-escaping="yes" select="name"/>
		</td>
		<td align="center">
			<select name="values[{control_id}][{part_of_town_id}][{id}][new][]" multiple="multiple" class="user_roles">
				<!--				<option value="">
					<xsl:value-of select="php:function('lang', 'select')"/>
				</option>-->
				<xsl:apply-templates select="../roles/options">
					<xsl:with-param name="selected" select="selected_role"/>
				</xsl:apply-templates>
			</select>
			<input type="hidden" name="values[{control_id}][{part_of_town_id}][{id}][original]" value="{original_value}"/>
		</td>
		<td>
			<xsl:value-of select="lastlogin"/>
		</td>
		<td>
			<xsl:value-of select="status"/>
		</td>
	</tr>
</xsl:template>


<!-- BEGIN cat_list -->

<xsl:template match="edit">

	<section id="tabs">
		<div class="container">
			<div class="row">
				<div id="tab-content" class="col-xs-12 ">

					<xsl:value-of disable-output-escaping="yes" select="tabs"/>

					<div id="category_assignment">
						<form class="pure-form pure-form-aligned" id="form" name="form" method="post" action="{form_action}">
							<table border="0" cellspacing="2" cellpadding="2" class="pure-table pure-table-bordered ">
								<xsl:apply-templates select="cat_header"/>
								<xsl:apply-templates select="cat_data"/>
							</table>
							<xsl:apply-templates select="cat_add"/>
						</form>

					</div>
					<div id="vendors">
					</div>

				</div>
			</div>
		</div>
	</section>

</xsl:template>

<!-- BEGIN cat_header -->

<xsl:template match="cat_header">
	<tr class="th">
		<th width="45%">
			<xsl:value-of select="lang_name"/>
		</th>
		<th width="45%" align="center">
			<xsl:value-of select="lang_edit"/>
		</th>
	</tr>
</xsl:template>

<!-- BEGIN cat_data -->

<xsl:template match="cat_data" xmlns:php="http://php.net/xsl">
	<tr>
		<td>
			<xsl:value-of disable-output-escaping="yes" select="name"/>
		</td>
		<td align="center">
			<select name="values[{control_id}]" >
				<option value="">
					<xsl:value-of select="php:function('lang', 'select')"/>
				</option>
				<xsl:apply-templates select="cat_list/options"/>
			</select>
		</td>
	</tr>
</xsl:template>

<!-- BEGIN cat_add -->

<xsl:template match="cat_add">
	<table>
		<tr height="50" valign="bottom">
			<td colspan="2">
				<xsl:variable name="lang_add">
					<xsl:value-of select="php:function('lang', 'save')"/>
				</xsl:variable>
				<input type="submit" name="save" value="{$lang_add}" class="pure-button pure-button-primary" >
					<xsl:attribute name="title">
						<xsl:value-of select="lang_add_statustext"/>
					</xsl:attribute>
				</input>
			</td>
			<td colspan="3" align="right">
				<xsl:variable name="cancel_url">
					<xsl:value-of select="//cancel_url"/>
				</xsl:variable>
				<input type="button" class="pure-button pure-button-primary" name="cancel" onClick="window.location = '{$cancel_url}';">
					<xsl:attribute name="value">
						<xsl:value-of select="php:function('lang', 'cancel')"/>
					</xsl:attribute>
				</input>
			</td>
		</tr>
	</table>
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