<func:function name="phpgw:conditional">
	<xsl:param name="test"/>
	<xsl:param name="true"/>
	<xsl:param name="false"/>

	<func:result>
		<xsl:choose>
			<xsl:when test="$test">
				<xsl:value-of select="$true"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$false"/>
			</xsl:otherwise>
		</xsl:choose>
	</func:result>
</func:function>

<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div id="content">
		<dl class="form">
			<dt class="heading">
				<xsl:value-of select="php:function('lang', 'Export Settings')"/>
			</dt>
		</dl>

		<xsl:call-template name="msgbox"/>
		<!--xsl:call-template name="yui_booking_i18n"/-->

		<form action="" method="POST">
			<dl class="form-col">
				<input name="season_id" type="hidden" id="field_season_id" value="{export/season_id}"/>
				<input name="season_name" type="hidden" id="field_season_name" value="{export/season_name}"/>
				<dt>
					<label for="field_season_id">
						<xsl:value-of select="php:function('lang', 'Season')" />
					</label>
				</dt>
				<dd>
					<xsl:value-of select="phpgw:conditional((export/season_id and normalize-space(export/season_id)), export/season_name, php:function('lang', 'All'))"/>
				</dd>
			
				<input name="building_id" type="hidden" id="field_building_id" value="{export/building_id}"/>
				<input name="building_name" type="hidden" id="field_building_name" value="{export/building_name}"/>
				<dt>
					<label for="field_building_id">
						<xsl:value-of select="php:function('lang', 'Building')" />
					</label>
				</dt>
				<dd>
					<xsl:value-of select="phpgw:conditional((export/building_id and normalize-space(export/building_id)), export/building_name, php:function('lang', 'All'))"/>
				</dd>
			
			
				<input id="field_to" name="to_" type="hidden" value='{export/to_}'/>
				<xsl:if test="export/to_ and normalize-space(export/to_)">
					<dt>
						<label for="field_to">
							<xsl:value-of select="php:function('lang', 'To')"/>
						</label>
					</dt>
					<dd>
						<xsl:value-of select="export/to_"/>
					</dd>
				</xsl:if>
			
				<input name="export_configurations[internal][type]" type="hidden" value="internal"/>
				<dt>
					<label for="field_account_code_set_internal_name">
						<xsl:value-of select="php:function('lang', 'Choose')" />
						<xsl:text> </xsl:text>
						<xsl:value-of select="php:function('lang', 'Internal Account Codes')" />
					</label>
				</dt>
				<dd>
					<div class="autocomplete">
						<input id="field_account_code_set_internal_id" name="export_configurations[internal][account_code_set_id]" type="hidden" value="{export/export_configurations/internal/account_code_set_id}"/>
						<input id="field_account_code_set_internal_name" name="export_configurations[internal][account_code_set_name]" type="text" value="{export/export_configurations/internal/account_code_set_name}">
							<xsl:if test="not(new_form)">
								<xsl:attribute name="disabled">disabled</xsl:attribute>
							</xsl:if>
						</input>
						<div id="account_code_set_internal_container"/>
					</div>
				</dd>
		
				<input name="export_configurations[external][type]" type="hidden" value="external"/>
				<dt>
					<label for="field_account_code_set_external_name">
						<xsl:value-of select="php:function('lang', 'Choose')" />
						<xsl:text> </xsl:text>
						<xsl:value-of select="php:function('lang', 'External Account Codes')" />
					</label>
				</dt>
				<dd>
					<div class="autocomplete">
						<input id="field_account_code_set_external_id" name="export_configurations[external][account_code_set_id]" type="hidden" value="{export/export_configurations/external/account_code_set_id}"/>
						<input id="field_account_code_set_external_name" name="export_configurations[external][account_code_set_name]" type="text" value="{export/export_configurations/external/account_code_set_name}">
							<xsl:if test="not(new_form)">
								<xsl:attribute name="disabled">disabled</xsl:attribute>
							</xsl:if>
						</input>
						<div id="account_code_set_external_container"/>
					</div>
				</dd>
			</dl>
			
			<div class="clr"/>

			<div class="form-buttons">
				<input type="submit" value="{php:function('lang', phpgw:conditional(new_form, 'Export', 'Save'))}"/>
				<a class="cancel" href="{export/cancel_link}">
					<xsl:value-of select="php:function('lang', 'Cancel')" />
				</a>
			</div>
		</form>
	</div>

	<script type="text/javascript">
<![CDATA[
$(document).ready(function () {
	JqueryPortico.autocompleteHelper('index.php?menuaction=booking.uiaccount_code_set.index&phpgw_return_as=json&',
      'field_account_code_set_internal_name', 'field_account_code_set_internal_id', 'account_code_set_internal_container');

	JqueryPortico.autocompleteHelper('index.php?menuaction=booking.uiaccount_code_set.index&phpgw_return_as=json&',
       'field_account_code_set_external_name', 'field_account_code_set_external_id', 'account_code_set_external_container');

});
]]>
	</script>
</xsl:template>


