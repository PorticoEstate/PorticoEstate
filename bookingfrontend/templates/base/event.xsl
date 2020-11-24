<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div class="container wrapper">
		<span class="d-block">
			<xsl:text>#</xsl:text>
			<xsl:value-of select="event/id"/>
		</span>
		<h3>
			<xsl:if test="event/is_public=0">
				<xsl:value-of select="php:function('lang', 'Private event')"/>
			</xsl:if>
			<xsl:if test="event/is_public=1">
				<xsl:value-of select="event/name"/>
			</xsl:if>
		</h3>
		<span class="d-block">
			<xsl:value-of select="event/when"/>
		</span>
		<div>
			<span class="font-weight-bold text-uppercase">
				<xsl:value-of select="php:function('lang', 'Place')"/>:
			</span>
			<a href="{event/building_link}">
				<xsl:value-of select="event/building_name"/>
			</a>
			(<xsl:value-of select="event/resource_info"/>)
		</div>
		<div>
			<span class="font-weight-bold text-uppercase">
				<xsl:value-of select="php:function('lang', 'Organizer')"/>:
			</span>
			<xsl:if test="event/is_public=0">
				<br/>
				<xsl:value-of select="php:function('lang', 'Private event')"/>
			</xsl:if>
			<xsl:if test="event/is_public=1">
				<xsl:value-of select="event/organizer"/>
			</xsl:if>
		</div>
		<xsl:if test="event/is_public=1">
			<div class="tooltip-desc-btn">
				<xsl:if test="event/contact_email != '' or event/contact_phone != ''">
					<span>
						<i class="fas fa-info-circle"></i>
					</span>
				</xsl:if>
				<p class="tooltip-desc">
					<span class="d-block font-weight-normal">
						<xsl:if test="event/contact_email != ''">
							<br/>
							<xsl:value-of select="php:function('lang', 'contact_email')"/>: <xsl:value-of select="event/contact_email"/>
						</xsl:if>
						<xsl:if test="event/contact_phone != ''">
							<br/>
							<xsl:value-of select="php:function('lang', 'contact_phone')"/>: <xsl:value-of select="event/contact_phone"/>
						</xsl:if>
					</span>
				</p>
			</div>
		</xsl:if>

		<xsl:if test="event/participant_limit > 0">
			<p class="mt-2">
				<xsl:value-of select="php:function('lang', 'participant limit')" />:
				<xsl:value-of select="event/participant_limit"/>
			</p>
			<p class="mt-2">
				<!--<a href="{event/get_participants_link}" target="_blank">-->
					<xsl:value-of select="php:function('lang', 'number of participants')" />:
					<xsl:value-of select="event/number_of_participants" />
				<!--</a>-->
			</p>

			<span class="mt-2">
				<xsl:value-of select="event/participanttext" disable-output-escaping="yes"/>
			</span>

			<xsl:variable name="lang_registration">
				<xsl:value-of select="php:function('lang', 'registration')"/>
			</xsl:variable>

			<div class="mt-4">
				<a href="{event/participant_registration_link}" title="{$lang_registration}">
					For registering: enten klikk på lenken, eller skanne Qr-kode
				</a>
			</div>
			<div class="mt-4">
				<a href="{event/participant_registration_link}" title="{$lang_registration}">
					<img src="{event/encoded_qr}" alt="{$lang_registration}"/>
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
