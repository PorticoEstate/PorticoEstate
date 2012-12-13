<!-- $Id: condition_survey.xsl 10560 2012-11-30 13:52:18Z sigurdne $ -->

	<!-- import -->
<xsl:template match="data" xmlns:formvalidator="http://www.w3.org/TR/html4/" xmlns:php="http://php.net/xsl">
		<xsl:call-template name="yui_phpgw_i18n"/>

		<div class="yui-navset yui-navset-top" id="survey_edit_tabview">
	
		<h1>
			<xsl:value-of select="php:function('lang', 'condition survey import')" />
		</h1>


		<xsl:variable name="action_url">
			<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:property.uicondition_survey.import')" />
		</xsl:variable>

			<form name="form" id="form" action="{$action_url}" method="post" ENCTYPE="multipart/form-data">
		        <dl>
					<dt>
						<label><xsl:value-of select="php:function('lang', 'id')" /></label>
					</dt>
					<dd>
						<xsl:value-of select="survey/id"/>
						<input type="hidden" name="id" value="{survey/id}"/>
						<input type="hidden" name="step" value="{step}"/>
						<input type="hidden" name="selected_sheet_id" value="{sheet_id}"/>
						<input type="hidden" name="start_line" value="{start_line}"/>
					</dd>

					<xsl:choose>
						<xsl:when test="location_data2!=''">
							<xsl:call-template name="location_view2"/>
						</xsl:when>
					</xsl:choose>

					<dt>
						<label for="name"><xsl:value-of select="php:function('lang', 'name')" /></label>
					</dt>
					<dd>
						<xsl:value-of select="survey/title" />
					</dd>

					<dt>
						<label><xsl:value-of select="php:function('lang', 'date')" /></label>
					</dt>
					<dd>
						<xsl:value-of select="survey/report_date"/>
					</dd>
				</dl>
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<div class="yui-content">
					<xsl:choose>
						<xsl:when test="step=1">
							<xsl:call-template name="import_step_1"/>
						</xsl:when>
						<xsl:when test="step=2">
							<xsl:call-template name="import_step_2"/>
						</xsl:when>
						<xsl:when test="step=3">
							<xsl:call-template name="import_step_3"/>
						</xsl:when>
						<xsl:when test="step=4">
							<xsl:call-template name="import_step_4"/>
						</xsl:when>
						<xsl:otherwise>
							<dl class="proplist-col">
								<dt>
									<label><xsl:value-of select="php:function('lang', 'finished')" /></label>
								</dt>
							</dl>
						</xsl:otherwise>
					</xsl:choose>
				</div>

				<dl class="proplist-col">
					<div class="form-buttons">
						<xsl:variable name="lang_submit"><xsl:value-of select="lang_submit"/></xsl:variable>
						<xsl:variable name="lang_cancel"><xsl:value-of select="php:function('lang', 'cancel')" /></xsl:variable>
						<xsl:choose>
							<xsl:when test="$lang_submit != ''">
								<input type="submit" name="submit_step" value="{$lang_submit}" title = "{$lang_submit}" />
							</xsl:when>
						</xsl:choose>
						<input class="button" type="button" name="cancelButton" id ='cancelButton' value="{$lang_cancel}" title = "{$lang_cancel}" onClick="document.cancel_form.submit();"/>
					</div>
			</dl>
			</form>
		</div>

		<xsl:variable name="cancel_params">
			<xsl:text>menuaction:property.uicondition_survey.view, id:</xsl:text>
				<xsl:value-of select="survey/id" />
			</xsl:variable>
		<xsl:variable name="cancel_url">
				<xsl:value-of select="php:function('get_phpgw_link', '/index.php', $cancel_params )" />
		</xsl:variable>

		<form name="cancel_form" id="cancel_form" action="{$cancel_url}" method="post">
		</form>
		<xsl:variable name="new_url">
			<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:property.uicondition_survey.add')" />
		</xsl:variable>
		<form name="new_form" id="new_form" action="{$new_url}" method="post">
		</form>

		<xsl:variable name="edit_params">
			<xsl:text>menuaction:property.uicondition_survey.import, id:</xsl:text>
				<xsl:value-of select="survey/id" />
			</xsl:variable>
		<xsl:variable name="edit_url">
				<xsl:value-of select="php:function('get_phpgw_link', '/index.php', $edit_params )" />
		</xsl:variable>

		<form name="load_edit_form" id="load_edit_form" action="{$edit_url}" method="post">
		</form>

	</xsl:template>


<xsl:template name="import_step_1" xmlns:formvalidator="http://www.w3.org/TR/html4/" xmlns:php="http://php.net/xsl">
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
</xsl:template>


<xsl:template name="import_step_2" xmlns:formvalidator="http://www.w3.org/TR/html4/" xmlns:php="http://php.net/xsl">
	<dl class="proplist-col">
		<dt>
			<label for="status"><xsl:value-of select="php:function('lang', 'sheet')" /></label>
		</dt>
		<dd>
 			<select id="sheet_id" name="sheet_id">
				<xsl:apply-templates select="sheets/options"/>
			</select>
		</dd>
	</dl>
</xsl:template>

<xsl:template name="import_step_3" xmlns:php="http://php.net/xsl">
	<dl class="proplist-col">

		<dt>
			<label for="status"><xsl:value-of select="php:function('lang', 'sheet')" /></label>
		</dt>
		<dd>
			<xsl:for-each select="sheets/options">
				<xsl:if test="selected = 'selected' or selected = 1">
					<xsl:value-of disable-output-escaping="yes" select="name"/>
				</xsl:if>
			</xsl:for-each>
		</dd>

 		<dt>
			<label for="status"><xsl:value-of select="php:function('lang', 'table')" /></label>
		</dt>
		<dd>
			<xsl:value-of disable-output-escaping="yes" select="html_table"/>
		</dd>
	</dl>
</xsl:template>

<xsl:template name="import_step_4" xmlns:php="http://php.net/xsl">
	<dl class="proplist-col">
		<dt>
			<label><xsl:value-of select="php:function('lang', 'sheet')" /></label>
		</dt>
		<dd>
			<xsl:for-each select="sheets/options">
				<xsl:if test="selected = 'selected' or selected = 1">
					<xsl:value-of disable-output-escaping="yes" select="name"/>
				</xsl:if>
			</xsl:for-each>
		</dd>
		<dt>
			<label><xsl:value-of select="php:function('lang', 'line')" /></label>
		</dt>
		<dd>
			<xsl:value-of select="start_line"/>
		</dd>
		<dt>
			<label><xsl:value-of select="php:function('lang', 'columns')" /></label>
		</dt>
		<dd>
			<xsl:value-of disable-output-escaping="yes" select="html_table"/>
		</dd>
	</dl>
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

