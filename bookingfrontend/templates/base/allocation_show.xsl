<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div class="container wrapper">
		<span class="d-block">
			<xsl:text>#</xsl:text>
			<xsl:value-of select="allocation/id"/>
		</span>
		<h3>
			<!--<xsl:value-of select="php:function('lang', 'Allocation')"/> #
			<xsl:value-of select="allocation/id"/>-->
			<!--<a href="{allocation/org_link}">-->
			<xsl:value-of select="allocation/organization_name"/>
			<!--</a>-->
		</h3>
		<span class="d-block">
			<xsl:value-of select="allocation/when"/>
		</span>

		<div>
			<span class="font-weight-bold text-uppercase">
				<xsl:value-of select="php:function('lang', 'Place')"/>:
			</span>
			<a href="{allocation/building_link}">
				<xsl:value-of select="allocation/building_name"/>
			</a>
			(<xsl:value-of select="allocation/resource_info"/>)
		</div>
		<xsl:if test="allocation/contact_email != '' or allocation/contact_phone != '' or orginfo/name != ''">
			<div class="tooltip-desc-btn">
				<span>
					<i class="fas fa-info-circle"></i>
				</span>
				<p class="tooltip-desc">
					<span class="d-block font-weight-normal">
						<xsl:value-of select="allocation/description" disable-output-escaping="yes"/>
						<xsl:if test="allocation/is_public=1">
							<xsl:if test="orginfo">
								<a href="{orginfo/link}">
									<xsl:value-of select="orginfo/name"/>
								</a>:
							</xsl:if>
							<xsl:value-of select="allocation/contact_name"/>
							<xsl:if test="allocation/contact_email != ''">
								<br/>
								<xsl:value-of select="php:function('lang', 'contact_email')"/>: <xsl:value-of select="allocation/contact_email"/>
							</xsl:if>
							<xsl:if test="allocation/contact_phone != ''">
								<br/>
								<xsl:value-of select="php:function('lang', 'contact_phone')"/>: <xsl:value-of select="allocation/contact_phone"/>
							</xsl:if>
						</xsl:if>
						<xsl:if test="allocation/is_public=0">
							<xsl:value-of select="php:function('lang', 'Private event')"/>
						</xsl:if>
					</span>
				</p>
			</div>
		</xsl:if>
		<xsl:if test="allocation/participant_limit > 0">
			<p class="mt-2">
				<xsl:value-of select="php:function('lang', 'participant limit')" />:
				<xsl:value-of select="allocation/participant_limit"/>
			</p>
			<span class="mt-2">
				<!--<a href="{allocation/get_participants_link}" target="_blank">-->
					<xsl:value-of select="php:function('lang', 'number of participants')" />:
					<xsl:value-of select="allocation/number_of_participants" />
				<!--</a>-->
			</span>

			<span class="mt-2">
				<xsl:value-of select="allocation/participanttext" disable-output-escaping="yes"/>
			</span>

			<xsl:variable name="lang_registration">
				<xsl:value-of select="php:function('lang', 'registration')"/>
			</xsl:variable>

			<div class="mt-4">
				<a href="{allocation/participant_registration_link}" title="{$lang_registration}">
					For registering: enten klikk på lenken, eller skanne Qr-kode
				</a>
			</div>
			<div class="mt-4">
				<a href="{allocation/participant_registration_link}" title="{$lang_registration}">
					<img src="{allocation/encoded_qr}" alt="{$lang_registration}"/>
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
