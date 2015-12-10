<xsl:template match="data" xmlns:php="http://php.net/xsl">

	<xsl:call-template name="msgbox"/>
	<form id="form" name="form" method="post" action="" class="pure-form pure-form-aligned">
		<input type="hidden" name="tab" value="" />
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="season/tabs"/>
			<div id="generate_allocations">
				<xsl:if test="step = 1">
					<div class="pure-control-group">
						<xsl:value-of select="php:function('lang', 'Generate Allocations from week template')" /> (1/2)
					</div>					
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'From')" />
						</label>
						<input id="from_" name="from_" type="text" value="{from_}" readonly="true"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'To')" />
						</label>
						<input id="to_" name="to_" type="text" value="{to_}" readonly="true"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Interval')" />
						</label>
						<select id="field_interval" name="field_interval">
							<option value="1">
								<xsl:if test="interval=1">
									<xsl:attribute name="selected">checked</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="php:function('lang', '1 week')" />
							</option>
							<option value="2">
								<xsl:if test="interval=2">
									<xsl:attribute name="selected">checked</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="php:function('lang', '2 weeks')" />
							</option>
							<option value="3">
								<xsl:if test="interval=3">
									<xsl:attribute name="selected">checked</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="php:function('lang', '3 weeks')" />
							</option>
							<option value="4">
								<xsl:if test="interval=4">
									<xsl:attribute name="selected">checked</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="php:function('lang', '4 weeks')" />
							</option>
						</select>
					</div>
				</xsl:if>
				
				<xsl:if test="step = 2">
					<div class="pure-control-group">
						<xsl:value-of select="php:function('lang', 'Generate Allocations from week template')" /> (2/2)
					</div>					
					<input type="hidden" name="from_">
						<xsl:attribute name="value">
							<xsl:value-of select="from_" />
						</xsl:attribute>
					</input>
					<input type="hidden" name="to_">
						<xsl:attribute name="value">
							<xsl:value-of select="to_" />
						</xsl:attribute>
					</input>
					<div class="pure-control-group">
						<label></label>
						<xsl:value-of select="php:function('lang', 'Allocations that can be created (%1)', count(result/valid[from_]))" />
					</div>									
					<div class="pure-control-group">
						<label></label>
						<div class="pure-custom">
							<div class="allocation-list">
								<xsl:for-each select="result/valid[from_]">
									<li>
										<xsl:value-of select="from_"/> - <xsl:value-of select="to_"/>: <xsl:value-of select="organization_name"/>
									</li>
								</xsl:for-each>
							</div>
						</div>
					</div>
					<div class="pure-control-group">
						<label></label>
						<xsl:value-of select="php:function('lang', 'Allocations colliding with existing bookings or allocations (%1)', count(result/invalid[from_]))" />
					</div>									
					<div class="pure-control-group">
						<label></label>
						<div class="pure-custom">
							<div class="allocation-list">
								<xsl:for-each select="result/invalid[from_]">
									<li>
										<xsl:value-of select="from_"/> - <xsl:value-of select="to_"/>: <xsl:value-of select="organization_name"/>
									</li>
								</xsl:for-each>
							</div>
						</div>
					</div>							
				</xsl:if>
				
				<xsl:if test="step = 3">
					<div class="pure-control-group">
						<label></label>
						<xsl:value-of select="php:function('lang', 'Successfully created %1 allocations:', count(result/valid[from_]))" />
					</div>					
					<div class="pure-control-group">
						<label></label>
						<div class="pure-custom">
							<div class="allocation-list">
								<xsl:for-each select="result/valid[from_]">
									<li>
										<xsl:value-of select="from_"/> - <xsl:value-of select="to_"/>: <xsl:value-of select="organization_name"/>
									</li>
								</xsl:for-each>
							</div>
						</div>
					</div>
				</xsl:if>
			</div>
		</div>
		<div class="form-buttons">
			<xsl:if test="step = 1">
				<input type="submit" class="pure-button pure-button-primary" name="calculate">
					<xsl:attribute name="value">
						<xsl:value-of select="php:function('lang', 'Preview')" />
					</xsl:attribute>
				</input>
			</xsl:if>
			<xsl:if test="step = 2">
				<input type="submit" class="pure-button pure-button-primary" name="create">
					<xsl:attribute name="value">
						<xsl:value-of select="php:function('lang', 'Create')" />
					</xsl:attribute>
				</input>
			</xsl:if>
			<input type="button" class="pure-button pure-button-primary" name="cancel">
				<xsl:attribute name="onclick">window.location="<xsl:value-of select="season/wtemplate_link"/>"</xsl:attribute>
				<xsl:attribute name="value">
					<xsl:value-of select="php:function('lang', 'Cancel')" />
				</xsl:attribute>
			</input>			
		</div>
	</form>

</xsl:template>
