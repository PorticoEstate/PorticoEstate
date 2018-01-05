<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<form action="" method="POST" id='form' class="pure-form pure-form-aligned" name="form">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="account_code_set/tabs"/>
			<div id="account" class="booking-container">
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Name')" />
					</label>
					<xsl:value-of select="account_code_set/name"/>
				</div>
				<div class="pure-control-group">
					<xsl:if test="config_data/dim_3">
						<label>
							<xsl:value-of select="config_data/dim_3" />
						</label>
						<xsl:value-of select="account_code_set/object_number"/>
					</xsl:if>
				</div>
				<div class="pure-control-group">
					<xsl:if test="config_data/article">
						<label>
							<xsl:value-of select="php:function('lang', 'Article')" />
						</label>
						<xsl:value-of select="account_code_set/article"/>
					</xsl:if>
				</div>
				<div class="pure-control-group">
					<xsl:if test="config_data/dim_value_1">
						<label>
							<xsl:value-of select="config_data/dim_value_1" />
						</label>
						<xsl:value-of select="account_code_set/unit_number"/>
					</xsl:if>
				</div>
				<div class="pure-control-group">
					<xsl:if test="config_data/dim_value_4">
						<label>
							<xsl:value-of select="config_data/dim_value_4" />
						</label>
						<xsl:value-of select="account_code_set/dim_value_4"/>
					</xsl:if>
				</div>
				<div class="pure-control-group">
					<xsl:if test="config_data/dim_value_5">
						<label>
							<xsl:value-of select="config_data/dim_value_5" />
						</label>
						<xsl:value-of select="account_code_set/dim_value_5"/>
					</xsl:if>
				</div>
				<div class="pure-control-group">
					<!--xsl:if test="config_data/external_format != 'KOMMFAKT'"-->
					<label>
						<xsl:value-of select="php:function('lang', 'Unit Prefix')" />
					</label>
					<xsl:value-of select="account_code_set/unit_prefix"/>
					<!--/xsl:if-->
				</div>
				<div class="pure-control-group">
					<xsl:if test="config_data/dim_1">
						<label>
							<xsl:value-of select="config_data/dim_1" />
						</label>
						<xsl:value-of select="account_code_set/responsible_code"/>
					</xsl:if>
				</div>
				<div class="pure-control-group">
					<xsl:if test="config_data/dim_2">
						<label>
							<xsl:value-of select="config_data/dim_2" />
						</label>
						<xsl:value-of select="account_code_set/service"/>
					</xsl:if>
				</div>
				<div class="pure-control-group">
					<xsl:if test="config_data/dim_4">
						<label>
							<xsl:value-of select="config_data/dim_4" />
						</label>
						<xsl:value-of select="account_code_set/dim_4"/>
					</xsl:if>
				</div>
				<div class="pure-control-group">
					<xsl:if test="config_data/dim_5">
						<label>
							<xsl:value-of select="config_data/dim_5" />
						</label>
						<xsl:value-of select="account_code_set/project_number"/>
					</xsl:if>
				</div>
				<div class="pure-control-group">
					<xsl:if test="config_data/external_format != 'KOMMFAKT'">
						<label>
							<xsl:value-of select="php:function('lang', 'Reference')" />
						</label>
					</xsl:if>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Invoice instruction')" />
					</label>
					<div class="description" style="display:inline-block;max-width:80%;">
						<xsl:value-of select="account_code_set/invoice_instruction"/>
					</div>
				</div>
			</div>
		</div>
		<div class="form-buttons">
			<xsl:if test="account_code_set/permission/write">
				<input type="button" class="pure-button pure-button-primary" name="cancel">
					<xsl:attribute name="onclick">window.location="<xsl:value-of select="account_code_set/edit_link"/>"</xsl:attribute>
					<xsl:attribute name="value">
						<xsl:value-of select="php:function('lang', 'Edit')" />
					</xsl:attribute>
				</input>
			</xsl:if>
			<input type="button" class="pure-button pure-button-primary" name="cancel">
				<xsl:attribute name="onclick">window.location="<xsl:value-of select="account_code_set/cancel_link"/>"</xsl:attribute>
				<xsl:attribute name="value">
					<xsl:value-of select="php:function('lang', 'Cancel')" />
				</xsl:attribute>
			</input>		
		</div>
	</form>
</xsl:template>
