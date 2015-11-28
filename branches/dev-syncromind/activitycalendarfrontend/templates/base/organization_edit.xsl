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
            <h1><xsl:value-of select="php:function('lang', 'edit_organization')" /></h1>
            <form action="" method="post" name="form" id="form">
                <dl class="proplist-col">
                    <input type="hidden" name="organization_id" id="organization_id">
                        <xsl:attribute name="value"></xsl:attribute>
                    </input>
                    <div style="overflow:auto;">
                        <p>
                            Endre organisasjon 
                            <a href="javascript:void(0);">
                                <xsl:attribute name="onclick">
                                    alert('<xsl:value-of select="php:function('lang', 'help_edit_activity_org')" />');return false;
                                </xsl:attribute>
                                <img alt="Hjelp" src="{helpImg}" />
                            </a>
                        </p>
                        Felt merket med (*) er påkrevde felt <br /><br />
                        <p></p>
                        <dt>
                            <label for="orgname">Organisasjonsnavn (*)</label>
                            <a href="javascript:void(0);">
                                <xsl:attribute name="onclick">
                                    alert('<xsl:value-of select="php:function('lang' , 'help_organization_name')" />');return false;
                                </xsl:attribute>
                                <img alt="Hjelp" src="{helpImg}" />
                            </a>
                        </dt>
                        <dd>
                            <input type="text" name="orgname" id="orgname" size="80" maxlength="254">
                                <xsl:attribute name="value"></xsl:attribute>
                            </input>
                        </dd>
                        <dt><label for="orgno">Organisasjonsnummer</label></dt>
                        <dd>
                            <input type="text" name="orgno" maxlength="254">
                                <xsl:attribute name="value"></xsl:attribute>
                            </input>
                        </dd>
                        <dt style="margin-right:20px;float:left;">
                            <label for="street">Gateadresse</label>
                            <a href="javascript:void(0);">
                                <xsl:attribute name="onclick">
                                    alert('<xsl:value-of select="php:function('lang', 'help_streetaddress')" />');return false;
                                </xsl:attribute>
                                <img alt="Hjelp" src="{helpImg}" />
                            </a>
                            <br />
                            <input type="text" name="address" id="address" onkeyup="javascript:get_address_search()" size="50">
                                <xsl:attribute name="value"></xsl:attribute>
                            </input>
                            <br />
                            <div id="address_container" />
                        </dt>
                        <dt style="clear:right;float:left;">
                            <label for="number">Husnummer</label><br />
                            <input type="text" name="number" size="5" />
                        </dt><br />
                        <dt style="clear:left;margin-right:20px;float:left;">
                            <label for="postzip">Postnummer</label><br />
                            <input type="text" name="postzip" size="5">
                                <xsl:attribute name="value"></xsl:attribute>
                            </input>
                        </dt>
                        <dt style="float:left;">
                            <label for="postaddress">Poststed</label><br />
                            <input type="text" name="postaddress" size="40">
                                <xsl:attribute name="value"></xsl:attribute>
                            </input>
                        </dt><br /><br />
                    </div>
                    <dt>
                        <label for="homepage">
                            Hjemmeside 
                            <a href="javascript:void(0);">
                                <xsl:attribute name="onclick">
                                    alert('<xsl:value-of select="php:function('lang', 'help_homepage')" />');return false;
                                </xsl:attribute>
                                <img alt="Hjelp" src="{helpImg}" />
                            </a>
                        </label>
                    </dt>
                    <dd>
                        <input type="text" name="homepage" size="80">
                            <xsl:attribute name="value"></xsl:attribute>
                        </input>
                    </dd><br /><br />
                    <div style="overflow:auto;">
                        Kontaktperson for organisasjonen 
                        <a href="javascript:void(0);">
                            <xsl:attribute name="onclick">
                                alert('<xsl:value-of select="php:function('lang', 'help_contact_person')" />');return false;
                            </xsl:attribute>
                            <img alt="Hjelp" src="{helpImg}" />
                        </a>
                        <dt><label for="contact1_name">Navn (*)</label></dt>
                        <dd>
                            <input name="org_contact1_name" id="org_contact1_name" size="80" type="text">
                                <xsl:attribute name="value"></xsl:attribute>
                            </input>
                        </dd>
                        <dt><label for="contact1_phone">Telefon (*)</label></dt>
                        <dd>
                            <input name="org_contact1_phone" id="org_contact1_phone" type="text">
                                <xsl:attribute name="value"></xsl:attribute>
                            </input>
                        </dd>
                        <dt><label for="contact1_mail">E-post (*)</label></dt>
                        <dd>
                            <input name="org_contact1_mail" id="org_contact1_mail" size="50" type="text">
                                <xsl:attribute name="value"></xsl:attribute>
                            </input>
                        </dd>
                        <dt><label for="contact2_mail">Gjenta e-post (*)</label></dt>
                        <dd>
                            <input name="org_contact2_mail" id="org_contact2_mail" size="50" type="text">
                                <xsl:attribute name="value"></xsl:attribute>
                            </input>
                        </dd>
                    </div>
                    <div class="form-buttons">
                        <input type="submit" name="save_org" onclick="return isOK();">
                            <xsl:attribute name="value">
                                <xsl:value-of select="php:function('lang', 'send_change_request')" />
                            </xsl:attribute>
                        </input>
                    </div>
                </dl>
            </form>
        </div>
    </div>
</xsl:template>