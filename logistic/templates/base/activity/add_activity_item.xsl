<xsl:template match="data" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format"><xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')"/></xsl:variable>

<xsl:call-template name="yui_phpgw_i18n"/>
<div class="yui-navset yui-navset-top">

	<xsl:choose>
		<xsl:when test="project/id != '' or project/id != 0">
			<h1>
				<xsl:value-of select="php:function('lang', 'Add activity to project')" />
				<span style="margin-left:5px;"><xsl:value-of select="project/name" /></span>
			</h1>
		</xsl:when>
		<xsl:when test="activity/id != '' and activity/id != 0">
			<h1>
				<xsl:value-of select="php:function('lang', 'Edit activity')" />
			</h1>
		</xsl:when>
		<xsl:otherwise>
			<h1>
				<xsl:value-of select="php:function('lang', 'Add activity')" />
			</h1>
		</xsl:otherwise>
	</xsl:choose>
	
	<div id="activity_details" class="content-wrp">
		<div id="details">
			<xsl:variable name="action_url">
				<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:logistic.uiactivity.save')" />
			</xsl:variable>
			<form action="{$action_url}" method="post">
				<input type="hidden" name="id" value = "{activity/id}" />
				<input type="hidden" name="project_id" value="{activity/project_id}" />
				<input type="hidden" name="parent_id" value="{parent_activity/id}" />
				
				<dl class="proplist-col">
				  <xsl:choose>
						<xsl:when test="(editable) and (parent_activity/id &gt; 0)">
							<dt>		
									<div style="margin-bottom: 1em;">
										<label style="display:block;">Velg en annen hovedaktivitet</label>
										<select id="select_parent_activity" name="parent_activity_id">
											<option>Velg aktivitet</option>
											<xsl:for-each select="activities">
							        	<option value="{id}">
							        		<xsl:if test="activity/parent_id = id">
								        		<xsl:attribute name="selected">
							    						selected
							   						</xsl:attribute>
								        	</xsl:if>
							          	<xsl:value-of disable-output-escaping="yes" select="name"/>
								        </option>
										  </xsl:for-each>
										</select>					
									</div>
								</dt>
						  </xsl:when>
						  <xsl:otherwise>
							<dt>		
									<div style="margin-bottom: 1em;">
										<label style="display:block;">Velg et annet prosjekt for aktiviteten </label>
										<select id="select_project" name="select_project">
											<option>Velg prosjekt</option>
											<xsl:for-each select="projects">
							        	<option value="{id}">
							        		<xsl:if test="project/id = project_id">
								        		<xsl:attribute name="selected">
							    						selected
							   						</xsl:attribute>
								        	</xsl:if>
							          	<xsl:value-of disable-output-escaping="yes" select="name"/>
								        </option>
										  </xsl:for-each>
										</select>					
									</div>
								</dt>
						  </xsl:otherwise>
					  </xsl:choose>
					<dt>
						<label for="name"><xsl:value-of select="php:function('lang','Activity name')" /></label>
					</dt>
					<dd>
					<xsl:choose>
						<xsl:when test="editable">
							<xsl:if test="activity/error_msg_array/name != ''">
								<xsl:variable name="error_msg"><xsl:value-of select="activity/error_msg_array/name" /></xsl:variable>
								<div class='input_error_msg'><xsl:value-of select="php:function('lang', $error_msg)" /></div>
							</xsl:if>
							<div class="help_text">Angi navn for aktiviteten</div>
							<input type="text" name="name" id="name" value="{activity/name}" size="100"/>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="activity/name" />
						</xsl:otherwise>
					</xsl:choose>
					</dd>
					<dt>
						<label for="description"><xsl:value-of select="php:function('lang', 'Description')" /></label>
					</dt>
					<dd>
					<xsl:choose>
						<xsl:when test="editable">
							<xsl:if test="activity/error_msg_array/description != ''">
								<xsl:variable name="error_msg"><xsl:value-of select="activity/error_msg_array/description" /></xsl:variable>
								<div class='input_error_msg'><xsl:value-of select="php:function('lang', $error_msg)" /></div>
							</xsl:if>
							<div class="help_text">Gi en beskrivelse av aktiviteten</div>
							<textarea id="description" name="description" rows="5" cols="60"><xsl:value-of select="activity/description" disable-output-escaping="yes"/></textarea>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="activity/description" disable-output-escaping="yes"/>
						</xsl:otherwise>
					</xsl:choose>
					</dd>
					<dt>
						<label for="start_date">Startdato</label>
					</dt>
					<dd>
						<xsl:choose>
							<xsl:when test="editable">
								<xsl:if test="activity/error_msg_array/start_date != ''">
									<xsl:variable name="error_msg"><xsl:value-of select="activity/error_msg_array/start_date" /></xsl:variable>
									<div class='input_error_msg'><xsl:value-of select="php:function('lang', $error_msg)" /></div>
								</xsl:if>
								<div class="help_text">Angi startdato for aktiviteten</div>
								<input class="date" id="start_date" name="start_date" type="text">
						    	<xsl:if test="activity/start_date != ''">
						      	<xsl:attribute name="value"><xsl:value-of select="php:function('date', $date_format, number(activity/start_date))"/></xsl:attribute>
						    	</xsl:if>
					    	</input>
							</xsl:when>
							<xsl:otherwise>
							<span><xsl:value-of select="php:function('date', $date_format, number(activity/start_date))"/></span>
							</xsl:otherwise>
						</xsl:choose>
					</dd>
					<dt>
						<label for="end_date">Sluttdato</label>
					</dt>
					<dd>
						<xsl:choose>
							<xsl:when test="editable">
								<xsl:if test="activity/error_msg_array/end_date != ''">
									<xsl:variable name="error_msg"><xsl:value-of select="activity/error_msg_array/end_date" /></xsl:variable>
									<div class='input_error_msg'><xsl:value-of select="php:function('lang', $error_msg)" /></div>
								</xsl:if>
								<div class="help_text">Angi sluttdato for aktiviteten</div>
								<input class="date" id="end_date" name="end_date" type="text">
						    	<xsl:if test="activity/end_date != ''">
						      	<xsl:attribute name="value"><xsl:value-of select="php:function('date', $date_format, number(activity/end_date))"/></xsl:attribute>
						    	</xsl:if>
					    	</input>
							</xsl:when>
							<xsl:otherwise>
							<span><xsl:value-of select="php:function('date', $date_format, number(activity/end_date))"/></span>
							</xsl:otherwise>
						</xsl:choose>
					</dd>
					<dt>
						<label for="end_date">Ansvarlig</label>
					</dt>
					<dd>
						<xsl:choose>
							<xsl:when test="editable">
								<xsl:if test="activity/error_msg_array/responsible_user_id != ''">
									<xsl:variable name="error_msg"><xsl:value-of select="activity/error_msg_array/responsible_user_id" /></xsl:variable>
									<div class='input_error_msg'><xsl:value-of select="php:function('lang', $error_msg)" /></div>
								</xsl:if>
								<div class="help_text">Angi hvilken person som skal være ansvarlig for aktiviteten</div>
								<select name="responsible_user_id">
									<option value="">Velg ansvarlig bruker</option>
					        <xsl:for-each select="responsible_users">
					        	<xsl:variable name="full_name">
					        		<xsl:value-of disable-output-escaping="yes" select="account_firstname"/><xsl:text> </xsl:text>
					        		<xsl:value-of disable-output-escaping="yes" select="account_lastname"/>
					        	</xsl:variable>
					        	<xsl:choose>
					        		<xsl:when test="//activity/responsible_user_id = account_id">
												<option selected="selected" value="{account_id}">
					        				<xsl:value-of disable-output-escaping="yes" select="$full_name"/>
						        		</option>
					        		</xsl:when>
					        		<xsl:otherwise>
					        			<option value="{account_id}">
					        				<xsl:value-of disable-output-escaping="yes" select="$full_name"/>
						        		</option>
					        		</xsl:otherwise>
					        	</xsl:choose>
					        </xsl:for-each>
					      </select>
					      </xsl:when>
							<xsl:otherwise>
							<span><xsl:value-of select="activity/responsible_user_name"/></span>
							</xsl:otherwise>
						</xsl:choose>
					</dd>
				</dl>
				
				<div class="form-buttons">
					<xsl:choose>
						<xsl:when test="editable">
							<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
							<xsl:variable name="lang_cancel"><xsl:value-of select="php:function('lang', 'cancel')" /></xsl:variable>
							<input type="submit" name="save_activity" value="{$lang_save}" title = "{$lang_save}" />
							<input type="submit" name="cancel_activity" value="{$lang_cancel}" title = "{$lang_cancel}" />
						</xsl:when>
						<xsl:otherwise>
							<xsl:variable name="params">
								<xsl:text>menuaction:logistic.uiactivity.edit, id:</xsl:text>
								<xsl:value-of select="activity/id" />
							</xsl:variable>
							<xsl:variable name="edit_url">
								<xsl:value-of select="php:function('get_phpgw_link', '/index.php', $params )" />
							</xsl:variable>
							<a class="btn" href="{$edit_url}"><xsl:value-of select="php:function('lang', 'edit')" /></a>
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
