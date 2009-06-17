<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">
        <ul class="pathway">
            <li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="season/buildings_link"/></xsl:attribute>
                    <xsl:value-of select="php:function('lang', 'Buildings')" />
                </a>
            </li>
            <li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="season/building_link"/></xsl:attribute>
                    <xsl:value-of select="season/building_name"/>
                </a>
            </li>
            <li><xsl:value-of select="php:function('lang', 'Season')" /></li>
            <li><a href=""><xsl:value-of select="season/name"/></a></li>
        </ul>

		<dl class="form">
			<dt class="heading"><xsl:value-of select="php:function('lang', 'New Season')" /></dt>
		</dl>    
		
		<xsl:call-template name="msgbox"/>
		<xsl:call-template name="yui_booking_i18n"/>

    <form action="" method="POST">
        <dl class="form-col">
            <dt><label for="field_name"><xsl:value-of select="php:function('lang', 'Name')" /></label></dt>
            <dd>
                <input id="field_name" name="name" type="text">
                    <xsl:attribute name="value"><xsl:value-of select="season/name"/></xsl:attribute>
                </input>
            </dd>
            <dt><label for="field_officer"><xsl:value-of select="php:function('lang', 'Case officer')" /></label></dt>
            <dd>
                <div class="autocomplete">
                    <input id="field_officer_id" name="officer_id" type="hidden">
                        <xsl:attribute name="value"><xsl:value-of select="season/officer_id"/></xsl:attribute>
                    </input>
                    <input id="field_officer_name" name="officer_name" type="text">
                        <xsl:attribute name="value"><xsl:value-of select="season/officer_name"/></xsl:attribute>
                    </input>
                    <div id="officer_container"/>
                </div>
            </dd>
            <dt><label for="field_building"><xsl:value-of select="php:function('lang', 'Building')" /></label></dt>
            <dd>
                <div class="autocomplete">
                    <input id="field_building_id" name="building_id" type="hidden">
                        <xsl:attribute name="value"><xsl:value-of select="season/building_id"/></xsl:attribute>
                    </input>
                    <input id="field_building_name" name="building_name" type="text">
                        <xsl:attribute name="value"><xsl:value-of select="season/building_name"/></xsl:attribute>
                    </input>
                    <div id="building_container"/>
                </div>
            </dd>
            <dt><label for="field_resources"><xsl:value-of select="php:function('lang', 'Resources')" /></label></dt>
            <dd>
                <div id="resources-container"/>
            </dd>
        </dl>
        <dl class="form-col">
            <dt><label for="field_active"><xsl:value-of select="php:function('lang', 'Active')"/></label></dt>
            <dd>
                <select id="field_active" name="active">
                    <option value="1">
                    	<xsl:if test="season/active=1">
                    		<xsl:attribute name="selected">checked</xsl:attribute>
                    	</xsl:if>
                        <xsl:value-of select="php:function('lang', 'Active')"/>
                    </option>
                    <option value="0">
                    	<xsl:if test="season/active=0">
                    		<xsl:attribute name="selected">checked</xsl:attribute>
                    	</xsl:if>
                        <xsl:value-of select="php:function('lang', 'Inactive')"/>
                    </option>
                </select>
            </dd>
            <dt><label for="field_status"><xsl:value-of select="php:function('lang', 'Status')" /></label></dt>
            <dd>
                <select name="status" id="status_field">
                    <option value="PLANNING">
                        <xsl:if test="season/status='PLANNING'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>
                        <xsl:value-of select="php:function('lang', 'Planning')" />
                    </option>
                    <option value="PUBLISHED">
                        <xsl:if test="season/status='PUBLISHED'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>
                        <xsl:value-of select="php:function('lang', 'Published')" />
                    </option>
                    <option value="ARCHIVED">
                        <xsl:if test="season/status='ARCHIVED'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>
                        <xsl:value-of select="php:function('lang', 'Archived')" />
                    </option>
                </select>
            </dd>
            <dt><label for="field_from"><xsl:value-of select="php:function('lang', 'From')" /></label></dt>
            <dd>
                <div class="date-picker">
                <input id="field_from" name="from_" type="text">
                    <xsl:attribute name="value"><xsl:value-of select="season/from_"/></xsl:attribute>
                </input>
                </div>
            </dd>
            <dt><label for="field_to"><xsl:value-of select="php:function('lang', 'To')" /></label></dt>
            <dd>
                <div class="date-picker">
                <input id="field_to" name="to_" type="text">
                    <xsl:attribute name="value"><xsl:value-of select="season/to_"/></xsl:attribute>
                </input>
                </div>
            </dd>
        </dl>
        <div class="form-buttons">
            <input type="submit">
			<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Save')" /></xsl:attribute>
			</input>
            <a class="cancel">
                <xsl:attribute name="href"><xsl:value-of select="season/cancel_link"/></xsl:attribute>
                <xsl:value-of select="php:function('lang', 'Cancel')" />
            </a>
        </div>
    </form>
    </div>
<script type="text/javascript">
    YAHOO.booking.initialSelection = <xsl:value-of select="season/resources_json"/>;
	var lang = <xsl:value-of select="php:function('js_lang', 'Resource Type')"/>;
</script>
</xsl:template>
