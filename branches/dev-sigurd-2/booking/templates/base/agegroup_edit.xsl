<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">
    <h3><xsl:value-of select="php:function('lang', 'Edit agegroup group')"/></h3>
    <xsl:call-template name="msgbox"/>
	<xsl:call-template name="yui_booking_i18n"/>
    <form action="" method="POST">
    
    
        <dl class="form-col">
            <dt><label for="field_name"><xsl:value-of select="php:function('lang', 'Name')" /></label></dt>
            <dd>
                <input id="field_name" name="name" type="text">
                    <xsl:attribute name="value"><xsl:value-of select="resource/name"/></xsl:attribute>
                </input>
            </dd>
            <dt><label for="field_active"><xsl:value-of select="php:function('lang', 'Active')" /></label></dt>
            <dd>
                <select id="field_active" name="active">
                    <option value="1">
                    	<xsl:if test="resource/active=1">
                    		<xsl:attribute name="selected">checked</xsl:attribute>
                    	</xsl:if>
                        <xsl:value-of select="php:function('lang', 'Active')" />
                    </option>
                    <option value="0">
                    	<xsl:if test="resource/active=0">
                    		<xsl:attribute name="selected">checked</xsl:attribute>
                    	</xsl:if>
                        <xsl:value-of select="php:function('lang', 'Inactive')" />
                    </option>
                </select>
            </dd>
            <dt><label for="field_sort"><xsl:value-of select="php:function('lang', 'Sort order')" /></label></dt>
            <dd>
                <input id="field_sort" name="sort" type="text" value="{resource/sort}"/>
            </dd>

            <dt><label for="field_description"><xsl:value-of select="php:function('lang', 'Description')" /></label></dt>
            <dd>
                <textarea cols="5" rows="5" id="field_description" name="description">
                   <xsl:value-of select="resource/description"/>
                </textarea>
            </dd>
        </dl>



        <div class="form-buttons">
            <input type="submit">
				<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Update')" /></xsl:attribute>
			</input>
            <a class="cancel">
                <xsl:attribute name="href"><xsl:value-of select="resource/cancel_link"></xsl:value-of></xsl:attribute>
                <xsl:value-of select="php:function('lang', 'Cancel')" />
            </a>			
		</div>        
    </form>
    </div>
    <script type="text/javascript">
        YAHOO.booking.initialSelection = <xsl:value-of select="booking/resources_json"/>;
    </script>
</xsl:template>
