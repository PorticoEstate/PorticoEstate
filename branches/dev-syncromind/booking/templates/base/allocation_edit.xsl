<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">

       <ul class="pathway">
           <li><xsl:value-of select="php:function('lang', 'Allocations')" /></li>
           <li>#<xsl:value-of select="allocation/id"/></li>
       </ul>
    <xsl:call-template name="msgbox"/>
	<xsl:call-template name="yui_booking_i18n"/>

    <form action="" method="POST">
		<dl class="form">
            <dt><label><xsl:value-of select="php:function('lang', 'Application')"/></label></dt>
            <dd>
				<xsl:if test="allocation/application_id!=''">
					<a href="{allocation/application_link}">#<xsl:value-of select="allocation/application_id"/></a>
				</xsl:if>
            </dd>
		</dl>
        <dl class="form-col">
            <dt><label for="field_building"><xsl:value-of select="php:function('lang', 'Building')" /></label></dt>
            <dd>
                <div class="autocomplete">
                    <input id="field_building_id" name="building_id" type="hidden">
                        <xsl:attribute name="value"><xsl:value-of select="allocation/building_id"/></xsl:attribute>
                    </input>
                    <input id="field_building_name" name="building_name" type="text">
                        <xsl:attribute name="value"><xsl:value-of select="allocation/building_name"/></xsl:attribute>
                    </input>
                    <div id="building_container"/>
                </div>
            </dd>
            <dt><label for="field_active"><xsl:value-of select="php:function('lang', 'Active')"/></label></dt>
            <dd>
                <select id="field_active" name="active">
                    <option value="1">
                    	<xsl:if test="allocation/active=1">
                    		<xsl:attribute name="selected">checked</xsl:attribute>
                    	</xsl:if>
                        <xsl:value-of select="php:function('lang', 'Active')"/>
                    </option>
                    <option value="0">
                    	<xsl:if test="allocation/active=0">
                    		<xsl:attribute name="selected">checked</xsl:attribute>
                    	</xsl:if>
                        <xsl:value-of select="php:function('lang', 'Inactive')"/>
                    </option>
                </select>
            </dd>
            <dt><label for="field_season"><xsl:value-of select="php:function('lang', 'Season')" /></label></dt>
            <dd>
                <div id="season_container"><xsl:value-of select="php:function('lang', 'Select a building first')" /></div>
            </dd>
            <dt><label for="field_resources"><xsl:value-of select="php:function('lang', 'Resources')" /></label></dt>
            <dd>
                <div id="resources_container"><xsl:value-of select="php:function('lang', 'Select a building first')" /></div>
            </dd>
        </dl>
        <dl class="form-col">
            <dt><label for="field_organization"><xsl:value-of select="php:function('lang', 'Organization')" /></label></dt>
            <dd>
                <div class="autocomplete">
                    <input id="field_org_id" name="organization_id" type="hidden">
                        <xsl:attribute name="value"><xsl:value-of select="allocation/organization_id"/></xsl:attribute>
                    </input>
                    <input id="field_org_name" name="organization_name" type="text">
                        <xsl:attribute name="value"><xsl:value-of select="allocation/organization_name"/></xsl:attribute>
                    </input>
                    <div id="org_container"/>
                </div>
            </dd>
            <dt><label for="field_from"><xsl:value-of select="php:function('lang', 'From')" /></label></dt>
            <dd>
                <div class="datetime-picker">
                <input id="field_from" name="from_" type="text">
                    <xsl:attribute name="value"><xsl:value-of select="allocation/from_"/></xsl:attribute>
                </input>
                </div>
            </dd>
            <dt><label for="field_to"><xsl:value-of select="php:function('lang', 'To')" /></label></dt>
            <dd>
                <div class="datetime-picker">
                <input id="field_to" name="to_" type="text">
                    <xsl:attribute name="value"><xsl:value-of select="allocation/to_"/></xsl:attribute>
                </input>
                </div>
            </dd>
            <dt><label for="field_cost"><xsl:value-of select="php:function('lang', 'Cost')" /></label></dt>
            <dd>
                <input id="field_cost" name="cost" type="text">
                    <xsl:attribute name="value"><xsl:value-of select="allocation/cost"/></xsl:attribute>
                </input>
            </dd>
        </dl>
		<div style="clear: both" />
		<dl class="form">
			<dt><label for="field_mail"><xsl:value-of select="php:function('lang', 'Inform contact persons')" /></label></dt>
			<dd>
				<label><xsl:value-of select="php:function('lang', 'Text written in the text area below will be sent as an email to all registered contact persons.')" /></label><br />
				<textarea id="field_mail" name="mail" class="full-width"></textarea>
			</dd>
		</dl>
        <div class="form-buttons">
            <input type="submit">
			<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Save')"/></xsl:attribute>
			</input>
            <a class="cancel">
                <xsl:attribute name="href"><xsl:value-of select="allocation/cancel_link"/></xsl:attribute>
                <xsl:value-of select="php:function('lang', 'Cancel')" />
            </a>
        </div>
    </form>
    </div>
    <script type="text/javascript">
        YAHOO.booking.season_id = '<xsl:value-of select="allocation/season_id"/>';
        YAHOO.booking.initialSelection = <xsl:value-of select="allocation/resources_json"/>;
		var lang = <xsl:value-of select="php:function('js_lang', 'Resource Type')"/>;
    </script>
</xsl:template>
