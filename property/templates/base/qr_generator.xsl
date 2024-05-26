<!-- $Id: condition_survey.xsl 10560 2012-11-30 13:52:18Z sigurdne $ -->

<!-- import -->
<xsl:template match="data" xmlns:formvalidator="http://www.w3.org/TR/html4/" xmlns:php="http://php.net/xsl">
	
	<xsl:variable name="action_url">
		<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:property.uiqr_generator.index')" />
	</xsl:variable>
	<form name="form" id="form" action="{$action_url}" method="post" ENCTYPE="multipart/form-data" class="pure-form pure-form-aligned">
		<input type="hidden" name="id" value="{survey/id}"/>
		<input type="hidden" name="step" value="{step}"/>
		<input type="hidden" name="selected_sheet_id" value="{sheet_id}"/>
		<input type="hidden" name="start_line" value="{start_line}"/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="tabs"/>
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
					<div id="step_1"></div>
					<div id="step_2"></div>
					<div id="step_3"></div>
					<div id="step_4"></div>
					<div id="step_5">
						<table class="pure-table pure-table-bordered">
							<xsl:call-template name="convert_data"/>
						</table>
					</div>
				</xsl:otherwise>
			</xsl:choose>
		</div>

		<dl class="proplist-col">
			<div class="form-buttons">
				<xsl:variable name="lang_submit">
					<xsl:value-of select="lang_submit"/>
				</xsl:variable>
				<xsl:variable name="lang_cancel">
					<xsl:value-of select="php:function('lang', 'cancel')" />
				</xsl:variable>
				<xsl:choose>
					<xsl:when test="$lang_submit != ''">
						<input type="submit" name="submit_step" value="{$lang_submit}" title = "{$lang_submit}" class="pure-button pure-button-primary" />
					</xsl:when>
				</xsl:choose>
				<input class="pure-button pure-button-primary" type="button" name="cancelButton" id ='cancelButton' value="{$lang_cancel}" title = "{$lang_cancel}" onClick="document.cancel_form.submit();"/>
			</div>
		</dl>
	</form>

	<xsl:variable name="cancel_params">
		<xsl:text>menuaction:property.uiqr_generator.index</xsl:text>
	</xsl:variable>
	<xsl:variable name="cancel_url">
		<xsl:value-of select="php:function('get_phpgw_link', '/index.php', $cancel_params )" />
	</xsl:variable>

	<form name="cancel_form" id="cancel_form" action="{$cancel_url}" method="post">
	</form>

</xsl:template>


<xsl:template name="import_step_1" xmlns:formvalidator="http://www.w3.org/TR/html4/" xmlns:php="http://php.net/xsl">
	<div id="step_1">
		<div class="pure-control-group" >
			<label for="name">
				<xsl:value-of select="php:function('lang', 'upload file')"/>
			</label>
			<input type="file" name="import_file" size="40">
				<xsl:attribute name="title">
					<xsl:value-of select="php:function('lang', 'Select file to upload')"/>
				</xsl:attribute>
				<xsl:attribute name="data-validation">
					<xsl:text>required extension</xsl:text>
				</xsl:attribute>
				<xsl:attribute name="data-validation-allowing">
					<xsl:text>xls,xlsx</xsl:text>
				</xsl:attribute>
			</input>
		</div>
	</div>
	<div id="step_2"></div>
	<div id="step_3"></div>
	<div id="step_4"></div>
	<div id="step_5"></div>
</xsl:template>


<xsl:template name="import_step_2" xmlns:formvalidator="http://www.w3.org/TR/html4/" xmlns:php="http://php.net/xsl">
	<div id="step_1"></div>
	<div id="step_2">
		<div class="pure-control-group" >
			<label>
				<xsl:value-of select="php:function('lang', 'sheet')" />
			</label>
			<select id="sheet_id" name="sheet_id">
				<xsl:apply-templates select="sheets/options"/>
			</select>
		</div>
	</div>
	<div id="step_3"></div>
	<div id="step_4"></div>
	<div id="step_5"></div>
</xsl:template>

<xsl:template name="import_step_3" xmlns:php="http://php.net/xsl">
	<div id="step_1"></div>
	<div id="step_2"></div>
	<div id="step_3">
		<div class="pure-control-group" >

			<label for="status">
				<xsl:value-of select="php:function('lang', 'sheet')" />
			</label>
			<xsl:for-each select="sheets/options">
				<xsl:if test="selected = 'selected' or selected = 1">
					<xsl:value-of disable-output-escaping="yes" select="name"/>
				</xsl:if>
			</xsl:for-each>
		</div>
		<div class="pure-control-group" >
			<xsl:value-of disable-output-escaping="yes" select="html_table"/>
		</div>
	</div>
	<div id="step_4"></div>
	<div id="step_5"></div>

</xsl:template>

<xsl:template name="import_step_4" xmlns:php="http://php.net/xsl">
	<div id="step_1"></div>
	<div id="step_2"></div>
	<div id="step_3"></div>
	<div id="step_4">
		<div class="pure-control-group" >
			<label>
				<xsl:value-of select="php:function('lang', 'sheet')" />
			</label>
			<xsl:for-each select="sheets/options">
				<xsl:if test="selected = 'selected' or selected = 1">
					<xsl:value-of disable-output-escaping="yes" select="name"/>
				</xsl:if>
			</xsl:for-each>
		</div>
		<div class="pure-control-group" >
			<label>
				<xsl:value-of select="php:function('lang', 'line')" />
			</label>
			<xsl:value-of select="start_line"/>
		</div>
		<div class="pure-control-group" >
			<label>
				<xsl:value-of select="php:function('lang', 'columns')" />
			</label>
			<xsl:value-of disable-output-escaping="yes" select="html_table"/>
		</div>
	</div>
	<div id="step_5"></div>
</xsl:template>

<xsl:template name="convert_data" xmlns:php="http://php.net/xsl">
	<xsl:for-each select="convert_data">
		<tr>
			<td align="left">
				<xsl:value-of disable-output-escaping="yes" select="qr_input"/>
			</td>
			<td align="center">
				<img src="{encoded_text}"/>
			</td>
		</tr>
	</xsl:for-each>
	
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



