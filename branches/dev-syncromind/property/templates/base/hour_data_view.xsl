  <!-- $Id$ -->
	<xsl:template name="hour_data_view">
		<xsl:apply-templates select="table_header_hour"/>
		<xsl:apply-templates select="values_hour"/>
		<xsl:apply-templates select="table_sum"/>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="table_header_hour">
		<tr class="th">
			<td class="th_text" width="5%" align="right">
				<xsl:value-of select="lang_post"/>
			</td>
			<td class="th_text" width="15%" align="left">
				<xsl:value-of select="lang_code"/>
			</td>
			<td class="th_text" width="40%" align="left">
				<xsl:value-of select="lang_descr"/>
			</td>
			<td class="th_text" width="4%" align="left">
				<xsl:value-of select="lang_unit"/>
			</td>
			<td class="th_text" width="2%" align="right">
				<xsl:value-of select="lang_quantity"/>
			</td>
			<td class="th_text" width="5%" align="right">
				<xsl:value-of select="lang_billperae"/>
			</td>
			<td class="th_text" width="15%" align="right">
				<xsl:value-of select="lang_cost"/>
			</td>
			<td class="th_text" width="15%" align="right">
				<xsl:value-of select="lang_deviation"/>
			</td>
			<td class="th_text" width="15%" align="right">
				<xsl:value-of select="lang_result"/>
			</td>
		</tr>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="values_hour">
		<xsl:choose>
			<xsl:when test="new_grouping=1">
				<tr>
					<td class="th_text" align="center" colspan="10" width="100%">
						<xsl:value-of select="grouping_descr"/>
					</td>
				</tr>
			</xsl:when>
		</xsl:choose>
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
				<xsl:value-of select="post"/>
			</td>
			<td align="left">
				<xsl:value-of select="code"/>
			</td>
			<td align="left">
				<xsl:value-of select="hours_descr"/>
				<br/>
				<xsl:value-of select="remark"/>
			</td>
			<td align="left">
				<xsl:value-of select="unit_name"/>
			</td>
			<td align="right">
				<xsl:value-of select="quantity"/>
			</td>
			<td align="right">
				<xsl:value-of select="billperae"/>
			</td>
			<td align="right">
				<xsl:value-of select="cost"/>
			</td>
			<td align="right">
				<xsl:choose>
					<xsl:when test="deviation=''">
						<xsl:text>0.00</xsl:text>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="deviation"/>
					</xsl:otherwise>
				</xsl:choose>
			</td>
			<td align="right">
				<xsl:value-of select="result"/>
			</td>
		</tr>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="table_sum">
		<tr>
			<td>
			</td>
			<td align="left" colspan="5">
				<xsl:value-of select="lang_sum_calculation"/>
			</td>
			<td align="right">
				<xsl:value-of select="value_sum_calculation"/>
			</td>
			<td align="right">
				<xsl:value-of select="sum_deviation"/>
			</td>
			<td align="right">
				<xsl:value-of select="sum_result"/>
			</td>
		</tr>
		<tr>
			<td>
			</td>
			<td align="left" colspan="7">
				<xsl:value-of select="lang_addition_rs"/>
			</td>
			<td align="right">
				<xsl:value-of select="value_addition_rs"/>
			</td>
		</tr>
		<tr>
			<td>
			</td>
			<td align="left" colspan="7">
				<xsl:value-of select="lang_addition_percentage"/>
			</td>
			<td align="right">
				<xsl:value-of select="value_addition_percentage"/>
			</td>
		</tr>
		<tr>
			<td>
			</td>
			<td align="left" colspan="7">
				<xsl:value-of select="lang_sum_tax"/>
			</td>
			<td align="right">
				<xsl:value-of select="value_sum_tax"/>
			</td>
		</tr>
		<tr>
			<td>
			</td>
			<td align="left" colspan="7">
				<xsl:value-of select="lang_total_sum"/>
			</td>
			<td align="right">
				<xsl:value-of select="value_total_sum"/>
			</td>
		</tr>
	</xsl:template>
