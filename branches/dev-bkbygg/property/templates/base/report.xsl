
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
		var jsonB = {};
		<xsl:if test="report_definition != ''">
			jsonB = <xsl:value-of select="report_definition"/>;
		</xsl:if>
		var operators = {};
		<xsl:if test="operators != ''">
			operators = <xsl:value-of select="operators"/>;
		</xsl:if>
		
		var operators_equal = {};
		<xsl:if test="operators_equal != ''">
			operators_equal = <xsl:value-of select="operators_equal"/>;
		</xsl:if>
		var operators_like = {};
		<xsl:if test="operators_like != ''">
			operators_like = <xsl:value-of select="operators_like"/>;
		</xsl:if>
		var operators_in = {};
		<xsl:if test="operators_in != ''">
			operators_in = <xsl:value-of select="operators_in"/>;
		</xsl:if>
		var operators_null = {};
		<xsl:if test="operators_null != ''">
			operators_null = <xsl:value-of select="operators_null"/>;
		</xsl:if>
		
		var lang = {};
		<xsl:if test="lang != ''">
			lang = <xsl:value-of select="lang"/>;
		</xsl:if>
		
		var columns = {};
	</script>
	
	<style type="text/css">
		.content_columns {
			position: relative; 
			overflow: auto; 
			max-height: 50vh; 
			width: 100%;	
		}
	</style>
	<div id="document_edit_tabview">
		
		<xsl:variable name="form_action">
			<xsl:value-of select="form_action"/>
		</xsl:variable>		
		<form name="form" class="pure-form pure-form-aligned" id="form" action="{$form_action}" method="post">	
			<div id="tab-content">					
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>						
				<div id="report">
					<input type="hidden" id="report_id"  name="report_id" value="{report_id}"/>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'report name')" />
						</label>
						<input type="text" data-validation="required" name="report_name" value="{report_name}"></input>
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
						<img src="{image_loader}" class="processing" align="absmiddle"></img>									
					</div>
					
					<div id="responsiveTabsGroups">
						<ul>
							<li><a href="#tab-columns"><xsl:value-of select="php:function('lang', 'Columns')"/></a></li>
							<li><a href="#tab-group"><xsl:value-of select="php:function('lang', 'Group by')"/></a></li>
							<li><a href="#tab-sort"><xsl:value-of select="php:function('lang', 'Sort by')"/></a></li>
							<li><a href="#tab-count-sum"><xsl:value-of select="php:function('lang', 'Count / Sum')"/></a></li>
							<li><a href="#tab-criteria"><xsl:value-of select="php:function('lang', 'Criteria')"/></a></li>
							<li><a href="#tab-preview"><xsl:value-of select="php:function('lang', 'Preview')"/></a></li>
						</ul>
						<div id="tab-columns">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Choose')" />
								</label>
								<div id="container_columns" class="content_columns"></div>			
							</div>									
						</div>
						<div id="tab-group">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Choose')" />
								</label>
								<div id="container_groups" class="pure-custom"></div>
							</div>					
						</div>
						<div id="tab-sort">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Choose')" />
								</label>
								<div id="container_order" class="pure-custom"></div>
							</div>				
						</div>
						<div id="tab-count-sum">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Choose')" />
								</label>
								<div id="container_aggregates" class="pure-custom"></div>
							</div>		
						</div>
						<div id="tab-criteria">
							<div class="pure-control-group">
								<input type="button" class="pure-button pure-button-primary" name="btn_add_restricted_value" id="btn_add_restricted_value">
									<xsl:attribute name="value">
										<xsl:value-of select="php:function('lang', 'add')" />
									</xsl:attribute>
								</input>								
								<div id="container_criteria" class="pure-custom"></div>
							</div>		
						</div>
						<div id="tab-preview">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Show')" />
								</label>
								<input type="button" class="pure-button pure-button-primary" name="btn_preview" id="btn_preview">
									<xsl:attribute name="value">
										<xsl:value-of select="php:function('lang', 'preview')" />
									</xsl:attribute>
								</input>
								<img src="{image_loader}" class="processing-preview" align="absmiddle"></img>
							</div>
							<div id="container_preview" class="content_columns"></div>				
						</div>					
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
	<script>
		
		$('#responsiveTabsGroups').responsiveTabs({
			startCollapsed: 'accordion'
		});
		
		function  validate_group ()
		{
			if ($('input[name="group"]:checked').length == 0) 
			{
				return {
				  element : $('input[name="group"]'),
				  message : lang['select_group']
				}
			} 
			else {
				return {};
			}		
		}
		
		function  validate_column ()
		{
			if ($('input[name^="columns"]:checked').length == 0) 
			{
				return {
				  element : $('input[name^="columns"]'),
				  message : lang['select_one_column']
				}
			} 
			else {
				return {};
			}		
		}
		
		function  validate_aggregate ()
		{
			if ($('input[name^="aggregate"]:checked').length == 0) 
			{
				return {
				  element : $('input[name^="aggregate"]'),
				  message : lang['select_count_sum']
				}
			} 
			else {
				return {};
			}		
		}
		
		function validate_criteria_2 ()
		{
			var result = {};
			var order = "";
			var field = "";
			var operator = "";
			var text = "";
			var conector = "";

			var values = {};
			values['cbo_restricted_value'] = {};
			values['cbo_operator'] = {};
			values['txt_value1'] = {};
			values['cbo_conector'] = {};

			var length = 0;
			$('.criteria').each(function() 
			{
				order = $(this).val();
				field = $("#cbo_restricted_value_" + order).val();
				operator = $("#cbo_operator_" + order).val();
				text = $("#txt_value1_" + order).val();
				conector = $("#cbo_conector_" + order).val();

				if (field == "")
				{
					return true;
				}

				if (field &#38;&#38; operator == "")
				{
					result = {
						element : $("#cbo_operator_" + order),
						message : lang['select_operator'] + ' ' + field
					  }
					  
					return false;
				}

				switch (true)
				{
					case (in_array_object(operator, operators_null)):
						break;
					default: 
						if ($("#txt_value1_" + order).val() == "")
						{
							result = {
								element : $("#txt_value1_" + order),
								message : lang['enter_value'] + ' ' + field
							  }
						}
				}

				if (jQuery.isEmptyObject(result))
				{
					values['cbo_restricted_value'][order] = field;
					values['cbo_operator'][order] = operator;
					values['txt_value1'][order] = text;
					values['cbo_conector'][order] = conector;		
					length++;
				}
			});

			if (!jQuery.isEmptyObject(result))
			{
				return result;				
			}

			var n = 0;
			$.each(values.cbo_restricted_value, function(key, value) 
			{
				if (n &#60; (length - 1))
				{
					if ($("#cbo_conector_" + key).val() == '')
					{
						result = {
							element : $("#cbo_conector_" + key),
							message : lang['select_conector'] + ' ' + values.cbo_restricted_value[key]
						  }
						return false;				
					}
				}
				n++;
			});

			return result;
		}

		$(document).ready(function () 
		{
			$.validate({
				lang: 'en', // (supported languages are fr, de, se, sv, en, pt, no)
				modules : "location,date,security,file",
				form: '#form',
				validateOnBlur : false,
				scrollToTopOnError : false,
				errorMessagePosition : 'top',
				scrollToTopOnError: true,		
				onValidate : function($form) 
				{					
					var result = validate_column();
					if (!jQuery.isEmptyObject(result))
					{
						$('#responsiveTabsGroups').responsiveTabs('activate', 0);
						return result;
					}
					
					result = validate_aggregate();
					if (!jQuery.isEmptyObject(result))
					{
						$('#responsiveTabsGroups').responsiveTabs('activate', 3);
						return result;
					}
					
					result = validate_criteria_2();
					if (!jQuery.isEmptyObject(result))
					{
						$('#responsiveTabsGroups').responsiveTabs('activate', 4);
						return result;
					}
					
					return true;
				}
			});
		});
	
	</script>
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
