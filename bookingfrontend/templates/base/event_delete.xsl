<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div id="event-delete-page-content" class="margin-top-content">
        	<div class="container wrapper">
				<div class="location">
					<span>
						<a><xsl:attribute name="href">
								<xsl:value-of select="php:function('get_phpgw_link', '/bookingfrontend/index.php', 'menuaction:bookingfrontend.uisearch.index')"/>
							</xsl:attribute>
							<xsl:value-of select="php:function('lang', 'Home')" />
						</a>
					</span>
					<span><xsl:value-of select="php:function('lang', 'Delete Event')"/></span>
					<span>#<xsl:value-of select="event/id"/></span>										
				</div>

            	<div class="row">
					<div class="col-12">					
						<xsl:if test="can_delete_events=1">
							<dd>
								<xsl:value-of select="php:function('lang', 'Event Delete Information')"/>
							</dd>
						</xsl:if>
						<xsl:if test="can_delete_events=0">
							<dd>
								<xsl:value-of select="php:function('lang', 'Event Delete Information2')"/>
							</dd>
						</xsl:if>
					</div>
					<form action="" method="POST" id="form" name="form" class="col-md-8">

						<div class="col mb-4">
							<xsl:call-template name="msgbox"/>
						</div>

							<div class="form-group">
								<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Building (2018)')" /></label>
								<xsl:value-of select="event/building_name"/>
							</div>

					<div class="form-group">
						<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Event name')" /></label>
						<xsl:value-of select="event/name"/>
					</div>

							<div class="form-group">
								<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Description')" /></label>
								<xsl:value-of select="event/description"/>
							</div>

							<div class="form-group">
								<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Activity')" /></label>
								<xsl:for-each select="activities">
									<xsl:if test="../event/activity_id = id">
										<xsl:value-of select="name"/>
									</xsl:if>
								</xsl:for-each>
							</div>

							<div class="form-group">
								<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'From')" /></label>
								<xsl:value-of select="event/from_"/>
							</div>

							<div class="form-group">
								<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'To')"/></label>
								<xsl:value-of select="event/to_"/>
							</div>

							<div class="form-group">
								<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Message')" /></label>
								<textarea id="field-message" class="form-control" name="message" type="text">
									<xsl:value-of select="system_message/message"/>
								</textarea>
							</div>

						<div class="form-group mt-5">
							<input type="submit" class="btn btn-light mr-4">
								<xsl:attribute name="value">
									<xsl:value-of select="php:function('lang', 'Delete')"/>
								</xsl:attribute>
							</input>
							<a class="cancel">
								<xsl:attribute name="href" class="btn btn-light mr-4">
									<xsl:value-of select="event/cancel_link"/>
								</xsl:attribute>
								<xsl:value-of select="php:function('lang', 'Cancel')"/>
							</a>
						</div>
					</form>
            	</div>         
            
        	</div>
    	
	</div>
    <div class="push"></div>

	<script type="text/javascript">
		var initialSelection = <xsl:value-of select="booking/resources_json" />
		var lang = <xsl:value-of select="php:function('js_lang', 'Resource Type')" />;
	</script>
</xsl:template>
