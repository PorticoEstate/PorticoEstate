<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">

	<dl class="form">
    	<dt class="heading"><xsl:value-of select="php:function('lang', 'Delete Booking')"/></dt>
	</dl>
    <xsl:call-template name="msgbox"/>
	<xsl:call-template name="yui_booking_i18n"/>
	<dl class="form">
    	<dd>
            <xsl:value-of select="php:function('lang', 'Booking Delete Information')"/>
            <xsl:if test="user_can_delete_allocations != 0">
                <xsl:value-of select="php:function('lang', 'Booking Delete Information3')"/>
            </xsl:if>
        </dd>
    	<dd><xsl:value-of select="php:function('lang', 'Booking Delete Information2')"/></dd>
	</dl>
    <div class="clr"/>
    <form action="" method="POST">
		<input type="hidden" name="application_id" value="{booking/application_id}"/>
        <input type="hidden" name="group_id" value="{booking/group_id}" />
        <input type="hidden" name="building_id" value="{booking/building_id}" />
        <input type="hidden" name="season_id" value="{booking/season_id}" />
        <input type="hidden" name="from_" value="{booking/from_}" />
        <input type="hidden" name="to_" value="{booking/to_}" />

        <dl class="form-col">
            <dt><label for="field_building"><xsl:value-of select="php:function('lang', 'Building')" /></label></dt>
            <dd>
                <div>
                        <xsl:value-of select="booking/building_name"/>
                </div>
            </dd>
            <dt><label for="field_from"><xsl:value-of select="php:function('lang', 'From')" /></label></dt>
            <dd>
                <div>
                    <xsl:value-of select="booking/from_"/>
                </div>
            </dd>
            <dt><label for="field_to"><xsl:value-of select="php:function('lang', 'To')"/></label></dt>
            <dd>
                <div>
                    <xsl:value-of select="booking/to_"/>
                </div>
            </dd>
			<dt><label for="field_repeat_until"><xsl:value-of select="php:function('lang', 'Recurring allocation deletion')" /></label></dt>
			<dd>
				<label>
					<input type="checkbox" name="outseason" id="outseason">
						<xsl:if test="outseason='on'">
							<xsl:attribute name="checked">checked</xsl:attribute>
						</xsl:if>
					</input>
					<xsl:value-of select="php:function('lang', 'Out season')" />
				</label>
			</dd>
			<dd>
				<label>
					<input type="checkbox" name="recurring" id="recurring">
						<xsl:if test="recurring='on'">
							<xsl:attribute name="checked">checked</xsl:attribute>
						</xsl:if>
					</input>
					<xsl:value-of select="php:function('lang', 'Delete until')" />
				</label>
			</dd>
			<dd class="date-picker">
				<input id="field_repeat_until" name="repeat_until" type="text">
					<xsl:attribute name="value"><xsl:value-of select="repeat_until"/></xsl:attribute>
				</input>
			</dd>
			<dt><xsl:value-of select="php:function('lang', 'Interval')" /></dt>
			<dd>
				<xsl:value-of select="../field_interval" />
				<select id="field_interval" name="field_interval">
					<option value="1">
						<xsl:if test="interval=1">
							<xsl:attribute name="selected">selected</xsl:attribute>
						</xsl:if>
						<xsl:value-of select="php:function('lang', '1 week')" />
					</option>
					<option value="2">
						<xsl:if test="interval=2">
							<xsl:attribute name="selected">selected</xsl:attribute>
						</xsl:if>
						<xsl:value-of select="php:function('lang', '2 weeks')" />
					</option>
					<option value="3">
						<xsl:if test="interval=3">
							<xsl:attribute name="selected">selected</xsl:attribute>
						</xsl:if>
						<xsl:value-of select="php:function('lang', '3 weeks')" />
					</option>
					<option value="4">
						<xsl:if test="interval=4">
							<xsl:attribute name="selected">selected</xsl:attribute>
						</xsl:if>
						<xsl:value-of select="php:function('lang', '4 weeks')" />
					</option>
				</select>
			</dd>
        </dl>
        <dl class="form-col">
            <dt><label for="field_group"><xsl:value-of select="php:function('lang', 'Group')"/></label></dt>
            <dd>
                        <xsl:value-of select="booking/group_name"/>
            </dd>
            <dt><label for="field_season"><xsl:value-of select="php:function('lang', 'Season')"/></label></dt>
            <dd>
                        <xsl:value-of select="booking/season_name"/>
            </dd>
			<xsl:if test="user_can_delete_allocations != 0">						
			<dt><label for="field_repeat_until"><xsl:value-of select="php:function('lang', 'Delete allocation also')" /></label></dt>
			<dd>
				<label>
					<input type="checkbox" name="delete_allocation" id="delete_allocation">
						<xsl:if test="delete_allocation='on'">
							<xsl:attribute name="checked">checked</xsl:attribute>
						</xsl:if>
					</input>
					<xsl:value-of select="php:function('lang', 'Delete allocations')" />
				</label>
			</dd>
			</xsl:if>
        </dl>
	
		<div style='clear:left; padding:0; margin:0'/>

        <dl class="form-col">
		<dt><label for="field_message"><xsl:value-of select="php:function('lang', 'Message')" /></label></dt>
		<dd class="yui-skin-sam">
		<textarea id="field-message" name="message" type="text"><xsl:value-of select="system_message/message"/></textarea>
		</dd>
        </dl>
        <div class="form-buttons">
            <input type="submit">
				<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Delete')"/></xsl:attribute>
			</input>
            <a class="cancel">
                <xsl:attribute name="href"><xsl:value-of select="booking/cancel_link"/></xsl:attribute>
                <xsl:value-of select="php:function('lang', 'Cancel')"/>
            </a>
        </div>
    </form>
    </div>
    <script type="text/javascript">
        YAHOO.booking.season_id = '<xsl:value-of select="booking/season_id"/>';
        YAHOO.booking.group_id = '<xsl:value-of select="booking/group_id"/>';
        YAHOO.booking.initialSelection = <xsl:value-of select="booking/resources_json"/>;
		var lang = <xsl:value-of select="php:function('js_lang', 'Resource Type')"/>;
        <![CDATA[
        var descEdit = new YAHOO.widget.SimpleEditor('field-message', {
            height: '150px',
            width: '522px',
            dompath: true,
            animate: true,
    	    handleSubmit: true,
            toolbar: {
                titlebar: '',
                buttons: [
                   { group: 'textstyle', label: ' ',
                        buttons: [
                            { type: 'push', label: 'Bold', value: 'bold' },
                            { type: 'separator' },
                            { type: 'push', label: 'HTML Link CTRL + SHIFT + L', value: 'createlink'}
                        ]
                    }
                ]
            }
        });
        descEdit.render();
        ]]>
    </script>
</xsl:template>
