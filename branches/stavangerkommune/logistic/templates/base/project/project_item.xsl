<!-- $Id: project_item.xsl 10469 2012-11-05 09:02:14Z vator $ -->
<!-- item  -->

<xsl:template match="data" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format"><xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')"/></xsl:variable>

<xsl:call-template name="yui_phpgw_i18n"/>
<div class="yui-navset yui-navset-top">
	<xsl:choose>
		<xsl:when test="project/id != '' or project/id != 0">
			<h1>
				<xsl:value-of select="php:function('lang', 'Edit project')" />
			</h1>
		</xsl:when>
		<xsl:otherwise>
			<h1>
				<xsl:value-of select="php:function('lang', 'Add project')" />
			</h1>
		</xsl:otherwise>
	</xsl:choose>
	
	<div id="project_details" class="content-wrp">
		<div id="details">
			<xsl:variable name="action_url">
				<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:logistic.uiproject.save')" />
			</xsl:variable>
			<form action="{$action_url}" method="post">
				<input type="hidden" name="id" value="{project/id}">
				</input>
				<dl class="proplist-col">
					<dt>
						<label for="name"><xsl:value-of select="php:function('lang','Project title')" /></label>
					</dt>
					<dd>
					<xsl:choose>
						<xsl:when test="editable">
							<xsl:if test="project/error_msg_array/name != ''">
								<xsl:variable name="error_msg"><xsl:value-of select="project/error_msg_array/name" /></xsl:variable>
								<div class='input_error_msg'><xsl:value-of select="php:function('lang', $error_msg)" /></div>
							</xsl:if>
							<div style="margin-left:0; margin-bottom: 3px;" class="help_text line"><xsl:value-of select="php:function('lang','Give project name')" /></div>
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
							<xsl:if test="project/error_msg_array/project_type_id != ''">
								<xsl:variable name="error_msg"><xsl:value-of select="project/error_msg_array/project_type_id" /></xsl:variable>
								<div class='input_error_msg'><xsl:value-of select="php:function('lang', $error_msg)" /></div>
							</xsl:if>
							<div style="margin-left:0; margin-bottom: 3px;" class="help_text line"><xsl:value-of select="php:function('lang','Give project type')" /></div>
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
							<xsl:if test="project/error_msg_array/description != ''">
								<xsl:variable name="error_msg"><xsl:value-of select="project/error_msg_array/description" /></xsl:variable>
								<div class='input_error_msg'><xsl:value-of select="php:function('lang', $error_msg)" /></div>
							</xsl:if>
							<div style="margin-left:0; margin-bottom: 3px;" class="help_text line"><xsl:value-of select="php:function('lang','Give description to the project')" /></div>
							<textarea id="description" name="description" rows="5" cols="60"><xsl:value-of select="project/description" disable-output-escaping="yes"/></textarea>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="project/description" disable-output-escaping="yes"/>
						</xsl:otherwise>
					</xsl:choose>
					</dd>
					<dt>
						<label for="start_date"><xsl:value-of select="php:function('lang','Start date')" /></label>
					</dt>
					<dd>
						<xsl:choose>
							<xsl:when test="editable">
								<xsl:if test="project/error_msg_array/start_date != ''">
									<xsl:variable name="error_msg"><xsl:value-of select="project/error_msg_array/start_date" /></xsl:variable>
									<div class='input_error_msg'><xsl:value-of select="php:function('lang', $error_msg)" /></div>
								</xsl:if>
								<input class="date" id="start_date" name="start_date" type="text">
						    	<xsl:if test="project/start_date != ''">
						      	<xsl:attribute name="value"><xsl:value-of select="php:function('date', $date_format, number(project/start_date))"/></xsl:attribute>
						    	</xsl:if>
					    	</input>
					    	<span class="help_text line"><xsl:value-of select="php:function('lang','Give start date to project')" /></span>
							</xsl:when>
							<xsl:otherwise>
							<span><xsl:value-of select="php:function('date', $date_format, number(project/start_date))"/></span>
							</xsl:otherwise>
						</xsl:choose>
					</dd>
					<dt>
						<label for="end_date"><xsl:value-of select="php:function('lang','End date')" /></label>
					</dt>
					<dd>
						<xsl:choose>
							<xsl:when test="editable">
								<xsl:if test="project/error_msg_array/end_date != ''">
									<xsl:variable name="error_msg"><xsl:value-of select="project/error_msg_array/end_date" /></xsl:variable>
									<div class='input_error_msg'><xsl:value-of select="php:function('lang', $error_msg)" /></div>
								</xsl:if>
								<input class="date" id="end_date" name="end_date" type="text">
						    	<xsl:if test="project/end_date != ''">
						      	<xsl:attribute name="value"><xsl:value-of select="php:function('date', $date_format, number(project/end_date))"/></xsl:attribute>
						    	</xsl:if>
					    	</input>
					    	<span class="help_text line"><xsl:value-of select="php:function('lang','Give end date to project')" /></span>
							</xsl:when>
							<xsl:otherwise>
							<span><xsl:value-of select="php:function('date', $date_format, number(project/end_date))"/></span>
							</xsl:otherwise>
						</xsl:choose>
					</dd>
				</dl>

				<div class="form-buttons">
					<xsl:choose>
						<xsl:when test="editable">
							<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
							<input type="submit" name="save_project" value="{$lang_save}" title = "{$lang_save}" />
							
							<xsl:variable name="view_projects_url">
								<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:logistic.uiproject.index' )" />
							</xsl:variable>
							<a class="btn" href="{$view_projects_url}"><xsl:value-of select="php:function('lang', 'Cancel')" /></a>
						</xsl:when>
						<xsl:otherwise>
							<xsl:variable name="lang_edit"><xsl:value-of select="php:function('lang', 'edit')" /></xsl:variable>
							<input type="submit" name="edit_project" value="{$lang_edit}" title = "{$lang_edit}" />
							
							<xsl:variable name="view_projects_url_2">
								<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:logistic.uiproject.index' )" />
							</xsl:variable>
							<a class="btn" href="{$view_projects_url_2}"><xsl:value-of select="php:function('lang','Show project overview')" /></a>
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
