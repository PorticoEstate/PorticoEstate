<!-- $Id: condition_survey.xsl 10560 2012-11-30 13:52:18Z sigurdne $ -->

	<!-- import -->
<xsl:template match="data" xmlns:formvalidator="http://www.w3.org/TR/html4/" xmlns:php="http://php.net/xsl">
		<xsl:call-template name="yui_phpgw_i18n"/>

		<div class="yui-navset" id="survey_edit_tabview">
	
		<h1>
			<xsl:value-of select="php:function('lang', 'condition survey import')" />
		</h1>


		<xsl:variable name="action_url">
			<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:property.uicondition_survey.import')" />
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

					<xsl:choose>
						<xsl:when test="survey/id!=''">
								<dt>
									<label><xsl:value-of select="php:function('lang', 'id')" /></label>
								</dt>
								<dd>
									<xsl:value-of select="survey/id"/>
									<input type="hidden" name="id" value="{survey/id}"/>
									<input type="hidden" name="step" value="{step}"/>
									<input type="hidden" name="selected_sheet_id" value="{sheet_id}"/>
								</dd>
						</xsl:when>
					</xsl:choose>


				</dl>
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<div class="yui-content">
					<xsl:choose>
						<xsl:when test="step=1">
							<xsl:call-template name="import_step_1"/>
						</xsl:when>
						<xsl:otherwise>
							<div id="step_1" class="content-wrp">
							</div>
						</xsl:otherwise>
					</xsl:choose>

					<xsl:choose>
						<xsl:when test="step=2">
							<xsl:call-template name="import_step_2"/>
						</xsl:when>
						<xsl:otherwise>
							<div id="step_2" class="content-wrp">
							</div>
						</xsl:otherwise>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="step=3">
							<xsl:call-template name="import_step_3"/>
						</xsl:when>
						<xsl:otherwise>
							<div id="step_3" class="content-wrp">
							</div>
						</xsl:otherwise>
					</xsl:choose>

				</div>

				<dl class="proplist-col">
					<div class="form-buttons">
						<xsl:variable name="lang_cancel"><xsl:value-of select="php:function('lang', 'cancel')" /></xsl:variable>
						<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
						<input type="submit" name="save_project" value="{$lang_save}" title = "{$lang_save}" />
						<input class="button" type="button" name="cancelButton" id ='cancelButton' value="{$lang_cancel}" title = "{$lang_cancel}" onClick="document.cancel_form.submit();"/>
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
			<xsl:text>menuaction:property.uicondition_survey.import, id:</xsl:text>
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


<xsl:template name="import_step_1" xmlns:formvalidator="http://www.w3.org/TR/html4/" xmlns:php="http://php.net/xsl">
	<div id="step_1" class="content-wrp">
		<dl class="proplist-col">
			<dt>
			<label for="status"><xsl:value-of select="php:function('lang', 'sheets')" /></label>
		</dt>
		<dd>
 			<select id="sheet_id" name="sheet_id">
				<xsl:apply-templates select="sheets/options"/>
			</select>
		</dd>
		</dl>
	</div>
</xsl:template>

<xsl:template name="import_step_2" xmlns:php="http://php.net/xsl">
	<div id="step_2" class="content-wrp">
		<dl class="proplist-col">
			<dt>
			<label for="status"><xsl:value-of select="php:function('lang', 'table')" /></label>
		</dt>
		<dd>
			<xsl:value-of disable-output-escaping="yes" select="html_table"/>
		</dd>
		</dl>
	</div>
</xsl:template>

<xsl:template name="import_step_3" xmlns:php="http://php.net/xsl">
	<div id="step_2" class="content-wrp">
		<dl class="proplist-col">
			<dt>
			<label for="status"><xsl:value-of select="php:function('lang', 'table')" /></label>
		</dt>
		<dd>
			<xsl:value-of disable-output-escaping="yes" select="html_table"/>
		</dd>
		</dl>
	</div>
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

