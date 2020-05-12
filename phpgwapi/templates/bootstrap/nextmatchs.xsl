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
	<xsl:variable name="allow_all_rows">
		<xsl:value-of select="allow_all_rows"/>
	</xsl:variable>
	<xsl:variable name="start_record">
		<xsl:value-of select="start_record"/>
	</xsl:variable>
	<xsl:variable name="cur_record">
		<xsl:value-of select="number($start_record) + number(1)"/>
	</xsl:variable>
	<xsl:variable name="record_limit">
		<xsl:value-of select="record_limit"/>
	</xsl:variable>
	<xsl:variable name="num_records">
		<xsl:value-of select="num_records"/>
	</xsl:variable>
	<xsl:variable name="all_records">
		<xsl:value-of select="all_records"/>
	</xsl:variable>
	<xsl:variable name="nextmatchs_url">
		<xsl:value-of select="nextmatchs_url"/>&amp;query=<xsl:value-of select="query"/>
	</xsl:variable>


	<xsl:variable name="title_first">
		<xsl:value-of select="title_first"/>
	</xsl:variable>
	<xsl:variable name="title_previous">
		<xsl:value-of select="title_previous"/>
	</xsl:variable>
	<xsl:variable name="title_next">
		<xsl:value-of select="title_next"/>
	</xsl:variable>
	<xsl:variable name="title_last">
		<xsl:value-of select="title_last"/>
	</xsl:variable>
	<xsl:variable name="title_all">
		<xsl:value-of select="title_all"/>
	</xsl:variable>
	<xsl:variable name="allrows">
		<xsl:value-of select="allrows"/>
	</xsl:variable>

	<ul class="pagination justify-content-left mt-2">
		<xsl:choose>
			<xsl:when test="number($cur_record) > number(1)">
				<xsl:variable name="first">
					<xsl:value-of select="$nextmatchs_url"/>&amp;start=0</xsl:variable>
				<li class="page-item">
					<a class="page-link" href="{$first}">
						<xsl:value-of select="$title_first"/>
					</a>
				</li>
			</xsl:when>
			<xsl:otherwise>
				<li class="page-item disabled">
					<div class="page-link">
						<xsl:value-of select="$title_first"/>
					</div>
				</li>
			</xsl:otherwise>
		</xsl:choose>

		<xsl:choose>
			<xsl:when test="number($cur_record) > number(1)">
				<xsl:variable name="prev_num">
					<xsl:value-of select="number($cur_record) - number($record_limit)"/>
				</xsl:variable>
				<xsl:choose>
					<xsl:when test="number($prev_num)+number(1) >= number(1)">
						<xsl:choose>
							<xsl:when test="number($cur_record) - number($record_limit) > number(0)">
								<xsl:variable name="prev_number">
									<xsl:value-of select="number($cur_record) - number($record_limit)"/>
								</xsl:variable>
								<xsl:variable name="prev">
									<xsl:value-of select="$nextmatchs_url"/>&amp;start=<xsl:value-of select="number($prev_number) - number(1)"/>
								</xsl:variable>
								<li class="page-item">
									<a class="page-link" href="{$prev}" aria-label="{$title_previous}" title="{$title_previous}">
										<span aria-hidden="true">&#171;</span>
										<span class="sr-only">
											<xsl:value-of select="$title_previous"/>
										</span>
									</a>
								</li>
							</xsl:when>
							<xsl:otherwise>
								<xsl:variable name="prev">
									<xsl:value-of select="$nextmatchs_url"/>&amp;start=0</xsl:variable>
								<li class="page-item disabled">
									<a class="page-link" href="{$prev}" aria-label="{$title_previous}" title="{$title_previous}">
										<span aria-hidden="true">&#171;</span>
										<span class="sr-only">
											<xsl:value-of select="$title_previous"/>
										</span>
									</a>
								</li>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:when>
					<xsl:otherwise>
						<li class="page-item disabled">
							<a class="page-link" href="#">
								<span aria-hidden="true">
									<span aria-hidden="true">&#171;</span>
								</span>
								<span class="sr-only">
									<xsl:value-of select="$title_previous"/>
								</span>
							</a>
						</li>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:when>
			<xsl:otherwise>
				<li class="page-item disabled">
					<a class="page-link" href="#" aria-label="{$title_previous}" title="{$title_previous}">
						<span aria-hidden="true">&#171;</span>
						<span class="sr-only">
							<xsl:value-of select="$title_previous"/>
						</span>
					</a>
				</li>
			</xsl:otherwise>
		</xsl:choose>
	
		<li class="page-item disabled">
			<div class="page-link">
				<xsl:value-of select="lang_showing"/>
			</div>
		</li>
		<xsl:choose>
			<xsl:when test="number($all_records) > (number($start_record) + number($record_limit)) and allrows !=1">
				<xsl:variable name="next_num">
					<xsl:value-of select="number($cur_record) + number($record_limit)"/>
				</xsl:variable>
				<xsl:choose>
					<xsl:when test="number($all_records) > number($next_num)-number(1)">
						<xsl:variable name="next">
							<xsl:value-of select="$nextmatchs_url"/>&amp;start=<xsl:value-of select="number($next_num) - number(1)"/>
						</xsl:variable>
						<li class="page-item">
							<a class="page-link" href="{$next}" aria-label="{$title_next}" title="{$title_next}">
								<span aria-hidden="true">&#187;</span>
								<span class="sr-only">
									<xsl:value-of select="$title_next"/>
								</span>
							</a>
						</li>
					</xsl:when>
					<xsl:otherwise>
						<li class="page-item disabled">
							<a class="page-link" href="#">
								<span aria-hidden="true">&#187;</span>
								<span class="sr-only">
									<xsl:value-of select="$title_next"/>
								</span>
							</a>
						</li>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:when>
			<xsl:otherwise>
				<li class="page-item disabled">
					<a class="page-link" href="#">
						<span aria-hidden="true">&#187;</span>
						<span class="sr-only">
							<xsl:value-of select="$title_next"/>
						</span>
					</a>
				</li>
			</xsl:otherwise>
		</xsl:choose>

		<xsl:choose>
			<xsl:when test="number($all_records) > (number($start_record) + number($record_limit)) and allrows !=1">
				<xsl:variable name="last_num">
					<xsl:value-of select="number($all_records)-number($record_limit)+number(1)"/>
				</xsl:variable>
				<xsl:choose>
					<xsl:when test="number($last_num) > number($cur_record)">
						<xsl:variable name="last">
							<xsl:value-of select="$nextmatchs_url"/>&amp;start=<xsl:value-of select="number($last_num)-number(1)"/>
						</xsl:variable>
						<li class="page-item">
							<a class="page-link" href="{$last}">
								<xsl:value-of select="$title_last"/>
							</a>
						</li>
					</xsl:when>
					<xsl:otherwise>
						<li class="page-item disabled">
							<div class="page-link">
								<xsl:value-of select="$title_last"/>
							</div>
						</li>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:when>
			<xsl:otherwise>
				<li class="page-item disabled">
					<div class="page-link">
						<xsl:value-of select="$title_last"/>
					</div>
				</li>
			</xsl:otherwise>
		</xsl:choose>

		<xsl:choose>
			<xsl:when test="allow_all_rows = 1">
				<xsl:choose>
					<xsl:when test="allrows = 1">
						<xsl:variable name="all">
							<xsl:value-of select="$nextmatchs_url"/>
							<xsl:text>&amp;start=0&amp;allrows=0</xsl:text>
						</xsl:variable>
						<li class="page-item">
							<a class="page-link" href="{$all}">
								<xsl:value-of select="$title_all"/>
							</a>
						</li>
					</xsl:when>
					<xsl:otherwise>
						<xsl:variable name="all">
							<xsl:value-of select="$nextmatchs_url"/>
							<xsl:text>&amp;allrows=1</xsl:text>
						</xsl:variable>
						<li class="page-item">
							<a class="page-link" href="{$all}">
								<xsl:value-of select="$title_all"/>
							</a>
						</li>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:when>
		</xsl:choose>
	</ul>
</xsl:template>
