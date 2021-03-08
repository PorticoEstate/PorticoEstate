<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="msgbox"/>
	<form action="" method="POST" id='form' class="pure-form pure-form-aligned" name="form">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="data/tabs"/>
			<div id="account_code" class="booking-container">
				<legend>Base: LG04 - Batch innlesing salgsordre - fullt format 5.3</legend>
				<fieldset>
					<legend>line</legend>
					<div class="pure-control-group">
						<label>Article (pos 283 - 297)</label>
						<input id="field_article" name="article" type="text">
							<xsl:attribute name="value">
								<xsl:value-of select="config_data/article"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label>Client (pos 359 -360)</label>
						<input id="field_voucher_client" name="voucher_client" type="text" maxlength="2">
							<xsl:attribute name="value">
								<xsl:value-of select="config_data/voucher_client"/>
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
						<label>Dim6 (pos 906 - 909)</label>
						<input id="field_dim_6" name="dim_6" type="text">
							<xsl:attribute name="value">
								<xsl:value-of select="config_data/dim_6"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label>Dim7 (pos 910 - 913)</label>
						<input id="field_dim_5" name="dim_7" type="text">
							<xsl:attribute name="value">
								<xsl:value-of select="config_data/dim_7"/>
							</xsl:attribute>
						</input>
					</div>
				</fieldset>
				<fieldset>
					<legend>head</legend>
					<div class="pure-control-group">
						<label>Att_1_id (298 -299)</label>
						<input id="field_att_1_id" name="att_1_id" type="text">
							<xsl:attribute name="value">
								<xsl:value-of select="config_data/att_1_id"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label>Att_2_id (300 -301)</label>
						<input id="field_att_2_id" name="att_2_id" type="text">
							<xsl:attribute name="value">
								<xsl:value-of select="config_data/att_2_id"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label>Att_3_id (302 -303)</label>
						<input id="field_att_3_id" name="att_3_id" type="text">
							<xsl:attribute name="value">
								<xsl:value-of select="config_data/att_3_id"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label>Att_4_id (304 -305)</label>
						<input id="field_att_4_id" name="att_4_id" type="text">
							<xsl:attribute name="value">
								<xsl:value-of select="config_data/att_4_id"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label>Att_5_id (306 -307)</label>
						<input id="field_att_5_id" name="att_5_id" type="text">
							<xsl:attribute name="value">
								<xsl:value-of select="config_data/att_5_id"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label>Att_6_id (308 -309)</label>
						<input id="field_att_6_id" name="att_6_id" type="text">
							<xsl:attribute name="value">
								<xsl:value-of select="config_data/att_6_id"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label>Att_7_id (310 -311)</label>
						<input id="field_att_7_id" name="att_7_id" type="text">
							<xsl:attribute name="value">
								<xsl:value-of select="config_data/att_7_id"/>
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
						<label>Dim_value_2 (pos 926 - 937)</label>
						<input id="field_dim_value_2" name="dim_value_2" type="text">
							<xsl:attribute name="value">
								<xsl:value-of select="config_data/dim_value_2"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label>Dim_value_3 (pos 938 - 949)</label>
						<input id="field_dim_value_3" name="dim_value_3" type="text">
							<xsl:attribute name="value">
								<xsl:value-of select="config_data/dim_value_3"/>
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
						<label>Dim_value_6 (pos 974 - 985)</label>
						<input id="field_dim_value_6" name="dim_value_6" type="text">
							<xsl:attribute name="value">
								<xsl:value-of select="config_data/dim_value_6"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label>Dim_value_7 (pos 986 - 997)</label>
						<input id="field_dim_value_7" name="dim_value_7" type="text">
							<xsl:attribute name="value">
								<xsl:value-of select="config_data/dim_value_7"/>
							</xsl:attribute>
						</input>
					</div>

					<div class="pure-control-group">
						<label>Responsible (pos 1629 -1636)</label>
						<input id="field_voucher_responsible" name="voucher_responsible" type="text" maxlength="8">
							<xsl:attribute name="value">
								<xsl:value-of select="config_data/voucher_responsible"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label>voucher_type (pos 1885 - 1886)</label>
						<input id="field_voucher_type" name="voucher_type" type="text" maxlength="2">
							<xsl:attribute name="value">
								<xsl:value-of select="config_data/voucher_type"/>
							</xsl:attribute>
						</input>
					</div>
				</fieldset>

				<fieldsset>

					<legend>Kundeliste, Base: CS15 format 52</legend>
					<div class="pure-control-group">
						<label>Reskontrogruppe (apar_gr_id)</label>
						<input id="field_apar_gr_id" name="apar_gr_id" type="text" maxlength="2">
							<xsl:attribute name="value">
								<xsl:value-of select="config_data/apar_gr_id"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label>Betalingsmetode( pay_method )</label>
						<input id="field_pay_method" name="pay_method" type="text" maxlength="2">
							<xsl:attribute name="value">
								<xsl:value-of select="config_data/pay_method"/>
							</xsl:attribute>
						</input>
					</div>

				</fieldsset>

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
						<option value="FACTUM">
							<xsl:if test="config_data/external_format='FACTUM'">
								<xsl:attribute name="selected">checked</xsl:attribute>
							</xsl:if>
							FACTUM
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
						<option value="FACTUM">
							<xsl:if test="config_data/internal_format='FACTUM'">
								<xsl:attribute name="selected">checked</xsl:attribute>
							</xsl:if>
							FACTUM
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
								<xsl:value-of select="php:function('lang', 'export invoice')"/>
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
						<option value="ftps">
							<xsl:if test="config_data/invoice_export_method='ftps'">
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
