<!-- $Id: documents_add.xsl 11511 2013-12-08 20:57:07Z sigurdne $ -->

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

	<!-- add / edit -->
<xsl:template match="data" xmlns:formvalidator="http://www.w3.org/TR/html4/" xmlns:php="http://php.net/xsl">
		<xsl:call-template name="yui_phpgw_i18n"/>

		<div class="yui-navset" id="survey_edit_tabview">

		<h1>
			<xsl:value-of select="php:function('lang', 'documents')" />
		</h1>


		<xsl:variable name="action_url">
			<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:manual.uidocuments.save')" />
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

				<div class="yui-content">
				<div id="generic" class="content-wrp">

				<dl class="proplist-col">

					<dt>
						<label for="category"><xsl:value-of select="php:function('lang', 'category')" /></label>
					</dt>
					<dd>
						<xsl:choose>
							<xsl:when test="editable = 1">
 								<select id="cat_id" name="cat_id" onChange="update_fileuploader_action();" 
									formvalidator:FormField="yes"
	   								formvalidator:Type="SelectField">
									<xsl:apply-templates select="categories/options"/>
								</select>
							</xsl:when>
							<xsl:otherwise>
								<select id="cat_id" name="cat_id" onChange="refresh_files();"> 
									<xsl:apply-templates select="categories/options"/>
								</select>
 							</xsl:otherwise>
						</xsl:choose>
					</dd>

				</dl>
			</div>

			<div id="documents">
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
			</div>
			<xsl:choose>
				<xsl:when test="editable = 1">
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
					</xsl:when>
				</xsl:choose>

			</form>
		</div>

		<xsl:variable name="cancel_url">
			<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:manual.uidocuments.index')" />
		</xsl:variable>

		<form name="cancel_form" id="cancel_form" action="{$cancel_url}" method="post">
		</form>
		<xsl:variable name="new_url">
			<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:manual.uidocuments.add')" />
		</xsl:variable>
		<form name="new_form" id="new_form" action="{$new_url}" method="post">
		</form>

		<xsl:variable name="edit_params">
			<xsl:text>menuaction:manual.uidocuments.edit</xsl:text>
		</xsl:variable>
		<xsl:variable name="edit_url">
				<xsl:value-of select="php:function('get_phpgw_link', '/index.php', $edit_params )" />
		</xsl:variable>

		<form name="load_edit_form" id="load_edit_form" action="{$edit_url}" method="post">
		</form>

	</xsl:template>

<xsl:template name="datasource-definition">
	<script>
		var columnDefs = [];
		YAHOO.util.Event.onDOMReady(function(){
			<xsl:for-each select="datatable_def">
				columnDefs = [
					<xsl:for-each select="ColumnDefs">
					{
						resizeable: true,
						key: "<xsl:value-of select="key"/>",
						<xsl:if test="label">
						label: "<xsl:value-of select="label"/>",
						</xsl:if>
						sortable: <xsl:value-of select="phpgw:conditional(not(sortable = 0), 'true', 'false')"/>,
						<xsl:if test="hidden">
						hidden: true,
						</xsl:if>
						<xsl:if test="formatter">
						formatter: <xsl:value-of select="formatter"/>,
						</xsl:if>
						<xsl:if test="editor">
						editor: <xsl:value-of select="editor"/>,
					    </xsl:if>
						className: "<xsl:value-of select="className"/>"
					}<xsl:value-of select="phpgw:conditional(not(position() = last()), ',', '')"/>
				</xsl:for-each>
				];
			
			YAHOO.portico.inlineTableHelper("<xsl:value-of select="container"/>", <xsl:value-of select="requestUrl"/>, columnDefs);
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

