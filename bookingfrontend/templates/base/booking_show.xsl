<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div class="container wrapper">
		<span class="d-block">
			<xsl:text>#</xsl:text>
			<xsl:value-of select="booking/id"/>
		</span>
		<h3>
			<xsl:value-of select="booking/group/organization_name"/>
		</h3>
		<div class="mb-3">
			<span class="font-weight-bold text-uppercase">
				<xsl:value-of select="php:function('lang', 'Group (2018)')"/>:
			</span>
			<a href="{booking/group_link}">
				<xsl:value-of select="booking/group/name"/>
			</a>
		</div>
		<span class="d-block">
			<xsl:value-of select="booking/when"/>
		</span>
		<div>
			<span class="font-weight-bold text-uppercase">
				<xsl:value-of select="php:function('lang', 'Place')"/>:
			</span>
			<a href="{booking/building_link}">
				<xsl:value-of select="booking/building_name"/>
			</a>
			(<xsl:value-of select="booking/resource_info"/>)
		</div>
		<xsl:if test="booking/participant_limit > 0">
			<p class="mt-2">
				<xsl:value-of select="php:function('lang', 'participant limit')" />:
				<xsl:value-of select="booking/participant_limit"/>
			</p>
			<span class="mt-2">
				<a href="{booking/get_participants_link}" target="_blank">
					<xsl:value-of select="php:function('lang', 'number of participants')" />:
					<xsl:value-of select="booking/number_of_participants" />
				</a>
			</span>

			<span class="mt-2">
				<xsl:value-of select="booking/participanttext" disable-output-escaping="yes"/>
			</span>
			<xsl:variable name="lang_registration">
				<xsl:value-of select="php:function('lang', 'registration')"/>
			</xsl:variable>

			<div class="mt-4">
				<a href="{booking/participant_registration_link}" title="{$lang_registration}">
					For registering: enten klikk på lenken, eller skanne Qr-kode
				</a>
			</div>

			<div class="mt-4">
				<a href="{booking/participant_registration_link}" title="{$lang_registration}">
					<img src="{booking/encoded_qr}" alt="{$lang_registration}"/>
				</a>
			</div>

			<xsl:for-each select="datatable_def">
				<xsl:if test="container = 'datatable-container_0'">
					<div style="width:15em;">
						<xsl:call-template name="table_setup">
							<xsl:with-param name="container" select ='container'/>
							<xsl:with-param name="requestUrl" select ='requestUrl'/>
							<xsl:with-param name="ColumnDefs" select ='ColumnDefs'/>
							<xsl:with-param name="data" select ='data'/>
							<xsl:with-param name="config" select ='config'/>
						</xsl:call-template>
					</div>
				</xsl:if>
			</xsl:for-each>

		</xsl:if>
	</div>
</xsl:template>
