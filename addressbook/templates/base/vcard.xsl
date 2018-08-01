
<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="in">
			<xsl:apply-templates select="in" />
		</xsl:when>
		<xsl:when test="out">
			<xsl:apply-templates select="out" />
		</xsl:when>                
	</xsl:choose>
</xsl:template>

<xsl:template xmlns:php="http://php.net/xsl" match="in">
    <div class="content">
        <div>
            <xsl:variable name="form_action">
                <xsl:value-of select="form_action"/>
            </xsl:variable>

            <form id="form" name="form" method="post" action="{$form_action}" class="pure-form pure-form-aligned" enctype="multipart/form-data">
                <div id="tab-content">
                    <xsl:value-of disable-output-escaping="yes" select="tabs"/>

                    <input type="hidden" id="active_tab" name="active_tab" value="{value_active_tab}"/>

                    <div id="import">
                        <fieldset>
                            <div class="pure-control-group">                                           
                                <label><xsl:value-of select="php:function('lang', 'VCard')"/></label>
                                <input name="uploadedfile" value="" type="file"></input>                                     
                            </div>                                                                                                                                                                                                                                                                                             
                        </fieldset>
                    </div>
                </div>
                <div id="submit_group_bottom" class="proplist-col">
                    <xsl:variable name="lang_save">
                            <xsl:value-of select="php:function('lang', 'Load vcard')"/>
                    </xsl:variable>
                    <input type="submit" class="pure-button pure-button-primary" name="convert">
                            <xsl:attribute name="value">
                                    <xsl:value-of select="$lang_save"/>
                            </xsl:attribute>
                            <xsl:attribute name="title">
                                    <xsl:value-of select="$lang_save"/>
                            </xsl:attribute>
                    </input>
                    <xsl:variable name="cancel_url">
                            <xsl:value-of select="cancel_url"/>
                    </xsl:variable>
                    <input type="button" class="pure-button pure-button-primary" name="cancel" onClick="window.location = '{cancel_url}';">
                            <xsl:attribute name="value">
                                    <xsl:value-of select="php:function('lang', 'cancel')"/>
                            </xsl:attribute>
                    </input>
                </div>
            </form>
        </div>
    </div>
</xsl:template>

<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected != 0">
			<xsl:attribute name="selected" value="selected"/>
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>
