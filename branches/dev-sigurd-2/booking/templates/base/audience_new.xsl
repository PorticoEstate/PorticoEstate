<xsl:template match="data" class="foo">
    <div id="content">
    <h3><xsl:value-of select="lang/title" /></h3>
    <xsl:call-template name="msgbox"/>
    <form action="" method="POST">
    
    
        <dl class="form-col">
            <dt><label for="field_name"><xsl:value-of select="lang/name" /></label></dt>
            <dd>
                <input id="field_name" name="name" type="text">
                    <xsl:attribute name="value"><xsl:value-of select="booking/name"/></xsl:attribute>
                </input>
            </dd>
            
            <dt><label for="field_active"><xsl:value-of select="lang/active" /></label></dt>
            <dd>
                <select id="field_active" name="active">
                    <option value="1">
                    	<xsl:if test="resource/active=1">
                    		<xsl:attribute name="selected">checked</xsl:attribute>
                    	</xsl:if>
                        <xsl:value-of select="lang/active" />
                    </option>
                    <option value="0">
                    	<xsl:if test="resource/active=0">
                    		<xsl:attribute name="selected">checked</xsl:attribute>
                    	</xsl:if>
                        <xsl:value-of select="lang/inactive" />
                    </option>
                </select>
            </dd>

            <dt><label for="field_description"><xsl:value-of select="lang/description" /></label></dt>
            <dd>
                <textarea cols="5" rows="5" id="field_description" name="description"><xsl:value-of select="booking/description"/></textarea>
            </dd>
        </dl>



        <div class="form-buttons">
            <input type="submit">
				<xsl:attribute name="value"><xsl:value-of select="lang/create"/></xsl:attribute>
			</input>
            <a class="cancel">
                <xsl:attribute name="href"><xsl:value-of select="booking/cancel_link"/></xsl:attribute>
                <xsl:value-of select="lang/cancel" />
            </a>
        </div>
    </form>
    </div>
    <script type="text/javascript">
        YAHOO.booking.initialSelection = <xsl:value-of select="booking/resources_json"/>;
    </script>
</xsl:template>
