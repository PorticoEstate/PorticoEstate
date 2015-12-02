<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div>
        <div id="details">
            <xsl:if test="message != ''">
                <div class="success">
                    <xsl:value-of select="message" disable-output-escaping="yes" />
                </div>
            </xsl:if>
            <xsl:if test="error != ''">
                <div class="error">
                    <xsl:value-of select="error" disable-output-escaping="yes" />
                </div>
            </xsl:if>
        </div>
        <div class="pageTop">
            <h1><xsl:value-of select="php:function('lang', 'activity')" /></h1>
        </div>
        <form action="" method="post" name="form" id="form">
            <input type="hidden" name="id">
                <xsl:attributed name="value">
                    <xsl:value-of select="activity/id" />
                </xsl:attributed>
            </input>
            <dl class="proplist-col">
                <div class="form-buttons">
                    <xsl:if test="change_request = 1">
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
                        <xsl:value-of select="activity/title" />
                    </dd>
                    <dt>
                        <label for="description"><xsl:value-of select="php:function('lang', 'description')" /></label>
                    </dt>
                    <dd>
                        <xsl:value-of select="activity/description" disable-output-escaping="yes" />
                    </dd>
                    <dt>
                        <label for="category"><xsl:value-of select="php:function('lang', 'category')" /></label>
                    </dt>
                    <dd>
                        <xsl:value-of select="activity/category" disable-output-escaping="yes" />
                    </dd>
                </fieldset>
                <fieldset id="hvem">
                    <legend>For hvem</legend>
                    <dt>
                        <label for="target"><xsl:value-of select="php:function('lang', 'target')" /></label>
                    </dt>
                    <dd>
                        <xsl:value-of select="activity/targets" disable-output-escaping="yes" />
                    </dd>
                    <dt>
                        <input type="checkbox" name="special_adaptation" id="special_adaptation" disabled="disabled">
                            <xsl:if test="activity/special_adaptation = 1">
                                <xsl:attribute name="checked">
                                    checked
                                </xsl:attribute>
                            </xsl:if>
                        </input>
                        <label for="special_adaptation"><xsl:value-of select="php:function('lang', 'special_adaptation')" /></label>
                    </dt>
                </fieldset>
                <fieldset title="hvor">
                    <legend>Hvor og når</legend>
                    <xsl:if test="activity/internal_arena = 1">
                        <dt>
                            <label for="arena"><xsl:value-of select="php:function('lang', 'building')" /></label>
                        </dt>
                        <dd>
                            <xsl:value-of select="activity/building_name" />
                        </dd>
                    </xsl:if>
                    <xsl:if test="activity/arena = 1">
                        <dt>
                            <label for="arena"><xsl:value-of select="php:function('lang', 'arena')" /></label>
                        </dt>
                        <dd>
                            <xsl:value-of select="activity/arena_name" disable-output-escaping="yes" />
                        </dd>
                    </xsl:if>
                    <dt>
                        <label for="district"><xsl:value-of select="php:function('lang', 'district')" /></label>
                    </dt>
                    <dd>
                        <xsl:value-of select="activity/districts" disable-output-escaping="yes" />
                    </dd>
                    <dt>
                        <label for="time"><xsl:value-of select="php:function('lang', 'time')" /></label>
                    </dt>
                    <dd>
                        <xsl:value-of select="activity/time" disable-output-escaping="yes" />
                    </dd>
                </fieldset>
                <fieldset id="arr">
                    <legend>Arrangør</legend>
                    <dd>
                        <xsl:value-of select="organization/name" />
                        <xsl:if test="change_request != 1">
                            <xsl:if test="organization/new_org != 1">
                                <a target="_blank">
                                    <xsl:attribute name="href">
                                        <xsl:value-of select="organization/edit_link" />
                                    </xsl:attribute>
                                    <xsl:value-of select="php:function('lang', 'edit_organization')" />
                                </a>
                            </xsl:if>
                        </xsl:if>
                    </dd><br />
                    <legend>Kontaktperson</legend>
                    <dt>
                        <xsl:if test="activity/contact_person_1 = 1">
                            <label for="contact_person_1"><xsl:value-of select="php:function('lang', 'contact_person')" /></label>
                        </xsl:if>
                    </dt>
                    <dd>
                        <label for="contact1_name">Navn </label>
                        <xsl:value-of select="concat(' ', activity/contact1_name)" /><br />
                        <label for="contact1_phone">Telefon </label>
                        <xsl:value-of select="concat(' ', activity/contact1_phone)" /><br />
                        <label for="contact1_mail">E-post </label>
                        <xsl:value-of select="concat(' ', activity/contact1_mail)" /><br />
                    </dd>
                </fieldset>
                <fieldset>
                    <br />
                    <dt>
                        <label for="office">Kulturkontor</label>
                    </dt>
                    <dd>
                        <xsl:value-of select="activity/office" disable-output-escaping="yes" />
                    </dd>
                </fieldset>
                <br /><br />
                <div class="form-buttons">
                    <xsl:if test="change_request = 1">
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
