<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">

   <h3><xsl:value-of select="php:function('lang', 'Edit Resource')" /></h3>
    <xsl:call-template name="msgbox"/>

    <form action="" method="POST" id="form">
        <dl class="form">
            <dt><label for="field_name"><xsl:value-of select="php:function('lang', 'Resource')" /></label></dt>
            <dd>
                <input id="inputs" name="name" type="text">
                    <xsl:attribute name="value"><xsl:value-of select="resource/name"/></xsl:attribute>
                </input>
            </dd>
            <dt><label for="field_building"><xsl:value-of select="php:function('lang', 'Building')" /></label></dt>
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
                          <dt><label for="field_active_id"><xsl:value-of select="php:function('lang', 'Activity')" /></label></dt>
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
            <dt><label for="field_description"><xsl:value-of select="php:function('lang', 'Description')" /></label></dt>
            <dd>
	            <textarea cols="5" rows="5" id="field_description" name="description">
	            	<xsl:value-of select="resource/description"/>
	            </textarea>
            </dd>
            
        </dl>
        <div class="form-buttons">
            <input type="submit" id="button" >
                    <xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Save')" /></xsl:attribute>
            </input>
            <a class="cancel">
                <xsl:attribute name="href"><xsl:value-of select="resource/cancel_link"></xsl:value-of></xsl:attribute>
                <xsl:value-of select="php:function('lang', 'Cancel')" />
            </a>			
        </div>
    </form>
    </div>
</xsl:template>
