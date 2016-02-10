
<!-- $Id: dashboard.xsl 12604 2015-11-23 17:06:11Z nelson224 $ -->
<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="edit">
			<xsl:apply-templates select="edit"/>
		</xsl:when>
	</xsl:choose>
	<xsl:call-template name="jquery_phpgw_i18n"/>
</xsl:template>


<xsl:template xmlns:php="http://php.net/xsl" match="edit">	
	<div>
		<form id="form" name="form" method="post" action="" class="pure-form pure-form-aligned">
			<div id="tab-content">
				<h3>
					<xsl:value-of select="php:function('lang', 'organization')"/>
				</h3>
				<div>					
					<div>
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
				<h3>
					<xsl:value-of select="php:function('lang', 'activities')"/>
				</h3>
				<div>
					<div class="pure-custom">
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'activity_state')"/>
							</label>
							<select id="activity_state" name="activity_state">
								<xsl:apply-templates select="list_activity_state_options/options"/>
							</select>
							<label>
								<xsl:value-of select="php:function('lang', 'office')"/>
							</label>
							<select id="activity_district" name="activity_district">
								<xsl:apply-templates select="list_activity_district_options/options"/>
							</select>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'Category')"/>
							</label>
							<select id="activity_category" name="activity_category">
								<xsl:apply-templates select="list_activity_category_options/options"/>
							</select>													
							<label>
								<xsl:value-of select="php:function('lang', 'date')"/>
							</label>
							<input type="text" id="date_change" name="date_change" value=""></input>
						</div>														
					</div>
					<div>
						<xsl:for-each select="datatable_def">
							<xsl:if test="container = 'datatable-container_1'">
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
			</div>
		</form>
	</div>
</xsl:template>

<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected != 0">
			<xsl:attribute name="selected" value="selected"/>
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>

<xsl:template match="option_group">
	<optgroup label="{label}">
		<xsl:apply-templates select="options"/>
	</optgroup>
</xsl:template>