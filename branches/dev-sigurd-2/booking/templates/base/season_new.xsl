<xsl:template match="data">
    <div id="content">
        <ul class="pathway">
            <li><xsl:value-of select="lang/title" /></li>
        </ul>

    <xsl:call-template name="msgbox"/>

    <form action="" method="POST">
        <dl class="form-col">
            <dt><label for="field_name"><xsl:value-of select="lang/name" /></label></dt>
            <dd>
                <input id="field_name" name="name" type="text">
                    <xsl:attribute name="value"><xsl:value-of select="season/name"/></xsl:attribute>
                </input>
            </dd>
            <dt><label for="field_building"><xsl:value-of select="lang/building" /></label></dt>
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
            <dt><label for="field_building"><xsl:value-of select="lang/resources" /></label></dt>
            <dd>
                    <div id="resources-container"><xsl:value-of select="lang/select-building-first" /></div>
            </dd>
        </dl>
        <dl class="form-col">
            <dt><label for="field_status"><xsl:value-of select="lang/status" /></label></dt>
            <dd>
                <select name="status" id="status_field">
                    <option value="PLANNING">
                        <xsl:if test="season/status='PLANNING'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>
                        <xsl:value-of select="lang/planning" />
                    </option>
                    <option value="PUBLISHED">
                        <xsl:if test="season/status='PUBLISHED'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>
                        <xsl:value-of select="lang/published" />
                    </option>
                    <option value="ARCHIVED">
                        <xsl:if test="season/status='ARCHIVED'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>
                        <xsl:value-of select="lang/archived" />
                    </option>
                </select>
            </dd>
            <dt><label for="field_from"><xsl:value-of select="lang/from" /></label></dt>
            <dd>
                <div class="date-picker">
                <input id="field_from" name="from_" type="text">
                    <xsl:attribute name="value"><xsl:value-of select="season/from_"/></xsl:attribute>
                </input>
                </div>
            </dd>
            <dt><label for="field_to"><xsl:value-of select="lang/to" /></label></dt>
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
				<xsl:attribute name="value"><xsl:value-of select="lang/create"/></xsl:attribute>
			</input>
            <a class="cancel">
                <xsl:attribute name="href"><xsl:value-of select="season/cancel_link"/></xsl:attribute>
                <xsl:value-of select="lang/cancel" />
            </a>
        </div>
    </form>
    </div>
<script type="text/javascript">
    YAHOO.booking.initialSelection = <xsl:value-of select="season/resources_json"/>;
</script>
</xsl:template>
