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
						<xsl:for-each select="entities">
							<xsl:value-of disable-output-escaping="yes" select="name"/><br/>
							<i><xsl:value-of disable-output-escaping="yes" select="descr"/></i><br/>
							<ul>
								<xsl:for-each select="cat_list">
									<li>
										<xsl:value-of disable-output-escaping="yes" select="name"/><br/>
										<i><xsl:value-of disable-output-escaping="yes" select="descr"/></i><br/>
											<xsl:for-each select="attrib">
													<xsl:value-of disable-output-escaping="yes" select="name"/><br/>
													<i><xsl:value-of disable-output-escaping="yes" select="descr"/></i>
											</xsl:for-each>
									</li>
								</xsl:for-each>
							</ul>
							<br/>
						</xsl:for-each>
					</dd>
					<dt>
						<label>Prosjekttype</label>
					</dt>
					<dd>
						<select name="project_type" id="project_type">
							<xsl:for-each select="project_types">
								<option value="{id}">
									<xsl:value-of select="name"/>
								</option>
							</xsl:for-each>
						</select>
					</dd>
					<dt>
						<label>BIM</label>
					</dt>
					<dd>
						<select name="level0" id="level0">
							<xsl:for-each select="entities">
								<option value="{id}">
									<xsl:value-of select="name"/>
								</option>
							</xsl:for-each>
						</select>
					</dd>
					<dt>
						<label>BIM2</label>
					</dt>
					<dd>
						<select name="level1" id="level1">
							<xsl:for-each select="entities/cat_list">
								<option value="{id}">
									<xsl:value-of select="name"/>
								</option>
							</xsl:for-each>
						</select>
					</dd>
					<dt>
						<label>BIM3</label>
					</dt>
					<dd>
						<select name="level2" id="level2">
							<xsl:for-each select="entities/cat_list/attrib">
								<option value="{id}">
									<xsl:value-of select="name"/>
								</option>
							</xsl:for-each>
						</select>
					</dd>
				</dl>
				<div class="form-buttons">
					<xsl:choose>
						<xsl:when test="editable">
							<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
							<xsl:variable name="lang_cancel"><xsl:value-of select="php:function('lang', 'cancel')" /></xsl:variable>
							<input type="submit" name="save" value="{$lang_save}" title = "{$lang_save}" />
							<input type="submit" name="cancel" value="{$lang_cancel}" title = "{$lang_cancel}" />
						</xsl:when>
						<xsl:otherwise>
							<xsl:variable name="lang_edit"><xsl:value-of select="php:function('lang', 'edit')" /></xsl:variable>
							<input type="submit" name="edit" value="{$lang_edit}" title = "{$lang_edit}" />
						</xsl:otherwise>
					</xsl:choose>
				</div>
			</form>
		</div>
	</div>
</div>
</xsl:template>
