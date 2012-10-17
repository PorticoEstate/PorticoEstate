<!-- $Id: activity_item.xsl 10096 2012-10-03 07:10:49Z vator $ -->
<!-- item  -->

<xsl:template name="requirement_values" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format"><xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')"/></xsl:variable>

<div class="yui-navset yui-navset-top">
	<div class="yui-content" style="padding: 20px;">
		<div id="details">
			<form id="frm-requirement-values" action="#" method="post">
				<input type="hidden" name="requirement_id" value = "{requirement/id}" />
											
				<dl class="proplist-col">
					<xsl:choose>
						<xsl:when test="editable">
						<dt>
							<label>Legg til kriterier</label>
						</dt>
							<dd>
							<div id="attributes">
								<xsl:for-each select="custom_attributes_array">
									<div class="attribute">
									<xsl:choose>
										<xsl:when test="column_info/type = 'T'">
												<label><xsl:value-of select="input_text"/></label>
												<input class="operator" type='hidden' name="operator" value='eq' />
												<input class="attrib_info" type='text' name="{column_name}" value='' />
										</xsl:when>
										<xsl:when test="column_info/type = 'V'">
											<label><xsl:value-of select="input_text"/></label>
											<input class="attrib_info constrain_1" style="display:none;margin-right: 10px;" type='text' name="{column_name}" value='' />
											<select class="operator" name="operator">
												<option value="eq"><xsl:text>Lik</xsl:text></option>
												<option value="lt"><xsl:text>Mindre enn</xsl:text></option>
												<option value="gt"><xsl:text>Større enn</xsl:text></option>
												<option value="btw"><xsl:text>Mellom</xsl:text></option>
											</select>
											<input class="attrib_info constrain_2" style="margin-left: 10px;" type='text' name="{column_name}" value='' />
										</xsl:when>
										<xsl:when test="column_info/type = 'LB'">
												<label><xsl:value-of select="input_text"/></label>
												<input class="operator" type='hidden' name="operator" value='eq' />
												<select class="attrib_info" name="{column_name}">
													<xsl:for-each select="choice">
														<option value="{value}">
															<xsl:value-of select="value"/>
														</option>
													</xsl:for-each>
												</select>
										</xsl:when>
									</xsl:choose>
									<input type="hidden" class="cust_attribute_id" name="cust_attribute_id" value="{id}" />
									<input type="hidden" class="location_id" name="location_id" value="{location_id}" />
									<input type="hidden" class="cust_attributes" name="cust_attributes[]" value="" />
									</div>
								</xsl:for-each>
							</div>
							</dd>					
						</xsl:when>
						<xsl:otherwise>
						<dt>
							<label>Kriterier for behovet</label>
						</dt>
							<dd>
							<div id="attributes">
								<xsl:for-each select="requirement_attributes_array">
									<div class="attribute">
									<label style="margin-left:10px;"><xsl:value-of select="cust_attribute/input_text" /></label>
										<xsl:choose>
											<xsl:when test="cust_attribute/column_info/type = 'T'">
												
												<span style="margin-left:10px;"><xsl:value-of select="value" /></span>
											</xsl:when>
											<xsl:when test="cust_attribute/column_info/type = 'V'">
													<xsl:choose>
														<xsl:when test="operator = 'eq'">
															<span style="margin-left:10px;">lik</span>
														</xsl:when>
														<xsl:when test="operator = 'gt'">
															<span style="margin-left:10px;">større enn</span>
														</xsl:when>
														<xsl:when test="operator = 'lt'">
															<span style="margin-left:10px;">mindre enn</span>
														</xsl:when>
													</xsl:choose>
												<span style="margin-left:10px;"><xsl:value-of select="value" /></span>
											</xsl:when>
											<xsl:when test="cust_attribute/column_info/type = 'LB'">
												<span style="margin-left:10px;"><xsl:value-of select="value" /></span>
											</xsl:when>
										</xsl:choose>
									</div>
								</xsl:for-each>
							</div>
							</dd>	
						</xsl:otherwise>
					</xsl:choose>
				</dl>
				
				<div class="form-buttons">
					<xsl:choose>
						<xsl:when test="editable">
							<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
							<xsl:variable name="lang_cancel"><xsl:value-of select="php:function('lang', 'cancel')" /></xsl:variable>
							<input type="submit" name="save_requirement_values" value="{$lang_save}" title = "{$lang_save}" />
							<input type="submit" name="cancel_requirement" value="{$lang_cancel}" title = "{$lang_cancel}" />
						</xsl:when>
						<xsl:otherwise>
							<xsl:variable name="lang_edit"><xsl:value-of select="php:function('lang', 'edit')" /></xsl:variable>
							<input type="submit" name="edit_requirement" value="{$lang_edit}" title = "{$lang_edit}" />
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
