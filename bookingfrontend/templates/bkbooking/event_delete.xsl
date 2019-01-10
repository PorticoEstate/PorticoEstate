<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div class="content">
        
		<div class="pure-g">
			<div class="pure-u-1">
				<dl class="form">
					<dt class="heading">
						<xsl:value-of select="php:function('lang', 'Delete Event')"/>
					</dt>
				</dl>
			</div>
		</div>
        
		<xsl:call-template name="msgbox"/>
        
		<div class="pure-g">
			<div class="pure-u-1">
				<dl class="form">
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

				</dl>
			</div>
		</div>

		<form action="" method="POST">
			<div class="pure-g">
				<div class="pure-u-1">
					<dl class="form-col">
						<dt>
							<label for="field_building">
								<xsl:value-of select="php:function('lang', 'Building')" />
							</label>
						</dt>
						<dd>
							<div>
								<xsl:value-of select="event/building_name"/>
							</div>
						</dd>
						<dt>
							<label for="field_building">
								<xsl:value-of select="php:function('lang', 'Description')" />
							</label>
						</dt>
						<dd>
							<div>
								<xsl:value-of select="event/description"/>
							</div>
						</dd>
						<dt>
							<label for="field_activity">
								<xsl:value-of select="php:function('lang', 'Activity')" />
							</label>
						</dt>
						<dd>
							<div>
								<xsl:for-each select="activities">
									<xsl:if test="../event/activity_id = id">
										<xsl:value-of select="name"/>
									</xsl:if>
								</xsl:for-each>
							</div>
						</dd>
						<dt>
							<label for="field_from">
								<xsl:value-of select="php:function('lang', 'From')" />
							</label>
						</dt>
						<dd>
							<div>
								<xsl:value-of select="event/from_"/>
							</div>
						</dd>
						<dt>
							<label for="field_to">
								<xsl:value-of select="php:function('lang', 'To')"/>
							</label>
						</dt>
						<dd>
							<div>
								<xsl:value-of select="event/to_"/>
							</div>
						</dd>
					</dl>
				</div>
			</div>
            
			<div class="pure-g">
				<div class="pure-u-1 pure-u-lg-4-5">
					<dl class="form-col">
						<dt>
							<label for="field_message">
								<xsl:value-of select="php:function('lang', 'Message')" />
							</label>
						</dt>
						<dd>
							<textarea id="field-message" name="message" type="text">
								<xsl:value-of select="system_message/message"/>
							</textarea>
						</dd>
					</dl>
				</div>
			</div>
            
			<div class="pure-g">
				<div class="pure-u-1">
					<div class="form-buttons">
						<input type="submit">
							<xsl:attribute name="value">
								<xsl:value-of select="php:function('lang', 'Delete')"/>
							</xsl:attribute>
						</input>
						<a class="cancel">
							<xsl:attribute name="href">
								<xsl:value-of select="event/cancel_link"/>
							</xsl:attribute>
							<xsl:value-of select="php:function('lang', 'Cancel')"/>
						</a>
					</div>
				</div>
			</div>
		</form>
	</div>
	<script type="text/javascript">
		var initialSelection = <xsl:value-of select="booking/resources_json" />
		var lang = <xsl:value-of select="php:function('js_lang', 'Resource Type')" />;
	</script>
</xsl:template>
