<!-- $Id: view_calendar_month.xsl 9200 2012-04-21 20:05:34Z vator $ -->
<xsl:template name="check_list_status_checker" xmlns:php="http://php.net/xsl">
 
 <xsl:param name="location_code" />
 
	<xsl:choose>
		<xsl:when test="status = 'CONTROL_REGISTERED'">
			<td>
			<a>
				<xsl:attribute name="href">
					<xsl:text>index.php?menuaction=controller.uicheck_list.add_check_list</xsl:text>
					<xsl:text>&amp;date=</xsl:text>
					<xsl:value-of select="info/date"/>
					<xsl:text>&amp;control_id=</xsl:text>
					<xsl:value-of select="info/control_id"/>
					<xsl:text>&amp;location_code=</xsl:text>
					<xsl:value-of select="$location_code"/>
				</xsl:attribute>
				<img height="15" src="controller/images/status_icon_yellow_ring.png" />
			</a>
			</td>
		</xsl:when>
		<xsl:when test="status = 'CONTROL_PLANNED'">
			<td>
			<a>
				<xsl:attribute name="href">
					<xsl:text>index.php?menuaction=controller.uicheck_list.edit_check_list</xsl:text>
					<xsl:text>&amp;check_list_id=</xsl:text>
					<xsl:value-of select="info/check_list_id"/>
				</xsl:attribute>
				<img height="15" src="controller/images/status_icon_yellow.png" />
			</a>
			</td>
		</xsl:when>
		<xsl:when test="status = 'CONTROL_NOT_DONE'">
			<td>
				<a>
					<xsl:attribute name="href">
						<xsl:text>index.php?menuaction=controller.uicheck_list.add_check_list</xsl:text>
						<xsl:text>&amp;date=</xsl:text>
						<xsl:value-of select="info/date"/>
						<xsl:text>&amp;control_id=</xsl:text>
						<xsl:value-of select="info/control_id"/>
						<xsl:text>&amp;location_code=</xsl:text>
						<xsl:value-of select="$location_code"/>
					</xsl:attribute>
					<img height="15" src="controller/images/status_icon_red_cross.png" />
				</a>
			</td>
		</xsl:when>
		<xsl:when test="status = 'CONTROL_NOT_DONE_WITH_PLANNED_DATE'">
			<td>
			<a>
				<xsl:attribute name="href">
					<xsl:text>index.php?menuaction=controller.uicheck_list.edit_check_list</xsl:text>
					<xsl:text>&amp;check_list_id=</xsl:text>
					<xsl:value-of select="info/check_list_id"/>
				</xsl:attribute>
				<img height="15" src="controller/images/status_icon_red_cross.png" />
			</a>
			</td>
	</xsl:when>
		<xsl:when test="status = 'CONTROL_DONE_IN_TIME_WITHOUT_ERRORS'">
			<td>
				<a>
				<xsl:attribute name="href">
					<xsl:text>index.php?menuaction=controller.uicheck_list.edit_check_list</xsl:text>
					<xsl:text>&amp;check_list_id=</xsl:text>
					<xsl:value-of select="info/check_list_id"/>
				</xsl:attribute>
					<span style="display:none"><xsl:value-of select="info/id"/></span>
					<img height="15" src="controller/images/status_icon_dark_green.png" />
				</a>
			</td>
		</xsl:when>
		<xsl:when test="status = 'CONTROL_DONE_OVER_TIME_WITHOUT_ERRORS'">
			<td>
				<div style="position:relative;">
   					<div id="info_box"></div>
					<a>
					<xsl:attribute name="href">
						<xsl:text>index.php?menuaction=controller.uicheck_list.edit_check_list</xsl:text>
						<xsl:text>&amp;check_list_id=</xsl:text>
						<xsl:value-of select="info/check_list_id"/>
					</xsl:attribute>
						<span style="display:none"><xsl:value-of select="info/id"/></span>
						<img height="15" src="controller/images/status_icon_light_green.png" />
					</a>
				</div>
			</td>
		</xsl:when>
		<xsl:when test="status = 'CONTROL_DONE_WITH_ERRORS'">
			<td>
				<div class="info_box_wrp">
					<div id="info_box"></div>
   					<a class="view_info_box">
					 	<xsl:attribute name="href">
							<xsl:text>index.php?menuaction=controller.uicheck_list.edit_check_list</xsl:text>
							<xsl:text>&amp;check_list_id=</xsl:text>
							<xsl:value-of select="info/check_list_id"/>
						</xsl:attribute>
						<span style="display:none">
							<xsl:text>&amp;check_list_id=</xsl:text><xsl:value-of select="info/check_list_id"/>
							<xsl:text>&amp;phpgw_return_as=json</xsl:text>
						</span>
						<xsl:value-of select="info/num_open_cases"/>
					</a>
				</div>
			</td>
		</xsl:when>
		<xsl:when test="status = 'CONTROLS_DONE_WITH_ERRORS'">
			<td>
				<div class="info_box_wrp">
					<div id="info_box"></div>
   					<a class="view_info_box">
					 	<xsl:attribute name="href">
							<xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_for_month</xsl:text>
							<xsl:text>&amp;control_id=</xsl:text>
							<xsl:value-of select="//control/id"/>
							<xsl:text>&amp;location_code=</xsl:text>
							<xsl:value-of select="//current_location/location_code"/>
							<xsl:text>&amp;year=</xsl:text>
							<xsl:value-of select="//current_year"/>
							<xsl:text>&amp;month=</xsl:text>
							<xsl:number />
						</xsl:attribute>
						<span style="display:none">
							<xsl:text>&amp;check_list_id=</xsl:text><xsl:value-of select="info/check_list_id"/>
							<xsl:text>&amp;phpgw_return_as=json</xsl:text>
						</span>
						<xsl:value-of select="info/agg_open_errors"/>
					</a>
				</div>
			</td>
		</xsl:when>
		<xsl:when test="status = 'CONTROL_CANCELED'">
			<td>
				<img height="15" src="controller/images/status_icon_red_cross.png" />
			</td>
		</xsl:when>
		<xsl:otherwise>
			<td></td>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>
