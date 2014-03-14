<!-- $Id: project_type_item.xsl 10272 2012-10-19 12:20:21Z vator $ -->
<!-- item  -->

<xsl:template match="data" xmlns:php="http://php.net/xsl">

<div id="main_content" class="yui-content">
	<h1>Prosjekttype</h1>
		<div style="padding:20px;" class="content-wrp">
			<form action="#" method="post">
				<input type="hidden" name="id" value = "{value_id}">
				</input>
				<dl class="proplist-col">
					<dt>
						<label for="name"><xsl:value-of select="php:function('lang','Project type name')" /></label>
					</dt>
					<dd>
					<xsl:choose>
						<xsl:when test="editable">
							<input type="text" name="title" id="title" value="{project/name}" size="100"/>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="project/name" />
						</xsl:otherwise>
					</xsl:choose>
					</dd>		
				</dl>
				
				<div class="form-buttons">
					<xsl:choose>
						<xsl:when test="editable">
							<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
							<xsl:variable name="lang_cancel"><xsl:value-of select="php:function('lang', 'cancel')" /></xsl:variable>
							<input type="submit" name="save_project_type" value="{$lang_save}" title = "{$lang_save}" />
							<input type="submit" name="cancel_project_type" value="{$lang_cancel}" title = "{$lang_cancel}" />
						</xsl:when>
						<xsl:otherwise>
							<xsl:variable name="lang_edit"><xsl:value-of select="php:function('lang', 'edit')" /></xsl:variable>
							<input type="submit" name="edit_project_type" value="{$lang_edit}" title = "{$lang_edit}" />
						</xsl:otherwise>
					</xsl:choose>
				</div>
			</form>
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
