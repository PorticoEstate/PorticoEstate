
<!-- $Id$ -->
<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="add">
			<xsl:apply-templates select="add"/>
		</xsl:when>
		<xsl:when test="history">
			<xsl:apply-templates select="history"/>
		</xsl:when>
	</xsl:choose>
</xsl:template>


<xsl:template match="history">
	<xsl:call-template name="top-toolbar" />
	<div>
		<xsl:choose>
			<xsl:when test="msgbox_data != ''">
				<dl>
					<dt>
						<xsl:call-template name="msgbox"/>
					</dt>
				</dl>
			</xsl:when>
		</xsl:choose>
	    <div id="message" class='message'/>
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
	<xsl:call-template name="end-toolbar" />
</xsl:template>

<xsl:template name="top-toolbar">
	<div class="toolbar-container">
		<div class="pure-g">
			<div class="pure-u-1-3">
				<xsl:for-each select="info">
					<div>
						<span>
							<xsl:value-of select="name"/>
						</span>: <span>
							<xsl:value-of select="value"/>
						</span>
					</div>
				</xsl:for-each>
				<xsl:for-each select="hidden">
					<input type="hidden" id="{name}" name="{name}" value="{value}" />
				</xsl:for-each>
			</div>
			<div class="pure-u-2-3">
				<xsl:for-each select="top_toolbar">
					<a class="pure-button pure-button-primary" href="{url}">
						<xsl:value-of select="value"/>
					</a>
				</xsl:for-each>
			</div>
		</div>
	</div>
</xsl:template>

<xsl:template name="end-toolbar">
	<div class="toolbar-container">
		<div class="toolbar">
			<form class="pure-form pure-form-stacked">
				<div class="pure-g">
					<div class="pure-u-1">
						<xsl:for-each select="end_toolbar">
							<xsl:choose>
								<xsl:when test="type = 'date-picker'">
									<div>
										<input id="filter_{name}" name="filter_{name}" type="text"></input>
									</div>
								</xsl:when>
								<xsl:when test="type='button'">
									<button id="{id}" type="{type}" class="pure-button pure-button-primary" onclick="{action}">
										<xsl:value-of select="value"/>
									</button>
								</xsl:when>
								<xsl:when test="type='label'">
									<xsl:value-of select="value"/>
								</xsl:when>
								<xsl:otherwise>
									<input id="{id}" type="{type}" name="{name}" value="{value}">
										<xsl:if test="type = 'checkbox' and checked = '1'">
											<xsl:attribute name="checked">checked</xsl:attribute>
										</xsl:if>
									</input>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:for-each>
					</div>
				</div>
			</form>
		</div>
	</div>
</xsl:template>


<!-- add -->
<xsl:template match="add">
	<script type="text/javascript">
		self.name="first_Window";
		function location_lookup()
		{
		Window1=window.open('<xsl:value-of select="location_link"/>',"Search","left=50,top=100,width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");
		}
	</script>
	<dl>
		<xsl:choose>
			<xsl:when test="msgbox_data != ''">
				<tr>
					<td align="left" colspan="3">
						<xsl:call-template name="msgbox"/>
					</td>
				</tr>
			</xsl:when>
		</xsl:choose>
	</dl>
	<xsl:variable name="form_action">
		<xsl:value-of select="form_action"/>
	</xsl:variable>
	<form method="post" class="pure-form pure-form-aligned" id="form" action="{$form_action}" name="form">
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="tabs"/>
			<div id="general">
				<xsl:call-template name="location_form"/>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="lang_write_off_period"/>
					</label>
					<xsl:call-template name="cat_select_investment" />
					<xsl:text>  </xsl:text>
					<xsl:value-of select="lang_new"/>
					<input type="text"  id="numperiod" name="values[new_period]" value="{value_new_period}" size="3" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_new_period_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="lang_type"/>
					</label>
					<xsl:call-template name="filter_select"/>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="lang_amount"/>
					</label>
					<input type="text" name="values[initial_value]" value="{value_inital_value}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_value_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="lang_date"/>
					</label>
					<input type="text" id="values_date" name="values[date]" size="10" value="{value_date}" readonly="readonly" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_date_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="lang_descr"/>
					</label>
					<input type="text" name="values[descr]" value="{value_descr}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_name_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<xsl:variable name="lang_save">
						<xsl:value-of select="lang_save"/>
					</xsl:variable>
					<input type="submit" class="pure-button pure-button-primary" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_save_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</div>
			</div>
		</div>
	</form>
	<div class="pure-control-group">
		<xsl:variable name="done_action">
			<xsl:value-of select="done_action"/>
		</xsl:variable>
		<xsl:variable name="lang_done">
			<xsl:value-of select="lang_done"/>
		</xsl:variable>
		<form method="post" action="{$done_action}">
			<input type="submit" class="pure-button pure-button-primary" name="done" value="{$lang_done}" onMouseout="window.status='';return true;">
				<xsl:attribute name="onMouseover">
					<xsl:text>window.status='</xsl:text>
					<xsl:value-of select="lang_done_statustext"/>
					<xsl:text>'; return true;</xsl:text>
				</xsl:attribute>
			</input>
		</form>
	</div>
	<script type="text/javascript">
		$.formUtils.addValidator({
		name : 'write_period_num',
		validatorFunction : function(value, $el, config, language, $form) {
		var select_num = (value == '') ? 0 : 1;
		var nun_period = ($('#numperiod').val() == parseInt($('#numperiod').val(), 10)) ? 1 : 0;
		var result = (nun_period + select_num == 0) ? false : true;
		return result;
		},
		errorMessage : '',
		errorMessageKey: ''
		});
	</script>
                    
</xsl:template>
