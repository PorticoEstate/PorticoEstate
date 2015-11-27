<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div>
        <div id="details">
            <xsl:choose>
                <xsl:when test="message">
                    <div class="success">
                        <xsl:value-of select="message" />
                    </div>
                </xsl:when>
                <xsl:when test="error">
                    <div class="error">
                        <xsl:value-of select="error" />
                    </div>
                </xsl:when>
            </xsl:choose>
        </div>
        <div class="pageTop">
            <h1><xsl:value-of select="php:function('lang', 'activity')" /></h1>
            <div>
                <xsl:value-of select="php:function('lang', 'required_fields')" />
            </div>
        </div>
        <form action="" method="post" name="form" id="form">
            <input type="hidden" name="id">
                <xsl:attribute name="value">
                    
                </xsl:attribute>
            </input>
            <dl class="proplist-col">
                <fieldset>
                    <xsl:attributed name="title">
                        <xsl:value-of select="php:function('lang', 'what')" />
                    </xsl:attributed>
                    <legend>Hva</legend>
                    <dt>
                        <label for="title">
                            <xsl:value-of select="php:function('lang', 'activity_title')" /> (*)
                            <a href="javascript:void(0)">
                                <xsl:attribute name="onclick">
                                    alert('<xsl:value-of select="php:function('lang', 'help_new_activity_title')" />');return false;
                                </xsl:attribute>
                                <img alt="Hjelp" src="{helpImg}">
                            </a>
                        </label>
                    </dt>
                    <dd>
                        
                    </dd>
                </fieldset>
            </dl>
        </form>
    </div>
</xsl:template>