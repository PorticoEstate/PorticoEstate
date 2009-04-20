<xsl:template match="data">
    <div id="content">

   <h3>New Resource</h3>
    <xsl:call-template name="msgbox"/>

    <form action="" method="POST" id="form">
        <dl class="form">
            <dt><label for="field_name"><xsl:value-of select="lang/name"/></label></dt>
            <dd>
                <input id="inputs" name="name" type="text">
                    <xsl:attribute name="value"><xsl:value-of select="resource/name"/></xsl:attribute>
                </input>
            </dd>
            <dt><label for="field_building"><xsl:value-of select="lang/building"/></label></dt>
            <dd>
                <div class="autocomplete">
                <input id="field_building_id" name="building_id" type="hidden">
                    <xsl:attribute name="value"><xsl:value-of select="resource/building_id"/></xsl:attribute>
                </input>
                <input id="field_building_name" name="building_name" type="text">
                    <xsl:attribute name="value"><xsl:value-of select="resource/building_name"/></xsl:attribute>
                </input>
                <div id="building_container"/>
            </div>
            </dd>
                          <dt><label for="field_active_id"><xsl:value-of select="lang/activity" /></label></dt>
            <dd>
                <select id="field_activity_id" name="activity_id">
                <xsl:for-each select="activitydata/results">
                	<option><xsl:attribute name="value"><xsl:value-of select="id"/></xsl:attribute>
                    	<xsl:if test="resource_id=id">
                    		<xsl:attribute name="selected">selected</xsl:attribute>
                    	</xsl:if>
                        <xsl:value-of select="name" />
                	</option>
                </xsl:for-each>
                </select>
            </dd>
            <dt><label for="field_description"><xsl:value-of select="lang/description"/></label></dt>
            <dd>
	            <textarea cols="5" rows="5" id="field_description" name="description">
	            	<xsl:value-of select="resource/description"/>
	            </textarea>
            </dd>
            
        </dl>
        <div class="form-buttons">
            <input type="submit" id="button" >
                    <xsl:attribute name="value"><xsl:value-of select="lang/save"/></xsl:attribute>
            </input>
        </div>
    </form>
    </div>
</xsl:template>
