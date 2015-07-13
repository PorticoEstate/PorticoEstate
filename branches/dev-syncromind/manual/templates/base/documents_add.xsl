<!-- $Id$ -->

<!-- add / edit -->
<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="jquery_phpgw_i18n"/>
	<div>
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

			<div id="tab-content">

				<xsl:value-of disable-output-escaping="yes" select="tabs"/>

				<div id="documents">

					<div class="pure-control-group">
						<label for="category">
							<xsl:value-of select="php:function('lang', 'category')" />
						</label>
						<xsl:choose>
							<xsl:when test="editable = 1">
								<select id="cat_id" name="cat_id" onChange="update_fileuploader_action();">
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
								<select id="cat_id" name="cat_id" onChange="refresh_files();">
									<xsl:apply-templates select="categories/options"/>
								</select>
							</xsl:otherwise>
						</xsl:choose>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'files')"/>
						</label>
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
						<xsl:choose>
							<xsl:when test="editable = 1">
								<xsl:call-template name="file_upload"/>
							</xsl:when>
						</xsl:choose>
					</div>
				</div>
			</div>
			<xsl:choose>
				<xsl:when test="editable = 1">
					<dl class="proplist-col">
						<div class="form-buttons">
							<xsl:variable name="lang_cancel">
								<xsl:value-of select="php:function('lang', 'cancel')" />
							</xsl:variable>
							<xsl:choose>
								<xsl:when test="editable = 1">
									<xsl:variable name="lang_save">
										<xsl:value-of select="php:function('lang', 'save')" />
									</xsl:variable>
									<input  class="pure-button pure-button-primary" type="submit" name="save_project" value="{$lang_save}" title = "{$lang_save}" />
									<input  class="pure-button pure-button-primary" type="button" name="cancelButton" id ='cancelButton' value="{$lang_cancel}" title = "{$lang_cancel}" onClick="document.cancel_form.submit();"/>
								</xsl:when>
							</xsl:choose>
						</div>
					</dl>
				</xsl:when>
				<xsl:otherwise>
					<xsl:variable name="lang_add">
						<xsl:value-of select="php:function('lang', 'add')" />
					</xsl:variable>
						<input  class="pure-button pure-button-primary" type="button" name="edit_survey" value="{$lang_add}" title = "{$lang_add}"  onClick="document.load_edit_form.submit();"/>
				</xsl:otherwise>
			</xsl:choose>
		</form>
	</div>

	<xsl:variable name="cancel_url">
		<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:manual.uidocuments.index')" />
	</xsl:variable>

	<form name="cancel_form" id="cancel_form" action="{$cancel_url}" method="post">
	</form>

	<xsl:variable name="edit_params">
		<xsl:text>menuaction:manual.uidocuments.add</xsl:text>
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

<xsl:template xmlns:php="http://php.net/xsl" name="file_upload">
	<dt>
		<label>
			<xsl:value-of select="php:function('lang', 'upload file')"/>
		</label>
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
				<label>
					<a href="javascript:fileuploader()">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'upload multiple files')"/>
						</xsl:attribute>
						<xsl:value-of select="php:function('lang', 'upload multiple files')"/>
					</a>
				</label>
			</dt>
		</xsl:when>
	</xsl:choose>
</xsl:template>

