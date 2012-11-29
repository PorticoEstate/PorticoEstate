<!-- $Id$ -->

	<!-- add / edit -->
<xsl:template match="data" xmlns:formvalidator="http://www.w3.org/TR/html4/" xmlns:php="http://php.net/xsl">
		<div class="yui-navset" id="survey_edit_tabview">
	
		<h1>
			<xsl:value-of select="php:function('lang', 'condition survey')" />
		</h1>


		<xsl:variable name="action_url">
			<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:property.uicondition_survey.save')" />
		</xsl:variable>

			<form name="form" id="form" action="{$action_url}" method="post" ENCTYPE="multipart/form-data">
		        <dl>
					<xsl:choose>
						<xsl:when test="msgbox_data != ''">
								<dt>
									<xsl:call-template name="msgbox"/>
								</dt>
						</xsl:when>
					</xsl:choose>
				</dl>
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<div class="yui-content">
				<div id="generic" class="content-wrp">
				
				<dl class="proplist-col">
					<xsl:choose>
						<xsl:when test="survey/id!=''">
								<dt>
									<label><xsl:value-of select="php:function('lang', 'id')" /></label>
								</dt>
								<dd>
									<xsl:value-of select="survey/id"/>
									<input type="hidden" name="id" value="{survey/id}"/>
								</dd>
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
				
					<dt>
						<label for="name"><xsl:value-of select="php:function('lang', 'name')" /></label>
					</dt>
					<dd>
						<xsl:choose>
							<xsl:when test="editable = 1">
	   							<input id="title" name='values[title]' type="text" value="{survey/title}"
	   								formvalidator:FormField="yes"
	   								formvalidator:Type="TextBaseField">
	   							</input>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="survey/title" />
							</xsl:otherwise>
						</xsl:choose>
					</dd>
				
				
					<dt>
						<label for="name"><xsl:value-of select="php:function('lang', 'description')" /></label>
					</dt>
					<dd>
						<xsl:choose>
							<xsl:when test="editable = 1">
								<textarea id="descr" name="values[descr]" rows="5" cols="60"
									formvalidator:FormField="yes"
	   								formvalidator:Type="TextBaseField">
									<xsl:value-of select="survey/descr" disable-output-escaping="yes"/>
								</textarea>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="survey/descr" disable-output-escaping="yes"/>
							</xsl:otherwise>
						</xsl:choose>
					</dd>
				
				
					<dt>
						<label for="category"><xsl:value-of select="php:function('lang', 'category')" /></label>
					</dt>
					<dd>
						<xsl:choose>
							<xsl:when test="editable = 1">
 								<select id="cat_id" name="values[cat_id]"
									formvalidator:FormField="yes"
	   								formvalidator:Type="SelectField">
									<xsl:apply-templates select="categories/options"/>
								</select>
							</xsl:when>
							<xsl:otherwise>
 								<select id="cat_id" disabled="disabled">
									<xsl:apply-templates select="categories/options"/>
								</select>
							</xsl:otherwise>
						</xsl:choose>
					</dd>
				
				
					<dt>
							<label for="category"><xsl:value-of select="php:function('lang', 'date')" /></label>
					</dt>
					<dd>
						<xsl:choose>
							<xsl:when test="editable = 1">
	 							<input id="report_date" name='values[report_date]' type="text" value="{survey/report_date}"
									formvalidator:FormField="yes"
									formvalidator:type="TextBaseField"/>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="survey/report_date"/>
							</xsl:otherwise>
						</xsl:choose>
					</dd>
				
				
					<dt>
						<label for="status"><xsl:value-of select="php:function('lang', 'status')" /></label>
					</dt>
					<dd>
						<xsl:choose>
							<xsl:when test="editable = 1">
 								<select id="status_id" name="values[status_id]"
									formvalidator:FormField="yes"
	   								formvalidator:Type="SelectField">
									<xsl:apply-templates select="status_list/options"/>
								</select>
							</xsl:when>
							<xsl:otherwise>
 								<select id="status_id" disabled="disabled">
									<xsl:apply-templates select="status_list/options"/>
								</select>
							</xsl:otherwise>
						</xsl:choose>

					</dd>
				
				
					<dt>
						<label for="coordinator"><xsl:value-of select="php:function('lang', 'coordinator')" /></label>
					</dt>
					<dd>

						<xsl:choose>
							<xsl:when test="editable = 1">
							    <div class="autocomplete">
							        <input type="hidden" id="coordinator_id" name="values[coordinator_id]"  value="{survey/coordinator_id}"/>
							        <input type="text" id="coordinator_name" name="values[coordinator_name]" value="{survey/coordinator_name}">
									</input>
							        <div id="coordinator_container"/>
							    </div>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="survey/coordinator_name" />
							</xsl:otherwise>
						</xsl:choose>

					</dd>
				

				
					<dt>
						<label for="vendor"><xsl:value-of select="php:function('lang', 'vendor')" /></label>
					</dt>
					<dd>

						<xsl:choose>
							<xsl:when test="editable = 1">
							    <div class="autocomplete">
							        <input type="hidden" id="vendor_id" name="values[vendor_id]"  value="{survey/vendor_id}"/>
							        <input type="text" id="vendor_name" name="values[vendor_name]" value="{survey/vendor_name}">
									</input>
							        <div id="vendor_container"/>
							    </div>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="survey/vendor_name" />
							</xsl:otherwise>
						</xsl:choose>

					</dd>
				

				</dl>
			</div>

			<div id="documents">
				<script type="text/javascript">
				   var fileuploader_action = {
						menuaction:'property.fileuploader.add',
						upload_target:'property.bocondition_survey.addfiles',
						id: '<xsl:value-of select='survey/id'/>'
					};
				</script>

				<xsl:call-template name="datasource-definition" />

				<dl class="proplist-col">
					
						<dt>
							<label><xsl:value-of select="php:function('lang', 'files')"/></label>
						</dt>
						<dd>
							<div style="clear:both;" id="datatable-container_0"></div>		
						</dd>
					
					<xsl:choose>
						<xsl:when test="editable = 1">
							<xsl:call-template name="file_upload"/>
						</xsl:when>
					</xsl:choose>
				</dl>
			</div>
			<xsl:choose>
				<xsl:when test="editable = 1">
					<div id="import">
				<dl class="proplist-col">
							
								<dt>
									<label><xsl:value-of select="php:function('lang', 'upload file')"/></label>
								</dt>
								<dd>
									<input type="file" name="import_file" size="40">
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'Select file to upload')"/>
										</xsl:attribute>
									</input>
								</dd>
							
						</dl>
					</div>
				</xsl:when>
				</xsl:choose>
				</div>

				<dl class="proplist-col">
				

						<div class="form-buttons">
							<xsl:variable name="lang_cancel"><xsl:value-of select="php:function('lang', 'cancel')" /></xsl:variable>
							<xsl:choose>
								<xsl:when test="editable = 1">
									<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
									<input type="submit" name="save_project" value="{$lang_save}" title = "{$lang_save}" />
									<input class="button" type="button" name="cancelButton" id ='cancelButton' value="{$lang_cancel}" title = "{$lang_cancel}" onClick="document.cancel_form.submit();"/>
								</xsl:when>
								<xsl:otherwise>
									<xsl:variable name="lang_edit"><xsl:value-of select="php:function('lang', 'edit')" /></xsl:variable>
									<xsl:variable name="lang_new_survey"><xsl:value-of select="php:function('lang', 'new')" /></xsl:variable>
									<input type="button" name="edit_survey" value="{$lang_edit}" title = "{$lang_edit}"  onClick="document.load_edit_form.submit();"/>
									<input type="button" name="new_survey" value="{$lang_new_survey}" title = "{$lang_new_survey}" onClick="document.new_form.submit();"/>
									<input class="button" type="button" name="cancelButton" id ='cancelButton' value="{$lang_cancel}" title = "{$lang_cancel}" onClick="document.cancel_form.submit();"/>
								</xsl:otherwise>
							</xsl:choose>
						</div>
		

			</dl>
			</form>
		</div>

		<xsl:variable name="cancel_url">
			<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:property.uicondition_survey.index')" />
		</xsl:variable>

		<form name="cancel_form" id="cancel_form" action="{$cancel_url}" method="post">
		</form>
		<xsl:variable name="new_url">
			<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:property.uicondition_survey.add')" />
		</xsl:variable>
		<form name="new_form" id="new_form" action="{$new_url}" method="post">
		</form>

		<xsl:variable name="edit_params">
			<xsl:text>menuaction:property.uicondition_survey.edit, id:</xsl:text>
				<xsl:value-of select="survey/id" />
			</xsl:variable>
		<xsl:variable name="edit_url">
				<xsl:value-of select="php:function('get_phpgw_link', '/index.php', $edit_params )" />
		</xsl:variable>

		<form name="load_edit_form" id="load_edit_form" action="{$edit_url}" method="post">
		</form>

	</xsl:template>

<xsl:template name="datasource-definition">
	<script>
	YAHOO.util.Event.onDOMReady(function(){

		<xsl:for-each select="datatable_def">
			YAHOO.portico.inlineTableHelper("<xsl:value-of select="container"/>", <xsl:value-of select="requestUrl"/>, <xsl:value-of select="ColumnDefs"/>);
		</xsl:for-each>

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

	<xsl:template xmlns:php="http://php.net/xsl" name="file_upload">
		<dt>
			<label><xsl:value-of select="php:function('lang', 'upload file')"/></label>
		</dt>
		<dd>
			<input type="file" name="file" size="40">
				<xsl:attribute name="title">
					<xsl:value-of select="php:function('lang', 'Select file to upload')"/>
				</xsl:attribute>
			</input>
		</dd>
		<xsl:choose>
			<xsl:when test="multiple_uploader!=''">
				<dt>
					<label><a href="javascript:fileuploader()">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'upload multiple files')"/>
						</xsl:attribute>
						<xsl:value-of select="php:function('lang', 'upload multiple files')"/>
					</a></label>
				</dt>
			</xsl:when>
		</xsl:choose>
	</xsl:template>

