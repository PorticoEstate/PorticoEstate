<!-- $Id$ -->

	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="list_tenant">
				<xsl:apply-templates select="list_tenant"></xsl:apply-templates>
			</xsl:when>
			<xsl:when test="list_entity">
				<xsl:apply-templates select="list_entity"></xsl:apply-templates>
			</xsl:when>
			<xsl:when test="list_street">
				<xsl:apply-templates select="list_street"></xsl:apply-templates>
			</xsl:when>
			<xsl:when test="list_ns3420">
				<xsl:apply-templates select="list_ns3420"></xsl:apply-templates>
			</xsl:when>
			<xsl:when test="list_b_account">
				<xsl:apply-templates select="list_b_account"></xsl:apply-templates>
			</xsl:when>
			<xsl:when test="list_vendor">
				<xsl:apply-templates select="list_vendor"></xsl:apply-templates>
			</xsl:when>
			<xsl:when test="list_phpgw_user">
				<xsl:apply-templates select="list_phpgw_user"></xsl:apply-templates>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="list_contact"></xsl:apply-templates>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="list_contact">
		<script type="text/javascript">
			function ExchangeContact(thisform)
			{
			opener.document.form.<xsl:value-of select="contact_id"></xsl:value-of>.value = thisform.elements[0].value;
			opener.document.form.<xsl:value-of select="contact_name"></xsl:value-of>.value = thisform.elements[1].value;
			window.close()
			}
		</script>


		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<tr>
				<td>
					<xsl:call-template name="cat_filter"></xsl:call-template>
				</td>
				<!--	<td align="center">
					<xsl:call-template name="filter_select"/>
				</td> -->
				<td align="right">
					<xsl:call-template name="search_field"></xsl:call-template>
				</td>
			</tr>
			<tr>
				<td colspan="3" width="100%">
					<xsl:call-template name="nextmatchs"></xsl:call-template>
				</td>
			</tr>
		</table>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:apply-templates select="table_header_contact"></xsl:apply-templates>
			<xsl:apply-templates select="values_contact"></xsl:apply-templates>

			<tr>
				<td colspan="3" width="100%">
					<xsl:call-template name="nextmatchs"></xsl:call-template>
				</td>
			</tr>
		</table>
		<xsl:apply-templates select="table_done"></xsl:apply-templates>

	</xsl:template>

	<xsl:template match="table_header_contact">
		<xsl:variable name="sort_id"><xsl:value-of select="sort_id"></xsl:value-of></xsl:variable>
		<xsl:variable name="sort_name"><xsl:value-of select="sort_name"></xsl:value-of></xsl:variable>
		<tr class="th">
			<td class="th_text" width="10%" align="right">
				<a href="{$sort_id}"><xsl:value-of select="lang_id"></xsl:value-of></a>
			</td>
			<td class="th_text" width="40%" align="left">
				<a href="{$sort_name}"><xsl:value-of select="lang_name"></xsl:value-of></a>
			</td>
			<td class="th_text" width="10%">
				<xsl:value-of select="lang_select"></xsl:value-of>
			</td>
		</tr>
	</xsl:template>


	<xsl:template match="values_contact">
		<xsl:variable name="lang_select_statustext"><xsl:value-of select="lang_select_statustext"></xsl:value-of></xsl:variable>
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

			<td class="small_text" align="right">
				<xsl:value-of select="id"></xsl:value-of>
			</td>
			<td class="small_text" align="left">
				<xsl:value-of select="contact_name"></xsl:value-of>
			</td>
			<xsl:choose>
				<xsl:when test="id">
					<form>
						<td class="small_text" valign="top">
							<input type="hidden" name="hidden" value="{id}"></input>
							<input type="hidden" name="hidden" value="{contact_name}"></input>
							<xsl:variable name="lang_select"><xsl:value-of select="lang_select"></xsl:value-of></xsl:variable>
							<input type="button" name="convert" value="{$lang_select}" onClick="ExchangeContact(this.form);" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_select_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</form>
				</xsl:when>
			</xsl:choose>
		</tr>
	</xsl:template>

<!-- list list_vendor-->
	<xsl:template match="list_vendor">
		<script type="text/javascript">
			function ExchangeVendor(thisform)
			{
			/* opener.document.form.<xsl:value-of select="contact_id"></xsl:value-of>.value = thisform.elements[0].value;
			opener.document.form.<xsl:value-of select="org_name"></xsl:value-of>.value = thisform.elements[1].value;*/

			//cramirez: modifying this seccion for use in datatable YUI
			opener.document.forms[0].<xsl:value-of select="contact_id"></xsl:value-of>.value = thisform.elements[0].value;
			opener.document.forms[0].<xsl:value-of select="org_name"></xsl:value-of>.value = thisform.elements[1].value;

			window.close()
			}
		</script>


		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<tr>
				<td>
					<xsl:call-template name="cat_filter"></xsl:call-template>
				</td>
				<!--	<td align="center">
					<xsl:call-template name="filter_select"/>
				</td> -->
				<td align="right">
					<xsl:call-template name="search_field"></xsl:call-template>
				</td>
			</tr>
			<tr>
				<td colspan="3" width="100%">
					<xsl:call-template name="nextmatchs"></xsl:call-template>
				</td>
			</tr>
		</table>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:apply-templates select="table_header_vendor"></xsl:apply-templates>
			<xsl:apply-templates select="values_vendor"></xsl:apply-templates>

			<tr>
				<td colspan="3" width="100%">
					<xsl:call-template name="nextmatchs"></xsl:call-template>
				</td>
			</tr>
		</table>
		<xsl:apply-templates select="table_done"></xsl:apply-templates>

	</xsl:template>

	<xsl:template match="table_header_vendor">
		<xsl:variable name="sort_id"><xsl:value-of select="sort_id"></xsl:value-of></xsl:variable>
		<xsl:variable name="sort_name"><xsl:value-of select="sort_name"></xsl:value-of></xsl:variable>
		<tr class="th">
			<td class="th_text" width="10%" align="right">
				<a href="{$sort_id}"><xsl:value-of select="lang_id"></xsl:value-of></a>
			</td>
			<td class="th_text" width="40%" align="left">
				<a href="{$sort_name}"><xsl:value-of select="lang_name"></xsl:value-of></a>
			</td>
			<td class="th_text" width="10%">
				<xsl:value-of select="lang_select"></xsl:value-of>
			</td>
		</tr>
	</xsl:template>


	<xsl:template match="values_vendor">
		<xsl:variable name="lang_select_statustext"><xsl:value-of select="lang_select_statustext"></xsl:value-of></xsl:variable>
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

			<td class="small_text" align="right">
				<xsl:value-of select="id"></xsl:value-of>
			</td>
			<td class="small_text" align="left">
				<xsl:value-of select="vendor_name"></xsl:value-of>
			</td>
			<xsl:choose>
				<xsl:when test="id">
					<form>
						<td class="small_text" valign="top">
							<input type="hidden" name="hidden" value="{id}"></input>
							<input type="hidden" name="hidden" value="{vendor_name}"></input>
							<xsl:variable name="lang_select"><xsl:value-of select="lang_select"></xsl:value-of></xsl:variable>
							<input type="button" name="convert" value="{$lang_select}" onClick="ExchangeVendor(this.form);" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_select_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</form>
				</xsl:when>
			</xsl:choose>
		</tr>
	</xsl:template>

<!-- list b_account-->

	<xsl:template match="list_b_account">
		<script type="text/javascript">
			function Exchangeb_account(thisform)
			{
			opener.document.form.b_account_id.value = thisform.elements[0].value;
			opener.document.form.b_account_name.value = thisform.elements[1].value;
			window.close()
			}
		</script>


		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<tr>
				<td align="right">
					<xsl:call-template name="search_field"></xsl:call-template>
				</td>
			</tr>
			<tr>
				<td colspan="3" width="100%">
					<xsl:call-template name="nextmatchs"></xsl:call-template>
				</td>
			</tr>
		</table>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:apply-templates select="table_header_b_account"></xsl:apply-templates>
			<xsl:apply-templates select="values_b_account"></xsl:apply-templates>

			<tr>
				<td colspan="3" width="100%">
					<xsl:call-template name="nextmatchs"></xsl:call-template>
				</td>
			</tr>
		</table>
		<xsl:apply-templates select="table_done"></xsl:apply-templates>

	</xsl:template>

	<xsl:template match="table_header_b_account">
		<xsl:variable name="sort_id"><xsl:value-of select="sort_id"></xsl:value-of></xsl:variable>
		<xsl:variable name="sort_name"><xsl:value-of select="sort_name"></xsl:value-of></xsl:variable>
		<tr class="th">
			<td class="th_text" width="10%" align="right">
				<a href="{$sort_id}"><xsl:value-of select="lang_id"></xsl:value-of></a>
			</td>
			<td class="th_text" width="40%" align="right">
				<a href="{$sort_name}"><xsl:value-of select="lang_name"></xsl:value-of></a>
			</td>
			<td class="th_text" width="10%">
				<xsl:value-of select="lang_select"></xsl:value-of>
			</td>
		</tr>
	</xsl:template>


	<xsl:template match="values_b_account">
		<xsl:variable name="lang_select_statustext"><xsl:value-of select="lang_select_statustext"></xsl:value-of></xsl:variable>
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

			<td class="small_text" align="right">
				<xsl:value-of select="id"></xsl:value-of>
			</td>
			<td class="small_text" align="right">
				<xsl:value-of select="b_account_name"></xsl:value-of>
			</td>
			<xsl:choose>
				<xsl:when test="id">
					<form>
						<td class="small_text" valign="top">
							<input type="hidden" name="hidden" value="{id}"></input>
							<input type="hidden" name="hidden" value="{b_account_name}"></input>
							<xsl:variable name="lang_select"><xsl:value-of select="lang_select"></xsl:value-of></xsl:variable>
							<input type="button" name="convert" value="{$lang_select}" onClick="Exchangeb_account(this.form);" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_select_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</form>
				</xsl:when>
			</xsl:choose>
		</tr>
	</xsl:template>

<!-- list street-->

	<xsl:template match="list_street">
		<script type="text/javascript">
			function ExchangeStreet(thisform)
			{
			opener.document.form.street_id.value = thisform.elements[0].value;
			opener.document.form.street_name.value = thisform.elements[1].value;
			window.close()
			}
		</script>


		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<tr>
				<td align="right">
					<xsl:call-template name="search_field"></xsl:call-template>
				</td>
			</tr>
			<tr>
				<td colspan="3" width="100%">
					<xsl:call-template name="nextmatchs"></xsl:call-template>
				</td>
			</tr>
		</table>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:apply-templates select="table_header_street"></xsl:apply-templates>
			<xsl:apply-templates select="values_street"></xsl:apply-templates>

			<tr>
				<td colspan="3" width="100%">
					<xsl:call-template name="nextmatchs"></xsl:call-template>
				</td>
			</tr>
		</table>
		<xsl:apply-templates select="table_done"></xsl:apply-templates>

	</xsl:template>

	<xsl:template match="table_header_street">
		<xsl:variable name="sort_id"><xsl:value-of select="sort_id"></xsl:value-of></xsl:variable>
		<xsl:variable name="sort_name"><xsl:value-of select="sort_name"></xsl:value-of></xsl:variable>
		<tr class="th">
			<td class="th_text" width="5%" align="right">
				<a href="{$sort_id}"><xsl:value-of select="lang_id"></xsl:value-of></a>
			</td>
			<td class="th_text" width="90%" align="right">
				<a href="{$sort_name}"><xsl:value-of select="lang_name"></xsl:value-of></a>
			</td>
			<td class="th_text" width="5%">
				<xsl:value-of select="lang_select"></xsl:value-of>
			</td>
		</tr>
	</xsl:template>


	<xsl:template match="values_street">
		<xsl:variable name="lang_select_statustext"><xsl:value-of select="lang_select_statustext"></xsl:value-of></xsl:variable>
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

			<td class="small_text" align="right">
				<xsl:value-of select="id"></xsl:value-of>
			</td>
			<td class="small_text" align="right">
				<xsl:value-of select="street_name"></xsl:value-of>
			</td>
			<xsl:choose>
				<xsl:when test="id">
					<form>
						<td class="small_text" valign="top">
							<input type="hidden" name="hidden" value="{id}"></input>
							<input type="hidden" name="hidden" value="{street_name}"></input>
							<xsl:variable name="lang_select"><xsl:value-of select="lang_select"></xsl:value-of></xsl:variable>
							<input type="button" name="convert" value="{$lang_select}" onClick="ExchangeStreet(this.form);" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_select_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</form>
				</xsl:when>
			</xsl:choose>
		</tr>
	</xsl:template>

<!-- list tenant-->

	<xsl:template match="list_tenant">
		<script type="text/javascript">
			function Exchangetenant(thisform)
			{
			opener.document.form.tenant_id.value = thisform.elements[0].value;
			opener.document.form.last_name.value = thisform.elements[1].value;
			opener.document.form.first_name.value = thisform.elements[2].value;
			window.close()
			}
		</script>


		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<tr>
				<td align="right">
					<xsl:call-template name="search_field"></xsl:call-template>
				</td>
			</tr>
			<tr>
				<td colspan="3" width="100%">
					<xsl:call-template name="nextmatchs"></xsl:call-template>
				</td>
			</tr>
		</table>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:apply-templates select="table_header_tenant_new"></xsl:apply-templates>
			<xsl:apply-templates select="values_tenant_new"></xsl:apply-templates>

			<tr>
				<td colspan="3" width="100%">
					<xsl:call-template name="nextmatchs"></xsl:call-template>
				</td>
			</tr>
		</table>
		<xsl:apply-templates select="table_done"></xsl:apply-templates>

	</xsl:template>

	<xsl:template match="table_header_tenant_new">
		<xsl:variable name="sort_id"><xsl:value-of select="sort_id"></xsl:value-of></xsl:variable>
		<xsl:variable name="sort_last_name"><xsl:value-of select="sort_last_name"></xsl:value-of></xsl:variable>
		<xsl:variable name="sort_first_name"><xsl:value-of select="sort_first_name"></xsl:value-of></xsl:variable>
		<tr class="th">
			<td class="th_text" width="5%" align="right">
				<a href="{$sort_id}"><xsl:value-of select="lang_id"></xsl:value-of></a>
			</td>
			<td class="th_text" width="40%" align="right">
				<a href="{$sort_last_name}"><xsl:value-of select="lang_last_name"></xsl:value-of></a>
			</td>
			<td class="th_text" width="40%" align="right">
				<a href="{$sort_first_name}"><xsl:value-of select="lang_first_name"></xsl:value-of></a>
			</td>
			<td class="th_text" width="5%">
				<xsl:value-of select="lang_select"></xsl:value-of>
			</td>
		</tr>
	</xsl:template>


	<xsl:template match="values_tenant_new">
		<xsl:variable name="lang_select_statustext"><xsl:value-of select="lang_select_statustext"></xsl:value-of></xsl:variable>
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

			<td class="small_text" align="right">
				<xsl:value-of select="id"></xsl:value-of>
			</td>
			<td class="small_text" align="right">
				<xsl:value-of select="last_name"></xsl:value-of>
			</td>
			<td class="small_text" align="right">
				<xsl:value-of select="first_name"></xsl:value-of>
			</td>
			<xsl:choose>
				<xsl:when test="id">
					<form>
						<td class="small_text" valign="top">
							<input type="hidden" name="hidden" value="{id}"></input>
							<input type="hidden" name="hidden" value="{last_name}"></input>
							<input type="hidden" name="hidden" value="{first_name}"></input>
							<xsl:variable name="lang_select"><xsl:value-of select="lang_select"></xsl:value-of></xsl:variable>
							<input type="button" name="convert" value="{$lang_select}" onClick="Exchangetenant(this.form);" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_select_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</form>
				</xsl:when>
			</xsl:choose>
		</tr>
	</xsl:template>

<!-- list ns3420-->

	<xsl:template match="list_ns3420">
		<script type="text/javascript">
			function Exchangens3420(thisform)
			{
			opener.document.form.ns3420_id.value = thisform.elements[0].value;
			opener.document.form.ns3420_descr.value = thisform.elements[1].value;
			window.close()
			}
		</script>


		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<tr>
				<td align="right">
					<xsl:call-template name="search_field"></xsl:call-template>
				</td>
			</tr>
			<tr>
				<td colspan="3" width="100%">
					<xsl:call-template name="nextmatchs"></xsl:call-template>
				</td>
			</tr>
		</table>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:apply-templates select="table_header_ns3420"></xsl:apply-templates>
			<xsl:apply-templates select="values_ns3420"></xsl:apply-templates>

			<tr>
				<td colspan="3" width="100%">
					<xsl:call-template name="nextmatchs"></xsl:call-template>
				</td>
			</tr>
		</table>
		<xsl:apply-templates select="table_done"></xsl:apply-templates>

	</xsl:template>

	<xsl:template match="table_header_ns3420">
		<xsl:variable name="sort_id"><xsl:value-of select="sort_id"></xsl:value-of></xsl:variable>
		<xsl:variable name="sort_descr"><xsl:value-of select="sort_descr"></xsl:value-of></xsl:variable>
		<tr class="th">
			<td class="th_text" width="15%" align="right">
				<a href="{$sort_id}"><xsl:value-of select="lang_id"></xsl:value-of></a>
			</td>
			<td class="th_text" width="85%" align="right">
				<a href="{$sort_descr}"><xsl:value-of select="lang_descr"></xsl:value-of></a>
			</td>
			<td class="th_text" width="5%">
				<xsl:value-of select="lang_select"></xsl:value-of>
			</td>
		</tr>
	</xsl:template>


	<xsl:template match="values_ns3420">
		<xsl:variable name="lang_select_statustext"><xsl:value-of select="lang_select_statustext"></xsl:value-of></xsl:variable>
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

			<td class="small_text" align="left">
				<xsl:value-of select="id"></xsl:value-of>
			</td>
			<td class="small_text" align="left">
				<xsl:value-of select="ns3420_descr"></xsl:value-of>
			</td>
			<xsl:choose>
				<xsl:when test="id">
					<form>
						<td class="small_text" valign="top">
							<input type="hidden" name="hidden" value="{id}"></input>
							<input type="hidden" name="hidden" value="{ns3420_descr}"></input>
							<xsl:variable name="lang_select"><xsl:value-of select="lang_select"></xsl:value-of></xsl:variable>
							<input type="button" name="convert" value="{$lang_select}" onClick="Exchangens3420(this.form);" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_select_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</form>
				</xsl:when>
			</xsl:choose>
		</tr>
	</xsl:template>

	<xsl:template match="list_entity">
		<xsl:choose>
			<xsl:when test="//lookup=1">
				<script type="text/javascript">
					function Exchange_values(thisform)
					{
					<xsl:value-of select="function_exchange_values"></xsl:value-of>
					}
				</script>
			</xsl:when>
		</xsl:choose>

		<table width="95%" cellpadding="2" cellspacing="2" align="center">
			<tr>
				<td>
					<xsl:call-template name="cat_filter"></xsl:call-template>
				</td>
				<td>
					<xsl:call-template name="filter_district"></xsl:call-template>
				</td>
				<td align="right">
					<xsl:call-template name="search_field"></xsl:call-template>
				</td>
			</tr>
			<tr>
				<td colspan="3" width="100%">
					<xsl:call-template name="nextmatchs"></xsl:call-template>
				</td>
			</tr>
		</table>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<tr class="th">
				<xsl:choose>
					<xsl:when test="//lookup=1">
						<td>
							<!--	make room for hidden fields  -->
						</td>
					</xsl:when>
				</xsl:choose>
				<xsl:for-each select="table_header_entity">
					<td class="th_text" width="{with}" align="{align}">
						<xsl:choose>
							<xsl:when test="sort_link!=''">
								<a href="{sort}" onMouseover="window.status='{header}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="header"></xsl:value-of></a>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="header"></xsl:value-of>
							</xsl:otherwise>
						</xsl:choose>
					</td>
				</xsl:for-each>
			</tr>
			<xsl:call-template name="list_values_entity"></xsl:call-template>

		</table>
		<xsl:apply-templates select="table_done"></xsl:apply-templates>

	</xsl:template>

	<xsl:template name="list_values_entity">
		<xsl:for-each select="values_entity">
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
				<form>
					<xsl:choose>
						<xsl:when test="//lookup=1">
							<td>
								<xsl:for-each select="hidden">
									<input type="hidden" name="{name}" value="{value}"></input>
								</xsl:for-each>
							</td>
						</xsl:when>
					</xsl:choose>
					<xsl:for-each select="row">
						<xsl:choose>
							<xsl:when test="link">
								<td class="small_text" align="center">
									<a href="{link}" onMouseover="window.status='{statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text"></xsl:value-of></a>
								</td>
							</xsl:when>
							<xsl:otherwise>
								<td class="small_text" align="left">
									<xsl:value-of select="value"></xsl:value-of>
									<xsl:choose>
										<xsl:when test="//lookup=1">
											<xsl:if test="position() = last()">
												<td class="small_text" align="center">
													<input type="button" name="select" value="{//lang_select}" onClick="{//exchange_values}" onMouseout="window.status='';return true;">
														<xsl:attribute name="onMouseover">
															<xsl:text>window.status='</xsl:text>
															<xsl:value-of select="lang_select_statustext"></xsl:value-of>
															<xsl:text>'; return true;</xsl:text>
														</xsl:attribute>
													</input>
												</td>

											</xsl:if>
										</xsl:when>
									</xsl:choose>
								</td>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:for-each>
				</form>
			</tr>
		</xsl:for-each>
	</xsl:template>


	<xsl:template match="table_done">
		<table width="95%" cellpadding="2" cellspacing="2" align="center">
			<tr>
				<td height="50">
					<xsl:variable name="lang_done"><xsl:value-of select="lang_done"></xsl:value-of></xsl:variable>
					<form method="post">
						<input type="button" name="done" value="{$lang_done}" onClick="window.close()" onMouseout="window.status='';return true;">
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
	</xsl:template>


<!-- list phpgw_user-->

	<xsl:template match="list_phpgw_user">
		<script type="text/javascript">
			function Exchangephpgw_user(thisform)
			{
			opener.document.form.<xsl:value-of select="user_id"></xsl:value-of>.value = thisform.elements[0].value;
			opener.document.form.<xsl:value-of select="user_name"></xsl:value-of>.value = thisform.elements[1].value;
			window.close()
			}
		</script>


		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<tr>
				<td align="right">
					<xsl:call-template name="search_field"></xsl:call-template>
				</td>
			</tr>
			<tr>
				<td colspan="3" width="100%">
					<xsl:call-template name="nextmatchs"></xsl:call-template>
				</td>
			</tr>
		</table>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:apply-templates select="table_header_phpgw_user"></xsl:apply-templates>
			<xsl:apply-templates select="values_phpgw_user"></xsl:apply-templates>

			<tr>
				<td colspan="3" width="100%">
					<xsl:call-template name="nextmatchs"></xsl:call-template>
				</td>
			</tr>
		</table>
		<xsl:apply-templates select="table_done"></xsl:apply-templates>

	</xsl:template>

	<xsl:template match="table_header_phpgw_user">
		<xsl:variable name="sort_id"><xsl:value-of select="sort_id"></xsl:value-of></xsl:variable>
		<xsl:variable name="sort_last_name"><xsl:value-of select="sort_last_name"></xsl:value-of></xsl:variable>
		<xsl:variable name="sort_first_name"><xsl:value-of select="sort_first_name"></xsl:value-of></xsl:variable>
		<tr class="th">
			<td class="th_text" width="5%" align="right">
				<a href="{$sort_id}"><xsl:value-of select="lang_id"></xsl:value-of></a>
			</td>
			<td class="th_text" width="40%" align="right">
				<a href="{$sort_first_name}"><xsl:value-of select="lang_first_name"></xsl:value-of></a>
			</td>
			<td class="th_text" width="40%" align="right">
				<a href="{$sort_last_name}"><xsl:value-of select="lang_last_name"></xsl:value-of></a>
			</td>
			<td class="th_text" width="5%">
				<xsl:value-of select="lang_select"></xsl:value-of>
			</td>
		</tr>
	</xsl:template>


	<xsl:template match="values_phpgw_user">
		<xsl:variable name="lang_select_statustext"><xsl:value-of select="lang_select_statustext"></xsl:value-of></xsl:variable>
		<xsl:variable name="full_name"><xsl:value-of select="first_name"></xsl:value-of><xsl:text> </xsl:text><xsl:value-of select="last_name"></xsl:value-of></xsl:variable>
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

			<td class="small_text" align="right">
				<xsl:value-of select="id"></xsl:value-of>
			</td>
			<td class="small_text" align="right">
				<xsl:value-of select="first_name"></xsl:value-of>
			</td>
			<td class="small_text" align="right">
				<xsl:value-of select="last_name"></xsl:value-of>
			</td>
			<xsl:choose>
				<xsl:when test="id">
					<form>
						<td class="small_text" valign="top">
							<input type="hidden" name="hidden" value="{id}"></input>
							<input type="hidden" name="hidden" value="{$full_name}"></input>
							<xsl:variable name="lang_select"><xsl:value-of select="lang_select"></xsl:value-of></xsl:variable>
							<input type="button" name="convert" value="{$lang_select}" onClick="Exchangephpgw_user(this.form);" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_select_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</form>
				</xsl:when>
			</xsl:choose>
		</tr>
	</xsl:template>
