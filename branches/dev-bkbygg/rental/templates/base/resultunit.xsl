
<!-- $Id: resultunit.xsl 12604 2015-01-15 17:06:11Z nelson224 $ -->
<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="edit">
			<xsl:apply-templates select="edit"/>
		</xsl:when>
	</xsl:choose>
	<xsl:call-template name="jquery_phpgw_i18n"/>
</xsl:template>

<xsl:template name="top-toolbar">
	<div class="toolbar-container">
		<div class="pure-g">
			<div class="pure-u-1-3">
				<div>
					<xsl:value-of select="php:function('lang', 'unit_id')"/> : <xsl:value-of select="value_org_unit_id"/>
				</div>
				<div>
					<xsl:value-of select="php:function('lang', 'unit_name')"/> : <xsl:value-of select="value_org_unit_name"/>
				</div>
				<div>
					<xsl:value-of select="php:function('lang', 'unit_leader_name')"/> : <xsl:value-of select="value_leader_fullname"/>
				</div>
				<div>
					<xsl:value-of select="php:function('lang', 'unit_no_of_delegates')"/> : <xsl:value-of select="value_unit_no_of_delegates"/>
				</div>
			</div>
		</div>
	</div>
</xsl:template>

<!-- edit  -->
<xsl:template xmlns:php="http://php.net/xsl" match="edit">
	<xsl:call-template name="top-toolbar" />
	<div>
		<form id="form" name="form" method="post" action="" class="pure-form pure-form-aligned">
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<div id="delegates">
					<input type="hidden" value="{unit_id}" name="unit_id" id="unit_id" />
					<input type="hidden" value="{unit_level}" name="unit_level" id="unit_level" />
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'username')"/>
						</label>
						<input type="hidden" value="" name="account_id" id="account_id" />
						<input type="text" id="username" name="username"/> 
						<xsl:text> </xsl:text>
						<input type="button" class="pure-button pure-button-primary" onclick="searchUser()" name="search" id="search" value="search" />
						<div class="loading"></div>
						<div id='custom_message' class='custom-message'/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'firstname')"/>
						</label>
						<input type="text" id="firstname" name="firstname"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'lastname')"/>
						</label>
						<input type="text" id="lastname" name="lastname"/>
					</div>					
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'email')"/>
						</label>
						<input type="text" id="email" name="email"/>
					</div>	
					<div class="pure-control-group">
						<label></label>
						<input type="button" class="pure-button pure-button-primary" onclick="addDelegate()" name="add" id="add" value="add" />
					</div>								
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
			</div>
			<div class="proplist-col">
				<xsl:variable name="cancel_url">
					<xsl:value-of select="cancel_url"/>
				</xsl:variable>				
				<input type="button" class="pure-button pure-button-primary" name="cancel" value="{lang_cancel}" onMouseout="window.status='';return true;" onClick="window.location = '{cancel_url}';"/>
			</div>
		</form>
	</div>
</xsl:template>