<!-- $Id: nextmatchs.xsl,v 1.3 2006/09/20 08:42:09 sigurdne Exp $ -->

<xsl:template name="nextmatchs">
	<xsl:variable name="allow_allrows"><xsl:value-of select="allow_allrows"/></xsl:variable>
	<xsl:variable name="start_record"><xsl:value-of select="start_record"/></xsl:variable>
	<xsl:variable name="cur_record"><xsl:value-of select="number($start_record) + number(1)"/></xsl:variable>
	<xsl:variable name="record_limit"><xsl:value-of select="record_limit"/></xsl:variable>
	<xsl:variable name="num_records"><xsl:value-of select="num_records"/></xsl:variable>
	<xsl:variable name="all_records"><xsl:value-of select="all_records"/></xsl:variable>
	<xsl:variable name="link_url"><xsl:value-of select="link_url"/></xsl:variable>
	<xsl:variable name="img_path"><xsl:value-of select="img_path"/></xsl:variable>

	<xsl:choose>
		<xsl:when test="number($num_records) = number(0)">
			0 - 0 of 0
		</xsl:when>
		<xsl:otherwise>
			<xsl:choose>
				<xsl:when test="number($cur_record) + number($record_limit) > number($num_records)">
				<xsl:variable name="of_num"><xsl:value-of select="number($cur_record)+number($num_records) - number(1)"/></xsl:variable>
					<xsl:value-of select="$cur_record"/> - <xsl:value-of select="$of_num"/> of <xsl:value-of select="$all_records"/>
				</xsl:when>
				<xsl:otherwise>
				<xsl:variable name="of_num"><xsl:value-of select="number($cur_record) + number($record_limit) - number(1)"/></xsl:variable>
					<xsl:value-of select="$cur_record"/> - <xsl:value-of select="$of_num"/> of <xsl:value-of select="$all_records"/>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:otherwise>
	</xsl:choose>

	<xsl:choose>
		<xsl:when test="number($cur_record) > number(1)">
		<xsl:variable name="first"><xsl:value-of select="link_url"/>&amp;start=0</xsl:variable>
			<a href="{$first}">First</a>
		</xsl:when>
		<xsl:otherwise>
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
						<xsl:variable name="prev"><xsl:value-of select="link_url"/>&amp;start=<xsl:value-of select="number($prev_number) - number(1)"/></xsl:variable>
							<a href="{$prev}">Prev</a>
						</xsl:when>
						<xsl:otherwise>
						<xsl:variable name="prev"><xsl:value-of select="link_url"/>&amp;start=0</xsl:variable>
							<a href="{$prev}">Prev</a>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:when>
		<xsl:otherwise>
		</xsl:otherwise>
	</xsl:choose>

	<xsl:choose>
		<xsl:when test="number($all_records) > (number($start_record) + number($record_limit))">
		<xsl:variable name="next_num"><xsl:value-of select="number($cur_record) + number($record_limit)"/></xsl:variable>
			<xsl:choose>
				<xsl:when test="number($all_records) > number($next_num)-number(1)">
				<xsl:variable name="next"><xsl:value-of select="link_url"/>&amp;start=<xsl:value-of select="number($next_num) - number(1)"/></xsl:variable>
					<a href="{$next}">Next</a>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:when>
		<xsl:otherwise>
		</xsl:otherwise>
	</xsl:choose>

	<xsl:choose>
		<xsl:when test="number($all_records) > (number($start_record) + number($record_limit))">
		<xsl:variable name="last_num"><xsl:value-of select="number($all_records)-number($record_limit)+number(1)"/></xsl:variable>
			<xsl:choose>
				<xsl:when test="number($last_num) > number($cur_record)">
				<xsl:variable name="last"><xsl:value-of select="link_url"/>&amp;start=<xsl:value-of select="number($last_num)-number(1)"/></xsl:variable>
					<a href="{$last}">Last</a>
				</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:when>
		<xsl:otherwise>
		</xsl:otherwise>
	</xsl:choose>

	<xsl:choose>
		<xsl:when test="$allow_allrows=1">
			<xsl:choose>
				<xsl:when test="allrows =1">
				<xsl:variable name="all"><xsl:value-of select="link_url"/>&amp;start=0</xsl:variable>
						<a href="{$all}">All</a>
				</xsl:when>
				<xsl:otherwise>
				<xsl:variable name="all"><xsl:value-of select="link_url"/>&amp;allrows=1</xsl:variable>
					<a href="{$all}">All</a>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:when>
	</xsl:choose>
</xsl:template>
