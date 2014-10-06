<!-- $Id$ -->

<xsl:template name="nextmatchs">
	<xsl:choose>
		<xsl:when test="nm_data != ''">
			<xsl:apply-templates select="nm_data"/>
		</xsl:when>
	</xsl:choose>
</xsl:template>

<xsl:template match="nm_data">
<!--	<xsl:param name="nextmatchs_params"/> -->
	<xsl:variable name="allow_all_rows"><xsl:value-of select="allow_all_rows"/></xsl:variable>
	<xsl:variable name="start_record"><xsl:value-of select="start_record"/></xsl:variable>
	<xsl:variable name="cur_record"><xsl:value-of select="number($start_record) + number(1)"/></xsl:variable>
	<xsl:variable name="record_limit"><xsl:value-of select="record_limit"/></xsl:variable>
	<xsl:variable name="num_records"><xsl:value-of select="num_records"/></xsl:variable>
	<xsl:variable name="all_records"><xsl:value-of select="all_records"/></xsl:variable>
	<xsl:variable name="nextmatchs_url"><xsl:value-of select="nextmatchs_url"/></xsl:variable>
	<xsl:variable name="first_img"><xsl:value-of select="first_img"/></xsl:variable>
	<xsl:variable name="first_grey_img"><xsl:value-of select="first_grey_img"/></xsl:variable>
	<xsl:variable name="left_img"><xsl:value-of select="left_img"/></xsl:variable>
	<xsl:variable name="left_grey_img"><xsl:value-of select="left_grey_img"/></xsl:variable>
	<xsl:variable name="right_img"><xsl:value-of select="right_img"/></xsl:variable>
	<xsl:variable name="right_grey_img"><xsl:value-of select="right_grey_img"/></xsl:variable>
	<xsl:variable name="last_img"><xsl:value-of select="last_img"/></xsl:variable>
	<xsl:variable name="last_grey_img"><xsl:value-of select="last_grey_img"/></xsl:variable>
	<xsl:variable name="all_img"><xsl:value-of select="all_img"/></xsl:variable>

	<xsl:variable name="img_width"><xsl:value-of select="img_width"/></xsl:variable>
	<xsl:variable name="img_height"><xsl:value-of select="img_height"/></xsl:variable>

	<xsl:variable name="title_first"><xsl:value-of select="title_first"/></xsl:variable>
	<xsl:variable name="title_previous"><xsl:value-of select="title_previous"/></xsl:variable>
	<xsl:variable name="title_next"><xsl:value-of select="title_next"/></xsl:variable>
	<xsl:variable name="title_last"><xsl:value-of select="title_last"/></xsl:variable>
	<xsl:variable name="title_all"><xsl:value-of select="title_all"/></xsl:variable>
	<xsl:variable name="allrows"><xsl:value-of select="allrows"/></xsl:variable>

	<table border="0" width="100%">
		<tr>
			<xsl:choose>
				<xsl:when test="number($cur_record) > number(1)">
				<xsl:variable name="first"><xsl:value-of select="nextmatchs_url"/>&amp;start=0</xsl:variable>
					<td width="25">
						<a href="{$first}"><img src="{$first_img}" border="0" width="{$img_width}" height="{$img_height}" alt="{$title_first}" title="{$title_first}"/></a>
					</td>
				</xsl:when>
				<xsl:otherwise>
					<td width="25">
						<img src="{$first_grey_img}" border="0" width="{$img_width}" height="{$img_height}"/>
					</td>
				</xsl:otherwise>
			</xsl:choose>

			<xsl:choose>
				<xsl:when test="number($cur_record) > number(1)">
				<xsl:variable name="prev_num"><xsl:value-of select="number($cur_record) - number($record_limit)"/></xsl:variable>
					<xsl:choose>
						<xsl:when test="number($prev_num)+number(1) >= number(1)">
							<xsl:choose>
								<xsl:when test="number($cur_record) - number($record_limit) > number(0)">
								<xsl:variable name="prev_number"><xsl:value-of select="number($cur_record) - number($record_limit)"/></xsl:variable>
								<xsl:variable name="prev"><xsl:value-of select="nextmatchs_url"/>&amp;start=<xsl:value-of select="number($prev_number) - number(1)"/></xsl:variable>
									<td width="25">
										<a href="{$prev}"><img src="{$left_img}" border="0" width="{$img_width}" height="{$img_height}" alt="{$title_previous}" title="{$title_previous}"/></a>
									</td>
								</xsl:when>
								<xsl:otherwise>
								<xsl:variable name="prev"><xsl:value-of select="nextmatchs_url"/>&amp;start=0</xsl:variable>
									<td width="25">
										<a href="{$prev}"><img src="{$left_grey_img}" border="0" width="{$img_width}" height="{$img_height}"/></a>
									</td>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:when>
						<xsl:otherwise>
							<td width="25">
								<img src="{$left_grey_img}" border="0" width="{$img_width}" height="{$img_height}"/>
							</td>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:when>
				<xsl:otherwise>
					<td width="25">
						<img src="{$left_grey_img}" border="0" width="{$img_width}" height="{$img_height}"/>
					</td>
				</xsl:otherwise>
			</xsl:choose>
		
			<!-- <xsl:choose>
				<xsl:when test="number($num_records) = number(0)">
					<td nowrap="nowrap" align="center">0 - 0 of 0&#160;</td>
				</xsl:when>
				<xsl:otherwise>
					<xsl:choose>
						<xsl:when test="number($cur_record) + number($record_limit) > number($num_records)">
						<xsl:variable name="of_num"><xsl:value-of select="number($cur_record)+number($num_records) - number(1)"/></xsl:variable>
							<td nowrap="nowrap" align="center">
								<xsl:value-of select="$cur_record"/> - <xsl:value-of select="$of_num"/> of <xsl:value-of select="$all_records"/>&#160;
							</td>
						</xsl:when>
						<xsl:otherwise>
						<xsl:variable name="of_num"><xsl:value-of select="number($cur_record) + number($record_limit) - number(1)"/></xsl:variable>
							<td nowrap="nowrap" align="center">
								<xsl:value-of select="$cur_record"/> - <xsl:value-of select="$of_num"/> of <xsl:value-of select="$all_records"/>&#160;
							</td>
						</xsl:otherwise>
					</xsl:choose>							
				</xsl:otherwise>
			</xsl:choose> -->

			<td nowrap="nowrap" align="center"><xsl:value-of select="lang_showing"/></td>
			<xsl:choose>
				<xsl:when test="number($all_records) > (number($start_record) + number($record_limit))">
				<xsl:variable name="next_num"><xsl:value-of select="number($cur_record) + number($record_limit)"/></xsl:variable>
					<xsl:choose>
						<xsl:when test="number($all_records) > number($next_num)-number(1)">
						<xsl:variable name="next"><xsl:value-of select="nextmatchs_url"/>&amp;start=<xsl:value-of select="number($next_num) - number(1)"/></xsl:variable>
							<td width="25" align="right">
								<a href="{$next}"><img src="{$right_img}" border="0" width="{$img_width}" height="{$img_height}" alt="{$title_next}" title="{$title_next}"/></a>
							</td>
						</xsl:when>
						<xsl:otherwise>
							<td width="25" align="right">
								<img src="{$right_grey_img}" border="0" width="{$img_width}" height="{$img_height}"/>
							</td>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:when>
				<xsl:otherwise>
					<td width="25" align="right">
						<img src="{$right_grey_img}" border="0" width="{$img_width}" height="{$img_height}"/>
					</td>
				</xsl:otherwise>
			</xsl:choose>

			<xsl:choose>
				<xsl:when test="number($all_records) > (number($start_record) + number($record_limit))">
				<xsl:variable name="last_num"><xsl:value-of select="number($all_records)-number($record_limit)+number(1)"/></xsl:variable>
					<xsl:choose>
						<xsl:when test="number($last_num) > number($cur_record)">
						<xsl:variable name="last"><xsl:value-of select="nextmatchs_url"/>&amp;start=<xsl:value-of select="number($last_num)-number(1)"/></xsl:variable>
							<td width="25" align="right">
								<a href="{$last}"><img src="{$last_img}" border="0" width="{$img_width}" height="{$img_height}" alt="{$title_last}" title="{$title_last}"/></a>
							</td>
						</xsl:when>
						<xsl:otherwise>
							<td width="25" align="right">
								<img src="{$last_grey_img}" border="0" width="{$img_width}" height="{$img_height}"/>
							</td>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:when>
				<xsl:otherwise>
					<td width="25" align="right">
						<img src="{$last_grey_img}" border="0" width="{$img_width}" height="{$img_height}"/>
					</td>
				</xsl:otherwise>
			</xsl:choose>

			<xsl:choose>
				<xsl:when test="allow_all_rows = 1">
					<xsl:choose>
						<xsl:when test="allrows = 1">
						<xsl:variable name="all"><xsl:value-of select="nextmatchs_url"/>&amp;start=0</xsl:variable>
							<td width="25" align="right">
								<a href="{$all}"><img src="{$all_img}" border="0" alt="{$title_all}" title="{$title_all}"/></a>
							</td>
						</xsl:when>
						<xsl:otherwise>
						<xsl:variable name="all"><xsl:value-of select="nextmatchs_url"/>&amp;allrows=1</xsl:variable>
							<td width="25" align="right">
								<a href="{$all}"><img src="{$all_img}" border="0" alt="{$title_all}" title="{$title_all}"/></a>
							</td>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:when>
			</xsl:choose>

		</tr>
	</table>
</xsl:template>
