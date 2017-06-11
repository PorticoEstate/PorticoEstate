<!-- $Id: generic_document.xsl 14792 2016-03-01 18:59:36Z sigurdne $ -->

<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="jquery_phpgw_i18n"/>

	<div id="document_edit_tabview">

		<xsl:value-of select="validator"/>
		
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="tabs"/>
						
			<div id="locations">
				<form name="form" class="pure-form pure-form-aligned" id="form" action="" method="post">
					<div class="pure-control-group">
						<label for="vendor">
							<xsl:value-of select="php:function('lang', 'type')" />
						</label>
						<select id="type_id" name="type_id">
							<xsl:apply-templates select="type_filter/options"/>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="vendor">
							<xsl:value-of select="php:function('lang', 'category')" />
						</label>
						<select id="cat_location_id" name="cat_location_id">
							<xsl:apply-templates select="category_filter/options"/>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="vendor">
							<xsl:value-of select="php:function('lang', 'district')" />
						</label>
						<select id="district_id" name="district_id">
							<xsl:apply-templates select="district_filter/options"/>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="vendor">
							<xsl:value-of select="php:function('lang', 'part of town')" />
						</label>
						<select id="part_of_town_id" name="part_of_town_id">
							<xsl:apply-templates select="part_of_town_filter/options"/>
						</select>
					</div>

					<xsl:for-each select="datatable_def">
						<xsl:if test="container = 'datatable-container_0'">
							<xsl:call-template name="table_setup">
								<xsl:with-param name="container" select ='container'/>
								<xsl:with-param name="requestUrl" select ='requestUrl' />
								<xsl:with-param name="ColumnDefs" select ='ColumnDefs' />
								<xsl:with-param name="tabletools" select ='tabletools' />
								<xsl:with-param name="config" select ='config' />
							</xsl:call-template>
						</xsl:if>
					</xsl:for-each>
				</form>
			</div>
				
			<div id="files">
				<label>
					<xsl:value-of select="access_error_upload_dir" />
				</label>
				<xsl:call-template name="multi_upload_file"/>
				<!--<xsl:value-of disable-output-escaping="yes" select="form_file_upload"/>-->
			</div>
				
			<div id="components">
				<form id="form_components" name="form_components" class="pure-form pure-form-aligned" action="" method="POST" enctype="multipart/form-data">
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'location')"/>
						</label>
						<div class='pure-custom location_name'></div>
					</div>
					<div class="pure-control-group">
						<label for="vendor">
							<xsl:value-of select="php:function('lang', 'Profile')" />
						</label>
						<select id="profile_list" name="profile_list">
							<xsl:apply-templates select="profile_list/options"/>
						</select>
						<img src="{image_loader}" class="get-profile" align="absmiddle"></img>
					</div>
					<div class="pure-control-group">
						<label for="vendor">
							<xsl:value-of select="php:function('lang', 'Template')" />
						</label>
						<select id="template_list" name="template_list">
							<xsl:apply-templates select="template_list/options"/>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="vendor">
							<xsl:value-of select="php:function('lang', 'Attributes template')" />
						</label>
						<select id="attribute_name_component_id" name="attribute_name_component_id">
						</select>
						<div style='display:inline-block; margin-left:10px;'>
							<xsl:value-of select="php:function('lang', 'choose attribute name for Component ID')" />
						</div>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'upload file')"/>
						</label>
						<input type="file" id="excel_components" name="excel_components" size="40"></input>
					</div>
					<div class="pure-control-group">
						<label></label>
						<input type="button" id="import_components" name="import_components" size="40">
							<xsl:attribute name="value">
								<xsl:value-of select="php:function('lang', 'Start import')"/>
							</xsl:attribute>
						</input>
						<img src="{image_loader}" class="processing-import" align="absmiddle"></img>
					</div>
					<div class="pure-control-group">
						<div id="message1" class="message"></div>
					</div>
					<div id="responsiveTabsDemo">
						<ul>
							<li>
								<a href="#tab-1">
									<xsl:value-of select="php:function('lang', 'Choose Sheet')"/>
								</a>
							</li>
							<li>
								<a href="#tab-2">
									<xsl:value-of select="php:function('lang', 'Choose start line')"/>
								</a>
							</li>
							<li>
								<a href="#tab-3">
									<xsl:value-of select="php:function('lang', 'Choose columns')"/>
								</a>
							</li>
							<li>
								<a href="#tab-4">
									<xsl:value-of select="php:function('lang', 'Preview')"/>
								</a>
							</li>
						</ul>
						<div id="tab-1">
							<select id="sheet_id" name="sheet_id">
								<option value=''>
									<xsl:value-of select="php:function('lang', 'Select Sheet')"/>
								</option>
							</select>
							<input type="button" id="step2" name="step2" size="40">
								<xsl:attribute name="value">
									<xsl:value-of select="php:function('lang', 'Continue')"/>
								</xsl:attribute>
							</input>
							<img src="{image_loader}" class="processing-sheet" align="absmiddle"></img>
						</div>
						<div id="tab-2" style="overflow: scroll">
							<input type="button" id="step3" name="step3" size="40">
								<xsl:attribute name="value">
									<xsl:value-of select="php:function('lang', 'Continue')"/>
								</xsl:attribute>
							</input>
							<img src="{image_loader}" class="processing-start-line" align="absmiddle"></img>
							<div id="content_lines" class="pure-custom"></div>
						</div>
						<div id="tab-3">
							<input type="button" id="step4" name="step4" size="40">
								<xsl:attribute name="value">
									<xsl:value-of select="php:function('lang', 'Continue')"/>
								</xsl:attribute>
							</input>
							<img src="{image_loader}" class="processing-columns" align="absmiddle"></img>
							<div id="content_columns" class="pure-custom"></div>
						</div>
						<div id="tab-4">
							<div class="pure-control-group">
								<label for="vendor">
									<xsl:value-of select="php:function('lang', 'Profile')" />
								</label>
								<div class="pure-custom">
									<div class="pure-control-group">
										<label>
											<xsl:value-of select="php:function('lang', 'Category template')" />
										</label>
										<div id="template_name" class="pure-custom"></div>
									</div>
									<div class="pure-control-group">
										<label>
											<xsl:value-of select="php:function('lang', 'Attribute name for Component ID')" />
										</label>
										<div id="component_id_text" class="pure-custom"></div>
									</div>
									<div class="pure-control-group">
										<label>
											<xsl:value-of select="php:function('lang', 'Columns and attributes')" />
										</label>
										<div id="columns_name" class="pure-custom"></div>
									</div>
									<div class="pure-control-group">
										<label>
											<xsl:value-of select="php:function('lang', 'Save Profile')" />
										</label>
										<input type="checkbox" value="1" id="save_profile" name="save_profile" checked="true"/>
									</div>
									<div class="pure-control-group">
										<label>
											<xsl:value-of select="php:function('lang', 'add')" />
										</label>
										<input type="radio" value="1" id="profile_option_save_1" name="profile_option_save" checked="true"/>
										<input type="text" value="" id="name_profile" name="name_profile" />
									</div>
									<div class="pure-control-group">
										<label>
											<xsl:value-of select="php:function('lang', 'update')" />
										</label>
										<input type="radio" value="2" id="profile_option_save_2" name="profile_option_save" disabled="true"/>
										<div id="profile_selected" style="display:inline-block; margin-right:10px;"></div>
										<input type="hidden" id="cod_profile_selected" name="cod_profile_selected" value=""></input>
									</div>
								</div>
							</div>
							<div class="pure-control-group">
								<label for="vendor">
									<xsl:value-of select="php:function('lang', 'New Categories')" />
								</label>
								<div id="new_entity_categories" class="pure-custom"></div>
							</div>
							<div class="pure-control-group">
								<label for="vendor">
									<xsl:value-of select="php:function('lang', 'New Attributes')" />
								</label>
								<div id="new_attributes" class="pure-custom"></div>
							</div>
							<div class="pure-control-group">
								<label for="vendor">
									<xsl:value-of select="php:function('lang', 'Download preview components')" />
								</label>
								<input type="button" id="donwload_preview_components" name="donwload_preview_components" size="40">
									<xsl:attribute name="value">
										<xsl:value-of select="php:function('lang', 'Download')"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label for="vendor"></label>
								<input type="button" id="step5" name="step5" size="40">
									<xsl:attribute name="value">
										<xsl:value-of select="php:function('lang', 'Save')"/>
									</xsl:attribute>
								</input>
								<input type="button" id="cancel_steps" name="cancel_steps" size="40">
									<xsl:attribute name="value">
										<xsl:value-of select="php:function('lang', 'Cancel')"/>
									</xsl:attribute>
								</input>
								<img src="{image_loader}" class="processing-save" align="absmiddle"></img>
							</div>
							<div class="pure-control-group">
								<div id="message3" class="message"></div>
							</div>
						</div>
					</div>
				</form>
			</div>
				
			<div id="relations">
				<form id="form_files" name="form_files" class="pure-form pure-form-aligned" action="" method="POST" enctype="multipart/form-data">
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'category')"/>
						</label>
						<select id="doc_cat_id" name="doc_cat_id">
							<xsl:apply-templates select="document_category/options"/>
						</select>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'location')"/>
						</label>
						<div class='pure-custom location_name'></div>
						<input type="hidden" id="location_code" name="location_code" value=""></input>
						<input type="hidden" id="location_item_id" name="location_item_id" value=""></input>
					</div>
						
					<div id="responsiveTabsRelations">
						<ul>
							<li>
								<a href="#tab-components">
									<xsl:value-of select="php:function('lang', 'Components')"/>
								</a>
							</li>
							<li>
								<a href="#tab-files">
									<xsl:value-of select="php:function('lang', 'Files')"/>
								</a>
							</li>
							<li>
								<a href="#tab-preview">
									<xsl:value-of select="php:function('lang', 'Preview')"/>
								</a>
							</li>
						</ul>
						<div id="tab-components">
							<div class="pure-control-group">
								<div class="pure-custom">
									<div class="pure-control-group">
										<label>
											<xsl:value-of select="php:function('lang', 'without components')" />
										</label>
										<input type="radio" value="0" name="with_components_check" checked="true" />
									</div>
									<div class="pure-control-group">
										<label>
											<xsl:value-of select="php:function('lang', 'with components')" />
										</label>
										<input type="radio" value="1" name="with_components_check" />
										<input type="file" id="excel_files" name="excel_files" size="40"></input>
									</div>
								</div>
							</div>
							<div class="pure-control-group">
								<label></label>
								<input type="button" id="relations_step_1" name="relations_step_1" size="40">
									<xsl:attribute name="value">
										<xsl:value-of select="php:function('lang', 'Continue')"/>
									</xsl:attribute>
								</input>
								<img src="{image_loader}" class="processing-relations" align="absmiddle"></img>
							</div>
						</div>
						<div id="tab-files">
							<div class="pure-control-group">
								<div class="pure-custom">
									<div class="pure-control-group">
										<label>
											<xsl:value-of select="php:function('lang', 'Uncompressed')" />
										</label>
										<input type="radio" value="0" name="compressed_file_check" checked="true" />
									</div>
									<div class="pure-control-group">
										<label>
											<xsl:value-of select="php:function('lang', 'Compressed')" />
										</label>
										<input type="radio" value="1" name="compressed_file_check" />
										<input type="text" value="" id="compressed_file_name" name="compressed_file_name" >
											<xsl:attribute name="placeholder">
												<xsl:value-of select="php:function('lang', 'File name')"/>
											</xsl:attribute>
										</input> (zip, rar)
									</div>
								</div>
							</div>
							<div class="pure-control-group">
								<label></label>
								<input type="button" id="relations_step_2" name="relations_step_2" size="40">
									<xsl:attribute name="value">
										<xsl:value-of select="php:function('lang', 'Continue')"/>
									</xsl:attribute>
								</input>
								<img src="{image_loader}" class="processing-relations" align="absmiddle"></img>
							</div>
						</div>
						<div id="tab-preview">
							<div class="pure-control-group">
								<label for="vendor">
									<xsl:value-of select="php:function('lang', 'Messages')" />
								</label>
								<div class="pure-custom">
									<div id="message5" class="message"></div>
								</div>
							</div>
							<div class="pure-control-group">
								<label></label>
								<input type="button" id="save_relations" name="save_relations" size="40">
									<xsl:attribute name="value">
										<xsl:value-of select="php:function('lang', 'Save')"/>
									</xsl:attribute>
								</input>
								<img src="{image_loader}" class="processing-relations" align="absmiddle"></img>
							</div>
							<div class="pure-control-group">
								<label></label>
								<div id="message4" class="message"></div>
							</div>
						</div>
					</div>
				</form>
			</div>
		
		</div>
	</div>
	<script>
		$('#responsiveTabsDemo').responsiveTabs({
		startCollapsed: 'accordion',
		disabled: [1,2,3]
		});
		
		$('#responsiveTabsRelations').responsiveTabs({
		startCollapsed: 'accordion',
		disabled: [2]
		});
	</script>
</xsl:template>

<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected = 'selected' or selected = 1">
			<xsl:attribute name="selected" value="selected" />
		</xsl:if>
		<xsl:attribute name="title" value="description" />
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>
