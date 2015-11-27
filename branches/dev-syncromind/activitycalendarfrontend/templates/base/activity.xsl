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
        </div>
        <form action="" method="post" name="form" id="form">
            <input type="hidden" name="id">
                <xsl:attributed name="value">
                    
                </xsl:attributed>
            </input>
            <dl class="proplist-col">
                <div class="form-buttons">
                    <xsl:if test="change_request">
                        <input type="submit" name="activity_ok">
                            <xsl:attribute name="value">
                                <xsl:value-of select="php:function('lang', 'activity_ok')" />
                            </xsl:attribute>
                        </input>
                        <input type="submit" name="change_request">
                            <xsl:attribute name="value">
                                <xsl:value-of select="php:function('lang', 'change_activity')" />
                            </xsl:attribute>
                        </input>
                    </xsl:if>
                </div>
                <fieldset title="Hva">
                    <legend>Hva</legend>
                    <dt>
                        <label for="title"><xsl:value-of select="php:function('lang', 'activity_title')" /></label>
                    </dt>
                    <dd>
                        
                    </dd>
                    <dt>
                        <label for="description"><xsl:value-of select="php:function('lang', 'description')" /></label>
                    </dt>
                    <dd>
                        
                    </dd>
                    <dt>
                        <label for="category"><xsl:value-of select="php:function('lang', 'category')" /></label>
                    </dt>
                    <dd>
                        
                    </dd>
                </fieldset>
                <fieldset id="hvem">
                    <legend>For hvem</legend>
                    <dt>
                        <label for="target"><xsl:value-of select="php:function('lang', 'target')" /></label>
                    </dt>
                    <dd>
                        
                    </dd>
                    <dt>
                        <input type="checkbox" name="special_adaptation" id="special_adaptation" disabled="disabled" />
                        <label for="special_adaptation"><xsl:value-of select="php:function('lang', 'special_adaptation')" /></label>
                    </dt>
                </fieldset>
                <fieldset title="hvor">
                    <legend>Hvor og når</legend>
                    <xsl:if test="">
                        <dt>
                            <label for="arena"><xsl:value-of select="php:function('lang', 'building')" /></label>
                        </dt>
                        <dd>
                            
                        </dd>
                    </xsl:if>
                    <xsl:if test="">
                        <dt>
                            <label for="arena"><xsl:value-of select="php:function('lang', 'arena')" /></label>
                        </dt>
                        <dd>
                            
                        </dd>
                    </xsl:if>
                    <dt>
                        <label for="district"><xsl:value-of select="php:function('lang', 'district')" /></label>
                    </dt>
                    <dd>
                        
                    </dd>
                    <dt>
                        <label for="time"><xsl:value-of select="php:function('lang', 'time')" /></label>
                    </dt>
                    <dd>
                        
                    </dd>
                </fieldset>
                <fieldset id="arr">
                    <legend>Arrangør</legend>
                    <dd>
                        <xsl:if test="">
                            <a></a>
                        </xsl:if>
                    </dd><br />
                    <legend>Kontaktperson</legend>
                    <dt>
                        <xsl:if test="">
                            <label for="contact_person_1"><xsl:value-of select="php:function('lang', 'contact_person')" /></label>
                        </xsl:if>
                    </dt>
                    <dd>
                        <label for="contact1_name">Navn</label>
                        
                        <label for="contact1_phone">Telefon</label>
                        
                        <label for="contact1_mail">E-post</label>
                    </dd>
                </fieldset>
                <fieldset>
                    <br />
                    <dt>
                        <label for="office">Kulturkontor</label>
                    </dt>
                    <dd>
                        
                    </dd>
                </fieldset>
                <br /><br />
                <div class="form-buttons">
                    <xsl:if test="change_request">
                        <input type="submit" name="activity_ok">
                            <xsl:attribute name="value">
                                <xsl:value-of select="php:function('lang', 'activity_ok')" />
                            </xsl:attribute>
                        </input>
                        <input type="submit" name="change_request">
                            <xsl:attributed name="value">
                                <xsl:value-of select="php:function('lang', 'change_activity')" />
                            </xsl:attributed>
                        </input>
                    </xsl:if>
                </div>
            </dl>
        </form>
    </div>
</xsl:template>