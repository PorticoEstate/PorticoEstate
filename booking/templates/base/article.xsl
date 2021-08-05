
<!-- $Id: price_item.xsl 12604 2015-01-15 17:06:11Z nelson224 $ -->
<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="edit">
			<xsl:apply-templates select="edit" />
		</xsl:when>
		<xsl:when test="view">
			<xsl:apply-templates select="view" />
		</xsl:when>
		<xsl:when test="adjustment_price">
			<xsl:apply-templates select="adjustment_price" />

		</xsl:when>
	</xsl:choose>

</xsl:template>

<!-- add / edit  -->
<xsl:template xmlns:php="http://php.net/xsl" match="edit">
	<div class="content">
		<xsl:variable name="date_format">
			<xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')" />
		</xsl:variable>

		<div id='receipt'></div>
		<div>
			<xsl:variable name="form_action">
				<xsl:value-of select="form_action"/>
			</xsl:variable>

			<script type="text/javascript">
				var lang = <xsl:value-of select="php:function('js_lang', 'Name or company is required', 'next', 'save', 'Name', 'Resource Type', 'Select')"/>;
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
									<input type="hidden" id="article_id" name="id" value="{article/id}"/>
									<xsl:value-of select="article/id"/>
								</div>
							</xsl:if>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'category')"/>
								</label>
								<select id="field_article_cat_id" name="article_cat_id" class="pure-input-1-2">
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
							<div id="resource_selector">
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
								</select>
							</div>

						</fieldset>
					</div>
					<div id="prizing">
						<fieldset>
							<legend>
								<xsl:value-of select="php:function('lang', 'arena requirement')"/>
							</legend>

							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'size of stage')"/>
								</label>
								<input type="text" id="stage_width" name="stage_width" value="{article/stage_width}" size="2">
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'width')"/>
									</xsl:attribute>
									<xsl:attribute name="data-validation">
										<xsl:text>number</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-optional">
										<xsl:text>true</xsl:text>
									</xsl:attribute>
								</input>
								<xsl:text> X </xsl:text>
								<input type="text" id="stage_depth" name="stage_depth" value="{article/stage_depth}" size="2">
									<xsl:attribute name="data-validation">
										<xsl:text>number</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'depth')"/>
									</xsl:attribute>
									<xsl:attribute name="data-validation-optional">
										<xsl:text>true</xsl:text>
									</xsl:attribute>
								</input>
								<xsl:text> M </xsl:text>
								<input id="stage_size" type="text" disabled="disabled" size="3"/>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'stage requirement')"/>
								</label>
								<textarea cols="47" rows="7" name="stage_requirement" class="pure-input-1-2" >
									<xsl:value-of select="article/stage_requirement"/>
								</textarea>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'wardrobe')"/>
								</label>
								<input type="checkbox" name="wardrobe" id="wardrobe" value="1">
									<xsl:if test="article/wardrobe = 1">
										<xsl:attribute name="checked" value="checked"/>
									</xsl:if>
								</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'audience limit')"/>
								</label>
								<input type="text" id="audience_limit" name="audience_limit" value="{article/audience_limit}"  size="5">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'audience limit')"/>
									</xsl:attribute>
									<xsl:attribute name="placeholder">
										<xsl:value-of select="php:function('lang', 'integer')"/>
									</xsl:attribute>
								</input>
							</div>
						</fieldset>
						<fieldset>
							<legend>
								<xsl:value-of select="php:function('lang', 'labour support')"/>
							</legend>

							<div class="pure-control-group">
								<label>
								</label>
								<div class="pure-custom">
									<table class="pure-table pure-table-bordered" border="0" cellspacing="2" cellpadding="2">
										<thead>
											<tr>
												<th></th>
												<th>
													<xsl:value-of select="php:function('lang', 'minute')"/>
												</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>
													<xsl:value-of select="php:function('lang', 'rig up min before')"/>
												</td>
												<td>
													<input type="text" id="rig_up_min_before" name="rig_up_min_before" value="{article/rig_up_min_before}" size="5">
														<xsl:attribute name="data-validation">
															<xsl:text>number</xsl:text>
														</xsl:attribute>
														<xsl:attribute name="data-validation-error-msg">
															<xsl:value-of select="php:function('lang', 'rig up min before')"/>
														</xsl:attribute>
														<xsl:attribute name="placeholder">
															<xsl:value-of select="php:function('lang', 'integer')"/>
														</xsl:attribute>
													</input>
												</td>
											</tr>
											<tr>
												<td>
													<xsl:value-of select="php:function('lang', 'rig down min after')"/>
												</td>
												<td>
													<input type="text" id="rig_down_min_after" name="rig_down_min_after" value="{article/rig_down_min_after}" size="5">
														<xsl:attribute name="data-validation">
															<xsl:text>number</xsl:text>
														</xsl:attribute>
														<xsl:attribute name="data-validation-error-msg">
															<xsl:value-of select="php:function('lang', 'rig down min after')"/>
														</xsl:attribute>
														<xsl:attribute name="placeholder">
															<xsl:value-of select="php:function('lang', 'integer')"/>
														</xsl:attribute>
													</input>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</fieldset>
						<fieldset>
							<legend>
								<xsl:value-of select="php:function('lang', 'technical support')"/>
							</legend>
							<div class="pure-control-group">
								<label>
								</label>
								<div class="pure-custom">
									<table class="pure-table pure-table-bordered" border="0" cellspacing="2" cellpadding="2">
										<thead>
											<tr>
												<th>Hva</th>
												<th>Ja</th>
												<th>Fritekst</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>Strøm</td>
												<td>
													<input type="checkbox" name="power" id="power" value="1">
														<xsl:if test="article/power = 1">
															<xsl:attribute name="checked" value="checked"/>
														</xsl:if>
													</input>
												</td>
												<td>
													<input type="text" id="power_remark" name="power_remark" value="{article/power_remark}">
													</input>
												</td>
											</tr>
											<tr>
												<td>Lydanlegg</td>
												<td>
													<input type="checkbox" name="sound" id="sound" value="1">
														<xsl:if test="article/sound = 1">
															<xsl:attribute name="checked" value="checked"/>
														</xsl:if>
													</input>
												</td>
												<td>
													<input type="text" id="sound_remark" name="sound_remark" value="{article/sound_remark}">
													</input>
												</td>
											</tr>
											<tr>
												<td>Lyssetting/blending</td>
												<td>
													<input type="checkbox" name="light" id="light" value="1">
														<xsl:if test="article/light = 1">
															<xsl:attribute name="checked" value="checked"/>
														</xsl:if>
													</input>
												</td>
												<td>
													<input type="text" id="light_remark" name="light_remark" value="{article/light_remark}">
													</input>
												</td>
											</tr>
											<tr>
												<td>Piano</td>
												<td>
													<input type="checkbox" name="piano" id="piano" value="1">
														<xsl:if test="article/piano = 1">
															<xsl:attribute name="checked" value="checked"/>
														</xsl:if>
													</input>
												</td>
												<td>
													<input type="text" id="piano_remark" name="piano_remark" value="{article/piano_remark}">
													</input>
												</td>

											</tr>
											<tr>
												<td>Anna utstyr (prosjektor, lerret mm)</td>
												<td>
												</td>
												<td>
													<input type="text" id="equipment_remark" name="equipment_remark" value="{article/equipment_remark}">
													</input>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>

						</fieldset>
						<fieldset>
							<legend>
								<xsl:value-of select="php:function('lang', 'raider')"/>
							</legend>
							<div class="pure-control-group">
								<label>
								</label>
								<textarea cols="47" rows="7" name="raider" class="pure-input-1-2" >
									<xsl:value-of select="article/raider"/>
								</textarea>
							</div>
						</fieldset>
					</div>

					<div id='files'>
						<script type="text/javascript">
							var multi_upload_parans = <xsl:value-of select="multi_upload_parans"/>;
						</script>
						<xsl:value-of disable-output-escaping="yes" select="application_condition"/>
						<fieldset>
							<legend>
								<xsl:text>Curriculum vitae</xsl:text>
							</legend>

							<!--
							<xsl:call-template name="file_upload">
								<xsl:with-param name="section">cv</xsl:with-param>
							</xsl:call-template>
							-->

							<xsl:call-template name="multi_upload_file_inline">
								<xsl:with-param name="class">pure-input-3-4 pure-custom</xsl:with-param>
								<xsl:with-param name="multi_upload_action">
									<xsl:value-of select="multi_upload_action_cv"/>
								</xsl:with-param>
								<xsl:with-param name="section">cv</xsl:with-param>
							</xsl:call-template>


							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'files')"/>
								</label>
								<div class="pure-custom pure-input-3-4">
									<xsl:for-each select="datatable_def">
										<xsl:if test="container = 'datatable-container_2'">
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

						<fieldset>
							<legend>
								<xsl:value-of select="php:function('lang', 'documents')"/>
							</legend>
							<!--
							<xsl:call-template name="file_upload">
								<xsl:with-param name="section">documents</xsl:with-param>
							</xsl:call-template>
							-->

							<xsl:call-template name="multi_upload_file_inline">
								<xsl:with-param name="class">pure-input-3-4 pure-custom</xsl:with-param>
								<xsl:with-param name="multi_upload_action">
									<xsl:value-of select="multi_upload_action_documents"/>
								</xsl:with-param>
								<xsl:with-param name="section">documents</xsl:with-param>
							</xsl:call-template>


							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'files')"/>
								</label>
								<div class="pure-custom pure-input-3-4">
									<xsl:for-each select="datatable_def">
										<xsl:if test="container = 'datatable-container_3'">
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


<xsl:template xmlns:php="http://php.net/xsl" match="view">
	<div>
		<form id="form" name="form" method="post" action="" class="pure-form pure-form-aligned">
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<div id="showing">
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
