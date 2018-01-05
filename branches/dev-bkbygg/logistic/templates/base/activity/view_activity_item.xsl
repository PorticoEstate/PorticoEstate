<xsl:template name="activity_details" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format"><xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')"/></xsl:variable>
<xsl:variable name="datetime_format"><xsl:value-of select="$date_format"/><xsl:text> H:i</xsl:text></xsl:variable>

	<div class="content-wrp">
		<div id="details">
		
			<h3 style="margin: 0 0 5px 0;">Detaljer</h3>
			<xsl:variable name="action_url">
				<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:logistic.uiactivity.save')" />
			</xsl:variable>
			<form action="{$action_url}" method="post">
				<input type="hidden" name="id" value = "{activity/id}" />
				<input type="hidden" name="project_id" value="{activity/project_id}" />
				<input type="hidden" name="parent_id" value="{parent_activity/id}" />
				
				<dl class="proplist-col">
					<dt>
						<label for="name"><xsl:value-of select="php:function('lang','Activity name')" /></label>
					</dt>
					<dd>
						<xsl:value-of select="activity/name" />
					</dd>
					<dt>
						<label for="description"><xsl:value-of select="php:function('lang', 'Description')" /></label>
					</dt>
					<dd>
						<xsl:value-of select="activity/description" disable-output-escaping="yes"/>
					</dd>
					<dt>
						<label for="start_date"><xsl:value-of select="php:function('lang','Start date')" /></label>
					</dt>
					<dd>
						<span><xsl:value-of select="php:function('date', $datetime_format, number(activity/start_date))"/></span>
					</dd>
					<dt>
						<label for="end_date"><xsl:value-of select="php:function('lang','End date')" /></label>
					</dt>
					<dd>
						<span><xsl:value-of select="php:function('date', $datetime_format, number(activity/end_date))"/></span>
					</dd>
					<dt>
						<label for="end_date">Ansvarlig</label>
					</dt>
					<dd>
						<span><xsl:value-of select="activity/responsible_user_name"/></span>
					</dd>
				</dl>
					<div class="form-buttons">
						<xsl:variable name="params">
							<xsl:text>menuaction:logistic.uiactivity.edit, id:</xsl:text>
							<xsl:value-of select="activity/id" />
						</xsl:variable>
						<xsl:variable name="edit_url">
							<xsl:value-of select="php:function('get_phpgw_link', '/index.php', $params )" />
						</xsl:variable>
						<a class="btn" href="{$edit_url}"><xsl:value-of select="php:function('lang', 'edit')" /></a>
				</div>
			</form>
			
			<!-- =========  SUBACTIVITIES  =========  -->
			<div style="">
				<xsl:variable name="add_sub_activity_params">
					<xsl:text>menuaction:logistic.uiactivity.edit, parent_id:</xsl:text>
					<xsl:value-of select="activity/id" />
				</xsl:variable>
				<xsl:variable name="add_sub_activity_url">
					<xsl:value-of select="php:function('get_phpgw_link', '/index.php', $add_sub_activity_params )" />
				</xsl:variable>
				
				<h3 style="clear:left; float:left;margin: 0; padding: 10px 0 5px;">Underaktiviteter</h3>
				<a id="add-sub-activity-btn" class="btn focus" href="{$add_sub_activity_url}"><xsl:value-of select="php:function('lang', 'Add sub activity')" /></a>
				
				<div style="clear:both;" id="sub-activities-container"></div>		
				<xsl:for-each select="datatable_def">
						<xsl:if test="container = 'datatable-container_0'">
							<xsl:call-template name="table_setup">
								<xsl:with-param name="container" select ='container'/>
								<xsl:with-param name="requestUrl" select ='requestUrl' />
								<xsl:with-param name="ColumnDefs" select ='ColumnDefs' />
								<xsl:with-param name="tabletools" select ='tabletools' />
								<xsl:with-param name="data" select ='data' />
								<xsl:with-param name="config" select ='config' />
							</xsl:call-template>
						</xsl:if>
				</xsl:for-each>
			</div>
		</div>
		<div id="allocation">
	</div>
			
	</div>
</xsl:template>
