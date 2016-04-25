<!-- $Id: demo.xsl 7561 2011-09-07 14:01:50Z sigurdne $ -->

<!-- add / edit  -->
<xsl:template match="edit" xmlns:php="http://php.net/xsl">
	<xsl:variable name="form_action">
		<xsl:value-of select="form_action"/>
	</xsl:variable>
	<form method="post" action="{$form_action}">
		<div class="yui-navset yui-navset-top" id="demo_tabview">
			<xsl:value-of disable-output-escaping="yes" select="tabs" />
			<div class="yui-content">
				<div id="general">
					<table cellpadding="2" cellspacing="2" width="90%" align="center">
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
							<td valign="top">
								<xsl:value-of select="php:function('lang', 'contract id')" />
							</td>
							<td>
								<xsl:value-of select="contract/id"/>
							</td>
						</tr>
						<tr>
							<td valign="top">
								<xsl:value-of select="php:function('lang', 'old contract id')" />
							</td>
							<td>
								<xsl:value-of select="contract/old_contract_id"/>
							</td>
						</tr>

						<xsl:choose>
							<xsl:when test="value_id != ''">
								<tr>
									<td valign="top">
										<xsl:value-of select="php:function('lang', 'id')" />
									</td>
									<td align="left">
										<xsl:value-of select="value_id"/>
									</td>
								</tr>
								<tr>
									<td valign="top">
										<xsl:value-of select="php:function('lang', 'entry_date')" />
									</td>
									<td>
										<xsl:value-of select="value_entry_date"/>
									</td>
								</tr>
							</xsl:when>
						</xsl:choose>
						<tr>
							<td>
								<xsl:value-of select="php:function('lang', 'category')" />
							</td>
							<td>
								<xsl:call-template name="categories"/>
							</td>
						</tr>
						<tr>
							<td valign="top" width="10%">
								<xsl:value-of select="php:function('lang', 'name')" />
							</td>
							<td>
								<input type="text" size="60" name="values[name]" value="{value_name}">
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'name')" />
									</xsl:attribute>
								</input>
							</td>
						</tr>
						<tr>
							<td valign="top">
								<xsl:value-of select="php:function('lang', 'address')" />
							</td>
							<td>
								<input type="text" size="60" name="values[address]" value="{value_address}">
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'address')" />
									</xsl:attribute>
								</input>
							</td>
						</tr>
						<tr>
							<td valign="top">
								<xsl:value-of select="php:function('lang', 'zip')" />
							</td>
							<td>
								<input type="text" size="6" name="values[zip]" value="{value_zip}">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_zip_status_text"/>
									</xsl:attribute>
								</input>
								<xsl:value-of select="php:function('lang', 'town')" />
								<input type="text" size="40" name="values[town]" value="{value_town}">
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'town')" />
									</xsl:attribute>
								</input>
							</td>
						</tr>
						<tr>
							<td valign="top">
								<xsl:value-of select="php:function('lang', 'remark')" />
							</td>
							<td>
								<textarea cols="60" rows="10" name="values[remark]" id="remark" wrap="virtual">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_remark_status_text"/>
									</xsl:attribute>
									<xsl:value-of select="value_remark"/>
								</textarea>
							</td>
						</tr>
						<tr>
							<td>
								<xsl:value-of select="php:function('lang', 'private')" />
							</td>
							<td>
								<xsl:choose>
									<xsl:when test="value_access = 'private'">
										<input type="checkbox" name="values[access]" value="True" checked="checked">
											<xsl:attribute name="title">
												<xsl:value-of select="php:function('lang', 'The note is private. If the note should be public, uncheck this box')" />
											</xsl:attribute>
										</input>
									</xsl:when>
									<xsl:otherwise>
										<input type="checkbox" name="values[access]" value="True">
											<xsl:attribute name="title">
												<xsl:value-of select="php:function('lang', 'The note is public. If the note should be private, check this box')" />
											</xsl:attribute>
										</input>
									</xsl:otherwise>
								</xsl:choose>
							</td>
						</tr>
					</table>
				</div>
				<div id="list">
					<table>
						<tr>
							<td valign="top">
								<xsl:value-of select="php:function('lang', 'generic list 1')" />
							</td>
							<td>
								<select name="values[generic_list_1]" >
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'Set a value')" />
									</xsl:attribute>
									<option value="0">
										<xsl:value-of select="php:function('lang', 'Set a value')" />
									</option>
									<xsl:apply-templates select="generic_list_1/options"/>
								</select>
							</td>
						</tr>
						<tr>
							<td valign="top">
								<xsl:value-of select="php:function('lang', 'generic list 2')" />
							</td>
							<td>
								<select name="values[generic_list_2]" >
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'Set a value')" />
									</xsl:attribute>
									<option value="0">
										<xsl:value-of select="php:function('lang', 'Set a value')" />
									</option>
									<xsl:apply-templates select="generic_list_2/options"/>
								</select>
							</td>
						</tr>
					</table>
				</div>
				<div id="tables">
					<table>
						<tr>
							<td valign="top">
								<xsl:value-of select="php:function('lang', 'table')" />
							</td>
							<td>
								<div id="paging_0"> </div>
								<div id="datatable-container_0"></div>
								<div id="contextmenu_0"></div>
							</td>
						</tr>
					</table>
				</div>
				<div id="dates">
					<table>
						<tr>
							<td valign="top">
								<xsl:value-of select="php:function('lang', 'date')" /> 1
							</td>
							<td>
								<input type="text" id="values_start_date" name="values[start_date]" size="10" value="{value_start_date}" readonly="readonly" >
									<xsl:attribute name="title">
										<xsl:value-of select="lang_start_date_statustext"/>
									</xsl:attribute>
								</input>
								<img id="values_start_date-trigger" src="{img_cal}" >
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'date')" />
									</xsl:attribute>
									<xsl:attribute name="alt">
										<xsl:value-of select="php:function('lang', 'date')" />
									</xsl:attribute>
									<xsl:attribute name="style">
										<xsl:text>cursor:pointer; cursor:hand;</xsl:text>
									</xsl:attribute>
								</img>
							</td>
						</tr>
						<tr>
							<td valign="top">
								<xsl:value-of select="php:function('lang', 'date')" /> 2
							</td>
							<td>
								<xsl:value-of disable-output-escaping="yes" select="end_date"/>
							</td>
						</tr>
					</table>
				</div>
				<div id="custom">
					<table>
						<xsl:apply-templates select="custom_attributes/attributes"/>
					</table>
				</div>
				<table>
					<tr height="50">
						<td colspan = "2" align = "center">
							<table>
								<tr>
									<td valign="bottom">
										<xsl:variable name="lang_save">
											<xsl:value-of select="php:function('lang', 'save')" />
										</xsl:variable>
										<input type="submit" name="values[save]" value="{$lang_save}">
											<xsl:attribute name="title">
												<xsl:value-of select="php:function('lang', 'save')" />
											</xsl:attribute>
										</input>
									</td>
									<td valign="bottom">
										<xsl:variable name="lang_apply">
											<xsl:value-of select="php:function('lang', 'apply')" />
										</xsl:variable>
										<input type="submit" name="values[apply]" value="{$lang_apply}">
											<xsl:attribute name="title">
												<xsl:value-of select="php:function('lang', 'apply the values')" />
											</xsl:attribute>
										</input>
									</td>
									<td align="left" valign="bottom">
										<xsl:variable name="lang_cancel">
											<xsl:value-of select="php:function('lang', 'cancel')" />
										</xsl:variable>
										<input type="submit" name="values[cancel]" value="{$lang_cancel}">
											<xsl:attribute name="title">
												<xsl:value-of select="php:function('lang', 'Back to the list')" />
											</xsl:attribute>
										</input>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</form>

	<script type="text/javascript">
		var property_js = <xsl:value-of select="property_js" />;
		var datatable = new Array();
		var myColumnDefs = new Array();

		<xsl:for-each select="datatable">
			datatable[<xsl:value-of select="name"/>] = [
			{
			values			:	<xsl:value-of select="values"/>,
			total_records	: 	<xsl:value-of select="total_records"/>,
			edit_action		:  	<xsl:value-of select="edit_action"/>,
			is_paginator	:  	<xsl:value-of select="is_paginator"/>,
			footer			:	<xsl:value-of select="footer"/>
			}
			]
		</xsl:for-each>

		<xsl:for-each select="myColumnDefs">
			myColumnDefs[<xsl:value-of select="name"/>] = <xsl:value-of select="values"/>
		</xsl:for-each>
	</script>

</xsl:template>

	
<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected != 0">
			<xsl:attribute name="selected" value="selected" />
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>

