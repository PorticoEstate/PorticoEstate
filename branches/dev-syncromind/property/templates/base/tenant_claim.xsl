  <!-- $Id$ -->
	<xsl:template match="data">
		<xsl:choose>
			<xsl:when test="edit">
				<xsl:apply-templates select="edit"/>
			</xsl:when>
			<xsl:when test="view">
				<xsl:apply-templates select="view"/>
			</xsl:when>
		</xsl:choose>
	</xsl:template>

	<!-- add / edit -->
	<xsl:template xmlns:php="http://php.net/xsl" match="edit">
		<script type="text/javascript">
			self.name="first_Window";
			function tenant_lookup()
			{
				Window1=window.open('<xsl:value-of select="tenant_link"/>',"Search","left=50,top=100,width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");
			}
		</script>
                        <div id="tab-content">
                            <xsl:value-of disable-output-escaping="yes" select="tabs"/>
                                <div id="general">
                                        <xsl:variable name="edit_url">
                                                <xsl:value-of select="edit_url"/>
                                        </xsl:variable>
                                        <form ENCTYPE="multipart/form-data" class="pure-form pure-form-aligned" name="form" method="post" action="{$edit_url}">
                                                    
                                                        <xsl:choose>
                                                                <xsl:when test="msgbox_data != ''">
                                                                    <dl>
                                                                        <dt>
                                                                                <xsl:call-template name="msgbox"/>
                                                                        </dt>
                                                                    </dl>
                                                                </xsl:when>
                                                        </xsl:choose>
                                                    
                                                        <xsl:choose>
                                                                <xsl:when test="value_claim_id!=''">
                                                                        <div class="pure-control-group">
                                                                                <label>
                                                                                        <xsl:value-of select="lang_claim_id"/>
                                                                                </label>
                                                                                        <xsl:value-of select="value_claim_id"/>
                                                                        </div>
                                                                </xsl:when>
                                                        </xsl:choose>
                                                        <div class="pure-control-group">
                                                                <label>
                                                                        <xsl:value-of select="lang_project_id"/>
                                                                </label>
                                                                        <xsl:value-of select="value_project_id"/>
                                                        </div>
                                                        <xsl:for-each select="value_origin">
                                                                <div class="pure-control-group">
                                                                        <label>
                                                                                <xsl:value-of select="descr"/>
                                                                        </label>
                                                                                <xsl:for-each select="data">
                                                                                        <a href="{link}" title="{//lang_origin_statustext}">
                                                                                                <xsl:value-of select="id"/>
                                                                                        </a>
                                                                                        <xsl:text> </xsl:text>
                                                                                </xsl:for-each>
                                                                </div>
                                                        </xsl:for-each>
                                                        <div class="pure-control-group">
                                                                <label>
                                                                        <xsl:value-of select="lang_name"/>
                                                                </label>
                                                                        <xsl:value-of select="value_name"/>
                                                        </div>
                                                        <div class="pure-control-group">
                                                                <label>
                                                                        <xsl:value-of select="lang_descr"/>
                                                                </label>
                                                                        <xsl:value-of select="value_descr"/>
                                                        </div>
                                                        <div class="pure-control-group">
                                                                <label>
                                                                        <xsl:value-of select="lang_category"/>
                                                                </label>
                                                                <xsl:for-each select="cat_list_project">
                                                                        <xsl:choose>
                                                                                <xsl:when test="selected='selected'">
                                                                                                <xsl:value-of select="name"/>
                                                                                </xsl:when>
                                                                        </xsl:choose>
                                                                </xsl:for-each>
                                                        </div>
                                                        <xsl:call-template name="location_view"/>
                                                        <xsl:choose>
                                                                <xsl:when test="contact_phone !=''">
                                                                        <div class="pure-control-group">
                                                                                <label>
                                                                                        <xsl:value-of select="lang_contact_phone"/>
                                                                                </label>
                                                                                        <xsl:value-of select="contact_phone"/>
                                                                        </div>
                                                                </xsl:when>
                                                        </xsl:choose>
                                                        <div class="pure-control-group">
                                                                <label>
                                                                        <xsl:value-of select="lang_power_meter"/>
                                                                </label>
                                                                        <xsl:value-of select="value_power_meter"/>
                                                        </div>
                                                        <div class="pure-control-group">
                                                                <label>
                                                                        <xsl:value-of select="lang_charge_tenant"/>
                                                                </label>
                                                                        <xsl:choose>
                                                                                <xsl:when test="charge_tenant='1'">
                                                                                        <b>X</b>
                                                                                </xsl:when>
                                                                        </xsl:choose>
                                                        </div>
                                                        <div class="pure-control-group">
                                                                <label>
                                                                        <xsl:value-of select="lang_budget"/>
                                                                </label>
                                                                    <xsl:value-of select="value_budget"/><xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
                                                        </div>
                                                        <div class="pure-control-group">
                                                                <label>
                                                                        <xsl:value-of select="lang_reserve"/>
                                                                </label>
                                                                        <xsl:value-of select="value_reserve"/><xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
                                                        </div>
                                                        <div class="pure-control-group">
                                                                <label>
                                                                        <xsl:value-of select="lang_reserve_remainder"/>
                                                                </label>
                                                                        <xsl:value-of select="value_reserve_remainder"/><xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
                                                                        <xsl:text> </xsl:text> ( <xsl:value-of select="value_reserve_remainder_percent"/>
                                                                        <xsl:text> % )</xsl:text>
                                                        </div>
                                                        <div class="pure-control-group">
                                                                <label>
                                                                        <xsl:value-of select="lang_actual_cost"/>
                                                                </label>
                                                                        <xsl:value-of select="sum_workorder_actual_cost"/><xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
                                                        </div>
                                                        <div class="pure-control-group">
                                                                        <!--div id="datatable-container_0"/-->
                                                                    <div class="pure-custom" style="width: 100%;">
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
                                                        <div class="pure-control-group">
                                                                <label>
                                                                        <xsl:value-of select="lang_coordinator"/>
                                                                </label>
                                                                <xsl:for-each select="user_list">
                                                                        <xsl:choose>
                                                                                <xsl:when test="selected">
                                                                                                <xsl:value-of select="name"/>
                                                                                </xsl:when>
                                                                        </xsl:choose>
                                                                </xsl:for-each>
                                                        </div>
                                                        <div class="pure-control-group">
                                                                <label>
                                                                        <xsl:value-of select="lang_status"/>
                                                                </label>
                                                                <xsl:for-each select="status_list">
                                                                        <xsl:choose>
                                                                                <xsl:when test="selected">
                                                                                                <xsl:value-of select="name"/>
                                                                                </xsl:when>
                                                                        </xsl:choose>
                                                                </xsl:for-each>
                                                        </div>
                                                        <div class="pure-control-group">
                                                                <label>
                                                                        <xsl:value-of select="php:function('lang', 'entry date')" />
                                                                </label>
                                                                        <xsl:value-of select="value_entry_date"/>
                                                        </div>
                                                        <div class="pure-control-group">
                                                                <label>
                                                                        <xsl:value-of select="lang_start_date"/>
                                                                </label>
                                                                        <xsl:value-of select="value_start_date"/>
                                                        </div>
                                                        <div class="pure-control-group">
                                                                <label>
                                                                        <xsl:value-of select="lang_end_date"/>
                                                                </label>
                                                                        <xsl:value-of select="value_end_date"/>
                                                        </div>
                                                        <div class="pure-control-group">
                                                                <label>
                                                                        <xsl:value-of select="lang_status"/>
                                                                </label>
                                                                        <xsl:call-template name="status_select"/>
                                                        </div>
                                                        <div class="pure-control-group">
                                                                <label>
                                                                        <a href="javascript:tenant_lookup()" onMouseover="window.status='{lang_tenant_statustext}';return true;" onMouseout="window.status='';return true;">
                                                                                <xsl:value-of select="lang_tenant"/>
                                                                        </a>
                                                                </label>
                                                                        <input type="hidden" name="tenant_id" value="{value_tenant_id}"/>
                                                                        <input size="{size_last_name}" type="text" name="last_name" value="{value_last_name}" onClick="tenant_lookup();" readonly="readonly">
                                                                                <xsl:attribute name="title">
                                                                                        <xsl:value-of select="lang_tenant_statustext"/>
                                                                                </xsl:attribute>
                                                                        </input>
                                                                        <input size="{size_first_name}" type="text" name="first_name" value="{value_first_name}" onClick="tenant_lookup();" readonly="readonly">
                                                                                <xsl:attribute name="title">
                                                                                        <xsl:value-of select="lang_tenant_statustext"/>
                                                                                </xsl:attribute>
                                                                        </input>
                                                        </div>
                            <xsl:call-template name="b_account_form"/>
                            <div class="pure-control-group">
                                <label>
                                    <xsl:value-of select="lang_amount"/>
                                </label>
                                    <input type="text" name="values[amount]" value="{value_amount}" onMouseout="window.status='';return true;">
                                        <xsl:attribute name="title">
                                            <xsl:value-of select="lang_amount_statustext"/>
                                        </xsl:attribute>
                                    </input>
                                    <xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
                            </div>
                            <div class="pure-control-group">
                                <label>
                                    <xsl:value-of select="lang_category"/>
                                </label>
                                    <xsl:call-template name="cat_select"/>
                            </div>
                            <div class="pure-control-group">
                                <label>
                                    <xsl:value-of select="lang_remark"/>
                                </label>
                                    <textarea cols="60" rows="6" name="values[remark]">
                                        <xsl:attribute name="title">
                                            <xsl:value-of select="lang_remark_statustext"/>
                                        </xsl:attribute>
                                        <xsl:value-of select="value_remark"/>
                                    </textarea>
                            </div>
                            <xsl:choose>
                                <xsl:when test="value_claim_id!=''">
                                    <div class="pure-control-group">
                                        <label>
                                            <xsl:value-of select="php:function('lang', 'files')"/>
                                        </label>
                                            <div id="paging_1"> </div>
                                            <!--div id="datatable-container_1"/-->
                                            <div class="pure-custom" style="width: 100%;">
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
                                    <xsl:call-template name="file_upload"/>
                                </xsl:when>
                            </xsl:choose>
                            <br></br>
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
                                    <xsl:variable name="lang_apply">
                                        <xsl:value-of select="lang_apply"/>
                                    </xsl:variable>
                                    <input type="submit" class="pure-button pure-button-primary" name="values[apply]" value="{$lang_apply}" onMouseout="window.status='';return true;">
                                        <xsl:attribute name="onMouseover">
                                            <xsl:text>window.status='</xsl:text>
                                            <xsl:value-of select="lang_apply_statustext"/>
                                            <xsl:text>'; return true;</xsl:text>
                                        </xsl:attribute>
                                    </input>
                                    <xsl:variable name="lang_cancel">
                                        <xsl:value-of select="lang_cancel"/>
                                    </xsl:variable>
                                    <input type="submit" class="pure-button pure-button-primary" name="values[cancel]" value="{$lang_cancel}" onMouseout="window.status='';return true;">
                                        <xsl:attribute name="onMouseover">
                                            <xsl:text>window.status='</xsl:text>
                                            <xsl:value-of select="lang_cancel_statustext"/>
                                            <xsl:text>'; return true;</xsl:text>
                                        </xsl:attribute>
                                    </input>
                            </div>
                            <br></br>
                            <fieldset style="border: 1px solid #000;">
                                <div class="pure-control-group">
                                        <!--div id="datatable-container_2"/-->
                                        <div class="pure-custom" style="width: 100%;">
                                            <xsl:for-each select="datatable_def">
                                                    <xsl:if test="container = 'datatable-container_2'">
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
                            </fieldset>
                    </form>
                    <script type="text/javascript">
                        var property_js = <xsl:value-of select="property_js"/>;
                        var base_java_url = <xsl:value-of select="base_java_url"/>;
                        var datatable = new Array();
                        var myColumnDefs = new Array();

                        <xsl:for-each select="datatable">
                            datatable[<xsl:value-of select="name"/>] = [
                            {
                            values:<xsl:value-of select="values"/>,
                            total_records: <xsl:value-of select="total_records"/>,
                            edit_action:  <xsl:value-of select="edit_action"/>,
                            is_paginator:  <xsl:value-of select="is_paginator"/>,
                            <xsl:if test="rows_per_page">
                                rows_per_page: "<xsl:value-of select="rows_per_page"/>",
                            </xsl:if>
                            <xsl:if test="initial_page">
                                initial_page: "<xsl:value-of select="initial_page"/>",
                            </xsl:if>
                            footer:<xsl:value-of select="footer"/>
                            }
                            ]
                        </xsl:for-each>

                        <xsl:for-each select="myColumnDefs">
                            myColumnDefs[<xsl:value-of select="name"/>] = <xsl:value-of select="values"/>
                        </xsl:for-each>
                    </script>
                </div>
            </div>
        </xsl:template>

<!-- New template-->
<!-- view -->
<xsl:template match="view" xmlns:php="http://php.net/xsl">
    <script type="text/javascript">
        self.name="first_Window";
        <xsl:value-of select="lookup_functions"/>
    </script>
    <div id="tab-content">
        <xsl:value-of disable-output-escaping="yes" select="tabs"/>
            <div id="general">
            <div align="left">
                <form method="post" class="pure-form pure-form-aligned" name="form">
                    <div class="pure-control-group">
                        <label>
                            <xsl:value-of select="lang_claim_id"/>
                        </label>
                            <xsl:value-of select="value_claim_id"/>
                    </div>
                    <div class="pure-control-group">
                        <label>
                            <xsl:value-of select="lang_project_id"/>
                        </label>
                            <xsl:value-of select="value_project_id"/>
                    </div>
                    <div class="pure-control-group">
                        <label>
                            <xsl:value-of select="lang_name"/>
                        </label>
                            <xsl:value-of select="value_name"/>
                    </div>
                    <div class="pure-control-group">
                        <label>
                            <xsl:value-of select="lang_descr"/>
                        </label>
                            <xsl:value-of select="value_descr"/>
                    </div>
                    <div class="pure-control-group">
                        <label>
                            <xsl:value-of select="lang_category"/>
                        </label>
                        <xsl:for-each select="cat_list_project">
                            <xsl:choose>
                                <xsl:when test="selected='selected'">
                                        <xsl:value-of select="name"/>
                                </xsl:when>
                            </xsl:choose>
                        </xsl:for-each>
                    </div>
                    <xsl:call-template name="location_view"/>
                    <xsl:choose>
                        <xsl:when test="contact_phone !=''">
                            <div class="pure-control-group">
                                <label>
                                    <xsl:value-of select="lang_contact_phone"/>
                                </label>
                                    <xsl:value-of select="contact_phone"/>
                            </div>
                        </xsl:when>
                    </xsl:choose>
                    <div class="pure-control-group">
                        <label>
                            <xsl:value-of select="lang_power_meter"/>
                        </label>
                            <xsl:value-of select="value_power_meter"/>
                    </div>
                    <div class="pure-control-group">
                        <label>
                            <xsl:value-of select="lang_charge_tenant"/>
                        </label>
                            <xsl:choose>
                                <xsl:when test="charge_tenant='1'">
                                    <b>X</b>
                                </xsl:when>
                            </xsl:choose>
                    </div>
                    <div class="pure-control-group">
                        <label>
                            <xsl:value-of select="lang_budget"/>
                        </label>
                            <xsl:value-of select="value_budget"/>
                            <xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
                    </div>
                    <div class="pure-control-group">
                        <label>
                            <xsl:value-of select="lang_reserve"/>
                        </label>
                            <xsl:value-of select="value_reserve"/>
                            <xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
                    </div>
                    <div class="pure-control-group">
                        <label>
                            <xsl:value-of select="lang_reserve_remainder"/>
                        </label>
                            <xsl:value-of select="value_reserve_remainder"/>
                            <xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
                            <xsl:text> </xsl:text> ( <xsl:value-of select="value_reserve_remainder_percent"/>
                            <xsl:text> % )</xsl:text>
                    </div>
                    <div class="pure-control-group">
                        <label>
                            <xsl:value-of select="lang_actual_cost"/>
                        </label>
                            <xsl:value-of select="sum_workorder_actual_cost"/>
                            <xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
                    </div>
                    <div class="pure-control-group">
                        <xsl:choose>
                            <xsl:when test="sum_workorder_budget=''">
                                <label>
                                    <xsl:value-of select="lang_no_workorders"/>
                                </label>
                            </xsl:when>
                            <xsl:otherwise>
                                        <xsl:apply-templates select="table_header_workorder"/>
                                        <xsl:apply-templates select="workorder_budget"/>
                                        <div class="pure-control-group">
                                            <label>
                                                <xsl:value-of select="lang_sum"/>
                                            </label>
                                            <label>
                                                <xsl:value-of select="sum_workorder_budget"/>
                                            </label>
                                            <label>
                                                <xsl:value-of select="sum_workorder_calculation"/>
                                            </label>
                                            <td>
                                            </td>
                                            <td>
                                            </td>
                                            <td>
                                            </td>
                                        </div>
                            </xsl:otherwise>
                        </xsl:choose>
                    </div>
                    <div class="pure-control-group">
                        <label>
                            <xsl:value-of select="lang_coordinator"/>
                        </label>
                        <xsl:for-each select="user_list">
                            <xsl:choose>
                                <xsl:when test="selected">
                                        <xsl:value-of select="name"/>
                                </xsl:when>
                            </xsl:choose>
                        </xsl:for-each>
                    </div>
                    <div class="pure-control-group">
                        <label>
                            <xsl:value-of select="lang_status"/>
                        </label>
                        <xsl:for-each select="status_list">
                            <xsl:choose>
                                <xsl:when test="selected">
                                        <xsl:value-of select="name"/>
                                </xsl:when>
                            </xsl:choose>
                        </xsl:for-each>
                    </div>
                    <div class="pure-control-group">
                        <label>
                            <xsl:value-of select="php:function('lang', 'entry date')" />
                        </label>
                            <xsl:value-of select="value_entry_date"/>
                    </div>

                    <div class="pure-control-group">
                        <label>
                            <xsl:value-of select="lang_start_date"/>
                        </label>
                            <xsl:value-of select="value_start_date"/>
                    </div>
                    <div class="pure-control-group">
                        <label>
                            <xsl:value-of select="lang_end_date"/>
                        </label>
                            <xsl:value-of select="value_end_date"/>
                    </div>
                    <div class="pure-control-group">
                        <label>
                            <xsl:value-of select="lang_status"/>
                        </label>
                        <xsl:for-each select="status_list">
                            <xsl:choose>
                                <xsl:when test="selected='selected'">
                                        <xsl:value-of select="name"/>
                                </xsl:when>
                            </xsl:choose>
                        </xsl:for-each>
                    </div>
                    <div class="pure-control-group">
                        <label>
                            <xsl:value-of select="lang_tenant"/>
                        </label>
                            <input size="{size_last_name}" type="text" name="last_name" value="{value_last_name}" readonly="readonly">
                            </input>
                            <input size="{size_first_name}" type="text" name="first_name" value="{value_first_name}" readonly="readonly">
                            </input>
                    </div>
                    <xsl:call-template name="b_account_view"/>
                    <div class="pure-control-group">
                        <label>
                            <xsl:value-of select="lang_amount"/>
                        </label>
                            <xsl:value-of select="value_amount"/>
                            <xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
                    </div>
                    <div class="pure-control-group">
                        <label>
                            <xsl:value-of select="lang_category"/>
                        </label>
                        <xsl:for-each select="cat_list">
                            <xsl:choose>
                                <xsl:when test="selected='selected'">
                                        <xsl:value-of select="name"/>
                                </xsl:when>
                            </xsl:choose>
                        </xsl:for-each>
                    </div>
                    <div class="pure-control-group">
                        <label>
                            <xsl:value-of select="lang_remark"/>
                        </label>
                            <textarea cols="60" rows="6" name="values[remark]" onMouseout="window.status='';return true;">
                                <xsl:attribute name="onMouseover">
                                    <xsl:text>window.status='</xsl:text>
                                    <xsl:value-of select="lang_remark_statustext"/>
                                    <xsl:text>'; return true;</xsl:text>
                                </xsl:attribute>
                                <xsl:value-of select="value_remark"/>
                            </textarea>
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
                                <input type="submit" class="pure-button pure-button-primary forms" name="done" value="{$lang_done}" onMouseover="window.status='Back to the list.';return true;" onMouseout="window.status='';return true;"/>
                            </form>
                    </div>
            </div>
            </div>
    </div>
</xsl:template>

<!-- New template-->
<xsl:template match="table_header_workorder">
    <tr class="th">
	<td class="th_text" width="4%" align="right">
	    <xsl:value-of select="lang_workorder_id"/>
	</td>
	<td class="th_text" width="10%" align="right">
	    <xsl:value-of select="lang_budget"/>
	</td>
	<td class="th_text" width="5%" align="right">
	    <xsl:value-of select="lang_calculation"/>
	</td>
	<td class="th_text" width="10%" align="right">
	    <xsl:value-of select="lang_vendor"/>
	</td>
	<td class="th_text" width="10%" align="right">
	    <xsl:value-of select="lang_charge_tenant"/>
	</td>
	<td class="th_text" width="10%" align="right">
	    <xsl:value-of select="lang_select"/>
	</td>
    </tr>
</xsl:template>

<!-- New template-->
<xsl:template match="workorder_budget">
    <xsl:variable name="workorder_link">
	<xsl:value-of select="//workorder_link"/>&amp;id=<xsl:value-of select="workorder_id"/>
    </xsl:variable>
    <xsl:variable name="workorder_id">
	<xsl:value-of select="workorder_id"/>
    </xsl:variable>
    <tr>
	<xsl:attribute name="class">
	    <xsl:choose>
		<xsl:when test="@class">
		    <xsl:value-of select="@class"/>
		</xsl:when>
		<xsl:when test="position() mod 2 = 0">
		    <xsl:text>row_off</xsl:text>
		</xsl:when>
		<xsl:otherwise>
		    <xsl:text>row_on</xsl:text>
		</xsl:otherwise>
	    </xsl:choose>
	</xsl:attribute>
	<td align="right">
	    <a href="{$workorder_link}" target="_blank">
		<xsl:value-of select="workorder_id"/>
	    </a>
	</td>
	<td align="right">
	    <xsl:value-of select="budget"/>
	</td>
	<td align="right">
	    <xsl:value-of select="calculation"/>
	</td>
	<td align="left">
	    <xsl:value-of select="vendor_name"/>
	</td>
	<td align="center">
	    <xsl:choose>
		<xsl:when test="charge_tenant='1'">
		    <b>x</b>
		</xsl:when>
	    </xsl:choose>
	    <xsl:choose>
		<xsl:when test="claimed!=''">
		    <b>
			<xsl:text>[</xsl:text>
			<xsl:value-of select="claimed"/>
			<xsl:text>]</xsl:text>
		    </b>
		</xsl:when>
	    </xsl:choose>
	</td>
	<td align="center">
	    <xsl:choose>
		<xsl:when test="selected = 1">
		    <input type="checkbox" name="values[workorder][]" value="{$workorder_id}" checked="checked" onMouseout="window.status='';return true;">
			<xsl:attribute name="title">
			    <xsl:value-of select="//lang_select_workorder_statustext"/>
			</xsl:attribute>
		    </input>
		</xsl:when>
		<xsl:otherwise>
		    <input type="checkbox" name="values[workorder][]" value="{$workorder_id}" onMouseout="window.status='';return true;">
			<xsl:attribute name="title">
			    <xsl:value-of select="//lang_select_workorder_statustext"/>
			</xsl:attribute>
		    </input>
		</xsl:otherwise>
	    </xsl:choose>
	</td>
    </tr>
</xsl:template>
