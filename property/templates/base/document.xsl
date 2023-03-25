
<!-- $Id$ -->
<xsl:template name="app_data">
	<xsl:choose>
		<xsl:when test="edit">
			<xsl:apply-templates select="edit"/>
		</xsl:when>
		<xsl:when test="view">
			<xsl:apply-templates select="view"/>
		</xsl:when>
	</xsl:choose>
</xsl:template>




<!-- New template-->
<xsl:template match="cat_list">
	<xsl:variable name="id">
		<xsl:value-of select="id"/>
	</xsl:variable>
	<option value="{$id}{cat_id}">
		<xsl:if test="selected='selected' or selected = 1">
			<xsl:attribute name="selected">
				<xsl:text>selected</xsl:text>
			</xsl:attribute>
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>

<!-- add / edit -->
<xsl:template match="edit" xmlns:php="http://php.net/xsl">
	<style>
		.file {
		position: relative;
		background: linear-gradient(to right, lightblue 50%, transparent 50%);
		background-size: 200% 100%;
		background-position: right bottom;
		transition:all 1s ease;
		background: lightgrey;
		}
		.file.done {
		background: lightgreen;
		}
	</style>
	<dl>
		<xsl:choose>
			<xsl:when test="msgbox_data != ''">
				<dt>
					<xsl:call-template name="msgbox"/>
				</dt>
			</xsl:when>
		</xsl:choose>
	</dl>
	<xsl:variable name="form_action">
		<xsl:value-of select="form_action"/>
	</xsl:variable>
	<form ENCTYPE="multipart/form-data" method="post" id="form" name="form" action="{$form_action}" class="pure-form pure-form-aligned">
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="tabs"/>
			<div id="general">
				<fieldset>
					<xsl:choose>
						<xsl:when test="location_type='form'">
							<xsl:call-template name="location_form2">
								<xsl:with-param name="class">pure-input-3-4</xsl:with-param>
							</xsl:call-template>
						</xsl:when>
						<xsl:otherwise>
							<xsl:call-template name="location_view"/>
						</xsl:otherwise>
					</xsl:choose>
					<div class="pure-control-group">
						<xsl:call-template name="vendor_form">
							<xsl:with-param name="class">pure-input-3-4</xsl:with-param>
						</xsl:call-template>
					</div>
					<xsl:choose>
						<xsl:when test="value_document_name!=''">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'document name')"/>
									<input type="hidden" name="values[document_name_orig]" value="{value_document_name}"/>
									<input type="hidden" name="values[location_code]" value="{value_location_code}"/>
								</label>
								<a>
									<xsl:attribute name="href">
										<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:property.uidocument.view_file')" />
										<xsl:text>&amp;id=</xsl:text>
										<xsl:value-of select="value_id"/>
									</xsl:attribute>
									<xsl:attribute name="target">
										<xsl:text>_blank</xsl:text>
									</xsl:attribute>
									<xsl:value-of select="value_document_name"/>
								</a>
							</div>
						</xsl:when>
					</xsl:choose>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'upload files')"/>
						</label>
						<div id="drop-area" class="pure-input-3-4 pure-custom">
							<div style="border: 2px dashed #ccc; padding: 20px;">
								<p>
									<xsl:value-of select="php:function('lang', 'Upload file with the file dialog, or by dragging and dropping the file onto the dashed region')"/>
								</p>
								<div class="fileupload-buttonbar">
									<div  class="fileupload-buttons">
										<!-- The fileinput-button span is used to style the file input field as button -->
										<span class="fileinput-button pure-button">
											<span>
												<xsl:value-of select="php:function('lang', 'Add files')"/>
												<xsl:text>...</xsl:text>
											</span>
											<input id="fileupload" type="file" name="files[]">
												<xsl:attribute name="data-url">
													<xsl:value-of select="multi_upload_action"/>
												</xsl:attribute>
											</input>
										</span>

										<!-- The global file processing state -->
										<span class="fileupload-process"></span>
									</div>
									<div class="fileupload-count">
										<xsl:value-of select="php:function('lang', 'Number files')"/>: <span id="files_count"></span> <span id="files_progress"></span>
									</div>
									<div class="fileupload-progress" style="display:none">
										<!-- The global progress bar -->
										<div id = 'progress' class="progress" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
										<!-- The extended global progress state -->
										<div class="progress-extended">&nbsp;</div>
									</div>
								</div>
								<!-- The table listing the files available for upload/download -->
								<div class="content_upload_download">
									<div class="presentation files" style="display: inline-table;"></div>
								</div>
							</div>
						</div>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'version')"/>
						</label>
						<input type="text" name="values[version]" value="{value_version}" size="12" class="pure-input-3-4">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'Enter document version')"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'link')"/>
						</label>
						<input type="text" name="values[link]" value="{value_link}" size="50" class="pure-input-3-4">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'Alternative - link instead of uploading a file')"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'title')"/>

						</label>
						<input type="text" name="values[title]" value="{value_title}" size="50" class="pure-input-3-4">
							<xsl:attribute name="data-validation">
								<xsl:text>required</xsl:text>
							</xsl:attribute>
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'Enter document title')"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Description')"/>

						</label>
						<textarea cols="60" rows="6" name="values[descr]" class="pure-custom pure-input-3-4">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'Enter a description of the document')"/>
							</xsl:attribute>
							<xsl:value-of select="value_descr"/>
						</textarea>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'category')"/>
						</label>
						<xsl:call-template name="categories">
							<xsl:with-param name="class">pure-input-3-4</xsl:with-param>
						</xsl:call-template>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Coordinator')"/>
						</label>
						<xsl:call-template name="user_id_select">
							<xsl:with-param name="class">pure-input-3-4</xsl:with-param>
							<xsl:with-param name="required">required</xsl:with-param>
						</xsl:call-template>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Status')"/>
						</label>
						<xsl:call-template name="status_select">
							<xsl:with-param name="class">pure-input-3-4</xsl:with-param>
							<xsl:with-param name="required">required</xsl:with-param>
						</xsl:call-template>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'document date')"/>
						</label>
						<input type="text" id="values_document_date" name="values[document_date]" size="10" value="{value_document_date}" readonly="readonly">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'Select date the document was created')"/>
							</xsl:attribute>
							<xsl:attribute name="data-validation">
								<xsl:text>required</xsl:text>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'branch')"/>
						</label>
						<xsl:variable name="lang_branch_statustext">
							<xsl:value-of select="php:function('lang', 'Select the branch for this document')"/>
						</xsl:variable>
						<select name="values[branch_id]"  class="pure-input-3-4">
							<xsl:attribute name="data-validation">
								<xsl:text>required</xsl:text>
							</xsl:attribute>
							<xsl:attribute name="title">
								<xsl:value-of select="$lang_branch_statustext"/>
							</xsl:attribute>
							<option value="">
								<xsl:value-of select="php:function('lang', 'No branch')"/>
							</option>
							<xsl:apply-templates select="branch_list"/>
						</select>
					</div>
				</fieldset>
			</div>
			<div id="history">
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
		<input type="hidden" id="save" name="values[save]" value=""/>
		<input type="hidden" id="apply" name="values[apply]" value=""/>
		<input type="hidden" id="cancel" name="values[cancel]" value=""/>
		<input type="hidden" id="document_name" name="values[document_name]" value=""/>
	</form>
	<div class="proplist-col">
		<button class="pure-button pure-button-primary" name="apply" onClick="confirm_session('apply');">
			<xsl:attribute name="title">
				<xsl:value-of select="php:function('lang', 'Save the document')"/>
			</xsl:attribute>
			<xsl:value-of select="php:function('lang', 'save')"/>

		</button>
		<button class="pure-button pure-button-primary" name="cancel" onClick="confirm_session('cancel');">
			<xsl:attribute name="title">
				<xsl:value-of select="php:function('lang', 'Back to the ticket list')"/>
			</xsl:attribute>
			<xsl:value-of select="php:function('lang', 'done')"/>

		</button>
	</div>

</xsl:template>

<!-- New template-->
<xsl:template match="branch_list">
	<xsl:variable name="id">
		<xsl:value-of select="id"/>
	</xsl:variable>
	<xsl:choose>
		<xsl:when test="selected">
			<option value="{$id}" selected="selected">
				<xsl:value-of disable-output-escaping="yes" select="name"/>
			</option>
		</xsl:when>
		<xsl:otherwise>
			<option value="{$id}">
				<xsl:value-of disable-output-escaping="yes" select="name"/>
			</option>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>


<!-- view -->
<xsl:template match="view">
	<div align="left">
		<table cellpadding="2" cellspacing="2" width="80%" align="center">
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_document_name"/>
				</td>
				<td>
					<xsl:value-of select="value_document_name"/>
				</td>
			</tr>
			<tr>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_version"/>
				</td>
				<td>
					<xsl:value-of select="value_version"/>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_title"/>
				</td>
				<td>
					<xsl:value-of select="value_title"/>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_descr"/>
				</td>
				<td>
					<xsl:value-of select="value_descr"/>
				</td>
			</tr>
			<xsl:call-template name="vendor_view"/>
			<tr>
				<td>
					<xsl:value-of select="lang_category"/>
				</td>
				<xsl:for-each select="cat_list">
					<xsl:choose>
						<xsl:when test="selected='selected' or selected = 1">
							<td>
								<xsl:value-of select="name"/>
							</td>
						</xsl:when>
					</xsl:choose>
				</xsl:for-each>
			</tr>
			<xsl:call-template name="location_view"/>
			<tr>
				<td>
					<xsl:value-of select="lang_coordinator"/>
				</td>
				<xsl:for-each select="user_list">
					<xsl:choose>
						<xsl:when test="selected">
							<td>
								<xsl:value-of select="name"/>
							</td>
						</xsl:when>
					</xsl:choose>
				</xsl:for-each>
			</tr>
			<tr>
				<td>
					<xsl:value-of select="lang_status"/>
				</td>
				<xsl:for-each select="status_list">
					<xsl:choose>
						<xsl:when test="selected">
							<td>
								<xsl:value-of select="name"/>
							</td>
						</xsl:when>
					</xsl:choose>
				</xsl:for-each>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_document_date"/>
				</td>
				<td>
					<xsl:value-of select="value_document_date"/>
				</td>
			</tr>
			<tr>
				<td>
					<xsl:value-of select="lang_branch"/>
				</td>
				<xsl:for-each select="branch_list">
					<xsl:choose>
						<xsl:when test="selected">
							<td>
								<xsl:value-of select="name"/>
							</td>
						</xsl:when>
					</xsl:choose>
				</xsl:for-each>
			</tr>
			<tr height="50">
				<td>
					<xsl:variable name="done_action">
						<xsl:value-of select="done_action"/>
					</xsl:variable>
					<xsl:variable name="lang_done">
						<xsl:value-of select="lang_done"/>
					</xsl:variable>
					<form method="post" action="{$done_action}">
						<input type="submit" class="forms" name="done" value="{$lang_done}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_done_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</form>
					<xsl:variable name="edit_action">
						<xsl:value-of select="edit_action"/>
					</xsl:variable>
					<xsl:variable name="lang_edit">
						<xsl:value-of select="lang_edit"/>
					</xsl:variable>
					<form method="post" action="{$edit_action}">
						<input type="submit" class="forms" name="edit" value="{$lang_edit}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_edit_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</form>
				</td>
			</tr>
		</table>
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
	<hr noshade="noshade" width="100%" align="center" size="1"/>
</xsl:template>
