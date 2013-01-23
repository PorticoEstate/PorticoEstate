<xsl:template name="requirement_values" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format"><xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')"/></xsl:variable>

<div class="yui-content" style="padding: 20px;">
		<h2><xsl:value-of select="php:function('lang', 'Criterias')" /></h2>
		
		<xsl:variable name="action_url">
			<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:logistic.uirequirement.save_requirement_values')" />
		</xsl:variable>
		<form id="frm-requirement-values" action="{$action_url}" method="post">
			<input type="hidden" name="requirement_id" value = "{requirement/id}" />
										
			<dl class="proplist-col">
			<dd>
				<xsl:choose>
					<xsl:when test="editable">
						<div id="attributes">
							<xsl:for-each select="requirement_attributes_array">
								<div class="attribute">
									<div style="display:none;" class='input_error_msg'><xsl:value-of select="php:function('lang', 'error_msg_1')" /></div>
									<xsl:choose>
										<xsl:when test="cust_attribute/column_info/type = 'T'">
												<label><xsl:value-of select="cust_attribute/input_text"/></label>
												<input class="operator" type='hidden' name="operator" value='eq' />
												<input class="attrib_info" type='text' name="{cust_attribute/column_name}" value='{attrib_value}' />
										</xsl:when>
										<xsl:when test="cust_attribute/column_info/type = 'V' or cust_attribute/column_info/type = 'I'">
											<label><xsl:value-of select="cust_attribute/input_text"/></label>
											
											<xsl:choose>
												<xsl:when test="operator = 'btw'">
													<xsl:variable name="gt-attrib-value"><xsl:value-of select="substring-before(attrib_value, ':')" /></xsl:variable>
													<input class="constraint_1" style="margin-right: 10px;" type='text' name="{cust_attribute/column_name}" value="{$gt-attrib-value}" />
												</xsl:when>
												<xsl:otherwise>
													<xsl:variable name="gt-attrib-value"><xsl:value-of select="substring-before(attrib_value, ':')" /></xsl:variable>
													<input class="constraint_1" style="margin-right: 10px;display:none;" type='text' name="{cust_attribute/column_name}" value="{$gt-attrib-value}" />
												</xsl:otherwise>
											</xsl:choose>
																								
											<select class="operator" name="operator">
												<xsl:choose>
													<xsl:when test="operator = 'eq'">
														<option selected='true' value="eq"><xsl:text>Lik</xsl:text></option>	
													</xsl:when>
													<xsl:otherwise>
														<option value="eq"><xsl:text>Lik</xsl:text></option>
													</xsl:otherwise>
												</xsl:choose>
												<xsl:choose>
													<xsl:when test="operator = 'lt'">
														<option selected='true' value="lt"><xsl:text>Mindre enn</xsl:text></option>	
													</xsl:when>
													<xsl:otherwise>
														<option value="lt"><xsl:text>Mindre enn</xsl:text></option>
													</xsl:otherwise>
												</xsl:choose>
												<xsl:choose>
													<xsl:when test="operator = 'gt'">
														<option selected='true' value="gt"><xsl:text>Større enn</xsl:text></option>
													</xsl:when>
													<xsl:otherwise>
														<option value="gt"><xsl:text>Større enn</xsl:text></option>
													</xsl:otherwise>
												</xsl:choose>
												<xsl:choose>
													<xsl:when test="operator = 'btw'">
														<option selected='true' value="btw"><xsl:text>Mellom</xsl:text></option>
													</xsl:when>
													<xsl:otherwise>
														<option value="btw"><xsl:text>Mellom</xsl:text></option>
													</xsl:otherwise>
												</xsl:choose>
											</select>
											
											<xsl:choose>
												<xsl:when test="operator = 'btw'">
													<xsl:variable name="lt-attrib-value"><xsl:value-of select="substring-after(attrib_value, ':')" /></xsl:variable>
													<input class="constraint_2" style="margin-left: 10px;" type='text' name="{cust_attribute/column_name}" value="{$lt-attrib-value}" />
												</xsl:when>
												<xsl:otherwise>
													<input class="attrib_info" style="margin-left: 10px;" type='text' name="{cust_attribute/column_name}" value="{attrib_value}" />
												</xsl:otherwise>
											</xsl:choose>
										</xsl:when>
										<xsl:when test="cust_attribute/column_info/type = 'LB'">
												<label><xsl:value-of select="cust_attribute/input_text"/></label>
												<input class="operator" type='hidden' name="operator" value='eq' />
												<select class="attrib_info" name="{cust_attribute/column_name}">
													<xsl:for-each select="cust_attribute/choice">
														<xsl:choose>
															<xsl:when test="value = //attrib_value">
																<option selected='true' value="{id}">
																	<xsl:value-of select="value"/>
																</option>
															</xsl:when>
															<xsl:otherwise>
																<option value="{id}">
																	<xsl:value-of select="value"/>
																</option>
															</xsl:otherwise>	
														</xsl:choose>
													</xsl:for-each>
												</select>
										</xsl:when>
									</xsl:choose>
									<input type="hidden" class="cust_attribute_id" name="cust_attribute_id" value="{cust_attribute/id}" />
									<input type="hidden" class="cust_attributes" name="cust_attributes[]" value="" />
								</div>
							</xsl:for-each>
						</div>
					</xsl:when>
					<xsl:otherwise>
							<xsl:choose>
								<xsl:when test="requirement_attributes_array/child::node()">
									<div id="attributes">
										<xsl:for-each select="requirement_attributes_array">
										<div class="attribute">
											<label><xsl:value-of select="cust_attribute/input_text" /></label>
											<xsl:choose>
												<xsl:when test="cust_attribute/column_info/type = 'T'">
													<span style="margin-left:10px;"><xsl:value-of select="attrib_value" /></span>
												</xsl:when>
												<xsl:when test="cust_attribute/column_info/type = 'V' or cust_attribute/column_info/type = 'I'">
							 
														<xsl:if test="operator = 'btw'">
															<span style="margin-left:10px;"><xsl:value-of select="substring-before(attrib_value, ':')" /></span>
														</xsl:if>
												
														<xsl:choose>
															<xsl:when test="operator = 'eq'">
																<span style="margin-left:10px;">Lik</span>
															</xsl:when>
															<xsl:when test="operator = 'gt'">
																<span style="margin-left:10px;">Større enn</span>
															</xsl:when>
															<xsl:when test="operator = 'lt'">
																<span style="margin-left:10px;">Mindre enn</span>
															</xsl:when>
															<xsl:when test="operator = 'btw'">
																<span style="margin-left:10px;">Mellom</span>
															</xsl:when>
														</xsl:choose>
														
													<xsl:choose>
														<xsl:when test="operator = 'btw'">
														<span style="margin-left:10px;"><xsl:value-of select="substring-after(attrib_value, ':')" /></span>
														</xsl:when>
														<xsl:otherwise>
																<span style="margin-left:10px;"><xsl:value-of select="attrib_value" /></span>
														</xsl:otherwise>
													</xsl:choose>
							
												</xsl:when>
												<xsl:when test="cust_attribute/column_info/type = 'LB'">
													<xsl:for-each select="cust_attribute/choice">
															<xsl:if test="id = //attrib_value">
																<span>
																	<xsl:value-of select="value"/>
																</span>
															</xsl:if>
													</xsl:for-each>
												</xsl:when>
											</xsl:choose>
										</div>
									</xsl:for-each>
								</div>
								</xsl:when>
								<xsl:otherwise>
									<p>Ingen kriterier lagt til</p>
								</xsl:otherwise>
							</xsl:choose>
					</xsl:otherwise>
				</xsl:choose>
				</dd>	
			</dl>
			
			<div class="form-buttons">
			
				<xsl:variable name="view_resources_params">
					<xsl:text>menuaction:logistic.uiactivity.view_resource_allocation, activity_id:</xsl:text>
				  <xsl:value-of select="activity/id" />
				</xsl:variable>
				<xsl:variable name="view_resources_url">
				  <xsl:value-of select="php:function('get_phpgw_link', '/index.php', $view_resources_params )" />
				</xsl:variable>
				<xsl:choose>
					<xsl:when test="editable">
						<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
						<xsl:variable name="lang_cancel"><xsl:value-of select="php:function('lang', 'cancel')" /></xsl:variable>
						<input type="submit" name="save_requirement_values" value="{$lang_save}" title = "{$lang_save}" />
						<input type="submit" name="cancel_requirement_values" value="{$lang_cancel}" title = "{$lang_cancel}" />
						
						<!--<a style="margin-left: 20px;" id="view-resources-btn" class="btn non-focus" href="{$view_resources_url}">
						  <xsl:value-of select="php:function('lang', 'View resources overview')" />
						</a>-->
					</xsl:when>
					<xsl:otherwise>
						<xsl:variable name="params">
								<xsl:text>menuaction:logistic.uirequirement.add_requirement_values, requirement_id:</xsl:text>
								<xsl:value-of select="requirement/id" />
							</xsl:variable>
							<xsl:variable name="edit_url">
								<xsl:value-of select="php:function('get_phpgw_link', '/index.php', $params )" />
							</xsl:variable>
							<a class="btn" href="{$edit_url}"><xsl:value-of select="php:function('lang', 'edit')" /></a>
							
						<!--<a style="margin-left: 20px;" id="view-resources-btn" class="btn non-focus" href="{$view_resources_url}">
						  <xsl:value-of select="php:function('lang', 'View resources overview')" />
						</a>-->
					</xsl:otherwise>
				</xsl:choose>
			</div>
		</form>
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
