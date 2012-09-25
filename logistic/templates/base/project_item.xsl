<!-- $Id:$ -->
<!-- item  -->

<xsl:template match="data" xmlns:php="http://php.net/xsl">

<xsl:call-template name="yui_phpgw_i18n"/>
<div class="yui-navset yui-navset-top">
	<div class="identifier-header">
		<h1><img src="{img_go_home}" /> 
				<xsl:value-of select="php:function('lang', 'Project')" />
		</h1>
	</div>
	<div class="yui-content">
		<div id="details">
			<form action="#" method="post">
				<input type="hidden" name="id" value = "{value_id}">
				</input>
				<dl class="proplist-col">
					<dt>
						<label for="name"><xsl:value-of select="php:function('lang','Project title')" /></label>
					</dt>
					<dd>
					<xsl:choose>
						<xsl:when test="editable">
							<input type="text" name="name" id="name" value="{project/name}" size="100"/>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="project/name" />
						</xsl:otherwise>
					</xsl:choose>
					</dd>
					<dt>
						<label for="project_type"><xsl:value-of select="php:function('lang','Project_type')" /></label>
					</dt>
					<dd>
					<xsl:choose>
						<xsl:when test="editable">
							<select id="project_type_id" name="project_type_id">
								<xsl:apply-templates select="options"/>
							</select>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="project/project_type_label" />
						</xsl:otherwise>
					</xsl:choose>
					</dd>	
					<dt>
						<label for="description"><xsl:value-of select="php:function('lang', 'Description')" /></label>
					</dt>
					<dd>
					<xsl:choose>
						<xsl:when test="editable">
							<textarea id="description" name="description" rows="5" cols="60"><xsl:value-of select="project/description" disable-output-escaping="yes"/></textarea>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="project/description" disable-output-escaping="yes"/>
						</xsl:otherwise>
					</xsl:choose>
					</dd>
				</dl>
				
				<div class="form-buttons">
					<xsl:choose>
						<xsl:when test="editable">
							<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
							<xsl:variable name="lang_cancel"><xsl:value-of select="php:function('lang', 'cancel')" /></xsl:variable>
							<input type="submit" name="save_project" value="{$lang_save}" title = "{$lang_save}" />
							<input type="submit" name="cancel_project" value="{$lang_cancel}" title = "{$lang_cancel}" />
						</xsl:when>
						<xsl:otherwise>
							<xsl:variable name="lang_edit"><xsl:value-of select="php:function('lang', 'edit')" /></xsl:variable>
							<input type="submit" name="edit_project" value="{$lang_edit}" title = "{$lang_edit}" />
						</xsl:otherwise>
					</xsl:choose>
				</div>
			</form>
		</div>
	</div>
</div>
</xsl:template>

<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected">
			<xsl:attribute name="selected" value="selected" />
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>
