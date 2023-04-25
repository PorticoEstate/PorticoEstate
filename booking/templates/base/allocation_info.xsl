<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<!--div class="close" style="float: right"><a onclick="schedule.closeOverlay(); return false" style="cursor:pointer;"><xsl:value-of select="php:function('lang', 'Close')"/></a></div-->
	<h3>
		<xsl:value-of select="php:function('lang', 'Allocation')"/> #<xsl:value-of select="allocation/id"/>
	</h3>
	<div class="pure-form pure-form-aligned">
		<xsl:if test="allocation/application_id!=''">
			<div class="pure-control-group">
				<label>
					<xsl:value-of select="php:function('lang', 'Application')"/>
				</label>
				<a href="{allocation/application_link}">#<xsl:value-of select="allocation/application_id"/></a>
			</div>
		</xsl:if>
		<div class="pure-control-group">
			<label>
				<xsl:value-of select="php:function('lang', 'Where')"/>
			</label>
			<a href="{allocation/building_link}">
				<xsl:value-of select="allocation/building_name"/>
			</a>
			(<xsl:value-of select="allocation/resource_info"/>)
		</div>

		<div class="pure-control-group">
			<label>
				<xsl:value-of select="php:function('lang', 'When')"/>
			</label>
			<xsl:value-of select="allocation/when"/>
		</div>
		<div class="pure-control-group">
			<label>
				<xsl:value-of select="php:function('lang', 'Who')"/>
			</label>
			<a href="{allocation/org_link}">
				<xsl:value-of select="allocation/organization_name"/>
			</a>
		</div>
	</div>
	<xsl:if test="allocation/add_link">
		<div class="actions">
			<button onclick="location.href='{allocation/add_link}'" class="pure-button pure-button-primary">
				<xsl:value-of select="php:function('lang', 'Create new booking')"/>
			</button>
			<xsl:if test="allocation/delete_link">
				<button onclick="location.href='{allocation/delete_link}'" class="pure-button pure-button-primary">
					<xsl:value-of select="php:function('lang', 'Delete allocation')"/>
				</button>
			</xsl:if>
		</div>
	</xsl:if>
</xsl:template>
