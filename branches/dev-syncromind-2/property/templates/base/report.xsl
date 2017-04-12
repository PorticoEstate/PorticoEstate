
<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="edit">
			<xsl:apply-templates select="edit"/>
		</xsl:when>
		<xsl:when test="edit_dataset">
			<xsl:apply-templates select="edit_dataset"/>
		</xsl:when>
		<xsl:otherwise>
			<xsl:apply-templates select="lists"/>
		</xsl:otherwise>
	</xsl:choose>
	<xsl:call-template name="jquery_phpgw_i18n"/>
</xsl:template>
	
<xsl:template match="lists">
	<div id="document_edit_tabview">
		<xsl:value-of select="validator"/>
		<div id="tab-content">					
			<xsl:value-of disable-output-escaping="yes" select="tabs"/>
			<div id="reports">
				<form name="form" class="pure-form pure-form-aligned" id="form" action="" method="post">								
					<div class="pure-control-group">
						<label for="vendor">
							<xsl:value-of select="php:function('lang', 'datasets')" />
						</label>
						<select id="list_dataset" name="list_dataset">
							<xsl:apply-templates select="list_views/options"/>
						</select>
					</div>								

					<xsl:for-each select="datatable_def">
						<xsl:if test="container = 'datatable-container_0'">
							<xsl:call-template name="table_setup">
								<xsl:with-param name="container" select ='container'/>
								<xsl:with-param name="requestUrl" select ='requestUrl' />
								<xsl:with-param name="ColumnDefs" select ='ColumnDefs' />
								<xsl:with-param name="tabletools" select ='tabletools' />
								<xsl:with-param name="config" select ='config' />
							</xsl:call-template>
						</xsl:if>
					</xsl:for-each>	
				</form>
			</div>
							
			<div id="views">
				<form name="form" class="pure-form pure-form-aligned" id="form" action="" method="post">								
					<xsl:for-each select="datatable_def">
						<xsl:if test="container = 'datatable-container_1'">
							<xsl:call-template name="table_setup">
								<xsl:with-param name="container" select ='container'/>
								<xsl:with-param name="requestUrl" select ='requestUrl' />
								<xsl:with-param name="ColumnDefs" select ='ColumnDefs' />
								<xsl:with-param name="tabletools" select ='tabletools' />
								<xsl:with-param name="config" select ='config' />
							</xsl:call-template>
						</xsl:if>
					</xsl:for-each>	
				</form>
			</div>
		</div>
	</div>
</xsl:template>


<xsl:template match="edit">
	<xsl:choose>
		<xsl:when test="msgbox_data != ''">
		<dl>
			<dt>
				<xsl:call-template name="msgbox"/>
			</dt>
		</dl>
		</xsl:when>
	</xsl:choose>
	<script type="text/javascript">
		var jsonB = <xsl:value-of select="report_definition"/>;
	</script>
	<div id="document_edit_tabview">
		<xsl:value-of select="validator"/>
		
		<xsl:variable name="form_action">
			<xsl:value-of select="form_action"/>
		</xsl:variable>		
		<form name="form" class="pure-form pure-form-aligned" id="form" action="{$form_action}" method="post">	
			<div id="tab-content">					
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>						
				<div id="report">
					<input type="hidden" name="report_id" value="{report_id}"/>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'report name')" />
						</label>
						<input type="text" name="report_name" value="{report_name}"></input>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'dataset')" />
						</label>
						<select id="cbo_dataset_id" name="dataset_id">
							<xsl:apply-templates select="datasets/options"/>
						</select>
						<input type="button" class="pure-button pure-button-primary" name="btn_get_columns" id="btn_get_columns">
							<xsl:attribute name="value">
								<xsl:value-of select="php:function('lang', 'get columns')" />
							</xsl:attribute>
						</input>										
					</div>	
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Choose columns')" />
						</label>
						<div id="container_columns" class="pure-custom"></div>				
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Group by')" />
						</label>
						<div id="container_groups" class="pure-custom"></div>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Sort by')" />
						</label>
						<div id="container_order" class="pure-custom"></div>
					</div>	
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Count / Sum')" />
						</label>
						<div id="container_aggregates" class="pure-custom"></div>
					</div>											
				</div>
			</div>
			<div class="proplist-col">
				<input type="submit" class="pure-button pure-button-primary" name="save" id="btn_save">
					<xsl:attribute name="value">
						<xsl:value-of select="php:function('lang', 'save')" />
					</xsl:attribute>						
					<xsl:attribute name="title">
						<xsl:value-of select="lang_save_statustext"/>
					</xsl:attribute>
				</input>
				<xsl:variable name="cancel_action">
					<xsl:value-of select="cancel_action"/>
				</xsl:variable>
				<input type="button" class="pure-button pure-button-primary" name="cancel" onclick="location.href='{$cancel_action}'">
					<xsl:attribute name="value">
						<xsl:value-of select="php:function('lang', 'cancel')" />
					</xsl:attribute>
				</input>
			</div>	
		</form>
	</div>
</xsl:template>

<xsl:template match="edit_dataset">

	<xsl:choose>
		<xsl:when test="msgbox_data != ''">
		<dl>
			<dt>
				<xsl:call-template name="msgbox"/>
			</dt>
		</dl>
		</xsl:when>
	</xsl:choose>
	
	<div id="document_edit_tabview">
		<xsl:value-of select="validator"/>		
		<xsl:variable name="form_action">
			<xsl:value-of select="form_action"/>
		</xsl:variable>		
		<form name="form" class="pure-form pure-form-aligned" id="form" action="{$form_action}" method="post">	
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>						
				<div id="report">
					<input type="hidden" name="dataset_id" value="{dataset_id}"/>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'view')" />
						</label>
						<select name="values[view_name]">
							<xsl:apply-templates select="views/options"/>
						</select>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'dataset name')" />
						</label>
						<input type="text" name="values[dataset_name]" value="{dataset_name}"></input>
					</div>				
				</div>
			</div>
			<div class="proplist-col">
				<input type="submit" class="pure-button pure-button-primary" name="save" id="btn_save">
					<xsl:attribute name="value">
						<xsl:value-of select="php:function('lang', 'save')" />
					</xsl:attribute>						
					<xsl:attribute name="title">
						<xsl:value-of select="lang_save_statustext"/>
					</xsl:attribute>
				</input>
				<xsl:variable name="cancel_action">
					<xsl:value-of select="cancel_action"/>
				</xsl:variable>
				<input type="button" class="pure-button pure-button-primary" name="cancel" onclick="location.href='{$cancel_action}'">
					<xsl:attribute name="value">
						<xsl:value-of select="php:function('lang', 'cancel')" />
					</xsl:attribute>
				</input>
			</div>
		</form>
	</div>
</xsl:template>

<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected = 'selected' or selected = 1">
			<xsl:attribute name="selected" value="selected" />
		</xsl:if>
		<xsl:attribute name="title" value="description" />
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>
