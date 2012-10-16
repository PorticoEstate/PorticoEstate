<!-- $Id$ -->
<!-- item  -->

<xsl:template match="data" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format"><xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')"/></xsl:variable>

<xsl:call-template name="yui_phpgw_i18n"/>
<div class="yui-navset yui-navset-top">
	<div style="clear: both;margin-bottom: 0;overflow: hidden;padding: 1em;" class="identifier-header">
		
		<xsl:choose>
			<xsl:when test="parent_activity/id &gt; 0">
				<h1 style="float:left;"> 
					<xsl:value-of select="parent_activity/name" disable-output-escaping="yes"/>::<xsl:value-of select="php:function('lang', 'Add sub activity')" />
				</h1>
				
				<xsl:if test="editable">
				<div style="float: left; margin-left: 130px;" class="select-box">
					<label>Velg en annen hovedaktivitet</label>
					
					<form action="#">
						<input type="hidden" name="activity_id" value="{activity/id}" />
						<input type="hidden" name="parent_id" value="{parent_activity/id}" />
												
						<select id="select_activity" name="parent_activity_id" class="selectLocation">
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
					</form>
				</div>
				</xsl:if>
						
			</xsl:when>
			<xsl:otherwise>
				<h1 style="float:left;"> 
					<xsl:value-of select="php:function('lang', 'Add activity')" />
				</h1>
			</xsl:otherwise>
		</xsl:choose>
	</div>

	<div class="yui-content" style="padding: 20px;">
		<div id="details">
			<form action="#" method="post">
				<input type="hidden" name="id" value = "{activity/id}" />
				<input type="hidden" name="project_id" value="{activity/project_id}" />
				<input type="hidden" name="parent_id" value="{parent_activity/id}" />
				
				<dl class="proplist-col">
					<dt>
						<label for="name"><xsl:value-of select="php:function('lang','Activity name')" /></label>
					</dt>
					<dd>
					<xsl:choose>
						<xsl:when test="editable">
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
								<select name="responsible_user_id">
					        <xsl:for-each select="responsible_users">
					        	<xsl:variable name="full_name">
					        		<xsl:value-of disable-output-escaping="yes" select="account_firstname"/><xsl:text> </xsl:text>
					        		<xsl:value-of disable-output-escaping="yes" select="account_lastname"/>
					        	</xsl:variable>
					        	<option value="{account_id}">
					        		<xsl:if test="activity/responsible_user_id = account_id">
						        		<xsl:attribute name="selected">
	        								selected
        								</xsl:attribute>
						        	</xsl:if>
					          	<xsl:value-of disable-output-escaping="yes" select="$full_name"/>
					          </option>
					        </xsl:for-each>
					      </select>
					      </xsl:when>
							<xsl:otherwise>
							<span><xsl:value-of select="activity/responsible_user_id"/></span>
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
							<xsl:variable name="lang_edit"><xsl:value-of select="php:function('lang', 'edit')" /></xsl:variable>
							<input type="submit" name="edit_activity" value="{$lang_edit}" title = "{$lang_edit}" />
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
