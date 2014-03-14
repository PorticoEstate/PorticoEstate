<xsl:template name="requirement_details" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format"><xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')"/></xsl:variable>
<xsl:variable name="datetime_format"><xsl:value-of select="$date_format"/><xsl:text> H:i</xsl:text></xsl:variable>
<xsl:variable name="nonavbar"><xsl:value-of select="nonavbar"/></xsl:variable>

<xsl:variable name="action_params">
	<xsl:text>menuaction:logistic.uirequirement.save, nonavbar:</xsl:text>
		<xsl:value-of select="nonavbar" />
</xsl:variable>
<xsl:variable name="action_url">
	<xsl:value-of select="php:function('get_phpgw_link', '/index.php', $action_params )" />
</xsl:variable>


<div class="content-wrp">
	<div id="details">
		<form action="{$action_url}" method="post">
			<input type="hidden" name="id" value = "{requirement/id}" />
			<input type="hidden" id="activity_id" name="activity_id" value="{activity/id}" />
						
			<dl class="proplist-col">
				<dt>
					<label for="start_date">Startdato</label>
					<xsl:value-of select="$nonavbar"/>
				</dt>
				<dd>
					<xsl:choose>
						<xsl:when test="editable">
							<xsl:if test="requirement/error_msg_array/start_date != ''">
								<xsl:variable name="error_msg"><xsl:value-of select="requirement/error_msg_array/start_date" /></xsl:variable>
								<div class='input_error_msg'><xsl:value-of select="php:function('lang', $error_msg)" /></div>
							</xsl:if>
							<input class="datetime" id="start_date" name="start_date" type="text">
						    <xsl:if test="requirement/start_date != ''">
						     	<xsl:attribute name="value"><xsl:value-of select="php:function('date', $datetime_format, number(requirement/start_date))"/></xsl:attribute>
						    </xsl:if>
				    	</input>
				    	<span class="help_text line">Angi startdato for aktiviteten</span>
						</xsl:when>
						<xsl:otherwise>
						<span><xsl:value-of select="php:function('date', $datetime_format, number(requirement/start_date))"/></span>
						</xsl:otherwise>
					</xsl:choose>
				</dd>
				<dt>
					<label for="end_date">Sluttdato</label>
				</dt>
				<dd>
					<xsl:choose>
						<xsl:when test="editable">
							<xsl:if test="requirement/error_msg_array/end_date != ''">
								<xsl:variable name="error_msg"><xsl:value-of select="requirement/error_msg_array/end_date" /></xsl:variable>
								<div class='input_error_msg'><xsl:value-of select="php:function('lang', $error_msg)" /></div>
							</xsl:if>
							<input class="datetime" id="end_date" name="end_date" type="text">
					    	<xsl:if test="requirement/end_date != ''">
					      	<xsl:attribute name="value"><xsl:value-of select="php:function('date', $datetime_format, number(requirement/end_date))"/></xsl:attribute>
					    	</xsl:if>
				    	</input>
				    	<span class="help_text line">Angi startdato for aktiviteten</span>
						</xsl:when>
						<xsl:otherwise>
						<span><xsl:value-of select="php:function('date', $datetime_format, number(requirement/end_date))"/></span>
						</xsl:otherwise>
					</xsl:choose>
				</dd>
			
				<dt>
					<label for="no_of_items">Antall</label>
				</dt>
				<dd>
					<xsl:choose>
						<xsl:when test="editable">
							<xsl:if test="requirement/error_msg_array/no_of_items != ''">
								<xsl:variable name="error_msg"><xsl:value-of select="requirement/error_msg_array/no_of_items" /></xsl:variable>
								<div class='input_error_msg'><xsl:value-of select="php:function('lang', $error_msg)" /></div>
							</xsl:if>
							<input style="width: 20px;" id="no_of_items" name="no_of_items" type="text" value="{requirement/no_of_items}" />
							<span class="help_text line">Angi startdato for aktiviteten</span>
						</xsl:when>
						<xsl:otherwise>
						<span><xsl:value-of select="requirement/no_of_items"/></span>
						</xsl:otherwise>
					</xsl:choose>
				</dd>
			
				<xsl:choose>
					<xsl:when test="editable">
					<dt>
						<label>Velg hvilken kategori behovet gjelder</label>
					</dt>
						<dd>
							<xsl:if test="requirement/error_msg_array/location_id != ''">
								<xsl:variable name="error_msg"><xsl:value-of select="requirement/error_msg_array/location_id" /></xsl:variable>
								<div class='input_error_msg'><xsl:value-of select="php:function('lang', $error_msg)" /></div>
							</xsl:if>
							<select name="location_id" id="location_id">
								<option value="">Velg kategori</option>
								<xsl:for-each select="distict_locations">
									<xsl:choose>
										<xsl:when test="//requirement/location_id = location_id">
											<option selected="selected" value="{location_id}">
												<xsl:value-of select="descr"/>
											</option>
										</xsl:when>
										<xsl:otherwise>
											<option value="{location_id}">
												<xsl:value-of select="descr"/>
											</option>
										</xsl:otherwise>
										</xsl:choose>
								</xsl:for-each>
							</select>
							<span class="help_text line">Angi startdato for aktiviteten</span>
						</dd>					
					</xsl:when>
					<xsl:otherwise>
						<dt>
							<label>Kategori</label>
						</dt>
						<dd>
							<xsl:value-of select="location/descr" />
						</dd>		
					</xsl:otherwise>
				</xsl:choose>
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
						<input type="submit" name="save_requirement" value="{$lang_save}" title = "{$lang_save}" />
						<input type="submit" name="cancel_requirement" value="{$lang_cancel}" title = "{$lang_cancel}" />				
								
					<!--	<a style="margin-left: 20px;" id="view-resources-btn" class="btn non-focus" href="{$view_resources_url}">
						  <xsl:value-of select="php:function('lang', 'View resources overview')" />
						</a>-->
						
					</xsl:when>
					<xsl:otherwise>
						<xsl:variable name="edit_params">
						<xsl:text>menuaction:logistic.uirequirement.edit, id:</xsl:text>
							<xsl:value-of select="requirement/id" />
						</xsl:variable>
						<xsl:variable name="edit_url">
							<xsl:value-of select="php:function('get_phpgw_link', '/index.php', $edit_params )" />
						</xsl:variable>
						
						<a class="btn" href="{$edit_url}">
						  <xsl:value-of select="php:function('lang', 'edit')" />
						</a>

						<!--<a style="margin-left: 20px;" id="view-resources-btn" class="btn non-focus" href="{$view_resources_url}">
						  <xsl:value-of select="php:function('lang', 'View resources overview')" />
						</a>-->
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
