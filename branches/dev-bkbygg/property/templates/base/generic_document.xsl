<!-- $Id: generic_document.xsl 14792 2016-03-01 18:59:36Z sigurdne $ -->

<!-- add / edit -->
<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="jquery_phpgw_i18n"/>

	<div id="document_edit_tabview">

		<h1>
			<xsl:value-of select="php:function('lang', 'generic document')" />
		</h1>


		<xsl:variable name="action_url">
			<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:property.uigeneric_document.save')" />
		</xsl:variable>

		<xsl:value-of select="validator"/>
		
		<form name="form" class="pure-form pure-form-aligned" id="form" action="{$action_url}" method="post" ENCTYPE="multipart/form-data">
			<div id="tab-content">
					
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>

				<div id="generic">
					<xsl:choose>
						<xsl:when test="document/id!=''">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'id')" />
								</label>
								<xsl:value-of select="document/id"/>
								<input type="hidden" name="id" id="id" value="{document/id}"/>

							</div>
						</xsl:when>
					</xsl:choose>

					<xsl:choose>
						<xsl:when test="location_data2!=''">
							<xsl:choose>
								<xsl:when test="editable = 1">
									<xsl:call-template name="location_form2"/>
								</xsl:when>
								<xsl:otherwise>
									<xsl:call-template name="location_view2"/>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:when>
					</xsl:choose>

					<div class="pure-control-group">
						<label for="name">
							<xsl:value-of select="php:function('lang', 'name')" />
						</label>
						<xsl:choose>
							<xsl:when test="editable = 1">
								<input id="title" name='values[title]' type="text" value="{document/title}">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please enter a title !')"/>
									</xsl:attribute>
								</input>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="document/title" />
							</xsl:otherwise>
						</xsl:choose>
					</div>

					<div class="pure-control-group" >
						<label for="name">
							<xsl:value-of select="php:function('lang', 'description')" />
						</label>
						<xsl:choose>
							<xsl:when test="editable = 1">
								<textarea id="descr" rows="6" style="width:40%; resize:none;" name="values[descr]">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please enter a description !')"/>
									</xsl:attribute>
									<xsl:value-of select="document/descr" disable-output-escaping="yes"/>
								</textarea>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="document/descr" disable-output-escaping="yes"/>
							</xsl:otherwise>
						</xsl:choose>
					</div>

					<div class="pure-control-group">
						<label for="category">
							<xsl:value-of select="php:function('lang', 'category')" />
							<br/>
							<h3>-or multiple &quot;TAGS&quot;</h3>
						</label>
						<xsl:choose>
							<xsl:when test="editable = 1">
								<select id="cat_id" name="values[cat_id]">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please enter a category !')"/>
									</xsl:attribute>
									<xsl:apply-templates select="categories/options"/>
								</select>
							</xsl:when>
							<xsl:otherwise>
								<xsl:for-each select="categories/options">
									<xsl:if test="selected = 'selected' or selected = 1">
										<xsl:value-of disable-output-escaping="yes" select="name"/>
									</xsl:if>
								</xsl:for-each>
							</xsl:otherwise>
						</xsl:choose>
					</div>
                        
					<div class="pure-control-group">
						<label for="date">
							<xsl:value-of select="php:function('lang', 'date')" />
						</label>
						<xsl:choose>
							<xsl:when test="editable = 1">
								<input id="report_date" name='values[report_date]' type="text" value="{document/report_date}"
										   data-validation="date" data-validation-format="dd/mm/yyyy"/>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="document/report_date"/>
							</xsl:otherwise>
						</xsl:choose>
					</div>

					<div class="pure-control-group">
						<label for="status">
							<xsl:value-of select="php:function('lang', 'status')" />
						</label>
						<xsl:choose>
							<xsl:when test="editable = 1">
								<select id="status_id" name="values[status_id]">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please enter a status !')"/>
									</xsl:attribute>
									<xsl:apply-templates select="status_list/options"/>
								</select>
							</xsl:when>
							<xsl:otherwise>
								<xsl:for-each select="status_list/options">
									<xsl:if test="selected = 'selected' or selected = 1">
										<xsl:value-of disable-output-escaping="yes" select="name"/>
									</xsl:if>
								</xsl:for-each>
							</xsl:otherwise>
						</xsl:choose>
					</div>

					<div class="pure-control-group">
						<label for="coordinator">
							<xsl:value-of select="lang_coordinator" />
						</label>
						<xsl:choose>
							<xsl:when test="editable = 1">
								<!--div class="autocomplete"-->
								<input type="hidden" id="coordinator_id" name="values[coordinator_id]"  value="{document/coordinator_id}"/>
								<input type="text" id="coordinator_name" name="values[coordinator_name]" value="{document/coordinator_name}">
								</input>
								<div id="coordinator_container"/>
								<!--/div-->
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="document/coordinator_name" />
							</xsl:otherwise>
						</xsl:choose>
					</div>

					<div class="pure-control-group">
						<label for="vendor">
							<xsl:value-of select="php:function('lang', 'vendor')" />
						</label>
						<xsl:choose>
							<xsl:when test="editable = 1">
								<!--div class="autocomplete"-->
								<input type="hidden" id="vendor_id" name="values[vendor_id]"  value="{document/vendor_id}"/>
								<input type="text" id="vendor_name" name="values[vendor_name]" value="{document/vendor_name}">
								</input>
								<div id="vendor_container"/>
								<!--/div-->
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="document/vendor_name" />
							</xsl:otherwise>
						</xsl:choose>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'file')"/>
						</label>
						<a href="{link_file}">
							<xsl:value-of select="file_name" />
						</a>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Path')"/>
						</label>
						<div class='pure-custom'>
							<xsl:value-of disable-output-escaping="yes" select="document/paths"/>
						</div>
					</div>
					<xsl:choose>
						<xsl:when test="editable = 1">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'upload file')"/>
								</label>
								<input type="file" name="file" size="40">
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'Select file to upload')"/>
									</xsl:attribute>
								</input>
							</div>
						</xsl:when>
					</xsl:choose>
				</div>

				<xsl:choose>
					<xsl:when test="document/id!=''">
						<div id="relations">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'entity group')" />
								</label>
								<select id="entity_group_id" name="entity_group_id">
									<xsl:apply-templates select="entity_group_filter/options"/>
								</select>
								<label>
									<xsl:value-of select="php:function('lang', 'Only Related')" />
								</label>
								<input type="checkbox" id="check_components_related" name="check_components_related" value="1" onchange="showRelatedComponentes()"></input>
								<label>
									<xsl:value-of select="php:function('lang', 'all types')" />
								</label>
								<input type="checkbox" disabled="disabled" id="check_all_types" name="check_all_types" value="1" onchange="showAllTypes()"></input>
							</div>							
							<div class="pure-control-group">
								<label for="vendor">
									<xsl:value-of select="php:function('lang', 'item types')" />
								</label>
								<select id="location_id" name="location_id">
									<xsl:apply-templates select="location_filter/options"/>
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
						</div>
						
						<div id="locations">							
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
								<label>
									<xsl:value-of select="php:function('lang', 'Only Related')" />
								</label>
								<input type="checkbox" id="check_locations_related" name="check_locations_related" value="1" onchange="showRelatedLocations()"></input>
							</div>								
									
							<xsl:for-each select="datatable_def">
								<xsl:if test="container = 'datatable-container_1'">
									<xsl:call-template name="table_setup">
										<xsl:with-param name="container" select ='container'/>
										<xsl:with-param name="requestUrl" select ='requestUrl' />
										<xsl:with-param name="ColumnDefs" select ='ColumnDefs' />
										<xsl:with-param name="tabletools" select ='tabletools' />
										<xsl:with-param name="config" select ='config' />
									</xsl:call-template>
								</xsl:if>
							</xsl:for-each>				
						</div>
					</xsl:when>
				</xsl:choose>
			</div>
			<div class="proplist-col">
				<xsl:variable name="lang_cancel">
					<xsl:value-of select="php:function('lang', 'cancel')" />
				</xsl:variable>
				<xsl:choose>
					<xsl:when test="editable = 1">
						<xsl:variable name="lang_save">
							<xsl:value-of select="php:function('lang', 'save')" />
						</xsl:variable>
						<input type="submit" class="pure-button pure-button-primary" name="save_project" value="{$lang_save}" title = "{$lang_save}" />
						<input class="pure-button pure-button-primary" type="button" name="cancelButton" id ='cancelButton' value="{$lang_cancel}" title = "{$lang_cancel}" onClick="document.cancel_form.submit();"/>
					</xsl:when>
					<xsl:otherwise>
						<xsl:variable name="lang_edit">
							<xsl:value-of select="php:function('lang', 'edit')" />
						</xsl:variable>
						<xsl:variable name="lang_new_document">
							<xsl:value-of select="php:function('lang', 'new')" />
						</xsl:variable>
						<input type="button" class="pure-button pure-button-primary" name="edit_document" value="{$lang_edit}" title = "{$lang_edit}"  onClick="document.load_edit_form.submit();"/>
						<input type="button" class="pure-button pure-button-primary" name="new_document" value="{$lang_new_document}" title = "{$lang_new_document}" onClick="document.new_form.submit();"/>
						<input class="pure-button pure-button-primary" type="button" name="cancelButton" id ='cancelButton' value="{$lang_cancel}" title = "{$lang_cancel}" onClick="document.cancel_form.submit();"/>
					</xsl:otherwise>
				</xsl:choose>
			</div>
		</form>
	</div>

	<xsl:variable name="cancel_url">
		<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:property.uigeneric_document.index')" />
	</xsl:variable>

	<form name="cancel_form" id="cancel_form" action="{$cancel_url}" method="post">
	</form>
	<xsl:variable name="new_url">
		<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:property.uigeneric_document.add')" />
	</xsl:variable>
	<form name="new_form" id="new_form" action="{$new_url}" method="post">
	</form>

	<xsl:variable name="edit_params">
		<xsl:text>menuaction:property.uigeneric_document.edit, id:</xsl:text>
		<xsl:value-of select="document/id" />
	</xsl:variable>
	<xsl:variable name="edit_url">
		<xsl:value-of select="php:function('get_phpgw_link', '/index.php', $edit_params )" />
	</xsl:variable>

	<form name="load_edit_form" id="load_edit_form" action="{$edit_url}" method="post">
	</form>



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
