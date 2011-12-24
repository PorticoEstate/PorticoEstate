<!-- $Id$ -->
<xsl:template name="app_data">
	<xsl:choose>
		<xsl:when test="edit">
			<xsl:apply-templates select="edit"/>
		</xsl:when>
		<xsl:when test="view">
			<xsl:apply-templates select="view"/>
		</xsl:when>
		<xsl:when test="list_basis">
			<xsl:apply-templates select="list_basis"/>
		</xsl:when>
		<xsl:otherwise>
			<xsl:apply-templates select="list"/>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template match="list_basis">
	<xsl:apply-templates select="menu"/>
	<table width="100%" cellpadding="2" cellspacing="2" align="center">
		<xsl:choose>
			<xsl:when test="msgbox_data != ''">
				<tr>
					<td align="left" colspan="3">
						<xsl:call-template name="msgbox"/>
					</td>
				</tr>
			</xsl:when>
		</xsl:choose>
		<tr>
			<td align="left">
				<xsl:call-template name="filter_year"/>
			</td>

			<td align="left">
				<xsl:call-template name="filter_revision"/>
			</td>

			<td align="left">
				<xsl:call-template name="filter_district"/>
			</td>

			<td align="left">
				<xsl:call-template name="filter_grouping"/>
			</td>

			<td align="right">
				<xsl:call-template name="search_field"/>
			</td>
		</tr>
		<tr>
			<td colspan="8" width="100%">
				<xsl:call-template name="nextmatchs"/>
			</td>
		</tr>
	</table>
	<table width="100%" cellpadding="2" cellspacing="2" align="center">
		<xsl:apply-templates select="table_header_budget_basis"/>
		<xsl:apply-templates select="values_budget_basis"/>
		<tr>
			<td>
			</td>
			<td>
			</td>
			<td>
			</td>
			<td>
			</td>
			<td class="th_text" align="right">
				<xsl:value-of select="sum"/>
			</td>
			<td>
			</td>
			<td>
			</td>

		</tr>
		<xsl:apply-templates select="table_add"/>
	</table>
</xsl:template>

<xsl:template match="table_header_budget_basis">
	<xsl:variable name="sort_district_id"><xsl:value-of select="sort_district_id"/></xsl:variable>
	<xsl:variable name="sort_budget_cost"><xsl:value-of select="sort_budget_cost"/></xsl:variable>
	<xsl:variable name="sort_b_account"><xsl:value-of select="sort_b_account"/></xsl:variable>
	<xsl:variable name="sort_grouping"><xsl:value-of select="sort_grouping"/></xsl:variable>
	<tr class="th">
		<td class="th_text" width="5%" align="left">
			<xsl:value-of select="lang_year"/>
		</td>
		<td class="th_text" width="5%" align="left">
			<xsl:value-of select="lang_revision"/>
		</td>
		<td class="th_text" width="10%" align="left">
			<a href="{$sort_grouping}"><xsl:value-of select="lang_grouping"/></a>
		</td>

		<td class="th_text" width="10%" align="left">
			<a href="{$sort_district_id}"><xsl:value-of select="lang_district_id"/></a>
		</td>
		<td class="th_text" width="5%" align="center">
			<a href="{$sort_budget_cost}"><xsl:value-of select="lang_budget_cost"/></a>
		</td>
		<td class="th_text" width="5%" align="center">
			<xsl:value-of select="lang_edit"/>
		</td>
		<td class="th_text" width="5%" align="center">
			<xsl:value-of select="lang_delete"/>
		</td>
	</tr>
</xsl:template>


<xsl:template match="values_budget_basis"> 
	<xsl:variable name="lang_edit_text"><xsl:value-of select="lang_edit_text"/></xsl:variable>
	<xsl:variable name="lang_delete_text"><xsl:value-of select="lang_delete_text"/></xsl:variable>
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
		<td align="left">
			<xsl:value-of select="year"/>
		</td>
		<td>
			<xsl:value-of select="revision"/>
		</td>
		<td>
			<xsl:value-of select="grouping"/>
		</td>

		<td>
			<xsl:value-of select="district_id"/>
		</td>
		<td align="right">
			<xsl:value-of select="budget_cost"/>
		</td>

		<td align="center">
			<xsl:variable name="link_edit"><xsl:value-of select="link_edit"/></xsl:variable>
			<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_text}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_edit"/></a>
		</td>
		<td align="center">
			<xsl:variable name="link_delete"><xsl:value-of select="link_delete"/></xsl:variable>
			<a href="{$link_delete}" onMouseover="window.status='{$lang_delete_text}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_delete"/></a>
		</td>
	</tr>
</xsl:template>


<xsl:template match="list">
	<xsl:apply-templates select="menu"/>
	<table width="100%" cellpadding="2" cellspacing="2" align="center">
		<xsl:choose>
			<xsl:when test="msgbox_data != ''">
				<tr>
					<td align="left" colspan="3">
						<xsl:call-template name="msgbox"/>
					</td>
				</tr>
			</xsl:when>
		</xsl:choose>
		<tr>
			<td align="left">
				<xsl:call-template name="filter_year"/>
			</td>

			<td align="left">
				<xsl:call-template name="filter_revision"/>
			</td>

			<td align="left">
				<xsl:call-template name="filter_district"/>
			</td>

			<td align="left">
				<xsl:call-template name="filter_grouping"/>
			</td>

			<td align="right">
				<xsl:call-template name="search_field"/>
			</td>
		</tr>
		<tr>
			<td colspan="8" width="100%">
				<xsl:call-template name="nextmatchs"/>
			</td>
		</tr>
	</table>
	<table width="100%" cellpadding="2" cellspacing="2" align="center">
		<xsl:apply-templates select="table_header_budget"/>
		<xsl:apply-templates select="values_budget"/>
		<tr>
			<td>
			</td>
			<td>
			</td>
			<td>
			</td>
			<td>
			</td>
			<td>
			</td>
			<td>
			</td>
			<td class="th_text" align="right">
				<xsl:value-of select="sum"/>
			</td>
			<td>
			</td>
			<td>
			</td>

		</tr>
		<xsl:apply-templates select="table_add"/>
	</table>
</xsl:template>



<xsl:template match="table_header_budget">
	<xsl:variable name="sort_district_id"><xsl:value-of select="sort_district_id"/></xsl:variable>
	<xsl:variable name="sort_budget_cost"><xsl:value-of select="sort_budget_cost"/></xsl:variable>
	<xsl:variable name="sort_b_account"><xsl:value-of select="sort_b_account"/></xsl:variable>
	<xsl:variable name="sort_grouping"><xsl:value-of select="sort_grouping"/></xsl:variable>
	<tr class="th">
		<td class="th_text" width="5%" align="left">
			<xsl:value-of select="lang_year"/>
		</td>
		<td class="th_text" width="5%" align="left">
			<xsl:value-of select="lang_revision"/>
		</td>
		<td class="th_text" width="10%" align="left">
			<a href="{$sort_b_account}"><xsl:value-of select="lang_b_account"/></a>
		</td>

		<td class="th_text" width="5%" align="center">
			<xsl:value-of select="lang_name"/>
		</td>
		<td class="th_text" width="10%" align="left">
			<a href="{$sort_grouping}"><xsl:value-of select="lang_grouping"/></a>
		</td>

		<td class="th_text" width="10%" align="left">
			<a href="{$sort_district_id}"><xsl:value-of select="lang_district_id"/></a>
		</td>
		<td class="th_text" width="5%" align="center">
			<a href="{$sort_budget_cost}"><xsl:value-of select="lang_budget_cost"/></a>
		</td>
		<td class="th_text" width="5%" align="center">
			<xsl:value-of select="lang_edit"/>
		</td>
		<td class="th_text" width="5%" align="center">
			<xsl:value-of select="lang_delete"/>
		</td>
	</tr>
</xsl:template>


<xsl:template match="values_budget"> 
	<xsl:variable name="lang_edit_text"><xsl:value-of select="lang_edit_text"/></xsl:variable>
	<xsl:variable name="lang_delete_text"><xsl:value-of select="lang_delete_text"/></xsl:variable>
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
		<td align="left">
			<xsl:value-of select="year"/>
		</td>
		<td>
			<xsl:value-of select="revision"/>
		</td>
		<td>
			<xsl:value-of select="b_account_id"/>
		</td>

		<td>
			<xsl:value-of select="b_account_name"/>
		</td>
		<td>
			<xsl:value-of select="grouping"/>
		</td>

		<td>
			<xsl:value-of select="district_id"/>
		</td>
		<td align="right">
			<xsl:value-of select="budget_cost"/>
		</td>

		<td align="center">
			<xsl:variable name="link_edit"><xsl:value-of select="link_edit"/></xsl:variable>
			<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_text}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_edit"/></a>
		</td>
		<td align="center">
			<xsl:variable name="link_delete"><xsl:value-of select="link_delete"/></xsl:variable>
			<a href="{$link_delete}" onMouseover="window.status='{$lang_delete_text}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_delete"/></a>
		</td>
	</tr>
</xsl:template>


<xsl:template match="table_add">
	<tr>
		<td height="50">
			<xsl:variable name="add_action"><xsl:value-of select="add_action"/></xsl:variable>
			<xsl:variable name="lang_add"><xsl:value-of select="lang_add"/></xsl:variable>
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

<!-- add / edit -->

	<xsl:template match="edit_basis">
		<xsl:variable name="edit_url"><xsl:value-of select="edit_url"/></xsl:variable>
		<div align="left">
			<form name="form" method="post" action="{$edit_url}">
				<table cellpadding="2" cellspacing="2" width="50%" align="center">
					<xsl:choose>
						<xsl:when test="msgbox_data != ''">
							<tr>
								<td align="left" colspan="3">
									<xsl:call-template name="msgbox"/>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="value_budget_id!=''">
							<tr>
								<td width="25%" align="left">
									<xsl:value-of select="lang_budget_id"/>
								</td>
								<td width="75%" align="left">
									<xsl:value-of select="value_budget_id"/>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>

					<tr>
						<td valign="top">
							<xsl:value-of select="lang_year"/>
						</td>
						<xsl:choose>
							<xsl:when test="value_year !=''">
								<td>
									<xsl:value-of select="value_year"/>
								</td>
							</xsl:when>
							<xsl:otherwise>
								<td valign="top">
									<xsl:variable name="lang_year_statustext"><xsl:value-of select="lang_year_statustext"/></xsl:variable>
									<select name="values[year]" class="forms" onMouseover="window.status='{$lang_year_statustext}'; return true;" onMouseout="window.status='';return true;">
										<xsl:apply-templates select="year"/>
									</select>
								</td>
							</xsl:otherwise>
						</xsl:choose>
					</tr>

					<tr>
						<td valign="top">
							<xsl:value-of select="lang_revision"/>
						</td>
						<xsl:choose>
							<xsl:when test="value_revision !=''">
								<td>
									<xsl:value-of select="value_revision"/>
								</td>
							</xsl:when>
							<xsl:otherwise>
								<td valign="top">
									<xsl:variable name="lang_revision_statustext"><xsl:value-of select="lang_revision_statustext"/></xsl:variable>
									<select name="values[revision]" class="forms" onMouseover="window.status='{$lang_revision_statustext}'; return true;" onMouseout="window.status='';return true;">
										<xsl:apply-templates select="revision_list"/>
									</select>
								</td>
							</xsl:otherwise>
						</xsl:choose>
					</tr>

					<tr>
						<td align="left">
							<xsl:value-of select="lang_district"/>
						</td>

						<xsl:choose>
							<xsl:when test="value_district_id !=''">
								<td>
									<xsl:value-of select="value_district_id"/>
								</td>
							</xsl:when>
							<xsl:otherwise>
								<td align="left">
									<xsl:call-template name="select_district"/>
								</td>
							</xsl:otherwise>
						</xsl:choose>
					</tr>
					<xsl:call-template name="ecodimb_form"/>
					<tr>
						<tr>
							<td>
								<xsl:value-of select="lang_category"/>
							</td>
							<td>
								<xsl:call-template name="categories"/>
							</td>
						</tr>

						<td valign="top">
							<xsl:value-of select="lang_b_group"/>
						</td>
						<xsl:choose>
							<xsl:when test="value_b_group !=''">
								<td>
									<xsl:value-of select="value_b_group"/>
								</td>
							</xsl:when>
							<xsl:otherwise>
								<td valign="top">
									<xsl:variable name="lang_b_group_statustext"><xsl:value-of select="lang_b_group_statustext"/></xsl:variable>
									<select name="values[b_group]" class="forms" onMouseover="window.status='{$lang_b_group_statustext}'; return true;" onMouseout="window.status='';return true;">
										<xsl:apply-templates select="b_group_list"/>
									</select>
								</td>
							</xsl:otherwise>
						</xsl:choose>

					</tr>

					<tr align="left">
						<td valign="top">
							<xsl:value-of select="lang_budget_cost"/>
						</td>
						<td align="left">
							<input type="text" name="values[budget_cost]" value="{value_budget_cost}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_budget_cost_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr align="left">
						<td valign="top">
							<xsl:value-of select="lang_remark"/>
						</td>
						<td align="left">

							<textarea cols="60" rows="6" name="values[remark]" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_remark_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
								<xsl:value-of select="value_remark"/>		
							</textarea>
						</td>
					</tr>

					<tr align="left">
						<td valign="top">
							<xsl:value-of select="lang_distribute"/>
						</td>
						<td align="left" valign="top">
							<input type="checkbox" name="values[distribute][]" value="{value_distribute_id}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_distribute_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr align="left">
						<td valign="top">
							<xsl:value-of select="lang_distribute_year"/>
						</td>
						<td align="left" valign="top">
							<xsl:variable name="lang_distribute_year_statustext"><xsl:value-of select="lang_distribute_year_statustext"/></xsl:variable>
							<select name="values[distribute_year][]" class="forms" multiple="multiple" onMouseover="window.status='{$lang_distribute_year_statustext}'; return true;" onMouseout="window.status='';return true;">
								<xsl:apply-templates select="distribute_year_list"/>
							</select>

						</td>
					</tr>



					<tr height="50">
						<td valign="bottom">
							<xsl:variable name="lang_save"><xsl:value-of select="lang_save"/></xsl:variable>
							<input type="submit" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_save_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
						<td valign="bottom">
							<xsl:variable name="lang_apply"><xsl:value-of select="lang_apply"/></xsl:variable>
							<input type="submit" name="values[apply]" value="{$lang_apply}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_apply_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
						<td align="right" valign="bottom">
							<xsl:variable name="lang_cancel"><xsl:value-of select="lang_cancel"/></xsl:variable>
							<input type="submit" name="values[cancel]" value="{$lang_cancel}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_cancel_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
				</table>
			</form>
		</div>
	</xsl:template>

	<xsl:template match="edit">
		<xsl:variable name="edit_url"><xsl:value-of select="edit_url"/></xsl:variable>
		<div align="left">
			<form name="form" method="post" action="{$edit_url}">
				<table cellpadding="2" cellspacing="2" width="50%" align="center">
					<xsl:choose>
						<xsl:when test="msgbox_data != ''">
							<tr>
								<td align="left" colspan="3">
									<xsl:call-template name="msgbox"/>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="value_budget_id!=''">
							<tr>
								<td width="25%" align="left">
									<xsl:value-of select="lang_budget_id"/>
								</td>
								<td width="75%" align="left">
									<xsl:value-of select="value_budget_id"/>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>

					<tr>
						<td valign="top">
							<xsl:value-of select="lang_year"/>
						</td>
						<xsl:choose>
							<xsl:when test="value_year !=''">
								<td>
									<xsl:value-of select="value_year"/>
								</td>
							</xsl:when>
							<xsl:otherwise>
								<td valign="top">
									<xsl:variable name="lang_year_statustext"><xsl:value-of select="lang_year_statustext"/></xsl:variable>
									<select name="values[year]" class="forms" onMouseover="window.status='{$lang_year_statustext}'; return true;" onMouseout="window.status='';return true;">
										<xsl:apply-templates select="year"/>
									</select>
								</td>
							</xsl:otherwise>
						</xsl:choose>
					</tr>

					<tr>
						<td valign="top">
							<xsl:value-of select="lang_revision"/>
						</td>
						<xsl:choose>
							<xsl:when test="value_revision !=''">
								<td>
									<xsl:value-of select="value_revision"/>
								</td>
							</xsl:when>
							<xsl:otherwise>
								<td valign="top">
									<xsl:variable name="lang_revision_statustext"><xsl:value-of select="lang_revision_statustext"/></xsl:variable>
									<select name="values[revision]" class="forms" onMouseover="window.status='{$lang_revision_statustext}'; return true;" onMouseout="window.status='';return true;">
										<xsl:apply-templates select="revision_list"/>
									</select>
								</td>
							</xsl:otherwise>
						</xsl:choose>
					</tr>

					<tr>
						<td align="left">
							<xsl:value-of select="lang_district"/>
						</td>

						<xsl:choose>
							<xsl:when test="value_district_id !=''">
								<td>
									<xsl:value-of select="value_district_id"/>
								</td>
							</xsl:when>
							<xsl:otherwise>
								<td align="left">
									<xsl:call-template name="select_district"/>
								</td>
							</xsl:otherwise>
						</xsl:choose>
					</tr>

					<xsl:call-template name="ecodimb_form"/>
					<tr>
						<td>
							<xsl:value-of select="lang_category"/>
						</td>
						<td>
							<xsl:call-template name="categories"/>
						</td>
					</tr>


					<!--		<tr>
				<td valign="top">
					<xsl:value-of select="lang_b_group"/>
				</td>
				<xsl:choose>
					<xsl:when test="value_b_group !=''">
						<td>
							<xsl:value-of select="value_b_group"/>
						</td>
					</xsl:when>
					<xsl:otherwise>
						<td valign="top">
							<xsl:variable name="lang_b_group_statustext"><xsl:value-of select="lang_b_group_statustext"/></xsl:variable>
							<select name="values[b_group]" class="forms" onMouseover="window.status='{$lang_b_group_statustext}'; return true;" onMouseout="window.status='';return true;">
								<xsl:apply-templates select="b_group_list"/>
							</select>
						</td>
					</xsl:otherwise>
				</xsl:choose>

			</tr>
	-->
				<xsl:choose>
					<xsl:when test="value_b_account &gt; 0">
						<xsl:call-template name="b_account_view"/>
						<input type="hidden" name="b_account_id" value="{value_b_account}"/>
					</xsl:when>
					<xsl:otherwise>
						<xsl:call-template name="b_account_form"/>
					</xsl:otherwise>
				</xsl:choose>

				<tr align="left">
					<td valign="top">
						<xsl:value-of select="lang_budget_cost"/>
					</td>
					<td align="left">
						<input type="text" name="values[budget_cost]" value="{value_budget_cost}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_budget_cost_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</td>
				</tr>
				<tr align="left">
					<td valign="top">
						<xsl:value-of select="lang_remark"/>
					</td>
					<td align="left">

						<textarea cols="60" rows="6" name="values[remark]" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_remark_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
							<xsl:value-of select="value_remark"/>		
						</textarea>
					</td>
				</tr>

				<tr height="50">
					<td valign="bottom">
						<xsl:variable name="lang_save"><xsl:value-of select="lang_save"/></xsl:variable>
						<input type="submit" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_save_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</td>
					<td valign="bottom">
						<xsl:variable name="lang_apply"><xsl:value-of select="lang_apply"/></xsl:variable>
						<input type="submit" name="values[apply]" value="{$lang_apply}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_apply_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</td>
					<td align="right" valign="bottom">
						<xsl:variable name="lang_cancel"><xsl:value-of select="lang_cancel"/></xsl:variable>
						<input type="submit" name="values[cancel]" value="{$lang_cancel}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_cancel_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</td>
				</tr>
			</table>
		</form>
	</div>
</xsl:template>

<!-- view -->

	<xsl:template match="view">
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<tr>
				<td class="small_text" valign="top" align="right">
					<xsl:variable name="link_download"><xsl:value-of select="link_download"/></xsl:variable>
					<xsl:variable name="lang_download_help"><xsl:value-of select="lang_download_help"/></xsl:variable>
					<xsl:variable name="lang_download"><xsl:value-of select="lang_download"/></xsl:variable>
					<a href="javascript:var w=window.open('{$link_download}','','left=50,top=100')" onMouseOver="overlib('{$lang_download_help}', CAPTION, '{$lang_download}')" onMouseOut="nd()">
						<xsl:value-of select="lang_download"/></a>
				</td>
			</tr>
			<tr>
				<td colspan="3" width="100%">
					<xsl:call-template name="nextmatchs"/>
				</td>
			</tr>
		</table>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:call-template name="table_header"/>
			<xsl:call-template name="values"/>
			<tr height="50">
				<td>
					<xsl:variable name="done_action"><xsl:value-of select="done_action"/></xsl:variable>
					<xsl:variable name="lang_done"><xsl:value-of select="lang_done"/></xsl:variable>
					<form method="post" action="{$done_action}">
						<input type="submit" class="forms" name="done" value="{$lang_done}" onMouseover="window.status='Back to the list.';return true;" onMouseout="window.status='';return true;"/>
					</form>
				</td>
			</tr>
		</table>
	</xsl:template>


	<xsl:template match="list_obligations">
		<xsl:apply-templates select="menu"/>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:choose>
				<xsl:when test="msgbox_data != ''">
					<tr>
						<td align="left" colspan="3">
							<xsl:call-template name="msgbox"/>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<tr>
				<td align="left">
					<xsl:call-template name="filter_year"/>
				</td>

				<td align="left">
					<xsl:call-template name="filter_district"/>
				</td>
				<td>
					<xsl:call-template name="categories"/>
				</td>
				<td align="left">
					<xsl:call-template name="filter_grouping"/>
				</td>
				<td align="right">
					<xsl:call-template name="search_field"/>
				</td>
			</tr>
			<tr>
				<td colspan="8" width="100%">
					<xsl:call-template name="nextmatchs"/>
				</td>
			</tr>
		</table>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:apply-templates select="table_header_budget_obligations"/>
			<xsl:apply-templates select="values_budget_obligations"/>
			<tr>
				<td>
				</td>
				<td>
				</td>
				<td class="th_text" align="right">
					<xsl:value-of select="sum_hits"/>
				</td>
				<td class="th_text" align="right">
					<xsl:value-of select="sum_budget_cost"/>
				</td>
				<td class="th_text" align="right">
					<xsl:value-of select="sum_obligation"/>
				</td>
				<td class="th_text" align="right">
					<xsl:value-of select="sum_actual_cost"/>
				</td>
				<td class="th_text" align="right">
					<xsl:value-of select="sum_diff"/>
				</td>
			</tr>
		</table>
	</xsl:template>

	<xsl:template match="table_header_budget_obligations">
		<xsl:variable name="sort_grouping"><xsl:value-of select="sort_grouping"/></xsl:variable>
		<tr class="th">
			<td class="th_text" width="10%" align="left">
				<a href="{$sort_grouping}"><xsl:value-of select="lang_grouping"/></a>
			</td>

			<td class="th_text" width="1%" align="left">
				<xsl:value-of select="lang_district_id"/>
			</td>
			<td class="th_text" width="1%" align="right">
				<xsl:value-of select="lang_hits"/>
			</td>

			<td class="th_text" width="10%" align="center">
				<xsl:value-of select="lang_budget_cost"/>
			</td>

			<td class="th_text" width="10%" align="right">
				<xsl:value-of select="lang_obligations"/>
			</td>

			<td class="th_text" width="10%" align="center">
				<xsl:value-of select="lang_actual_cost"/>
			</td>
			<td class="th_text" width="10%" align="center">
				<xsl:value-of select="lang_diff"/>
			</td>
		</tr>
	</xsl:template>


	<xsl:template match="values_budget_obligations"> 
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
			<td>
				<a href="{link_b_account}"><xsl:value-of select="b_account"/></a>
			</td>

			<td>
				<xsl:value-of select="district_id"/>
			</td>
			<td align="right">
				<xsl:value-of select="hits"/>
			</td>

			<td align="right">
				<xsl:value-of select="budget_cost"/>
			</td>

			<td align="right">
				<xsl:variable name="link_obligation"><xsl:value-of select="link_obligation"/></xsl:variable>
				<a href="{$link_obligation}" onMouseover="window.status='{obligation}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="obligation"/></a>
			</td>

			<td align="right">
				<xsl:variable name="link_actual_cost"><xsl:value-of select="link_actual_cost"/></xsl:variable>
				<a href="{$link_actual_cost}" onMouseover="window.status='{actual_cost}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="actual_cost"/></a>
			</td>

			<td align="right">
				<xsl:value-of select="diff"/>
			</td>
		</tr>
	</xsl:template>



	<xsl:template match="year">
		<xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected='selected'">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="id"/></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="id"/></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="b_group_list">
		<xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected='selected'">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="revision_list">
		<xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected='selected'">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template name="filter_year">
		<xsl:variable name="select_action"><xsl:value-of select="select_action"/></xsl:variable>
		<xsl:variable name="lang_submit"><xsl:value-of select="lang_submit"/></xsl:variable>
		<form method="post" action="{$select_action}">
			<select name="year" onChange="this.form.submit();" onMouseout="window.status='';return true;">
				<xsl:attribute name="onMouseover">
					<xsl:text>window.status='</xsl:text>
					<xsl:value-of select="lang_year_statustext"/>
					<xsl:text>'; return true;</xsl:text>
				</xsl:attribute>
				<option value=""><xsl:value-of select="lang_no_year"/></option>
				<xsl:apply-templates select="year_list"/>
			</select>
			<noscript>
				<xsl:text> </xsl:text>
				<input type="submit" name="submit" value="{$lang_submit}"/>
			</noscript>
		</form>
	</xsl:template>

	<xsl:template match="year_list">
		<xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>


	<xsl:template name="filter_revision">
		<xsl:variable name="select_action"><xsl:value-of select="select_action"/></xsl:variable>
		<xsl:variable name="lang_submit"><xsl:value-of select="lang_submit"/></xsl:variable>
		<form method="post" action="{$select_action}">
			<select name="revision" onChange="this.form.submit();" onMouseout="window.status='';return true;">
				<xsl:attribute name="onMouseover">
					<xsl:text>window.status='</xsl:text>
					<xsl:value-of select="lang_revision_statustext"/>
					<xsl:text>'; return true;</xsl:text>
				</xsl:attribute>
				<option value=""><xsl:value-of select="lang_no_revision"/></option>
				<xsl:apply-templates select="revision_list"/>
			</select>
			<noscript>
				<xsl:text> </xsl:text>
				<input type="submit" name="submit" value="{$lang_submit}"/>
			</noscript>
		</form>
	</xsl:template>

	<xsl:template match="revision_list">
		<xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template name="filter_grouping">
		<xsl:variable name="select_action"><xsl:value-of select="select_action"/></xsl:variable>
		<xsl:variable name="lang_submit"><xsl:value-of select="lang_submit"/></xsl:variable>
		<form method="post" action="{$select_action}">
			<select name="grouping" onChange="this.form.submit();" onMouseout="window.status='';return true;">
				<xsl:attribute name="onMouseover">
					<xsl:text>window.status='</xsl:text>
					<xsl:value-of select="lang_grouping_statustext"/>
					<xsl:text>'; return true;</xsl:text>
				</xsl:attribute>
				<option value=""><xsl:value-of select="lang_no_grouping"/></option>
				<xsl:apply-templates select="grouping_list"/>
			</select>
			<noscript>
				<xsl:text> </xsl:text>
				<input type="submit" name="submit" value="{$lang_submit}"/>
			</noscript>
		</form>
	</xsl:template>

	<xsl:template match="grouping_list">
		<xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="distribute_year_list">
		<xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
