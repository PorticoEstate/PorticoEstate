<!-- $Id$ -->

	<xsl:template name="hour_data_view">
		<xsl:apply-templates select="table_header_hour"></xsl:apply-templates>
		<xsl:apply-templates select="values_hour"></xsl:apply-templates>
		<xsl:apply-templates select="table_sum"></xsl:apply-templates>
	</xsl:template>

	<xsl:template match="table_header_hour">
		<tr class="th">
			<td class="th_text" width="5%" align="right">
				<xsl:value-of select="lang_post"></xsl:value-of>
			</td>
			<td class="th_text" width="15%" align="left">
				<xsl:value-of select="lang_code"></xsl:value-of>
			</td>
			<td class="th_text" width="40%" align="left">
				<xsl:value-of select="lang_descr"></xsl:value-of>
			</td>
			<td class="th_text" width="4%" align="left">
				<xsl:value-of select="lang_unit"></xsl:value-of>
			</td>
			<td class="th_text" width="2%" align="right">
				<xsl:value-of select="lang_quantity"></xsl:value-of>
			</td>
			<td class="th_text" width="5%" align="right">
				<xsl:value-of select="lang_billperae"></xsl:value-of>
			</td>
			<td class="th_text" width="15%" align="right">
				<xsl:value-of select="lang_cost"></xsl:value-of>
			</td>
			<td class="th_text" width="15%" align="right">
				<xsl:value-of select="lang_deviation"></xsl:value-of>
			</td>
			<td class="th_text" width="15%" align="right">
				<xsl:value-of select="lang_result"></xsl:value-of>
			</td>
		</tr>
	</xsl:template>

	<xsl:template match="values_hour">
		<xsl:choose>
			<xsl:when test="new_grouping=1">
				<tr>
					<td class="th_text" align="center" colspan="10" width="100%">
						<xsl:value-of select="grouping_descr"></xsl:value-of>
					</td>
				</tr>
			</xsl:when>
		</xsl:choose>
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
				<xsl:value-of select="post"></xsl:value-of>
			</td>
			<td align="left">
				<xsl:value-of select="code"></xsl:value-of>
			</td>
			<td align="left">
				<xsl:value-of select="hours_descr"></xsl:value-of>
				<br></br>
				<xsl:value-of select="remark"></xsl:value-of>
			</td>
			<td align="left">
				<xsl:value-of select="unit"></xsl:value-of>
			</td>
			<td align="right">
				<xsl:value-of select="quantity"></xsl:value-of>
			</td>
			<td align="right">
				<xsl:value-of select="billperae"></xsl:value-of>
			</td>
			<td align="right">
				<xsl:value-of select="cost"></xsl:value-of>
			</td>
			<td align="right">
				<xsl:choose>
					<xsl:when test="deviation=''">
						0.00
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="deviation"></xsl:value-of>
					</xsl:otherwise>
				</xsl:choose>
			</td>
			<td align="right">
				<xsl:value-of select="result"></xsl:value-of>
			</td>
		</tr>
	</xsl:template>
	<xsl:template match="table_sum">
		<tr>
			<td>
			</td>
			<td align="left" colspan="5">
				<xsl:value-of select="lang_sum_calculation"></xsl:value-of>
			</td>
			<td align="right">
				<xsl:value-of select="value_sum_calculation"></xsl:value-of>
			</td>
			<td align="right">
				<xsl:value-of select="sum_deviation"></xsl:value-of>
			</td>
			<td align="right">
				<xsl:value-of select="sum_result"></xsl:value-of>
			</td>
		</tr>
		<tr>
			<td>
			</td>
			<td align="left" colspan="7">
				<xsl:value-of select="lang_addition_rs"></xsl:value-of>
			</td>
			<td align="right">
				<xsl:value-of select="value_addition_rs"></xsl:value-of>
			</td>
		</tr>
		<tr>
			<td>
			</td>
			<td align="left" colspan="7">
				<xsl:value-of select="lang_addition_percentage"></xsl:value-of>
			</td>
			<td align="right">
				<xsl:value-of select="value_addition_percentage"></xsl:value-of>
			</td>
		</tr>
		<tr>
			<td>
			</td>
			<td align="left" colspan="7">
				<xsl:value-of select="lang_sum_tax"></xsl:value-of>
			</td>
			<td align="right">
				<xsl:value-of select="value_sum_tax"></xsl:value-of>
			</td>
		</tr>
		<tr>
			<td>
			</td>
			<td align="left" colspan="7">
				<xsl:value-of select="lang_total_sum"></xsl:value-of>
			</td>
			<td align="right">
				<xsl:value-of select="value_total_sum"></xsl:value-of>
			</td>
		</tr>
	</xsl:template>
