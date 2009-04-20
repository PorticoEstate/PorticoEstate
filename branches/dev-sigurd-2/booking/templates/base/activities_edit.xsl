<xsl:template match="data" class="foo">
    <div id="content">
    <h3><xsl:value-of select="lang/title" /></h3>
    <xsl:call-template name="msgbox"/>
    <form action="" method="POST">
    
    
        <dl class="form-col">
            <dt><label for="field_name"><xsl:value-of select="lang/name" /></label></dt>
            <dd>
                <input id="field_name" name="name" type="text">
                    <xsl:attribute name="value"><xsl:value-of select="resource/name"/></xsl:attribute>
                </input>
            </dd>
            
            <dt><label for="field_description"><xsl:value-of select="lang/description" /></label></dt>
            <dd>
                <textarea cols="5" rows="5" id="field_description" name="description">
                   <xsl:value-of select="resource/description"/>
                </textarea>
            </dd>

            <dt><label for="field_current_parent"><xsl:value-of select="lang/current_parent" /></label></dt>
            <dd>
                <input id="field_current_parent" name="current_parent" type="text" disabled="disabled" style="background-color: #ffffff; border: 0px; color: #000000;">
                    <xsl:attribute name="value"><xsl:value-of select="parent/name"/></xsl:attribute>
                </input>
            </dd>


            <dt><label for="field_parent_id"><xsl:value-of select="lang/parent" /></label></dt>
            <dd>
                <div class="autocomplete">
                <select name="parent_id" id="field_parent_id">
                <option value="0" selected="selected"><xsl:value-of select="lang/novalue" /></option>
                <xsl:for-each select="dropdown/results">
                				<option>
                				<xsl:attribute name="value">
                				<xsl:value-of select="id"/>
                				</xsl:attribute>
                				<xsl:value-of select="name"/>
                				</option>
                </xsl:for-each>
                </select>
                    <div id="parent_container"/>
                </div>
            </dd>
        </dl>



        <div class="form-buttons">
            <input type="submit">
				<xsl:attribute name="value"><xsl:value-of select="lang/create"/></xsl:attribute>
			</input>
        </div>
			<div class="form-buttons">
<a class="button">
            <xsl:attribute name="href"><xsl:value-of select="resource/cancel_link"></xsl:value-of></xsl:attribute>
            <xsl:value-of select="lang/cancel" />
        </a>			
			</div>        
        
    </form>
    </div>
    <script type="text/javascript">
        YAHOO.booking.initialSelection = <xsl:value-of select="booking/resources_json"/>;
    </script>
</xsl:template>
