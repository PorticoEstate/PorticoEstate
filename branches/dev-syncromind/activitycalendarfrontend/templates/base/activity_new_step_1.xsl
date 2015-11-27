<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div>
        <div class="pageTop">
            <h1><xsl:value-of select="php:function('lang', 'new_activity_helptext')" /></h1>
            <form method="POST" name="form" id="form">
                <input type="hidden" name="organization_id_hidden" id="organization_id_hidden" value="" />
                <fieldset>
                    <dl class="proplist-col">
                        <legend><xsl:value-of select="php:function('lang', 'responsible')" /></legend>
                        <dt>
                            <label for="organization_id"><xsl:value-of select="php:function('lang', 'choose_org')" /></label>
                            <a href="javascript:void(0)">
                                <xsl:attribute name="onclick">
                                    alert('<xsl:value-of select="php:function('lang', 'help_choose_activity_org')" />');return false;
                                </xsl:attribute>
                                <img alt="Hjelp" src="{helpImg}" />
                            </a>
                        </dt>
                        <dd>
                            <select name="organization_id" id="organization_id">
                                <option value="">Ingen organisasjon valgt</option>
                                <!--xsl:for-each select="organizations">
                                    <option>
                                        <xsl:attribute name="value">
                                            <xsl:value-of select="id" />
                                        </xsl:attribute>
                                        <xsl:value-of select="name" />
                                    </option>
                                </xsl:for-each-->
                            </select>
                        </dd>
                        <a id="displayText3" href="javascript:void(0)">Ikke i listen? Registrer ny organisasjon</a><br />
                        <dt>
                            <div style="overflow:hidden;" id="toggleText3">
                                <dl>
                                    <div style="overflow:hidden;">
                                        <p>
                                            Registrer ny organisasjon
                                            <a href="javascript:void(0)">
                                                <xsl:attribute name="onclick">
                                                    alert('<xsl:value-of select="php:function('lang', 'help_new_activity_org')" />');return false;
                                                </xsl:attribute>
                                                <img alt="Hjelp" src="{helpImg}" />
                                            </a>
                                        </p>
                                        Felt merket med (*) er påkrevde felt <br /><br />
                                        <dt>
                                            <label for="orgname">Organisasjonsnavn (*)</label>
                                            <a href="javascript:void(0)">
                                                <xsl:attribute name="onclick">
                                                    alert('<xsl:value-of select="php:function('lang', 'help_organization_name')" />');return false;
                                                </xsl:attribute>
                                                <img alt="Hjelp" src="{helpImg}" />
                                            </a>
                                        </dt>
                                        <dd><input type="text" name="orgname" id="orgname" size="80" maxlength="254" /></dd>
                                        <dt><label for="orgno">Organisasjonsnummer</label></dt>
                                        <dd><input type="text" name="orgno" maxlength="254" /></dd>
                                        <dt style="margin-right:20px;float:left;">
                                            <label for="street">
                                                Gateadresse
                                                <a href="javascript:void(0);">
                                                    <xsl:attribute name="onclick">
                                                        alert('<xsl:value-of select="php:function('help_streetaddress')" />');return false;
                                                    </xsl:attribute>
                                                    <img alt="Hjelp" src="{helpImg}" />
                                                </a>
                                            </label><br />
                                            <input id="address" onkeyup="javascript:get_address_search()" name="address" size="50" type="text" /><br />
                                            <div id="address_container" />
                                        </dt>
                                        <dt style="clear:right;float:left;">
                                            <label for="number">Husnummer</label><br />
                                            <input name="number" size="5" type="text" />
                                        </dt><br />
                                        <dt style="clear:left;margin-right:20px;float:left;">
                                            <label for="postzip">Postnummer</label><br />
                                            <input name="postzip" size="5" type="text" />
                                        </dt>
                                        <dt style="float:left;">
                                            <label for="postaddress">Poststed</label><br />
                                            <input name="postaddress" size="40" type="text" />
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
                                    <dd><input name="homepage" value="http://" size="80" type="text" maxlength="254" /></dd><br /><br />
                                    <div style="overflow:hidden;">
                                        Kontaktperson for organisasjonen 
                                        <a href="javascript:void(0);">
                                            <xsl:attribute name="onclick">
                                                alert('<xsl:value-of select="php:function('lang', 'help_contact_person')" />');return false;
                                            </xsl:attribute>
                                            <img alt="Hjelp" src="{helpImg}" />
                                        </a><br />
                                        <dt><label for="contact1_name">Navn (*)</label></dt>
                                        <dd><input name="org_contact1_name" id="org_contact1_name" type="text" size="80" maxlength="254" /></dd>
                                        <dt><label for="contact1_phone">Telefon (*)</label></dt>
                                        <dd><input name="org_contact1_phone" id="org_contact1_phone" type="text" /></dd>
                                        <dt><label for="contact1_mail">E-post (*)</label></dt>
                                        <dd><input name="org_contact1_mail" id="org_contact1_mail" type="text" size="50" /></dd>
                                        <dt><label for="contact2_mail">Gjenta e-post (*)</label></dt>
                                        <dd><input name="org_contact2_mail" id="org_contact2_mail" type="text" size="50" /></dd>
                                    </div>
                                </dl>
                            </div>
                        </dt>
                        <br /><br />
                        <div class="form-buttons">
                            <input type="submit" name="step_1" onclick="return isOK();">
                                <xsl:attribute name="value">
                                    <xsl:value-of select="php:function('lang', 'next')" />
                                </xsl:attribute>
                            </input>
                        </div>
                    </dl>
                </fieldset>
            </form>
        </div>
    </div>
</xsl:template>
