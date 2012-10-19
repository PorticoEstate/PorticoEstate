<xsl:template match="data" xmlns:php="http://php.net/xsl">

<xsl:call-template name="yui_phpgw_i18n"/>
<div class="yui-navset yui-navset-top">
	<div>
		<h1><img src="{img_go_home}" />
				<xsl:value-of select="php:function('lang', 'Administrate requirements')" />
		</h1>
	</div>
	<div class="main_content">
		<div id="details" class="content-wrp">
			<form action="#" method="post">
				<input type="hidden" name="id" value = "{value_id}" />
				<input type="hidden" name="location_id" value = "{location_id}" />
				<input type="hidden" name="project_type_id" value = "{project_type_id}" />
				<dl class="proplist-col">
					<dt>
						<label><xsl:value-of select="php:function('lang', 'Project_type')" /></label>
					</dt>
					<dd>
						<xsl:choose>
							<xsl:when test="editable">
								<select name="project_type_id" id="project_type_id">
									<xsl:for-each select="project_types">
										<option value="{id}">
											<xsl:if test="selected">
												<xsl:attribute name="selected" value="selected" />
											</xsl:if>
											<xsl:value-of select="name"/>
										</option>
									</xsl:for-each>
								</select>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="project_type/name" />
							</xsl:otherwise>
						</xsl:choose>
					</dd>
					<xsl:choose>
						<xsl:when test="editable">
							<dt>
								<label><xsl:value-of select="php:function('lang', 'Entity')" /></label>
							</dt>
							<dd>
								<select name="entity_id" id="entity_id">
									<xsl:for-each select="entities">
										<option value="{id}">
											<xsl:if test="selected">
												<xsl:attribute name="selected" value="selected" />
											</xsl:if>
											<xsl:value-of select="name"/>
										</option>
									</xsl:for-each>
								</select>
							</dd>
							<dt>
								<label><xsl:value-of select="php:function('lang', 'Category')" /></label>
							</dt>
							<dd>
								<select name="category_id" id="category_id">
									<xsl:for-each select="categories">
										<option value="{id}">
											<xsl:if test="selected">
												<xsl:attribute name="selected" value="selected" />
											</xsl:if>
											<xsl:value-of select="name"/>
										</option>
									</xsl:for-each>
								</select>
							</dd>
							<dt>
								<label><xsl:value-of select="php:function('lang', 'Attributes')" /></label>
							</dt>
							<dd>
								<div id="attributes">
									<xsl:if test="req_type/cust_attribute_id">
										<xsl:for-each select="attributes">
											<xsl:if test="input_text">
												<xsl:choose>
													<xsl:when test="checked">
														<input type='checkbox' name='attributes[]' id='attributes[]' value='{id}' checked='checked'/><xsl:value-of select="input_text" /> <xsl:value-of select="trans_datatype" /><br/>
													</xsl:when>
													<xsl:otherwise>
														<input type='checkbox' name='attributes[]' id='attributes[]' value='{id}'/><xsl:value-of select="input_text" /> <xsl:value-of select="trans_datatype" /><br/>
													</xsl:otherwise>
												</xsl:choose>
											</xsl:if>
										</xsl:for-each>
									</xsl:if>
								</div>
							</dd>
						</xsl:when>
						<xsl:otherwise>
							<dt>
								<label><xsl:value-of select="php:function('lang', 'Entity')" /></label>
							</dt>
							<dd>
								<xsl:value-of select="entity/name" />
							</dd>
							<dt>
								<label><xsl:value-of select="php:function('lang', 'Category')" /></label>
							</dt>
							<dd>
								<xsl:value-of select="category/name" />
							</dd>
							<dt>
								<label><xsl:value-of select="php:function('lang', 'Chosen attributes')" /></label>
							</dt>
							<dd>
								<xsl:for-each select="attributes">
									<xsl:value-of select="input_text" /> (<xsl:value-of select="trans_datatype" />)<br/>
								</xsl:for-each>
							</dd>
						</xsl:otherwise>
					</xsl:choose>
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
