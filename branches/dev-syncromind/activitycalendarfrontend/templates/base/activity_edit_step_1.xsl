<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div>
        <div class="pageTop">
            <h1><xsl:value-of select="php:function('lang', 'edit_activity')" /></h1>
            <form action="" method="post" name="form" id="form">
                <dl class="proplist-col" style="width:200%">
                    <dt>
                        <xsl:if test="message">
                            <xsl:value-of select="message" />
                        </xsl:if>
                        <xsl:if test="not(message)">
                            <xsl:value-of select="php:function('lang', 'activity_edit_helptext_step1')" disable-output-escaping="yes" />
                            <br/><br/>
                        </xsl:if>
                    </dt>
                    <xsl:if test="not(message)">
                        <dd>
                            <select name="organization_id" id="organization_id" onchange="javascript:get_activities();">
                                <option value="">Ingen organisasjon valgt</option>
                                <xsl:for-each select="organizations">
                                    <option>
                                        <xsl:attribute name="value">
                                            <xsl:value-of select="id" />
                                        </xsl:attribute>
                                        <xsl:value-of select="name" />
                                    </option>
                                </xsl:for-each>
                            </select>
                        </dd>
                        <dt>
                            &nbsp;
                        </dt>
                        <dd>
                            <div id="activity_select">
                                <select name="activity_id" id="activity_id">
                                    <option value="0">Ingen aktivitet valgt</option>
                                </select>
                            </div>
                            <br /><br />
                        </dd>
                        <div class="form-buttons">
                            <input type="submit" name="step_1" onclick="return isOK();">
                                <xsl:attribute name="value">
                                    <xsl:value-of select="php:function('lang', 'send_change_request')" />
                                </xsl:attribute>
                            </input>
                        </div>
                    </xsl:if>
                </dl>
            </form>
        </div>
    </div>
</xsl:template>