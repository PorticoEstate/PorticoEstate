<func:function name="phpgw:conditional">
	<xsl:param name="test"/>
	<xsl:param name="true"/>
	<xsl:param name="false"/>

	<func:result>
		<xsl:choose>
			<xsl:when test="$test">
				<xsl:value-of select="$true"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$false"/>
			</xsl:otherwise>
		</xsl:choose>
	</func:result>
</func:function>

<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div id="content">
		<dl class="form">
			<dt class="heading"><xsl:value-of select="php:function('lang', 'Export Settings')"/></dt>
		</dl>

		<!-- <ul class="pathway">
		<li>
			<a href="{export/buildings_link}">
				<xsl:value-of select="php:function('lang', 'Export')" />
			</a>
		</li>
		<xsl:if test="not(new_form)">
			<li>
				<a href="{export/link}">
					<xsl:value-of select="export/id"/>
				</a>
			</li>
		</xsl:if>
	</ul> -->

	<xsl:call-template name="msgbox"/>
	<xsl:call-template name="yui_booking_i18n"/>

	<form action="" method="POST">
		<dl class="form-col">				
			<dt><label for="field_season_id"><xsl:value-of select="php:function('lang', 'Season')" /></label></dt>
			<dd><input name="season_id" type="text" id="field_season_id" value="{export/season_id}"/></dd>

			<dt><label for="field_building_id"><xsl:value-of select="php:function('lang', 'Building')" /></label></dt>
			<dd><input name="building_id" type="text" id="field_building_id" value="{export/building_id}"/></dd>

			<!-- <dt><label for="field_from"><xsl:value-of select="php:function('lang', 'From')"/></label></dt>
			<dd>
				<div class="date-picker">
					<input id="field_from" name="from_" type="text" value='{export/from_}'/>
				</div>
			</dd> -->

			<dt><label for="field_to"><xsl:value-of select="php:function('lang', 'To')"/></label></dt>
			<dd>
				<div class="date-picker">
					<input id="field_to" name="to_" type="text" value='{export/to_}'/>
				</div>
			</dd>
		</dl>

		<div class="clr"/>

		<div class="form-buttons">
			<input type="submit" value="{php:function('lang', phpgw:conditional(new_form, 'Export', 'Save'))}"/>
			<a class="cancel" href="{export/cancel_link}">
				<xsl:value-of select="php:function('lang', 'Cancel')" />
			</a>
		</div>
	</form>
</div>
</xsl:template>


