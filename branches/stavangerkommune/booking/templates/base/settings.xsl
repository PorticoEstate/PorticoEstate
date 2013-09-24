<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">

    <xsl:call-template name="msgbox"/>
	<xsl:call-template name="yui_booking_i18n"/>

   	<dl class="form">
   		<dt class="heading"><xsl:value-of select="php:function('lang', 'Booking system settings')"/></dt>
   	</dl>

    <form action="" method="POST">

       <dl class="form">
            <dt><label for="field_user_can_delete"><xsl:value-of select="php:function('lang', 'Frontend users can delete bookings and allocations')"/></label></dt>
			<dd>
				<select id="field_user_can_delete" name="config_data[user_can_delete]">
                    <option value="no">
                        <xsl:if test="config_data/user_can_delete='no'">
                            <xsl:attribute name="selected">checked</xsl:attribute>
                        </xsl:if>
                        <xsl:value-of select="php:function('lang', 'No')" />
                    </option>
                    <option value="yes">
                        <xsl:if test="config_data/user_can_delete='yes'">
                            <xsl:attribute name="selected">checked</xsl:attribute>
                        </xsl:if>
                        <xsl:value-of select="php:function('lang', 'Yes')" />
		           </option>
		        </select>
			</dd>
            <dt><label for="field_extra_schedule"><xsl:value-of select="php:function('lang', 'Activate extra kalendar field on building')"/></label></dt>
			<dd>
				<select id="field_extra_schedule" name="config_data[extra_schedule]">
                    <option value="no">
                        <xsl:if test="config_data/extra_schedule='no'">
                            <xsl:attribute name="selected">checked</xsl:attribute>
                        </xsl:if>
                        <xsl:value-of select="php:function('lang', 'No')" />
                    </option>
                    <option value="yes">
                        <xsl:if test="config_data/extra_schedule='yes'">
                            <xsl:attribute name="selected">checked</xsl:attribute>
                        </xsl:if>
                        <xsl:value-of select="php:function('lang', 'Yes')" />
		           </option>
		        </select>
			</dd>
            <dt><label for="field_extra_schedule_ids"><xsl:value-of select="php:function('lang', 'Ids that should be included in the calendar')"/></label></dt>
			<dd>
				<input id="field_extra_schedule_ids" type="text" name="config_data[extra_schedule_ids]">
					<xsl:attribute name="value"><xsl:value-of select="config_data/extra_schedule_ids"/></xsl:attribute>
				</input>
			</dd>
   		<dt class="heading"><xsl:value-of select="php:function('lang', 'Billing sequence numbers')"/></dt>
			<dd>
				<xsl:value-of select="php:function('lang', 'Do not change these values unless you know what they are.')"/>
			</dd>
			<dt><label for="field_internal_billing_sequence_number"><xsl:value-of select="php:function('lang', 'Current internal billing sequence number')" /></label></dt>
			<dd>
				<input type="number" name="billing[internal]">
					<xsl:attribute name="value"><xsl:value-of select="billing/internal"/></xsl:attribute>
				</input>
			</dd>

			<dt><label for="field_external_billing_sequence_number"><xsl:value-of select="php:function('lang', 'Current external billing sequence number')" /></label></dt>
			<dd>
				<input type="number" name="billing[external]">
					<xsl:attribute name="value"><xsl:value-of select="billing/external"/></xsl:attribute>
				</input>
			</dd>
		</dl>
		<div class="form-buttons">
			<input type="submit">
			<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Save')"/></xsl:attribute>
			</input>
		</div>
    </form>
    </div>
</xsl:template>
