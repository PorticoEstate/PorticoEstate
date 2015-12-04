<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div>
        <div class="pageTop">
            <h1><xsl:value-of select="php:function('lang', 'new_activity_helptext')" /></h1>
            <form method="POST" name="form" id="form">
                <div class="pure-g">
                    <div class="pure-u-1">
                        <input type="hidden" name="organization_id_hidden" id="organization_id_hidden" value="" />
                        <fieldset>
                            <dl class="proplist-col">
                                <legend><xsl:value-of select="php:function('lang', 'responsible')" /></legend>
                                <dt>
                                    <label for="organization_id"><xsl:value-of select="php:function('lang', 'choose_org')" /></label>
                                    <a href="javascript:void(0);" class="helpLink">
                                        <xsl:attribute name="onclick">
                                            alert('<xsl:value-of select="php:function('lang', 'help_choose_activity_org')" />');return false;
                                        </xsl:attribute>
                                        <img alt="Hjelp" src="{helpImg}" />
                                    </a>
                                </dt>
                                <dd>
                                    <select name="organization_id" id="organization_id" class="pure-u-1 input-80" data-validation="organization_id">
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
                                <a id="displayText3" href="javascript:void(0);">Ikke i listen? Registrer ny organisasjon</a><br />
                                <dt>
                                    <div style="overflow:hidden;" id="toggleText3">
                                        <dl>
                                            <div style="overflow:hidden;">
                                                <p>
                                                    Registrer ny organisasjon
                                                    <a href="javascript:void(0);" class="helpLink">
                                                        <xsl:attribute name="onclick">
                                                            alert('<xsl:value-of select="php:function('lang', 'help_new_activity_org')" />');return false;
                                                        </xsl:attribute>
                                                        <img alt="Hjelp" src="{helpImg}" />
                                                    </a>
                                                </p>
                                                Felt merket med (*) er påkrevde felt <br /><br />
                                                <dt>
                                                    <label for="orgname">Organisasjonsnavn (*)</label>
                                                    <a href="javascript:void(0);" class="helpLink">
                                                        <xsl:attribute name="onclick">
                                                            alert('<xsl:value-of select="php:function('lang', 'help_organization_name')" />');return false;
                                                        </xsl:attribute>
                                                        <img alt="Hjelp" src="{helpImg}" />
                                                    </a>
                                                </dt>
                                                <dd><input type="text" name="orgname" id="orgname" maxlength="254" class="pure-u-1 input-80" /></dd>
                                                <dt><label for="orgno">Organisasjonsnummer</label></dt>
                                                <dd><input type="text" name="orgno" maxlength="254" /></dd>
                                                <div class="input-part-1">
                                                    <dt>
                                                        <label for="street">
                                                            Gateadresse
                                                        </label>
                                                        <a href="javascript:void(0);" class="helpLink">
                                                            <xsl:attribute name="onclick">
                                                                alert('<xsl:value-of select="php:function('help_streetaddress')" />');return false;
                                                            </xsl:attribute>
                                                            <img alt="Hjelp" src="{helpImg}" />
                                                        </a>
                                                    </dt>
                                                    <dd>
                                                        <input id="address" onkeyup="javascript:get_address_search()" name="address" class="pure-u-1 input-50" type="text" /><br />
                                                        <div id="address_container" />
                                                    </dd>
                                                </div>
                                                <div class="input-part-2">
                                                    <dt>
                                                        <label for="number">Husnummer</label><br />
                                                    </dt>
                                                    <dd>
                                                        <input name="number" class="pure-u-1 input-5" type="text" />
                                                    </dd>
                                                </div>
                                                <div class="input-part-1">
                                                    <dt>
                                                        <label for="postzip">Postnummer</label><br />
                                                    </dt>
                                                    <dd>
                                                        <input name="postzip" class="pure-u-1 input-5" type="text" />
                                                    </dd>
                                                </div>
                                                <div class="input-part-2">
                                                    <dt>
                                                        <label for="postaddress">Poststed</label><br />
                                                    </dt>
                                                    <dd>
                                                        <input name="postaddress" class="pure-u-1 input-40" type="text" />
                                                    </dd>
                                                </div>
                                                <br /><br />
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
                                            <dd><input name="homepage" value="http://" class="pure-u-1 input-80" type="text" maxlength="254" /></dd><br /><br />
                                            <div style="overflow:hidden;">
                                                Kontaktperson for organisasjonen 
                                                <a href="javascript:void(0);" class="helpLink">
                                                    <xsl:attribute name="onclick">
                                                        alert('<xsl:value-of select="php:function('lang', 'help_contact_person')" />');return false;
                                                    </xsl:attribute>
                                                    <img alt="Hjelp" src="{helpImg}" />
                                                </a><br />
                                                <dt><label for="contact1_name">Navn (*)</label></dt>
                                                <dd><input name="org_contact1_name" id="org_contact1_name" type="text" class="pure-u-1 input-80" maxlength="254" /></dd>
                                                <dt><label for="contact1_phone">Telefon (*)</label></dt>
                                                <dd><input name="org_contact1_phone" id="org_contact1_phone" type="text" /></dd>
                                                <dt><label for="contact1_mail">E-post (*)</label></dt>
                                                <dd><input name="org_contact1_mail" id="org_contact1_mail" type="text" class="pure-u-1 input-50" /></dd>
                                                <dt><label for="contact2_mail">Gjenta e-post (*)</label></dt>
                                                <dd><input name="org_contact2_mail" id="org_contact2_mail" type="text" class="pure-u-1 input-50" /></dd>
                                            </div>
                                        </dl>
                                    </div>
                                </dt>
                                <br /><br />
                                <div class="form-buttons">
                                    <input type="submit" name="step_1">
                                        <xsl:attribute name="value">
                                            <xsl:value-of select="php:function('lang', 'next')" />
                                        </xsl:attribute>
                                    </input>
                                </div>                            
                            </dl>
                        </fieldset>
                    </div>
                </div>
            </form>
        </div>
    </div>
</xsl:template>
