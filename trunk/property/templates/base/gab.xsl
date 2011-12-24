<!-- $Id$ -->

	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="edit">
				<xsl:apply-templates select="edit"></xsl:apply-templates>
			</xsl:when>
			<xsl:when test="view">
				<xsl:apply-templates select="view"></xsl:apply-templates>
			</xsl:when>
			<xsl:when test="list_gab_detail">
				<xsl:apply-templates select="list_gab_detail"></xsl:apply-templates>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="list_gab"></xsl:apply-templates>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>


	<xsl:template match="search_field_header">
		<tr>
			<td class="th_text" width="17%" align="left" colspan="8">
				<xsl:value-of select="//lang_address"></xsl:value-of>
				<xsl:text>: </xsl:text>
				<input type="text" size="60" name="address" value="{//address}" onMouseout="window.status='';return true;">
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
						<xsl:value-of select="lang_search_address_statustext"></xsl:value-of>
						<xsl:text>'; return true;</xsl:text>
					</xsl:attribute>
				</input>
				<xsl:text> </xsl:text>
				<xsl:value-of select="//lang_check_payments"></xsl:value-of>
				<xsl:text>: </xsl:text>
				<xsl:choose>
					<xsl:when test="//value_check_payments !=''">
						<input type="checkbox" name="check_payments" value="1" checked="checked" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_check_payments_statustext"></xsl:value-of>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</xsl:when>
					<xsl:otherwise>
						<input type="checkbox" name="check_payments" value="1" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_check_payments_statustext"></xsl:value-of>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</xsl:otherwise>
				</xsl:choose>
			</td>

		</tr>
		<tr>
			<td class="th_text" width="17%" align="left">
				<xsl:value-of select="lang_property"></xsl:value-of>
			</td>
			<td class="th_text" width="17%" align="left">
				<xsl:value-of select="lang_gaards_nr"></xsl:value-of>
			</td>
			<td class="th_text" width="17%" align="left">
				<xsl:value-of select="lang_bruksnr"></xsl:value-of>
			</td>
			<td class="th_text" width="17%" align="left">
				<xsl:value-of select="lang_feste_nr"></xsl:value-of>
			</td>
			<td class="th_text" width="17%" align="left">
				<xsl:value-of select="lang_seksjons_nr"></xsl:value-of>
			</td>
		</tr>
	</xsl:template>
	<xsl:template name="search_field">
		<xsl:variable name="query"><xsl:value-of select="query"></xsl:value-of></xsl:variable>
		<xsl:variable name="lang_search"><xsl:value-of select="lang_search"></xsl:value-of></xsl:variable>
		<tr>
			<td align="left">
				<input type="text" size="6" name="location_code" value="{location_code}" onMouseout="window.status='';return true;">
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
						<xsl:value-of select="lang_search_location_statustext"></xsl:value-of>
						<xsl:text>'; return true;</xsl:text>
					</xsl:attribute>
				</input>
			</td>
			<td align="left">
				<input type="text" size="6" name="gaards_nr" value="{gaards_nr}" onMouseout="window.status='';return true;">
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
						<xsl:value-of select="lang_search_gaard_statustext"></xsl:value-of>
						<xsl:text>'; return true;</xsl:text>
					</xsl:attribute>
				</input>
			</td>
			<td align="left">
				<input type="text" size="6" name="bruksnr" value="{bruksnr}" onMouseout="window.status='';return true;">
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
						<xsl:value-of select="lang_search_bruk_statustext"></xsl:value-of>
						<xsl:text>'; return true;</xsl:text>
					</xsl:attribute>
				</input>
			</td>
			<td align="left">
				<input type="text" size="6" name="feste_nr" value="{feste_nr}" onMouseout="window.status='';return true;">
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
						<xsl:value-of select="lang_search_feste_statustext"></xsl:value-of>
						<xsl:text>'; return true;</xsl:text>
					</xsl:attribute>
				</input>
			</td>
			<td align="left">
				<input type="text" size="6" name="seksjons_nr" value="{seksjons_nr}" onMouseout="window.status='';return true;">
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
						<xsl:value-of select="lang_search_seksjon_statustext"></xsl:value-of>
						<xsl:text>'; return true;</xsl:text>
					</xsl:attribute>
				</input>
			</td>
			<td align="left">
				<input type="submit" name="submit" value="{$lang_search}" onMouseout="window.status='';return true;"> 
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
						<xsl:value-of select="lang_searchbutton_statustext"></xsl:value-of>
						<xsl:text>'; return true;</xsl:text>
					</xsl:attribute>
				</input>
				<xsl:text> </xsl:text>
				<input type="checkbox" name="reset_query" value="True" onMouseout="window.status='';return true;">
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
						<xsl:value-of select="lang_reset_query_statustext"></xsl:value-of>
						<xsl:text>'; return true;</xsl:text>
					</xsl:attribute>
				</input>
			</td>

			<td class="small_text" valign="top" align="left">
				<xsl:variable name="link_download"><xsl:value-of select="link_download"></xsl:value-of></xsl:variable>
				<xsl:variable name="lang_download_help"><xsl:value-of select="lang_download_help"></xsl:value-of></xsl:variable>
				<xsl:variable name="lang_download"><xsl:value-of select="lang_download"></xsl:value-of></xsl:variable>
				<a href="javascript:var w=window.open('{$link_download}','','left=50,top=100')" onMouseOver="overlib('{$lang_download_help}', CAPTION, '{$lang_download}')" onMouseOut="nd()">
					<xsl:value-of select="lang_download"></xsl:value-of></a>
			</td>

		</tr>
	</xsl:template>

	<xsl:template match="list_gab">
		<xsl:apply-templates select="menu"></xsl:apply-templates> 
		<div align="center">
			<table width="100%" cellpadding="2" cellspacing="2" align="center">
				<xsl:variable name="select_url"><xsl:value-of select="select_action"></xsl:value-of></xsl:variable>
				<form method="post" action="{$select_url}">
					<xsl:apply-templates select="search_field_header"></xsl:apply-templates>
					<xsl:call-template name="search_field"></xsl:call-template>
				</form>
				<tr>
					<td colspan="{colspan}" width="100%">
						<xsl:call-template name="nextmatchs"></xsl:call-template>
					</td>
				</tr>
			</table>
		</div>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:apply-templates select="table_header_gab"></xsl:apply-templates>
			<xsl:choose>
				<xsl:when test="values_gab[gaards_nr]">
					<xsl:apply-templates select="values_gab"></xsl:apply-templates>
				</xsl:when>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="table_add!=''">
					<xsl:apply-templates select="table_add"></xsl:apply-templates>
				</xsl:when>
			</xsl:choose>
		</table>
	</xsl:template>

	<xsl:template match="table_header_gab">
		<xsl:variable name="sort_gab_id"><xsl:value-of select="sort_gab_id"></xsl:value-of></xsl:variable>
		<xsl:variable name="sort_hits"><xsl:value-of select="sort_hits"></xsl:value-of></xsl:variable>
		<xsl:variable name="sort_location_code"><xsl:value-of select="sort_location_code"></xsl:value-of></xsl:variable>
		<tr class="th">
			<td class="th_text" width="10%" align="right">
				<a href="{$sort_gab_id}"><xsl:value-of select="lang_gaards_nr"></xsl:value-of></a>
			</td>
			<td class="th_text" width="10%" align="right">
				<xsl:value-of select="lang_bruksnr"></xsl:value-of>
			</td>
			<td class="th_text" width="10%" align="right">
				<xsl:value-of select="lang_feste_nr"></xsl:value-of>
			</td>
			<td class="th_text" width="10%" align="right">
				<xsl:value-of select="lang_seksjons_nr"></xsl:value-of>
			</td>
			<xsl:choose>
				<xsl:when test="payment_header!=''">
					<td class="th_text" width="8%" align="right">
						<xsl:value-of select="lang_hits"></xsl:value-of>
					</td>
				</xsl:when>
			</xsl:choose>

			<td class="th_text" width="10%" align="right">
				<xsl:value-of select="lang_owner"></xsl:value-of>
			</td>
			<td class="th_text" width="10%" align="right">
				<a href="{$sort_location_code}"><xsl:value-of select="lang_location_code"></xsl:value-of></a>
			</td>
			<td class="th_text" width="20%" align="center">
				<xsl:value-of select="lang_address"></xsl:value-of>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_view"></xsl:value-of>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_map"></xsl:value-of>
			</td>
			<xsl:choose>
				<xsl:when test="payment_header!=''">
					<xsl:for-each select="payment_header">
						<td class="th_text" width="5%" align="center">
							<xsl:value-of select="header"></xsl:value-of>
						</td>
					</xsl:for-each>
				</xsl:when>
			</xsl:choose>
		</tr>
	</xsl:template>

	<xsl:template match="values_gab">
		<xsl:variable name="lang_view_statustext"><xsl:value-of select="lang_view_statustext"></xsl:value-of></xsl:variable>
		<xsl:variable name="lang_edit_statustext"><xsl:value-of select="lang_edit_statustext"></xsl:value-of></xsl:variable>
		<xsl:variable name="lang_delete_statustext"><xsl:value-of select="lang_delete_statustext"></xsl:value-of></xsl:variable>
		<tr>
			<xsl:attribute name="class">
				<xsl:choose>
					<xsl:when test="@class">
						<xsl:value-of select="@class"></xsl:value-of>
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
				<xsl:value-of select="gaards_nr"></xsl:value-of>
			</td>
			<td align="right">
				<xsl:value-of select="bruks_nr"></xsl:value-of>
			</td>
			<td align="right">
				<xsl:value-of select="feste_nr"></xsl:value-of>
			</td>
			<td align="right">
				<xsl:value-of select="seksjons_nr"></xsl:value-of>
			</td>
			<xsl:choose>
				<xsl:when test="payment!=''">
					<td align="right">
						<xsl:value-of select="hits"></xsl:value-of>
					</td>
				</xsl:when>
			</xsl:choose>
			<td align="right">
				<xsl:value-of select="owner"></xsl:value-of>
			</td>
			<td align="right">
				<xsl:value-of select="location_code"></xsl:value-of>
			</td>
			<td align="left">
				<xsl:value-of select="address"></xsl:value-of>
			</td>
			<td align="center">
				<xsl:variable name="link_view"><xsl:value-of select="link_view"></xsl:value-of></xsl:variable>
				<a href="{$link_view}" onMouseover="window.status='{$lang_view_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_view"></xsl:value-of></a>
			</td>
			<td align="left">
				<pre>
					<xsl:variable name="link_map"><xsl:value-of select="link_map"></xsl:value-of></xsl:variable>
					<a href="{$link_map}" target="_blank" onMouseover="window.status='{lang_map_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_map"></xsl:value-of></a>
					<xsl:text> | </xsl:text>
					<xsl:variable name="link_gab"><xsl:value-of select="link_gab"></xsl:value-of></xsl:variable>
					<a href="{$link_gab}" target="_blank" onMouseover="window.status='{lang_gab_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_gab"></xsl:value-of></a>
				</pre>
			</td>

			<xsl:choose>
				<xsl:when test="payment!=''">
					<xsl:for-each select="payment">
						<td align="right">
							<xsl:value-of select="amount"></xsl:value-of>
						</td>
					</xsl:for-each>
				</xsl:when>
			</xsl:choose>

		</tr>
	</xsl:template>

	<xsl:template match="list_gab_detail">
		<xsl:apply-templates select="menu"></xsl:apply-templates> 
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<tr>
				<td class="th_text" width="5%" align="left">
					<table>
						<tr>
							<td class="th_text" align="left">
								<xsl:value-of select="lang_gaards_nr"></xsl:value-of>
							</td>
							<td align="left">
								<xsl:text> : </xsl:text>
								<xsl:value-of select="gaards_nr"></xsl:value-of>
							</td>
						</tr>
						<tr>
							<td class="th_text" align="left">
								<xsl:value-of select="lang_bruksnr"></xsl:value-of>
							</td>
							<td align="left">
								<xsl:text> : </xsl:text>
								<xsl:value-of select="bruks_nr"></xsl:value-of>
							</td>
						</tr>
						<tr>
							<td class="th_text" align="left">
								<xsl:value-of select="lang_feste_nr"></xsl:value-of>
							</td>
							<td align="left">
								<xsl:text> : </xsl:text>
								<xsl:value-of select="feste_nr"></xsl:value-of>
							</td>
						</tr>
						<tr>
							<td class="th_text" align="left">
								<xsl:value-of select="lang_seksjons_nr"></xsl:value-of>
							</td>
							<td align="left">
								<xsl:text> : </xsl:text>
								<xsl:value-of select="seksjons_nr"></xsl:value-of>
							</td>
						</tr>
						<tr>
							<td class="th_text" align="left">
								<xsl:value-of select="lang_owner"></xsl:value-of>
							</td>
							<td align="left">
								<xsl:text> : </xsl:text>
								<xsl:value-of select="value_owner"></xsl:value-of>		
							</td>
						</tr>					
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="8" width="100%">
					<xsl:call-template name="nextmatchs"></xsl:call-template>
				</td>
			</tr>
		</table>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:call-template name="table_header"></xsl:call-template>
			<xsl:choose>
				<xsl:when test="values">
					<xsl:call-template name="values"></xsl:call-template>
				</xsl:when>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="table_add !=''">
					<xsl:apply-templates select="table_add"></xsl:apply-templates>
				</xsl:when>
			</xsl:choose>	
			<xsl:apply-templates select="table_done"></xsl:apply-templates>
		</table>
	</xsl:template>

	<xsl:template match="table_add">
		<tr>
			<td height="50">
				<xsl:variable name="add_action"><xsl:value-of select="add_action"></xsl:value-of></xsl:variable>
				<xsl:variable name="lang_add"><xsl:value-of select="lang_add"></xsl:value-of></xsl:variable>
				<form method="post" action="{$add_action}">
					<input type="submit" name="add" value="{$lang_add}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_add_statustext"></xsl:value-of>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</form>
			</td>
		</tr>
	</xsl:template>

	<xsl:template match="table_done">
		<tr>
			<td height="50">
				<xsl:variable name="done_action"><xsl:value-of select="done_action"></xsl:value-of></xsl:variable>
				<xsl:variable name="lang_done"><xsl:value-of select="lang_done"></xsl:value-of></xsl:variable>
				<form method="post" action="{$done_action}">
					<input type="submit" name="done" value="{$lang_done}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_done_statustext"></xsl:value-of>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</form>
			</td>
		</tr>
	</xsl:template>

<!-- add / edit -->

	<xsl:template match="edit">

		<div align="left">
			<table cellpadding="2" cellspacing="2" width="80%" align="center">
				<xsl:choose>
					<xsl:when test="msgbox_data != ''">
						<tr>
							<td align="left" colspan="3">
								<xsl:call-template name="msgbox"></xsl:call-template>
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>
				<xsl:variable name="form_action"><xsl:value-of select="form_action"></xsl:value-of></xsl:variable>
				<form method="post" name="form" action="{$form_action}">
					<xsl:choose>
						<xsl:when test="gaards_nr&gt;0">
							<tr>
								<td class="th_text" align="left">
									<xsl:value-of select="lang_kommune_nr"></xsl:value-of>
								</td>
								<td align="left">
									<xsl:text> : </xsl:text>
									<xsl:value-of select="kommune_nr"></xsl:value-of>
									<input type="hidden" name="values[kommune_nr]" value="{kommune_nr}"></input>
								</td>
							</tr>
							<tr>
								<td class="th_text" align="left">
									<xsl:value-of select="lang_gaards_nr"></xsl:value-of>
								</td>
								<td align="left">
									<xsl:text> : </xsl:text>
									<xsl:value-of select="gaards_nr"></xsl:value-of>
									<input type="hidden" name="values[gaards_nr]" value="{gaards_nr}"></input>
								</td>
							</tr>
							<tr>
								<td class="th_text" align="left">
									<xsl:value-of select="lang_bruksnr"></xsl:value-of>
								</td>
								<td align="left">
									<xsl:text> : </xsl:text>
									<xsl:value-of select="bruks_nr"></xsl:value-of>
									<input type="hidden" name="values[bruks_nr]" value="{bruks_nr}"></input>
								</td>
							</tr>
							<tr>
								<td class="th_text" align="left">
									<xsl:value-of select="lang_feste_nr"></xsl:value-of>
								</td>
								<td align="left">
									<xsl:text> : </xsl:text>
									<xsl:value-of select="feste_nr"></xsl:value-of>
									<input type="hidden" name="values[feste_nr]" value="{feste_nr}"></input>
								</td>
							</tr>
							<tr>
								<td class="th_text" align="left">
									<xsl:value-of select="lang_seksjons_nr"></xsl:value-of>
								</td>
								<td align="left">
									<xsl:text> : </xsl:text>
									<xsl:value-of select="seksjons_nr"></xsl:value-of>
									<input type="hidden" name="values[seksjons_nr]" value="{seksjons_nr}"></input>
								</td>
							</tr>
						</xsl:when>
						<xsl:otherwise>
							<tr>
								<td class="th_text" align="left">
									<xsl:value-of select="lang_kommune_nr"></xsl:value-of>
								</td>
								<td align="left">
									<input type="text" name="values[kommune_nr]" maxlength="4" size="8" value="{kommune_nr}" onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_kommune_nr_statustext"></xsl:value-of>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
									</input>
								</td>
							</tr>
							<tr>
								<td class="th_text" align="left">
									<xsl:value-of select="lang_gaards_nr"></xsl:value-of>
								</td>
								<td align="left">
									<input type="text" name="values[gaards_nr]" maxlength="5" size="8" onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_gaards_nr_statustext"></xsl:value-of>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
									</input>
								</td>
							</tr>
							<tr>
								<td class="th_text" align="left">
									<xsl:value-of select="lang_bruksnr"></xsl:value-of>
								</td>
								<td align="left">
									<input type="text" name="values[bruks_nr]" maxlength="4" size="8" onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_bruks_nr_statustext"></xsl:value-of>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
									</input>
								</td>
							</tr>
							<tr>
								<td class="th_text" align="left">
									<xsl:value-of select="lang_feste_nr"></xsl:value-of>
								</td>
								<td align="left">
									<input type="text" name="values[feste_nr]" maxlength="4" size="8" onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_feste_nr_statustext"></xsl:value-of>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
									</input>
								</td>
							</tr>
							<tr>
								<td class="th_text" align="left">
									<xsl:value-of select="lang_seksjons_nr"></xsl:value-of>
								</td>
								<td align="left">
									<input type="text" name="values[seksjons_nr]" maxlength="3" size="8" onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_seksjons_nr_statustext"></xsl:value-of>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
									</input>
								</td>
							</tr>
						</xsl:otherwise>
					</xsl:choose>
					<tr>
						<td class="th_text" valign="top">
							<xsl:value-of select="lang_remark"></xsl:value-of>
						</td>
						<td>
							<textarea cols="60" rows="6" name="values[remark]" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_remark_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
								<xsl:value-of select="value_remark"></xsl:value-of>		
							</textarea>
						</td>
					</tr>
					<tr>
						<td class="th_text">
							<xsl:value-of select="lang_owner"></xsl:value-of>
						</td>
						<td>
							<xsl:choose>
								<xsl:when test="value_owner = 'yes'">
									<input type="checkbox" name="values[owner]" value="yes" checked="checked" onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_lookup_form_statustext"></xsl:value-of>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
									</input>
								</xsl:when>
								<xsl:otherwise>
									<input type="checkbox" name="values[owner]" value="yes" onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_lookup_form_statustext"></xsl:value-of>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
									</input>
								</xsl:otherwise>
							</xsl:choose>
						</td>
					</tr>
					<xsl:choose>
						<xsl:when test="lookup_type='form'">
							<xsl:call-template name="location_form"></xsl:call-template>
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_propagate"></xsl:value-of>
								</td>
								<td>
									<input type="checkbox" name="values[propagate]" value="True" onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_propagate_statustext"></xsl:value-of>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
									</input>
								</td>
							</tr>
						</xsl:when>
						<xsl:otherwise>
							<xsl:call-template name="location_view"></xsl:call-template>
						</xsl:otherwise>
					</xsl:choose>

					<tr height="50">
						<td>
							<input type="hidden" name="values[action]" value="{action}"></input>
							<xsl:variable name="lang_save"><xsl:value-of select="lang_save"></xsl:value-of></xsl:variable>
							<input type="submit" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_save_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
				</form>
				<tr>
					<td>
						<xsl:variable name="done_action"><xsl:value-of select="done_action"></xsl:value-of></xsl:variable>
						<xsl:variable name="lang_done"><xsl:value-of select="lang_done"></xsl:value-of></xsl:variable>
						<form method="post" action="{$done_action}">
							<input type="submit" name="done" value="{$lang_done}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_done_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</form>
					</td>
				</tr>
			</table>
		</div>
	</xsl:template>

<!-- view -->
	<xsl:template match="view">
		<div align="left">
			<table cellpadding="2" cellspacing="2" width="80%" align="center">
				<tr>
					<td class="th_text" align="left">
						<xsl:value-of select="lang_kommune_nr"></xsl:value-of>
					</td>
					<td align="left">
						<xsl:text> : </xsl:text>
						<xsl:value-of select="kommune_nr"></xsl:value-of>
						<input type="hidden" name="values[kommune_nr]" value="{kommune_nr}"></input>
					</td>
				</tr>
				<tr>
					<td class="th_text" align="left">
						<xsl:value-of select="lang_gaards_nr"></xsl:value-of>
					</td>
					<td align="left">
						<xsl:text> : </xsl:text>
						<xsl:value-of select="gaards_nr"></xsl:value-of>
						<input type="hidden" name="values[gaards_nr]" value="{gaards_nr}"></input>
					</td>
				</tr>
				<tr>
					<td class="th_text" align="left">
						<xsl:value-of select="lang_bruksnr"></xsl:value-of>
					</td>
					<td align="left">
						<xsl:text> : </xsl:text>
						<xsl:value-of select="bruks_nr"></xsl:value-of>
						<input type="hidden" name="values[bruks_nr]" value="{bruks_nr}"></input>
					</td>
				</tr>
				<tr>
					<td class="th_text" align="left">
						<xsl:value-of select="lang_feste_nr"></xsl:value-of>
					</td>
					<td align="left">
						<xsl:text> : </xsl:text>
						<xsl:value-of select="feste_nr"></xsl:value-of>
						<input type="hidden" name="values[feste_nr]" value="{feste_nr}"></input>
					</td>
				</tr>
				<tr>
					<td class="th_text" align="left">
						<xsl:value-of select="lang_seksjons_nr"></xsl:value-of>
					</td>
					<td align="left">
						<xsl:text> : </xsl:text>
						<xsl:value-of select="seksjons_nr"></xsl:value-of>
						<input type="hidden" name="values[seksjons_nr]" value="{seksjons_nr}"></input>
					</td>
				</tr>
				<tr>
					<td class="th_text" valign="top">
						<xsl:value-of select="lang_remark"></xsl:value-of>
					</td>
					<td>
						<xsl:value-of select="value_remark"></xsl:value-of>		
					</td>
				</tr>
				<tr>
					<td class="th_text">
						<xsl:value-of select="lang_owner"></xsl:value-of>
					</td>
					<td>
						<xsl:value-of select="value_owner"></xsl:value-of>		
					</td>
				</tr>
				<xsl:call-template name="location_view"></xsl:call-template>
				<tr height="50">
					<td>
						<xsl:variable name="done_action"><xsl:value-of select="done_action"></xsl:value-of></xsl:variable>
						<xsl:variable name="lang_done"><xsl:value-of select="lang_done"></xsl:value-of></xsl:variable>
						<form method="post" action="{$done_action}">
							<input type="submit" class="forms" name="done" value="{$lang_done}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_done_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>

						</form>
						<xsl:variable name="edit_action"><xsl:value-of select="edit_action"></xsl:value-of></xsl:variable>
						<xsl:variable name="lang_edit"><xsl:value-of select="lang_edit"></xsl:value-of></xsl:variable>
						<form method="post" action="{$edit_action}">
							<input type="submit" class="forms" name="edit" value="{$lang_edit}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_edit_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</form>
					</td>
				</tr>
			</table>
		</div>
	</xsl:template>
