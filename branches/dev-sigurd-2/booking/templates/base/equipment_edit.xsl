<xsl:template match="data">
    <div id="content">

        <ul class="pathway">
            <li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="resource/building_link"/></xsl:attribute>
                    <xsl:value-of select="lang/buildings"/>
                </a>
            </li>
            <li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="resource/resources_link"/></xsl:attribute>
                    <xsl:value-of select="lang/resources"/>
                </a>
            </li>
            <li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="resource/resource_link"/></xsl:attribute>
                    <xsl:value-of select="resource/resource_name"/>
                </a>
            </li>
            <li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="resource/equipment_link"/></xsl:attribute>
                    <xsl:value-of select="lang/equipment"/>
                </a>
            </li>
            <li>
                <a href="">
                    <xsl:value-of select="resource/name"/>
                </a>
            </li>
        </ul>

        <xsl:call-template name="msgbox"/>

        <form action="" method="POST" id="form">
            <dl class="form">
                <dt><label for="field_name"><xsl:value-of select="lang/name"/></label></dt>
                <dd>
                    <input id="inputs" name="name" type="text">
                        <xsl:attribute name="value"><xsl:value-of select="resource/name"/></xsl:attribute>
                    </input>
                </dd>
            <dt><label for="field_description"><xsl:value-of select="lang/description"/></label></dt>
            <dd>
                <textarea id="inputs" name="description" cols="5" rows="5">
                    <xsl:value-of select="resource/description"/>
                </textarea>
            </dd>
            <dt><label for="field_building"><xsl:value-of select="lang/resource"/></label></dt>
            <dd>
                <div class="autocomplete">
                <input id="field_resource_id" name="resource_id" type="hidden">
                    <xsl:attribute name="value"><xsl:value-of select="resource/resource_id"/></xsl:attribute>
                </input>
                <input id="field_resource_name" name="resource_name" type="text">
                    <xsl:attribute name="value"><xsl:value-of select="resource/resource_name"/></xsl:attribute>
                </input>
                <div id="resource_container"/>
            </div>
            </dd>
            </dl>
            <div class="form-buttons">
            <input type="submit">
			<xsl:attribute name="value"><xsl:value-of select="lang/save"/></xsl:attribute>
			</input>
            </div>
        </form>
    </div>
</xsl:template>
