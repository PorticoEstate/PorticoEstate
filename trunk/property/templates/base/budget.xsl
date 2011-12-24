<!-- $Id$ -->
<xsl:template name="app_data">
	<xsl:choose>
		<xsl:when test="edit">
			<xsl:apply-templates select="edit"></xsl:apply-templates>
		</xsl:when>
		<xsl:when test="view">
			<xsl:apply-templates select="view"></xsl:apply-templates>
		</xsl:when>
		<xsl:when test="list_basis">
			<xsl:apply-templates select="list_basis"></xsl:apply-templates>
		</xsl:when>
		<xsl:otherwise>
			<xsl:apply-templates select="list"></xsl:apply-templates>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template match="list_basis">
	<xsl:apply-templates select="menu"></xsl:apply-templates>
	<table width="100%" cellpadding="2" cellspacing="2" align="center">
		<xsl:choose>
			<xsl:when test="msgbox_data != ''">
				<tr>
					<td align="left" colspan="3">
						<xsl:call-template name="msgbox"></xsl:call-template>
					</td>
				</tr>
			</xsl:when>
		</xsl:choose>
		<tr>
			<td align="left">
				<xsl:call-template name="filter_year"></xsl:call-template>
			</td>

			<td align="left">
				<xsl:call-template name="filter_revision"></xsl:call-template>
			</td>

			<td align="left">
				<xsl:call-template name="filter_district"></xsl:call-template>
			</td>

			<td align="left">
				<xsl:call-template name="filter_grouping"></xsl:call-template>
			</td>

			<td align="right">
				<xsl:call-template name="search_field"></xsl:call-template>
			</td>
		</tr>
		<tr>
			<td colspan="8" width="100%">
				<xsl:call-template name="nextmatchs"></xsl:call-template>
			</td>
		</tr>
	</table>
	<table width="100%" cellpadding="2" cellspacing="2" align="center">
		<xsl:apply-templates select="table_header_budget_basis"></xsl:apply-templates>
		<xsl:apply-templates select="values_budget_basis"></xsl:apply-templates>
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
				<xsl:value-of select="sum"></xsl:value-of>
			</td>
			<td>
			</td>
			<td>
			</td>

		</tr>
		<xsl:apply-templates select="table_add"></xsl:apply-templates>
	</table>
</xsl:template>

<xsl:template match="table_header_budget_basis">
	<xsl:variable name="sort_district_id"><xsl:value-of select="sort_district_id"></xsl:value-of></xsl:variable>
	<xsl:variable name="sort_budget_cost"><xsl:value-of select="sort_budget_cost"></xsl:value-of></xsl:variable>
	<xsl:variable name="sort_b_account"><xsl:value-of select="sort_b_account"></xsl:value-of></xsl:variable>
	<xsl:variable name="sort_grouping"><xsl:value-of select="sort_grouping"></xsl:value-of></xsl:variable>
	<tr class="th">
		<td class="th_text" width="5%" align="left">
			<xsl:value-of select="lang_year"></xsl:value-of>
		</td>
		<td class="th_text" width="5%" align="left">
			<xsl:value-of select="lang_revision"></xsl:value-of>
		</td>
		<td class="th_text" width="10%" align="left">
			<a href="{$sort_grouping}"><xsl:value-of select="lang_grouping"></xsl:value-of></a>
		</td>

		<td class="th_text" width="10%" align="left">
			<a href="{$sort_district_id}"><xsl:value-of select="lang_district_id"></xsl:value-of></a>
		</td>
		<td class="th_text" width="5%" align="center">
			<a href="{$sort_budget_cost}"><xsl:value-of select="lang_budget_cost"></xsl:value-of></a>
		</td>
		<td class="th_text" width="5%" align="center">
			<xsl:value-of select="lang_edit"></xsl:value-of>
		</td>
		<td class="th_text" width="5%" align="center">
			<xsl:value-of select="lang_delete"></xsl:value-of>
		</td>
	</tr>
</xsl:template>


<xsl:template match="values_budget_basis"> 
	<xsl:variable name="lang_edit_text"><xsl:value-of select="lang_edit_text"></xsl:value-of></xsl:variable>
	<xsl:variable name="lang_delete_text"><xsl:value-of select="lang_delete_text"></xsl:value-of></xsl:variable>
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
		<td align="left">
			<xsl:value-of select="year"></xsl:value-of>
		</td>
		<td>
			<xsl:value-of select="revision"></xsl:value-of>
		</td>
		<td>
			<xsl:value-of select="grouping"></xsl:value-of>
		</td>

		<td>
			<xsl:value-of select="district_id"></xsl:value-of>
		</td>
		<td align="right">
			<xsl:value-of select="budget_cost"></xsl:value-of>
		</td>

		<td align="center">
			<xsl:variable name="link_edit"><xsl:value-of select="link_edit"></xsl:value-of></xsl:variable>
			<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_text}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_edit"></xsl:value-of></a>
		</td>
		<td align="center">
			<xsl:variable name="link_delete"><xsl:value-of select="link_delete"></xsl:value-of></xsl:variable>
			<a href="{$link_delete}" onMouseover="window.status='{$lang_delete_text}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_delete"></xsl:value-of></a>
		</td>
	</tr>
</xsl:template>


<xsl:template match="list">
	<xsl:apply-templates select="menu"></xsl:apply-templates>
	<table width="100%" cellpadding="2" cellspacing="2" align="center">
		<xsl:choose>
			<xsl:when test="msgbox_data != ''">
				<tr>
					<td align="left" colspan="3">
						<xsl:call-template name="msgbox"></xsl:call-template>
					</td>
				</tr>
			</xsl:when>
		</xsl:choose>
		<tr>
			<td align="left">
				<xsl:call-template name="filter_year"></xsl:call-template>
			</td>

			<td align="left">
				<xsl:call-template name="filter_revision"></xsl:call-template>
			</td>

			<td align="left">
				<xsl:call-template name="filter_district"></xsl:call-template>
			</td>

			<td align="left">
				<xsl:call-template name="filter_grouping"></xsl:call-template>
			</td>

			<td align="right">
				<xsl:call-template name="search_field"></xsl:call-template>
			</td>
		</tr>
		<tr>
			<td colspan="8" width="100%">
				<xsl:call-template name="nextmatchs"></xsl:call-template>
			</td>
		</tr>
	</table>
	<table width="100%" cellpadding="2" cellspacing="2" align="center">
		<xsl:apply-templates select="table_header_budget"></xsl:apply-templates>
		<xsl:apply-templates select="values_budget"></xsl:apply-templates>
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
				<xsl:value-of select="sum"></xsl:value-of>
			</td>
			<td>
			</td>
			<td>
			</td>

		</tr>
		<xsl:apply-templates select="table_add"></xsl:apply-templates>
	</table>
</xsl:template>



<xsl:template match="table_header_budget">
	<xsl:variable name="sort_district_id"><xsl:value-of select="sort_district_id"></xsl:value-of></xsl:variable>
	<xsl:variable name="sort_budget_cost"><xsl:value-of select="sort_budget_cost"></xsl:value-of></xsl:variable>
	<xsl:variable name="sort_b_account"><xsl:value-of select="sort_b_account"></xsl:value-of></xsl:variable>
	<xsl:variable name="sort_grouping"><xsl:value-of select="sort_grouping"></xsl:value-of></xsl:variable>
	<tr class="th">
		<td class="th_text" width="5%" align="left">
			<xsl:value-of select="lang_year"></xsl:value-of>
		</td>
		<td class="th_text" width="5%" align="left">
			<xsl:value-of select="lang_revision"></xsl:value-of>
		</td>
		<td class="th_text" width="10%" align="left">
			<a href="{$sort_b_account}"><xsl:value-of select="lang_b_account"></xsl:value-of></a>
		</td>

		<td class="th_text" width="5%" align="center">
			<xsl:value-of select="lang_name"></xsl:value-of>
		</td>
		<td class="th_text" width="10%" align="left">
			<a href="{$sort_grouping}"><xsl:value-of select="lang_grouping"></xsl:value-of></a>
		</td>

		<td class="th_text" width="10%" align="left">
			<a href="{$sort_district_id}"><xsl:value-of select="lang_district_id"></xsl:value-of></a>
		</td>
		<td class="th_text" width="5%" align="center">
			<a href="{$sort_budget_cost}"><xsl:value-of select="lang_budget_cost"></xsl:value-of></a>
		</td>
		<td class="th_text" width="5%" align="center">
			<xsl:value-of select="lang_edit"></xsl:value-of>
		</td>
		<td class="th_text" width="5%" align="center">
			<xsl:value-of select="lang_delete"></xsl:value-of>
		</td>
	</tr>
</xsl:template>


<xsl:template match="values_budget"> 
	<xsl:variable name="lang_edit_text"><xsl:value-of select="lang_edit_text"></xsl:value-of></xsl:variable>
	<xsl:variable name="lang_delete_text"><xsl:value-of select="lang_delete_text"></xsl:value-of></xsl:variable>
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
		<td align="left">
			<xsl:value-of select="year"></xsl:value-of>
		</td>
		<td>
			<xsl:value-of select="revision"></xsl:value-of>
		</td>
		<td>
			<xsl:value-of select="b_account_id"></xsl:value-of>
		</td>

		<td>
			<xsl:value-of select="b_account_name"></xsl:value-of>
		</td>
		<td>
			<xsl:value-of select="grouping"></xsl:value-of>
		</td>

		<td>
			<xsl:value-of select="district_id"></xsl:value-of>
		</td>
		<td align="right">
			<xsl:value-of select="budget_cost"></xsl:value-of>
		</td>

		<td align="center">
			<xsl:variable name="link_edit"><xsl:value-of select="link_edit"></xsl:value-of></xsl:variable>
			<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_text}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_edit"></xsl:value-of></a>
		</td>
		<td align="center">
			<xsl:variable name="link_delete"><xsl:value-of select="link_delete"></xsl:value-of></xsl:variable>
			<a href="{$link_delete}" onMouseover="window.status='{$lang_delete_text}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_delete"></xsl:value-of></a>
		</td>
	</tr>
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

<!-- add / edit -->

	<xsl:template match="edit_basis">
		<xsl:variable name="edit_url"><xsl:value-of select="edit_url"></xsl:value-of></xsl:variable>
		<div align="left">
			<form name="form" method="post" action="{$edit_url}">
				<table cellpadding="2" cellspacing="2" width="50%" align="center">
					<xsl:choose>
						<xsl:when test="msgbox_data != ''">
							<tr>
								<td align="left" colspan="3">
									<xsl:call-template name="msgbox"></xsl:call-template>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="value_budget_id!=''">
							<tr>
								<td width="25%" align="left">
									<xsl:value-of select="lang_budget_id"></xsl:value-of>
								</td>
								<td width="75%" align="left">
									<xsl:value-of select="value_budget_id"></xsl:value-of>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>

					<tr>
						<td valign="top">
							<xsl:value-of select="lang_year"></xsl:value-of>
						</td>
						<xsl:choose>
							<xsl:when test="value_year !=''">
								<td>
									<xsl:value-of select="value_year"></xsl:value-of>
								</td>
							</xsl:when>
							<xsl:otherwise>
								<td valign="top">
									<xsl:variable name="lang_year_statustext"><xsl:value-of select="lang_year_statustext"></xsl:value-of></xsl:variable>
									<select name="values[year]" class="forms" onMouseover="window.status='{$lang_year_statustext}'; return true;" onMouseout="window.status='';return true;">
										<xsl:apply-templates select="year"></xsl:apply-templates>
									</select>
								</td>
							</xsl:otherwise>
						</xsl:choose>
					</tr>

					<tr>
						<td valign="top">
							<xsl:value-of select="lang_revision"></xsl:value-of>
						</td>
						<xsl:choose>
							<xsl:when test="value_revision !=''">
								<td>
									<xsl:value-of select="value_revision"></xsl:value-of>
								</td>
							</xsl:when>
							<xsl:otherwise>
								<td valign="top">
									<xsl:variable name="lang_revision_statustext"><xsl:value-of select="lang_revision_statustext"></xsl:value-of></xsl:variable>
									<select name="values[revision]" class="forms" onMouseover="window.status='{$lang_revision_statustext}'; return true;" onMouseout="window.status='';return true;">
										<xsl:apply-templates select="revision_list"></xsl:apply-templates>
									</select>
								</td>
							</xsl:otherwise>
						</xsl:choose>
					</tr>

					<tr>
						<td align="left">
							<xsl:value-of select="lang_district"></xsl:value-of>
						</td>

						<xsl:choose>
							<xsl:when test="value_district_id !=''">
								<td>
									<xsl:value-of select="value_district_id"></xsl:value-of>
								</td>
							</xsl:when>
							<xsl:otherwise>
								<td align="left">
									<xsl:call-template name="select_district"></xsl:call-template>
								</td>
							</xsl:otherwise>
						</xsl:choose>
					</tr>
					<xsl:call-template name="ecodimb_form"></xsl:call-template>
					<tr>
						<tr>
							<td>
								<xsl:value-of select="lang_category"></xsl:value-of>
							</td>
							<td>
								<xsl:call-template name="categories"></xsl:call-template>
							</td>
						</tr>

						<td valign="top">
							<xsl:value-of select="lang_b_group"></xsl:value-of>
						</td>
						<xsl:choose>
							<xsl:when test="value_b_group !=''">
								<td>
									<xsl:value-of select="value_b_group"></xsl:value-of>
								</td>
							</xsl:when>
							<xsl:otherwise>
								<td valign="top">
									<xsl:variable name="lang_b_group_statustext"><xsl:value-of select="lang_b_group_statustext"></xsl:value-of></xsl:variable>
									<select name="values[b_group]" class="forms" onMouseover="window.status='{$lang_b_group_statustext}'; return true;" onMouseout="window.status='';return true;">
										<xsl:apply-templates select="b_group_list"></xsl:apply-templates>
									</select>
								</td>
							</xsl:otherwise>
						</xsl:choose>

					</tr>

					<tr align="left">
						<td valign="top">
							<xsl:value-of select="lang_budget_cost"></xsl:value-of>
						</td>
						<td align="left">
							<input type="text" name="values[budget_cost]" value="{value_budget_cost}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_budget_cost_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr align="left">
						<td valign="top">
							<xsl:value-of select="lang_remark"></xsl:value-of>
						</td>
						<td align="left">

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

					<tr align="left">
						<td valign="top">
							<xsl:value-of select="lang_distribute"></xsl:value-of>
						</td>
						<td align="left" valign="top">
							<input type="checkbox" name="values[distribute][]" value="{value_distribute_id}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_distribute_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr align="left">
						<td valign="top">
							<xsl:value-of select="lang_distribute_year"></xsl:value-of>
						</td>
						<td align="left" valign="top">
							<xsl:variable name="lang_distribute_year_statustext"><xsl:value-of select="lang_distribute_year_statustext"></xsl:value-of></xsl:variable>
							<select name="values[distribute_year][]" class="forms" multiple="multiple" onMouseover="window.status='{$lang_distribute_year_statustext}'; return true;" onMouseout="window.status='';return true;">
								<xsl:apply-templates select="distribute_year_list"></xsl:apply-templates>
							</select>

						</td>
					</tr>



					<tr height="50">
						<td valign="bottom">
							<xsl:variable name="lang_save"><xsl:value-of select="lang_save"></xsl:value-of></xsl:variable>
							<input type="submit" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_save_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
						<td valign="bottom">
							<xsl:variable name="lang_apply"><xsl:value-of select="lang_apply"></xsl:value-of></xsl:variable>
							<input type="submit" name="values[apply]" value="{$lang_apply}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_apply_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
						<td align="right" valign="bottom">
							<xsl:variable name="lang_cancel"><xsl:value-of select="lang_cancel"></xsl:value-of></xsl:variable>
							<input type="submit" name="values[cancel]" value="{$lang_cancel}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_cancel_statustext"></xsl:value-of>
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
		<xsl:variable name="edit_url"><xsl:value-of select="edit_url"></xsl:value-of></xsl:variable>
		<div align="left">
			<form name="form" method="post" action="{$edit_url}">
				<table cellpadding="2" cellspacing="2" width="50%" align="center">
					<xsl:choose>
						<xsl:when test="msgbox_data != ''">
							<tr>
								<td align="left" colspan="3">
									<xsl:call-template name="msgbox"></xsl:call-template>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="value_budget_id!=''">
							<tr>
								<td width="25%" align="left">
									<xsl:value-of select="lang_budget_id"></xsl:value-of>
								</td>
								<td width="75%" align="left">
									<xsl:value-of select="value_budget_id"></xsl:value-of>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>

					<tr>
						<td valign="top">
							<xsl:value-of select="lang_year"></xsl:value-of>
						</td>
						<xsl:choose>
							<xsl:when test="value_year !=''">
								<td>
									<xsl:value-of select="value_year"></xsl:value-of>
								</td>
							</xsl:when>
							<xsl:otherwise>
								<td valign="top">
									<xsl:variable name="lang_year_statustext"><xsl:value-of select="lang_year_statustext"></xsl:value-of></xsl:variable>
									<select name="values[year]" class="forms" onMouseover="window.status='{$lang_year_statustext}'; return true;" onMouseout="window.status='';return true;">
										<xsl:apply-templates select="year"></xsl:apply-templates>
									</select>
								</td>
							</xsl:otherwise>
						</xsl:choose>
					</tr>

					<tr>
						<td valign="top">
							<xsl:value-of select="lang_revision"></xsl:value-of>
						</td>
						<xsl:choose>
							<xsl:when test="value_revision !=''">
								<td>
									<xsl:value-of select="value_revision"></xsl:value-of>
								</td>
							</xsl:when>
							<xsl:otherwise>
								<td valign="top">
									<xsl:variable name="lang_revision_statustext"><xsl:value-of select="lang_revision_statustext"></xsl:value-of></xsl:variable>
									<select name="values[revision]" class="forms" onMouseover="window.status='{$lang_revision_statustext}'; return true;" onMouseout="window.status='';return true;">
										<xsl:apply-templates select="revision_list"></xsl:apply-templates>
									</select>
								</td>
							</xsl:otherwise>
						</xsl:choose>
					</tr>

					<tr>
						<td align="left">
							<xsl:value-of select="lang_district"></xsl:value-of>
						</td>

						<xsl:choose>
							<xsl:when test="value_district_id !=''">
								<td>
									<xsl:value-of select="value_district_id"></xsl:value-of>
								</td>
							</xsl:when>
							<xsl:otherwise>
								<td align="left">
									<xsl:call-template name="select_district"></xsl:call-template>
								</td>
							</xsl:otherwise>
						</xsl:choose>
					</tr>

					<xsl:call-template name="ecodimb_form"></xsl:call-template>
					<tr>
						<td>
							<xsl:value-of select="lang_category"></xsl:value-of>
						</td>
						<td>
							<xsl:call-template name="categories"></xsl:call-template>
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
						<xsl:call-template name="b_account_view"></xsl:call-template>
						<input type="hidden" name="b_account_id" value="{value_b_account}"></input>
					</xsl:when>
					<xsl:otherwise>
						<xsl:call-template name="b_account_form"></xsl:call-template>
					</xsl:otherwise>
				</xsl:choose>

				<tr align="left">
					<td valign="top">
						<xsl:value-of select="lang_budget_cost"></xsl:value-of>
					</td>
					<td align="left">
						<input type="text" name="values[budget_cost]" value="{value_budget_cost}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_budget_cost_statustext"></xsl:value-of>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</td>
				</tr>
				<tr align="left">
					<td valign="top">
						<xsl:value-of select="lang_remark"></xsl:value-of>
					</td>
					<td align="left">

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

				<tr height="50">
					<td valign="bottom">
						<xsl:variable name="lang_save"><xsl:value-of select="lang_save"></xsl:value-of></xsl:variable>
						<input type="submit" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_save_statustext"></xsl:value-of>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</td>
					<td valign="bottom">
						<xsl:variable name="lang_apply"><xsl:value-of select="lang_apply"></xsl:value-of></xsl:variable>
						<input type="submit" name="values[apply]" value="{$lang_apply}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_apply_statustext"></xsl:value-of>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</td>
					<td align="right" valign="bottom">
						<xsl:variable name="lang_cancel"><xsl:value-of select="lang_cancel"></xsl:value-of></xsl:variable>
						<input type="submit" name="values[cancel]" value="{$lang_cancel}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_cancel_statustext"></xsl:value-of>
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
					<xsl:variable name="link_download"><xsl:value-of select="link_download"></xsl:value-of></xsl:variable>
					<xsl:variable name="lang_download_help"><xsl:value-of select="lang_download_help"></xsl:value-of></xsl:variable>
					<xsl:variable name="lang_download"><xsl:value-of select="lang_download"></xsl:value-of></xsl:variable>
					<a href="javascript:var w=window.open('{$link_download}','','left=50,top=100')" onMouseOver="overlib('{$lang_download_help}', CAPTION, '{$lang_download}')" onMouseOut="nd()">
						<xsl:value-of select="lang_download"></xsl:value-of></a>
				</td>
			</tr>
			<tr>
				<td colspan="3" width="100%">
					<xsl:call-template name="nextmatchs"></xsl:call-template>
				</td>
			</tr>
		</table>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:call-template name="table_header"></xsl:call-template>
			<xsl:call-template name="values"></xsl:call-template>
			<tr height="50">
				<td>
					<xsl:variable name="done_action"><xsl:value-of select="done_action"></xsl:value-of></xsl:variable>
					<xsl:variable name="lang_done"><xsl:value-of select="lang_done"></xsl:value-of></xsl:variable>
					<form method="post" action="{$done_action}">
						<input type="submit" class="forms" name="done" value="{$lang_done}" onMouseover="window.status='Back to the list.';return true;" onMouseout="window.status='';return true;"></input>
					</form>
				</td>
			</tr>
		</table>
	</xsl:template>


	<xsl:template match="list_obligations">
		<xsl:apply-templates select="menu"></xsl:apply-templates>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:choose>
				<xsl:when test="msgbox_data != ''">
					<tr>
						<td align="left" colspan="3">
							<xsl:call-template name="msgbox"></xsl:call-template>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<tr>
				<td align="left">
					<xsl:call-template name="filter_year"></xsl:call-template>
				</td>

				<td align="left">
					<xsl:call-template name="filter_district"></xsl:call-template>
				</td>
				<td>
					<xsl:call-template name="categories"></xsl:call-template>
				</td>
				<td align="left">
					<xsl:call-template name="filter_grouping"></xsl:call-template>
				</td>
				<td align="right">
					<xsl:call-template name="search_field"></xsl:call-template>
				</td>
			</tr>
			<tr>
				<td colspan="8" width="100%">
					<xsl:call-template name="nextmatchs"></xsl:call-template>
				</td>
			</tr>
		</table>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:apply-templates select="table_header_budget_obligations"></xsl:apply-templates>
			<xsl:apply-templates select="values_budget_obligations"></xsl:apply-templates>
			<tr>
				<td>
				</td>
				<td>
				</td>
				<td class="th_text" align="right">
					<xsl:value-of select="sum_hits"></xsl:value-of>
				</td>
				<td class="th_text" align="right">
					<xsl:value-of select="sum_budget_cost"></xsl:value-of>
				</td>
				<td class="th_text" align="right">
					<xsl:value-of select="sum_obligation"></xsl:value-of>
				</td>
				<td class="th_text" align="right">
					<xsl:value-of select="sum_actual_cost"></xsl:value-of>
				</td>
				<td class="th_text" align="right">
					<xsl:value-of select="sum_diff"></xsl:value-of>
				</td>
			</tr>
		</table>
	</xsl:template>

	<xsl:template match="table_header_budget_obligations">
		<xsl:variable name="sort_grouping"><xsl:value-of select="sort_grouping"></xsl:value-of></xsl:variable>
		<tr class="th">
			<td class="th_text" width="10%" align="left">
				<a href="{$sort_grouping}"><xsl:value-of select="lang_grouping"></xsl:value-of></a>
			</td>

			<td class="th_text" width="1%" align="left">
				<xsl:value-of select="lang_district_id"></xsl:value-of>
			</td>
			<td class="th_text" width="1%" align="right">
				<xsl:value-of select="lang_hits"></xsl:value-of>
			</td>

			<td class="th_text" width="10%" align="center">
				<xsl:value-of select="lang_budget_cost"></xsl:value-of>
			</td>

			<td class="th_text" width="10%" align="right">
				<xsl:value-of select="lang_obligations"></xsl:value-of>
			</td>

			<td class="th_text" width="10%" align="center">
				<xsl:value-of select="lang_actual_cost"></xsl:value-of>
			</td>
			<td class="th_text" width="10%" align="center">
				<xsl:value-of select="lang_diff"></xsl:value-of>
			</td>
		</tr>
	</xsl:template>


	<xsl:template match="values_budget_obligations"> 
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
			<td>
				<a href="{link_b_account}"><xsl:value-of select="b_account"></xsl:value-of></a>
			</td>

			<td>
				<xsl:value-of select="district_id"></xsl:value-of>
			</td>
			<td align="right">
				<xsl:value-of select="hits"></xsl:value-of>
			</td>

			<td align="right">
				<xsl:value-of select="budget_cost"></xsl:value-of>
			</td>

			<td align="right">
				<xsl:variable name="link_obligation"><xsl:value-of select="link_obligation"></xsl:value-of></xsl:variable>
				<a href="{$link_obligation}" onMouseover="window.status='{obligation}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="obligation"></xsl:value-of></a>
			</td>

			<td align="right">
				<xsl:variable name="link_actual_cost"><xsl:value-of select="link_actual_cost"></xsl:value-of></xsl:variable>
				<a href="{$link_actual_cost}" onMouseover="window.status='{actual_cost}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="actual_cost"></xsl:value-of></a>
			</td>

			<td align="right">
				<xsl:value-of select="diff"></xsl:value-of>
			</td>
		</tr>
	</xsl:template>



	<xsl:template match="year">
		<xsl:variable name="id"><xsl:value-of select="id"></xsl:value-of></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected='selected'">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="id"></xsl:value-of></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="id"></xsl:value-of></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="b_group_list">
		<xsl:variable name="id"><xsl:value-of select="id"></xsl:value-of></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected='selected'">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="revision_list">
		<xsl:variable name="id"><xsl:value-of select="id"></xsl:value-of></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected='selected'">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template name="filter_year">
		<xsl:variable name="select_action"><xsl:value-of select="select_action"></xsl:value-of></xsl:variable>
		<xsl:variable name="lang_submit"><xsl:value-of select="lang_submit"></xsl:value-of></xsl:variable>
		<form method="post" action="{$select_action}">
			<select name="year" onChange="this.form.submit();" onMouseout="window.status='';return true;">
				<xsl:attribute name="onMouseover">
					<xsl:text>window.status='</xsl:text>
					<xsl:value-of select="lang_year_statustext"></xsl:value-of>
					<xsl:text>'; return true;</xsl:text>
				</xsl:attribute>
				<option value=""><xsl:value-of select="lang_no_year"></xsl:value-of></option>
				<xsl:apply-templates select="year_list"></xsl:apply-templates>
			</select>
			<noscript>
				<xsl:text> </xsl:text>
				<input type="submit" name="submit" value="{$lang_submit}"></input>
			</noscript>
		</form>
	</xsl:template>

	<xsl:template match="year_list">
		<xsl:variable name="id"><xsl:value-of select="id"></xsl:value-of></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>


	<xsl:template name="filter_revision">
		<xsl:variable name="select_action"><xsl:value-of select="select_action"></xsl:value-of></xsl:variable>
		<xsl:variable name="lang_submit"><xsl:value-of select="lang_submit"></xsl:value-of></xsl:variable>
		<form method="post" action="{$select_action}">
			<select name="revision" onChange="this.form.submit();" onMouseout="window.status='';return true;">
				<xsl:attribute name="onMouseover">
					<xsl:text>window.status='</xsl:text>
					<xsl:value-of select="lang_revision_statustext"></xsl:value-of>
					<xsl:text>'; return true;</xsl:text>
				</xsl:attribute>
				<option value=""><xsl:value-of select="lang_no_revision"></xsl:value-of></option>
				<xsl:apply-templates select="revision_list"></xsl:apply-templates>
			</select>
			<noscript>
				<xsl:text> </xsl:text>
				<input type="submit" name="submit" value="{$lang_submit}"></input>
			</noscript>
		</form>
	</xsl:template>

	<xsl:template match="revision_list">
		<xsl:variable name="id"><xsl:value-of select="id"></xsl:value-of></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template name="filter_grouping">
		<xsl:variable name="select_action"><xsl:value-of select="select_action"></xsl:value-of></xsl:variable>
		<xsl:variable name="lang_submit"><xsl:value-of select="lang_submit"></xsl:value-of></xsl:variable>
		<form method="post" action="{$select_action}">
			<select name="grouping" onChange="this.form.submit();" onMouseout="window.status='';return true;">
				<xsl:attribute name="onMouseover">
					<xsl:text>window.status='</xsl:text>
					<xsl:value-of select="lang_grouping_statustext"></xsl:value-of>
					<xsl:text>'; return true;</xsl:text>
				</xsl:attribute>
				<option value=""><xsl:value-of select="lang_no_grouping"></xsl:value-of></option>
				<xsl:apply-templates select="grouping_list"></xsl:apply-templates>
			</select>
			<noscript>
				<xsl:text> </xsl:text>
				<input type="submit" name="submit" value="{$lang_submit}"></input>
			</noscript>
		</form>
	</xsl:template>

	<xsl:template match="grouping_list">
		<xsl:variable name="id"><xsl:value-of select="id"></xsl:value-of></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="distribute_year_list">
		<xsl:variable name="id"><xsl:value-of select="id"></xsl:value-of></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
