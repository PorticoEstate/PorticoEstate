
<!-- $Id: price_item.xsl 12604 2015-01-15 17:06:11Z nelson224 $ -->
<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="edit">
			<xsl:apply-templates select="edit" />
		</xsl:when>
		<xsl:when test="view">
			<xsl:apply-templates select="view" />
		</xsl:when>
		<xsl:when test="adjustment_price">
			<xsl:apply-templates select="adjustment_price" />
			
		</xsl:when>
	</xsl:choose>
	
</xsl:template>

<!-- add / edit  -->
<xsl:template xmlns:php="http://php.net/xsl" match="edit">
	<div>
		<xsl:variable name="form_action">
			<xsl:value-of select="form_action"/>
		</xsl:variable>

		<xsl:value-of select="validator"/>

		<form id="form" name="form" method="post" action="{$form_action}" class="pure-form pure-form-aligned">
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<div id="showing">
					<fieldset>
						<input type="hidden" name="id" value="{price_item_id}"/>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'title')"/>
							</label>
							<input type="text" name="title" id="title" value="{value_title}">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
							</input>							
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'field_of_responsibility')"/>
							</label>
							<xsl:choose>
								<xsl:when test="price_item_id = 0 or price_item_id = ''">
									<input type="hidden" name="responsibility_id" id="responsibility_id" value="{responsibility_id}"/>
								</xsl:when>
							</xsl:choose>							
							<xsl:value-of select="value_field_of_responsibility"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'agresso_id')"/>
							</label>
							<input type="text" name="agresso_id" id="agresso_id" value="{value_agresso_id}">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'is_area')"/>
							</label>
							<div class="pure-custom">
								<div>
									<input type="radio" name="is_area" value="true">
										<xsl:if test="is_area = 1">
											<xsl:attribute name="checked" value="checked"/>
										</xsl:if>
									</input> 
									<xsl:value-of select="php:function('lang', 'calculate_price_per_area')"/>
								</div>
								<div>
									<input type="radio" name="is_area" value="false">
										<xsl:if test="is_area = 0">
											<xsl:attribute name="checked" value="checked"/>
										</xsl:if>
									</input>
									<xsl:value-of select="php:function('lang', 'calculate_price_apiece')"/>
								</div>
							</div>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'type')"/>
							</label>
							<select id="price_type_id" name="price_type_id">
								<xsl:apply-templates select="list_type/options"/>
							</select>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'price')"/>
							</label>
							<input type="text" name="price" id="price" value="{value_price}"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'is_inactive')"/>
							</label>
							<input type="checkbox" name="is_inactive" id="is_inactive">
								<xsl:if test="is_inactive = 1">
									<xsl:attribute name="checked" value="checked"/>
								</xsl:if>
								<xsl:if test="has_active_contract = 1">
									<xsl:attribute name="disabled" value="disabled"/>
								</xsl:if>
							</input>
							<xsl:if test="has_active_contract = 1">
								<xsl:value-of select="lang_price_element_in_use"/>
							</xsl:if>									
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'is_adjustable')"/>
							</label>
							<input type="checkbox" name="is_adjustable" id="is_adjustable">
								<xsl:if test="is_adjustable = 1">
									<xsl:attribute name="checked" value="checked"/>
								</xsl:if>
							</input>			
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'is_standard')"/>
							</label>
							<input type="checkbox" name="is_standard" id="is_standard">
								<xsl:if test="is_standard = 1">
									<xsl:attribute name="checked" value="checked"/>
								</xsl:if>
							</input>			
						</div>
					</fieldset>
				</div>
			</div>
			<div class="proplist-col">
				<input type="submit" class="pure-button pure-button-primary" name="save" value="{lang_save}" onMouseout="window.status='';return true;"/>
				<xsl:variable name="cancel_url">
					<xsl:value-of select="cancel_url"/>
				</xsl:variable>				
				<input type="button" class="pure-button pure-button-primary" name="cancel" value="{lang_cancel}" onMouseout="window.status='';return true;" onClick="window.location = '{cancel_url}';"/>
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


<xsl:template xmlns:php="http://php.net/xsl" match="view">
	<div>
		<form id="form" name="form" method="post" action="" class="pure-form pure-form-aligned">
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<div id="showing">
					<fieldset>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'title')"/>
							</label>
							<xsl:value-of select="value_title"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'field_of_responsibility')"/>
							</label>						
							<xsl:value-of select="value_field_of_responsibility"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'agresso_id')"/>
							</label>
							<xsl:value-of select="value_agresso_id"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'is_area')"/>
							</label>
							<div class="pure-custom">
								<div>
									<input type="radio" name="is_area" value="true" disabled="disabled">
										<xsl:if test="is_area = 1">
											<xsl:attribute name="checked" value="checked"/>
										</xsl:if>
									</input> 
									<xsl:value-of select="php:function('lang', 'calculate_price_per_area')"/>
								</div>
								<div>
									<input type="radio" name="is_area" value="false" disabled="disabled">
										<xsl:if test="is_area = 0">
											<xsl:attribute name="checked" value="checked"/>
										</xsl:if>
									</input> 
									<xsl:value-of select="php:function('lang', 'calculate_price_apiece')"/>
								</div>
							</div>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'type')"/>
							</label>
							<xsl:value-of select="lang_current_price_type"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'price')"/>
							</label>
							<xsl:value-of select="value_price_formatted"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'is_inactive')"/>
							</label>
							<input type="checkbox" name="is_inactive" id="is_inactive" disabled="disabled">
								<xsl:if test="is_inactive = 1">
									<xsl:attribute name="checked" value="checked"/>
								</xsl:if>
							</input>
							<xsl:if test="has_active_contract = 1">
								<xsl:value-of select="lang_price_element_in_use"/>
							</xsl:if>									
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'is_adjustable')"/>
							</label>
							<xsl:value-of select="lang_adjustable_text"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'is_standard')"/>
							</label>
							<xsl:value-of select="lang_standard_text"/>
						</div>
					</fieldset>
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


<xsl:template name="top-toolbar">
	<div class="toolbar-container">
		<div class="pure-g">
			<div class="pure-u-1">
				<div> 
					<xsl:value-of select="php:function('lang', 'manual_adjust_price_item_select')"/>
					<select id="price_item_id" name="price_item_id">
						<xsl:apply-templates select="list_type/options"/>
					</select>
					<xsl:value-of select="php:function('lang', 'price')"/>
					<input type="text" id="ctrl_adjust_price_item_price" name="ctrl_adjust_price_item_price"/>
					<xsl:variable name="lang_adjust_price">
						<xsl:value-of select="php:function('lang', 'adjust_price')"/>
					</xsl:variable>			
					<input type="button" class="pure-button pure-button-primary" name="adjust_price" value="{$lang_adjust_price}"  onClick="onAdjust_price()"/>
				</div>
			</div>
		</div>
	</div>
</xsl:template>

<xsl:template xmlns:php="http://php.net/xsl" match="adjustment_price">
	<xsl:call-template name="jquery_phpgw_i18n"/>
	<h3>
		<xsl:value-of select="php:function('lang', 'manual_adjust_price_item')"/>
	</h3>
	<div>
		<xsl:call-template name="top-toolbar" />
		<br/>
		<div id="showing">
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
</xsl:template>