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
            <h1><xsl:value-of select="php:function('lang', 'edit_organization')" /></h1>
            <form action="" method="post" name="form" id="form">
                <div class="pure-g">
                    <div class="pure-u-1">
                        <dl class="proplist-col">
                            <input type="hidden" name="organization_id" id="organization_id">
                                <xsl:attribute name="value">
                                    <xsl:value-of select="organization/id" />
                                </xsl:attribute>
                            </input>
                            <div style="overflow:auto;">
                                <p>
                                    Endre organisasjon 
                                    <a href="javascript:void(0);" class="helpLink">
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
                                    <a href="javascript:void(0);" class="helpLink">
                                        <xsl:attribute name="onclick">
                                            alert('<xsl:value-of select="php:function('lang' , 'help_organization_name')" />');return false;
                                        </xsl:attribute>
                                        <img alt="Hjelp" src="{helpImg}" />
                                    </a>
                                </dt>
                                <dd>
                                    <input type="text" name="orgname" id="orgname" class="pure-u-1 input-80" maxlength="254" data-validation="required" data-validation-error-msg="Organisasjonsnavn må fylles ut!">
                                        <xsl:attribute name="value">
                                            <xsl:value-of select="organization/name" />
                                        </xsl:attribute>
                                    </input>
                                </dd>
                                <dt><label for="orgno">Organisasjonsnummer</label></dt>
                                <dd>
                                    <input type="text" name="orgno" maxlength="254">
                                        <xsl:attribute name="value">
                                            <xsl:value-of select="organization/number" />
                                        </xsl:attribute>
                                    </input>
                                </dd>
                                <div class="input-part-1">
                                    <dt>
                                        <label for="street">Gateadresse</label>
                                        <a href="javascript:void(0);" class="helpLink">
                                            <xsl:attribute name="onclick">
                                                alert('<xsl:value-of select="php:function('lang', 'help_streetaddress')" />');return false;
                                            </xsl:attribute>
                                            <img alt="Hjelp" src="{helpImg}" />
                                        </a>
                                    </dt>
                                    <dd>
                                        <input type="text" name="address" id="address" onkeyup="javascript:get_address_search()" class="pure-u-1 input-50">
                                            <xsl:attribute name="value">
                                                <xsl:value-of select="organization/address" />
                                            </xsl:attribute>
                                        </input>
                                        <br />
                                        <div id="address_container" />
                                    </dd>
                                </div>
                                <div class="input-part-2">
                                    <dt style="clear:right;float:left;">
                                        <label for="number">Husnummer</label><br />
                                    </dt>
                                    <dd>
                                        <input type="text" name="number" class="pure-u-1 input-5" />
                                    </dd>
                                </div>
                                <div class="input-part-1">
                                    <dt style="clear:left;margin-right:20px;float:left;">
                                        <label for="postzip">Postnummer</label><br />
                                    </dt>
                                    <dd>
                                        <input type="text" name="postzip" class="pure-u-1 input-5">
                                            <xsl:attribute name="value">
                                                <xsl:value-of select="organization/zip_code" />
                                            </xsl:attribute>
                                        </input>
                                    </dd>
                                </div>
                                <div class="input-part-2">
                                    <dt style="float:left;">
                                        <label for="postaddress">Poststed</label><br />

                                    </dt>
                                    <dd>
                                        <input type="text" name="postaddress" class="pure-u-1 input-40">
                                            <xsl:attribute name="value">
                                                <xsl:value-of select="organization/city" />
                                            </xsl:attribute>
                                        </input>
                                    </dd>
                                </div>
                            </div>
                            <dt>
                                <label for="homepage">
                                    Hjemmeside
                                </label>
                                <a href="javascript:void(0);" class="helpLink">
                                    <xsl:attribute name="onclick">
                                        alert('<xsl:value-of select="php:function('lang', 'help_homepage')" />');return false;
                                    </xsl:attribute>
                                    <img alt="Hjelp" src="{helpImg}" />
                                </a>
                            </dt>
                            <dd>
                                <input type="text" name="homepage" class="pure-u-1 input-80">
                                    <xsl:attribute name="value">
                                        <xsl:value-of select="organization/homepage" />
                                    </xsl:attribute>
                                </input>
                            </dd><br /><br />
                            <div style="overflow:auto;">
                                Kontaktperson for organisasjonen 
                                <a href="javascript:void(0);" class="helpLink">
                                    <xsl:attribute name="onclick">
                                        alert('<xsl:value-of select="php:function('lang', 'help_contact_person')" />');return false;
                                    </xsl:attribute>
                                    <img alt="Hjelp" src="{helpImg}" />
                                </a>
                                <dt><label for="contact1_name">Navn (*)</label></dt>
                                <dd>
                                    <input name="org_contact1_name" id="org_contact1_name" class="pure-u-1 input-80" type="text" data-validation="required" data-validation-error-msg="Navn på kontaktperson må fylles ut!">
                                        <xsl:attribute name="value">
                                            <xsl:value-of select="organization/contact1_name" />
                                        </xsl:attribute>
                                    </input>
                                </dd>
                                <dt><label for="contact1_phone">Telefon (*)</label></dt>
                                <dd>
                                    <input name="org_contact1_phone" id="org_contact1_phone" type="text" data-validation="contact_phone contact_phone_length">
                                        <xsl:attribute name="value">
                                            <xsl:value-of select="organization/contact1_phone" />
                                        </xsl:attribute>
                                    </input>
                                </dd>
                                <dt><label for="contact1_mail">E-post (*)</label></dt>
                                <dd>
                                    <input name="org_contact1_mail" id="org_contact1_mail" class="pure-u-1 input-50" type="text" data-validation="contact_mail">
                                        <xsl:attribute name="value">
                                            <xsl:value-of select="organization/contact1_mail" />
                                        </xsl:attribute>
                                    </input>
                                </dd>
                                <dt><label for="contact2_mail">Gjenta e-post (*)</label></dt>
                                <dd>
                                    <input name="org_contact2_mail" id="org_contact2_mail" class="pure-u-1 input-50" type="text" data-validation="contact_mail2 contact_mail2_confirm">
                                        <xsl:attribute name="value">
                                            <xsl:value-of select="organization/contact1_mail" />
                                        </xsl:attribute>
                                    </input>
                                </dd>
                            </div>
                            <div class="form-buttons">
                                <input type="submit" name="save_org">
                                    <xsl:attribute name="value">
                                        <xsl:value-of select="php:function('lang', 'send_change_request')" />
                                    </xsl:attribute>
                                </input>
                            </div>
                        </dl>
                    </div>
                </div>
            </form>
        </div>
    </div>
</xsl:template>