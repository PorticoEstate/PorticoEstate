
<!-- $Id$ -->
<xsl:template match="data">
		<xsl:choose>
			<xsl:when test="edit">
				<xsl:apply-templates select="edit"/>
			</xsl:when>
			<xsl:when test="view">
				<xsl:apply-templates select="view"/>
			</xsl:when>
			<xsl:when test="list_gab_detail">
				<xsl:apply-templates select="list_gab_detail"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="list_gab"/>
			</xsl:otherwise>
		</xsl:choose>
</xsl:template>

<!-- New template-->
<xsl:template match="search_field_header">
		<tr>
			<td class="th_text" width="17%" align="left" colspan="8">
				<xsl:value-of select="//lang_address"/>
				<xsl:text>: </xsl:text>
				<input type="text" size="60" name="address" value="{//address}" onMouseout="window.status='';return true;">
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
						<xsl:value-of select="lang_search_address_statustext"/>
						<xsl:text>'; return true;</xsl:text>
					</xsl:attribute>
				</input>
				<xsl:text> </xsl:text>
				<xsl:value-of select="//lang_check_payments"/>
				<xsl:text>: </xsl:text>
				<xsl:choose>
					<xsl:when test="//value_check_payments !=''">
						<input type="checkbox" name="check_payments" value="1" checked="checked" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_check_payments_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</xsl:when>
					<xsl:otherwise>
						<input type="checkbox" name="check_payments" value="1" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_check_payments_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</xsl:otherwise>
				</xsl:choose>
			</td>
		</tr>
		<tr>
			<td class="th_text" width="17%" align="left">
				<xsl:value-of select="lang_property"/>
			</td>
			<td class="th_text" width="17%" align="left">
				<xsl:value-of select="lang_gaards_nr"/>
			</td>
			<td class="th_text" width="17%" align="left">
				<xsl:value-of select="lang_bruksnr"/>
			</td>
			<td class="th_text" width="17%" align="left">
				<xsl:value-of select="lang_feste_nr"/>
			</td>
			<td class="th_text" width="17%" align="left">
				<xsl:value-of select="lang_seksjons_nr"/>
			</td>
		</tr>
</xsl:template>

<!-- New template-->
<xsl:template name="search_field">
		<xsl:variable name="query">
			<xsl:value-of select="query"/>
		</xsl:variable>
		<xsl:variable name="lang_search">
			<xsl:value-of select="lang_search"/>
		</xsl:variable>
		<tr>
			<td align="left">
				<input type="text" size="6" name="location_code" value="{location_code}" onMouseout="window.status='';return true;">
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
						<xsl:value-of select="lang_search_location_statustext"/>
						<xsl:text>'; return true;</xsl:text>
					</xsl:attribute>
				</input>
			</td>
			<td align="left">
				<input type="text" size="6" name="gaards_nr" value="{gaards_nr}" onMouseout="window.status='';return true;">
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
						<xsl:value-of select="lang_search_gaard_statustext"/>
						<xsl:text>'; return true;</xsl:text>
					</xsl:attribute>
				</input>
			</td>
			<td align="left">
				<input type="text" size="6" name="bruksnr" value="{bruksnr}" onMouseout="window.status='';return true;">
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
						<xsl:value-of select="lang_search_bruk_statustext"/>
						<xsl:text>'; return true;</xsl:text>
					</xsl:attribute>
				</input>
			</td>
			<td align="left">
				<input type="text" size="6" name="feste_nr" value="{feste_nr}" onMouseout="window.status='';return true;">
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
						<xsl:value-of select="lang_search_feste_statustext"/>
						<xsl:text>'; return true;</xsl:text>
					</xsl:attribute>
				</input>
			</td>
			<td align="left">
				<input type="text" size="6" name="seksjons_nr" value="{seksjons_nr}" onMouseout="window.status='';return true;">
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
						<xsl:value-of select="lang_search_seksjon_statustext"/>
						<xsl:text>'; return true;</xsl:text>
					</xsl:attribute>
				</input>
			</td>
			<td align="left">
				<input type="submit" name="submit" value="{$lang_search}" onMouseout="window.status='';return true;">
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
						<xsl:value-of select="lang_searchbutton_statustext"/>
						<xsl:text>'; return true;</xsl:text>
					</xsl:attribute>
				</input>
				<xsl:text> </xsl:text>
				<input type="checkbox" name="reset_query" value="True" onMouseout="window.status='';return true;">
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
						<xsl:value-of select="lang_reset_query_statustext"/>
						<xsl:text>'; return true;</xsl:text>
					</xsl:attribute>
				</input>
			</td>
			<td class="small_text" valign="top" align="left">
				<xsl:variable name="link_download">
					<xsl:value-of select="link_download"/>
				</xsl:variable>
				<xsl:variable name="lang_download_help">
					<xsl:value-of select="lang_download_help"/>
				</xsl:variable>
				<xsl:variable name="lang_download">
					<xsl:value-of select="lang_download"/>
				</xsl:variable>
				<a href="javascript:var w=window.open('{$link_download}','','left=50,top=100')" onMouseOver="overlib('{$lang_download_help}', CAPTION, '{$lang_download}')" onMouseOut="nd()">
					<xsl:value-of select="lang_download"/>
				</a>
			</td>
		</tr>
</xsl:template>

<!-- New template-->
<xsl:template match="list_gab">
		<xsl:apply-templates select="menu"/>
		<div align="center">
			<table width="100%" cellpadding="2" cellspacing="2" align="center">
				<xsl:variable name="select_url">
					<xsl:value-of select="select_action"/>
				</xsl:variable>
				<form method="post" action="{$select_url}">
					<xsl:apply-templates select="search_field_header"/>
					<xsl:call-template name="search_field"/>
				</form>
				<tr>
					<td colspan="{colspan}" width="100%">
						<xsl:call-template name="nextmatchs"/>
					</td>
				</tr>
			</table>
		</div>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:apply-templates select="table_header_gab"/>
			<xsl:choose>
				<xsl:when test="values_gab[gaards_nr]">
					<xsl:apply-templates select="values_gab"/>
				</xsl:when>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="table_add!=''">
					<xsl:apply-templates select="table_add"/>
				</xsl:when>
			</xsl:choose>
		</table>
</xsl:template>

<!-- New template-->
<xsl:template match="table_header_gab">
		<xsl:variable name="sort_gab_id">
			<xsl:value-of select="sort_gab_id"/>
		</xsl:variable>
		<xsl:variable name="sort_hits">
			<xsl:value-of select="sort_hits"/>
		</xsl:variable>
		<xsl:variable name="sort_location_code">
			<xsl:value-of select="sort_location_code"/>
		</xsl:variable>
		<tr class="th">
			<td class="th_text" width="10%" align="right">
				<a href="{$sort_gab_id}">
					<xsl:value-of select="lang_gaards_nr"/>
				</a>
			</td>
			<td class="th_text" width="10%" align="right">
				<xsl:value-of select="lang_bruksnr"/>
			</td>
			<td class="th_text" width="10%" align="right">
				<xsl:value-of select="lang_feste_nr"/>
			</td>
			<td class="th_text" width="10%" align="right">
				<xsl:value-of select="lang_seksjons_nr"/>
			</td>
			<xsl:choose>
				<xsl:when test="payment_header!=''">
					<td class="th_text" width="8%" align="right">
						<xsl:value-of select="lang_hits"/>
					</td>
				</xsl:when>
			</xsl:choose>
			<td class="th_text" width="10%" align="right">
				<xsl:value-of select="lang_owner"/>
			</td>
			<td class="th_text" width="10%" align="right">
				<a href="{$sort_location_code}">
					<xsl:value-of select="lang_location_code"/>
				</a>
			</td>
			<td class="th_text" width="20%" align="center">
				<xsl:value-of select="lang_address"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_view"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_map"/>
			</td>
			<xsl:choose>
				<xsl:when test="payment_header!=''">
					<xsl:for-each select="payment_header">
						<td class="th_text" width="5%" align="center">
							<xsl:value-of select="header"/>
						</td>
					</xsl:for-each>
				</xsl:when>
			</xsl:choose>
		</tr>
</xsl:template>

<!-- New template-->
<xsl:template match="values_gab">
		<xsl:variable name="lang_view_statustext">
			<xsl:value-of select="lang_view_statustext"/>
		</xsl:variable>
		<xsl:variable name="lang_edit_statustext">
			<xsl:value-of select="lang_edit_statustext"/>
		</xsl:variable>
		<xsl:variable name="lang_delete_statustext">
			<xsl:value-of select="lang_delete_statustext"/>
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
				<xsl:value-of select="gaards_nr"/>
			</td>
			<td align="right">
				<xsl:value-of select="bruks_nr"/>
			</td>
			<td align="right">
				<xsl:value-of select="feste_nr"/>
			</td>
			<td align="right">
				<xsl:value-of select="seksjons_nr"/>
			</td>
			<xsl:choose>
				<xsl:when test="payment!=''">
					<td align="right">
						<xsl:value-of select="hits"/>
					</td>
				</xsl:when>
			</xsl:choose>
			<td align="right">
				<xsl:value-of select="owner"/>
			</td>
			<td align="right">
				<xsl:value-of select="location_code"/>
			</td>
			<td align="left">
				<xsl:value-of select="address"/>
			</td>
			<td align="center">
				<xsl:variable name="link_view">
					<xsl:value-of select="link_view"/>
				</xsl:variable>
				<a href="{$link_view}" onMouseover="window.status='{$lang_view_statustext}';return true;" onMouseout="window.status='';return true;">
					<xsl:value-of select="text_view"/>
				</a>
			</td>
			<td align="left">
				<pre>
					<xsl:variable name="link_map">
						<xsl:value-of select="link_map"/>
					</xsl:variable>
					<a href="{$link_map}" target="_blank" onMouseover="window.status='{lang_map_statustext}';return true;" onMouseout="window.status='';return true;">
						<xsl:value-of select="text_map"/>
					</a>
					<xsl:text> | </xsl:text>
					<xsl:variable name="link_gab">
						<xsl:value-of select="link_gab"/>
					</xsl:variable>
					<a href="{$link_gab}" target="_blank" onMouseover="window.status='{lang_gab_statustext}';return true;" onMouseout="window.status='';return true;">
						<xsl:value-of select="text_gab"/>
					</a>
				</pre>
			</td>
			<xsl:choose>
				<xsl:when test="payment!=''">
					<xsl:for-each select="payment">
						<td align="right">
							<xsl:value-of select="amount"/>
						</td>
					</xsl:for-each>
				</xsl:when>
			</xsl:choose>
		</tr>
</xsl:template>

<!-- New template-->
<xsl:template match="list_gab_detail">
	<xsl:call-template name="top-toolbar" />
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
</xsl:template>

<xsl:template name="top-toolbar">
	<div class="toolbar-container">
		<div class="pure-g">
			<div class="pure-u-1-3">
				<xsl:for-each select="info">
					<div>
						<xsl:value-of select="name"/>:<xsl:value-of select="value"/>
					</div>
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
		

<!-- New template-->
<xsl:template match="table_add">
		<tr>
			<td height="50">
				<xsl:variable name="add_action">
					<xsl:value-of select="add_action"/>
				</xsl:variable>
				<xsl:variable name="lang_add">
					<xsl:value-of select="lang_add"/>
				</xsl:variable>
				<form method="post" action="{$add_action}">
					<input type="submit" name="add" value="{$lang_add}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_add_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</form>
			</td>
		</tr>
</xsl:template>

<!-- New template-->
<xsl:template match="table_done">
		<tr>
			<td height="50">
				<xsl:variable name="done_action">
					<xsl:value-of select="done_action"/>
				</xsl:variable>
				<xsl:variable name="lang_done">
					<xsl:value-of select="lang_done"/>
				</xsl:variable>
				<form method="post" action="{$done_action}">
					<input type="submit" name="done" value="{$lang_done}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_done_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</form>
			</td>
		</tr>
</xsl:template>

<!-- add / edit -->
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
				<xsl:variable name="form_action">
					<xsl:value-of select="form_action"/>
				</xsl:variable>
	<form method="post" id="form" name="form" action="{$form_action}" class= "pure-form pure-form-aligned">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="tabs"/>
			<div id="generic">
				<fieldset>
					<xsl:choose>
						<xsl:when test="gaards_nr&gt;0">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_kommune_nr"/>
								</label>
									<xsl:value-of select="kommune_nr"/>
									<input type="hidden" name="values[kommune_nr]" value="{kommune_nr}"/>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_gaards_nr"/>
								</label>
									<xsl:value-of select="gaards_nr"/>
									<input type="hidden" name="values[gaards_nr]" value="{gaards_nr}"/>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_bruksnr"/>
								</label>
									<xsl:value-of select="bruks_nr"/>
									<input type="hidden" name="values[bruks_nr]" value="{bruks_nr}"/>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_feste_nr"/>
								</label>
									<xsl:value-of select="feste_nr"/>
									<input type="hidden" name="values[feste_nr]" value="{feste_nr}"/>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_seksjons_nr"/>
								</label>
									<xsl:value-of select="seksjons_nr"/>
									<input type="hidden" name="values[seksjons_nr]" value="{seksjons_nr}"/>
							</div>
						</xsl:when>
						<xsl:otherwise>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_kommune_nr"/>
								</label>
									<input type="text" name="values[kommune_nr]" maxlength="4" size="8" value="{kommune_nr}" onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_kommune_nr_statustext"/>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
									</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_gaards_nr"/>
								</label>
									<input type="text" name="values[gaards_nr]" maxlength="5" size="8" onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_gaards_nr_statustext"/>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
									</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_bruksnr"/>
								</label>
									<input type="text" name="values[bruks_nr]" maxlength="4" size="8" onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_bruks_nr_statustext"/>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
									</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_feste_nr"/>
								</label>
									<input type="text" name="values[feste_nr]" maxlength="4" size="8" onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_feste_nr_statustext"/>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
									</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_seksjons_nr"/>
								</label>
									<input type="text" name="values[seksjons_nr]" maxlength="3" size="8" onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_seksjons_nr_statustext"/>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
									</input>
							</div>
						</xsl:otherwise>
					</xsl:choose>
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
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_owner"/>
						</label>
							<xsl:choose>
								<xsl:when test="value_owner = 'yes'">
									<input type="checkbox" name="values[owner]" value="yes" checked="checked" onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_lookup_form_statustext"/>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
									</input>
								</xsl:when>
								<xsl:otherwise>
									<input type="checkbox" name="values[owner]" value="yes" onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_lookup_form_statustext"/>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
									</input>
								</xsl:otherwise>
							</xsl:choose>
					</div>
					<xsl:choose>
						<xsl:when test="lookup_type='form2'">
							<xsl:call-template name="location_form2"/>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_propagate"/>
								</label>
									<input type="checkbox" name="values[propagate]" value="True" onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_propagate_statustext"/>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
									</input>
							</div>
						</xsl:when>
						<xsl:otherwise>
							<xsl:call-template name="location_view2"/>
						</xsl:otherwise>
					</xsl:choose>
				</fieldset>
			</div>
		</div>
		<div class="proplist-col">
							<input type="hidden" name="values[action]" value="{action}"/>
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

						<xsl:variable name="lang_done">
							<xsl:value-of select="lang_done"/>
						</xsl:variable>
			<input type="button" class="pure-button pure-button-primary" name="done" value="{$lang_done}" onClick="document.done_form.submit();">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_done_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
		</div>
	</form>
	<xsl:variable name="done_action">
		<xsl:value-of select="done_action"/>
	</xsl:variable>
	<form method="post" name="done_form" id="done_form" action="{$done_action}"></form>

</xsl:template>

<!-- view -->
<xsl:template match="view">
	<xsl:variable name="edit_action">
		<xsl:value-of select="edit_action"/>
	</xsl:variable>
	<form method="post" id="form" name="form" action="{$edit_action}" class= "pure-form pure-form-aligned">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="tabs"/>
			<div id="generic">
				<fieldset>
					<div class="pure-control-group">
						<label>
						<xsl:value-of select="lang_kommune_nr"/>
						</label>
						<xsl:value-of select="kommune_nr"/>
						<input type="hidden" name="values[kommune_nr]" value="{kommune_nr}"/>
					</div>
					<div class="pure-control-group">
						<label>
						<xsl:value-of select="lang_gaards_nr"/>
						</label>
						<xsl:value-of select="gaards_nr"/>
						<input type="hidden" name="values[gaards_nr]" value="{gaards_nr}"/>
					</div>
					<div class="pure-control-group">
						<label>
						<xsl:value-of select="lang_bruksnr"/>
						</label>
						<xsl:value-of select="bruks_nr"/>
						<input type="hidden" name="values[bruks_nr]" value="{bruks_nr}"/>
					</div>
					<div class="pure-control-group">
						<label>
						<xsl:value-of select="lang_feste_nr"/>
						</label>
						<xsl:value-of select="feste_nr"/>
						<input type="hidden" name="values[feste_nr]" value="{feste_nr}"/>
					</div>
					<div class="pure-control-group">
						<label>
						<xsl:value-of select="lang_seksjons_nr"/>
						</label>
						<xsl:value-of select="seksjons_nr"/>
						<input type="hidden" name="values[seksjons_nr]" value="{seksjons_nr}"/>
					</div>
					<div class="pure-control-group">
						<label>
						<xsl:value-of select="lang_remark"/>
						</label>
						<xsl:value-of select="value_remark"/>
					</div>
					<div class="pure-control-group">
						<label>
						<xsl:value-of select="lang_owner"/>
						</label>
						<xsl:value-of select="value_owner"/>
					</div>
				<xsl:call-template name="location_view"/>
				</fieldset>
			</div>
		</div>
		<div class="proplist-col">
			<xsl:variable name="lang_edit">
				<xsl:value-of select="lang_edit"/>
						</xsl:variable>
			<input type="submit" class="pure-button pure-button-primary" name="edit" value="{$lang_edit}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
					<xsl:value-of select="lang_edit_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
			<xsl:variable name="lang_done">
				<xsl:value-of select="lang_done"/>
						</xsl:variable>
			<input type="button" class="pure-button pure-button-primary" name="done" value="{$lang_done}" onMouseout="window.status='';return true;" onClick="document.done_form.submit();">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
					<xsl:value-of select="lang_done_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
		</div>
	</form>
	<xsl:variable name="done_action">
		<xsl:value-of select="done_action"/>
	</xsl:variable>
	<form method="post" name="done_form" id="done_form" action="{$done_action}"></form>
		
</xsl:template>
