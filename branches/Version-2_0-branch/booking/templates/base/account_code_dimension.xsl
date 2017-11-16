<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="msgbox"/>
	<form action="" method="POST" id='form' class="pure-form pure-form-aligned" name="form">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="data/tabs"/>
			<div id="account_code" class="booking-container">
				<div class="pure-control-group">
					<label>Article (pos 283 - 297)</label>
					<input id="field_article" name="article" type="text">
						<xsl:attribute name="value">
							<xsl:value-of select="config_data/article"/>
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label>Dim1 (pos 862 - 869)</label>
					<input id="field_dim_1" name="dim_1" type="text">
						<xsl:attribute name="value">
							<xsl:value-of select="config_data/dim_1"/>
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label>Dim2 (pos 870 - 877)</label>
					<input id="field_dim_2" name="dim_2" type="text">
						<xsl:attribute name="value">
							<xsl:value-of select="config_data/dim_2"/>
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label>Dim3 (pos 878 - 885)</label>
					<input id="field_dim_3" name="dim_3" type="text">
						<xsl:attribute name="value">
							<xsl:value-of select="config_data/dim_3"/>
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label>Dim4 (pos 886 - 893)</label>
					<input id="field_dim_4" name="dim_4" type="text">
						<xsl:attribute name="value">
							<xsl:value-of select="config_data/dim_4"/>
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label>Dim5 (pos 894 - 905)</label>
					<input id="field_dim_5" name="dim_5" type="text">
						<xsl:attribute name="value">
							<xsl:value-of select="config_data/dim_5"/>
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label>Dim_value_1 (pos 914 - 925)</label>
					<input id="field_dim_value_1" name="dim_value_1" type="text">
						<xsl:attribute name="value">
							<xsl:value-of select="config_data/dim_value_1"/>
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label>Dim_value_4 (pos 950 - 961)</label>
					<input id="field_dim_value_4" name="dim_value_4" type="text">
						<xsl:attribute name="value">
							<xsl:value-of select="config_data/dim_value_4"/>
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label>Dim_value_5 (pos 962 - 973)</label>
					<input id="field_dim_value_5" name="dim_value_5" type="text">
						<xsl:attribute name="value">
							<xsl:value-of select="config_data/dim_value_5"/>
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<div class="heading">
						<legend>
							<h3>
								<xsl:value-of select="php:function('lang', 'External account')"/>
							</h3>
						</legend>
					</div>
				</div>
				<div class="pure-control-group">
					<p>
						<h4>
							<xsl:value-of select="php:function('lang', 'External_account_helptext')"/>
						</h4>
					</p>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'External customer output format')"/>
					</label>
					<select id="field_external_format" name="external_format">
						<option value="AGRESSO">
							<xsl:if test="config_data/external_format='AGRESSO'">
								<xsl:attribute name="selected">checked</xsl:attribute>
							</xsl:if>
							AGRESSO
						</option>
						<option value="CSV">
							<xsl:if test="config_data/external_format='CSV'">
								<xsl:attribute name="selected">checked</xsl:attribute>
							</xsl:if>
							CSV
						</option>
						<option value="KOMMFAKT">
							<xsl:if test="config_data/external_format='KOMMFAKT'">
								<xsl:attribute name="selected">checked</xsl:attribute>
							</xsl:if>
							KOMMFAKT
						</option>
						<option value="VISMA">
							<xsl:if test="config_data/external_format='VISMA'">
								<xsl:attribute name="selected">checked</xsl:attribute>
							</xsl:if>
							VISMA
						</option>
					</select>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'External file linebreak ')"/>
					</label>
					<select id="field_external_format_linebreak" name="external_format_linebreak">
						<option value="Windows">
							<xsl:if test="config_data/external_format_linebreak='Windows'">
								<xsl:attribute name="selected">checked</xsl:attribute>
							</xsl:if>
							Windows
						</option>
						<option value="Linux">
							<xsl:if test="config_data/external_format_linebreak='Linux'">
								<xsl:attribute name="selected">checked</xsl:attribute>
							</xsl:if>
							Linux
						</option>
					</select>
				</div>
				<div class="pure-control-group">
					<div class="heading">
						<legend>
							<h3>
								<xsl:value-of select="php:function('lang', 'Internal account')"/>
							</h3>
						</legend>
					</div>
				</div>
				<div class="pure-control-group">
					<p>
						<h4>
							<xsl:value-of select="php:function('lang', 'Internal_account_helptext')"/>
						</h4>
					</p>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Organization number')"/>
					</label>
					<input id="field_organization_value" name="organization_value" type="text">
						<xsl:attribute name="value">
							<xsl:value-of select="config_data/organization_value"/>
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Internal customer output format')"/>
					</label>
					<select id="field_internal_format" name="internal_format">
						<option value="AGRESSO">
							<xsl:if test="config_data/internal_format='AGRESSO'">
								<xsl:attribute name="selected">checked</xsl:attribute>
							</xsl:if>
							AGRESSO
						</option>
						<option value="CSV">
							<xsl:if test="config_data/internal_format='CSV'">
								<xsl:attribute name="selected">checked</xsl:attribute>
							</xsl:if>
							CSV
						</option>
						<option value="KOMMFAKT">
							<xsl:if test="config_data/internal_format='KOMMFAKT'">
								<xsl:attribute name="selected">checked</xsl:attribute>
							</xsl:if>
							KOMMFAKT
						</option>
						<option value="VISMA">
							<xsl:if test="config_data/internal_format='VISMA'">
								<xsl:attribute name="selected">checked</xsl:attribute>
							</xsl:if>
							VISMA
						</option>
					</select>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'File output')"/>
					</label>
					<select id="field_output_files" name="output_files">
						<option value="seperated">
							<xsl:if test="config_data/output_files='seperated'">
								<xsl:attribute name="selected">checked</xsl:attribute>
							</xsl:if>
							Records splited between two files internal and external.
						</option>
						<option value="single">
							<xsl:if test="config_data/output_files='single'">
								<xsl:attribute name="selected">checked</xsl:attribute>
							</xsl:if>
							All records in the external file.
						</option>
					</select>
				</div>
				<div class="pure-control-group">
					<div class="heading">
						<legend>
							<h3>
								<xsl:value-of select="php:function('lang', 'Export agresso')"/>
							</h3>
						</legend>
					</div>
				</div>
				<div class="pure-control-group">
					<p>
						<h4>
							<xsl:value-of select="php:function('lang', 'export_help_text')"/>
						</h4>
					</p>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Export method')"/>
					</label>
					<select id="field_invoice_export_method" name="invoice_export_method">
						<option value="local">
							<xsl:if test="config_data/invoice_export_method='local'">
								<xsl:attribute name="selected">checked</xsl:attribute>
							</xsl:if>
							local
						</option>
						<option value="ftp">
							<xsl:if test="config_data/invoice_export_method='ftp'">
								<xsl:attribute name="selected">checked</xsl:attribute>
							</xsl:if>
							ftp
						</option>
					</select>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Export path')"/>
					</label>
					<input id="field_invoice_export_path" name="invoice_export_path" type="text">
						<xsl:attribute name="value">
							<xsl:value-of select="config_data/invoice_export_path"/>
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Ftp host')"/>
					</label>
					<input id="field_invoice_ftp_host" name="invoice_ftp_host" type="text">
						<xsl:attribute name="value">
							<xsl:value-of select="config_data/invoice_ftp_host"/>
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Ftp basedir')"/>
					</label>
					<input id="field_invoice_ftp_basedir" name="invoice_ftp_basedir" type="text">
						<xsl:attribute name="value">
							<xsl:value-of select="config_data/invoice_ftp_basedir"/>
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Ftp user')"/>
					</label>
					<input id="field_invoice_ftp_user" name="invoice_ftp_user" type="text">
						<xsl:attribute name="value">
							<xsl:value-of select="config_data/invoice_ftp_user"/>
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Ftp password')"/>
					</label>
					<input id="field_invoice_ftp_password" name="invoice_ftp_password" type="password">
						<xsl:attribute name="value">
							<xsl:value-of select="config_data/invoice_ftp_password"/>
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'last id')"/>
						<span style="display:block;font-size:10px;font-weight:normal;margin-top:-8px;">
							<xsl:value-of select="php:function('lang', 'Do not edit!')"/>
						</span>
					</label>
					<input id="field_invoice_last_id" name="invoice_last_id" type="text">
						<xsl:attribute name="value">
							<xsl:value-of select="config_data/invoice_last_id"/>
						</xsl:attribute>
					</input>
				</div>
			</div>
		</div>
		<div class="form-buttons">
			<input type="submit" class="button pure-button pure-button-primary">
				<xsl:attribute name="value">
					<xsl:value-of select="php:function('lang', 'Save')"/>
				</xsl:attribute>
			</input>
		</div>
	</form>
</xsl:template>
