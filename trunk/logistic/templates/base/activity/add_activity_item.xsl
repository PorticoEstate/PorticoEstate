<xsl:template match="data" xmlns:php="http://php.net/xsl" xmlns:formvalidator="http://www.w3.org/TR/html4/">
<xsl:variable name="date_format"><xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')"/></xsl:variable>
<xsl:variable name="datetime_format"><xsl:value-of select="$date_format"/><xsl:text> H:i</xsl:text></xsl:variable>

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

	 
	<xsl:choose>
		<xsl:when test="breadcrumb != ''">
	 		<xsl:call-template name="breadcrumb" />
		</xsl:when>
	</xsl:choose>
	
	<div id="activity_details" class="content-wrp">
		<div id="details">
			<xsl:variable name="action_url">
				<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:logistic.uiactivity.save')" />
			</xsl:variable>
			<xsl:variable name="parent_id"><xsl:value-of select="parent_activity/id"/></xsl:variable>
			<form id='activity_form' action="{$action_url}" method="post">
				<input type="hidden" name="id" value = "{activity/id}" />
				<input type="hidden" name="project_id" value="{activity/project_id}" />
				<input type="hidden" name="parent_id" value="{parent_activity/id}" />
				
				<dl class="proplist-col">

				  <xsl:choose>
						<xsl:when test="(editable) and (activities !='')">
							<dt>		
									<div style="margin-bottom: 1em;">
										<label style="display:block;"><xsl:value-of select="php:function('lang', 'Choose another main activity for this sub activity')" /></label>
										<select id="select_parent_activity" name="parent_activity_id">
											<option value="0">Velg annen hovedaktivitet</option>
											<xsl:for-each select="activities">
							        	<option value="{id}">
							        		<xsl:if test="id = $parent_id">
								        		<xsl:attribute name="selected">
									        		<xsl:text>selected</xsl:text>
						   						</xsl:attribute>
								        	</xsl:if>
							          	<xsl:value-of disable-output-escaping="yes" select="name"/>
								        </option>
										  </xsl:for-each>
										</select>					
									</div>
								</dt>
						  </xsl:when>
						 <!-- <xsl:when test="(editable) and not(parent_activity) and not(project)">
							<dt>		
									<div style="margin-bottom: 1em;">
										<label style="display:block;"><xsl:value-of select="php:function('lang', 'Choose the project in which the activity is part of')" /></label>
										<select id="select_project" name="select_project">
											<option><xsl:value-of select="php:function('lang', 'Choose project')" /></option>
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
						  </xsl:when>-->
						</xsl:choose>
						<xsl:choose>
							<xsl:when test="projects != ''">
								<dt>		
									<div style="margin-bottom: 1em;">
										<label style="display:block;"><xsl:value-of select="php:function('lang', 'Choose another project for the activity')" /></label>
										<select id="select_project" name="select_project"
												formvalidator:FormField="yes"
	   											formvalidator:Type="SelectField">
											<option value=''><xsl:value-of select="php:function('lang', 'Choose another project')" /></option>
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
						  </xsl:when>
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
							<div class="help_text"><xsl:value-of select="php:function('lang','Give name to this activity')" /></div>
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
							<div class="help_text"><xsl:value-of select="php:function('lang','Give description to activity')" /></div>
							<textarea id="description" name="description" rows="5" cols="60"><xsl:value-of select="activity/description" disable-output-escaping="yes"/></textarea>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="activity/description" disable-output-escaping="yes"/>
						</xsl:otherwise>
					</xsl:choose>
					</dd>
					<dt>
						<label for="start_date"><xsl:value-of select="php:function('lang','Start date')" /></label>
					</dt>
					<dd>
						<xsl:choose>
							<xsl:when test="editable">
								<xsl:if test="activity/error_msg_array/start_date != ''">
									<xsl:variable name="error_msg"><xsl:value-of select="activity/error_msg_array/start_date" /></xsl:variable>
									<div class='input_error_msg'><xsl:value-of select="php:function('lang', $error_msg)" /></div>
								</xsl:if>
								<div class="help_text"><xsl:value-of select="php:function('lang','Give start date to activity')" /></div>
								<input class="datetime" id="start_date" name="start_date" type="text">
						    	<xsl:if test="activity/start_date != ''">
						      	<xsl:attribute name="value"><xsl:value-of select="php:function('date', $datetime_format, number(activity/start_date))"/></xsl:attribute>
						    	</xsl:if>
					    	</input>
							</xsl:when>
							<xsl:otherwise>
							<span><xsl:value-of select="php:function('date', $datetime_format, number(activity/start_date))"/></span>
							</xsl:otherwise>
						</xsl:choose>
					</dd>
					<dt>
						<label for="end_date"><xsl:value-of select="php:function('lang','End date')" /></label>
					</dt>
					<dd>
						<xsl:choose>
							<xsl:when test="editable">
								<xsl:if test="activity/error_msg_array/end_date != ''">
									<xsl:variable name="error_msg"><xsl:value-of select="activity/error_msg_array/end_date" /></xsl:variable>
									<div class='input_error_msg'><xsl:value-of select="php:function('lang', $error_msg)" /></div>
								</xsl:if>
								<div class="help_text"><xsl:value-of select="php:function('lang','Give end date to activity')" /></div>
								<input class="datetime" id="end_date" name="end_date" type="text">
						    	<xsl:if test="activity/end_date != ''">
						      	<xsl:attribute name="value"><xsl:value-of select="php:function('date', $datetime_format, number(activity/end_date))"/></xsl:attribute>
						    	</xsl:if>
					    	</input>
							</xsl:when>
							<xsl:otherwise>
							<span><xsl:value-of select="php:function('date', $datetime_format, number(activity/end_date))"/></span>
							</xsl:otherwise>
						</xsl:choose>
					</dd>
					<dt>
						<label for="end_date"><xsl:value-of select="php:function('lang', 'Responsible person')" /></label>
					</dt>
					<dd>
						<xsl:choose>
							<xsl:when test="editable">
								<xsl:if test="activity/error_msg_array/responsible_user_id != ''">
									<xsl:variable name="error_msg"><xsl:value-of select="activity/error_msg_array/responsible_user_id" /></xsl:variable>
									<div class='input_error_msg'><xsl:value-of select="php:function('lang', $error_msg)" /></div>
								</xsl:if>
								<div class="help_text"><xsl:value-of select="php:function('lang', 'Responsible person for activity')" /></div>
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
							<input class="submit" type="button" name="cancel_activity" id ='cancel_activity' value="{$lang_cancel}" title = "{$lang_cancel}" onClick="document.cancel_form.submit();"/>
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

<xsl:variable name="cancel_url">
<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:logistic.uiactivity.index')" />
</xsl:variable>

<form name="cancel_form" id="cancel_form" action="{$cancel_url}" method="post">
</form>


</xsl:template>

<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected">
			<xsl:attribute name="selected" value="selected" />
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>

<!-- =========== BREADCRUMB TEMPLATE  ============== -->
<xsl:template name="breadcrumb">
  <div id="breadcrumb">
		<span class="intro">Du er her:</span>
		<xsl:for-each select="breadcrumb">
			<xsl:choose>
				<xsl:when test="current = 1">
					<span class="current">
						<xsl:value-of select="name"/>
					</span>
				</xsl:when>
				<xsl:otherwise>
					<a href="{link}">
						<xsl:value-of select="name"/>
					</a>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:if test="not( position() = last() )">
      			<img src="logistic/images/arrow_right.png" />
    			</xsl:if>
      </xsl:for-each>
	</div>
</xsl:template>
