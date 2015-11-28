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
                            <a href="javascript:void(0);">
                                <xsl:attribute name="onclick">
                                    alert('<xsl:value-of select="php:function('lang', 'help_new_activity_title')" />');return false;
                                </xsl:attribute>
                                <img alt="Hjelp" src="{helpImg}" />
                            </a>
                        </label>
                    </dt>
                    <dd>
                        <input type="text" name="title" id="title" size="83" maxlength="254">
                            <xsl:attribute name="value">
                                
                            </xsl:attribute>
                        </input>
                    </dd>
                    <dt>
                        <label for="org_description">
                            <xsl:value-of select="php:function('lang', 'description')" /> (*) 
                            <a href="javascript:void(0);">
                                <xsl:attribute name="onclick">
                                    alert('<xsl:value-of select="php:function('lang', 'help_new_activity_description')" />');return false;
                                </xsl:attribute>
                                <img alt="Hjelp" src="{helpImg}" />
                            </a>
                        </label>
                    </dt>
                    <dd>
                        <textarea cols="80" rows="4" name="description" id="description">
                            
                        </textarea>
                    </dd>
                    <dt>
                        <label for="category">
                            <xsl:value-of select="php:function('lang', 'category')" /> (*) 
                            <a href="javascript:void(0);">
                                <xsl:attribute name="onclick">
                                    alert('<xsl:value-of select="php:function('lang', 'help_new_activity_category')" />');return false;
                                </xsl:attribute>
                                <img alt="Hjelp" src="{helpImg}" />
                            </a>
                        </label>
                    </dt>
                    <dd>
                        <select name="category" id="category">
                            <option value="0">Ingen kategori valgt</option>
                            <xsl:for-each select="categories">
                                <option value=""></option>
                            </xsl:for-each>
                        </select>
                    </dd>
                </fieldset>
                <fieldset id="hvem">
                    <legend>For hvem</legend>
                    <dt>
                        <label for="target">
                            <xsl:value-of select="php:function('lang', 'target')" /> (*) 
                            <a href="javascript:void">
                                <xsl:attribute name="onclick">
                                    alert('<xsl:value-of select="php:function('lang', 'help_new_activity_target')" />');return false;
                                </xsl:attribute>
                                <img alt="Hjelp" src="{helpImg}" />
                            </a>
                        </label>
                    </dt>
                    <dd>
                        <xsl:for-each select="targets">
                            <input name="target[]" type="checkbox">
                                <xsl:attribute name="value">
                                    
                                </xsl:attribute>
                            </input>
                            <br />
                        </xsl:for-each>
                    </dd>
                    <dt>
                        <input type="checkbox" name="special_adaptation" id="special_adaptation" />
                        <label for="special_adaptation">
                            <xsl:value-of select="php:function('lang', 'special_adaptation')" />
                            <a href="javascript:void(0);">
                                <xsl:attribute name="onclick">
                                    alert('<xsl:value-of select="php:function('lang', 'help_new_activity_spec_adapt')" />');return false;
                                </xsl:attribute>
                            </a>
                        </label>
                    </dt>
                </fieldset>
                <fieldset title="hvor">
                    <legend>Hvor og når</legend>
                    <dt>
                        <br />
                        <label for="arena">
                            <xsl:value-of select="php:function('lang', 'location')" /> (*) 
                            <a href="javascript:void(0);">
                                <xsl:attribute name="onclick">
                                    alert('<xsl:value-of select="php:function('lang', 'help_edit_activity_location')" />');return false;
                                </xsl:attribute>
                                <img alt="Hjelp" src="{helpImg}" />
                            </a>
                        </label>
                        <br />
                    </dt>
                    <dd>
                        <select name="internal_arena_id" id="internal_arena_id" style="width:200px">
                            <option value="0">Lokale ikke valgt</option>
                            <optgroup>
                                <xsl:attribute name="label">
                                    <xsl:value-of select="php:function('lang', 'buildings')" />
                                </xsl:attribute>
                                <xsl:for-each select="buildings">
                                    
                                </xsl:for-each>
                            </optgroup>
                            <optgroup>
                                <xsl:attribute name="label">
                                    <xsl:value-fo select="php.function('lang', 'external_arena')" />
                                </xsl:attribute>
                            </optgroup>
                        </select>
                        <br />
                    </dd>
                    <dt>
                        <label for="district">
                            <xsl:value-of select="php:function('lang', 'district')" /> (*) 
                            <a href="javascript:void(0);">
                                <xsl:attribute name="onclick">
                                    alert('<xsl:value-of select="php:function('lang', 'help_new_activity_district')" />');return false;
                                </xsl:attribute>
                                <img alt="Hjelp" src="{helpImg}" />
                            </a>
                        </label>
                    </dt>
                    <dd>
                        <xsl:value-of select="districts">
                            <input name="district" type="radio">
                                <xsl:attribute name="value">
                                    
                                </xsl:attribute>
                            </input>
                        </xsl:value-of>
                    </dd>
                    <dt>
                        <label for="time">
                            <xsl:value-of select="php:function('lang', 'time')" /> (*) 
                            <a href="javascript:void(0);">
                                <xsl:attribute name="onclick">
                                    alert('<xsl:value-of select="php:function('lang', 'help_new_activity_time')" />');return false;
                                </xsl:attribute>
                                <img alt="Hjelp" src="{helpImg}" />
                            </a>
                        </label>
                    </dt>
                    <dd>
                        <input type="text" name="time" id="time" size="80" maxlength="254">
                            <xsl:attribute name="value">
                                
                            </xsl:attribute>
                        </input>
                    </dd>
                </fieldset>
                <fieldset id="arr">
                    <legend>Kontaktperson</legend>
                    <br />
                    Kontaktperson for aktiviteten 
                    <a href="javascript:void(0);">
                        <xsl:attribute name="onclick">
                            alert('<xsl:value-of select="php:function('lang', 'help_new_activity_contact_person')" />');return false;
                        </xsl:attribute>
                        <img alt="Hjelp" src="{helpImg}" />
                    </a>
                    <br />
                    <dt><label for="contact_name">Navn (*)</label></dt>
                    <dd>
                        <input type="text" name="contact_name" id="contact_name" size="80">
                            <xsl:attribute name="value"></xsl:attribute>
                        </input>
                    </dd>
                    <dt><label for="contact_phone">Telefon (*)</label></dt>
                    <dd>
                        <input type="text" name="contact_phone" id="contact_name">
                            <xsl:attibute name="value"></xsl:attibute>
                        </input>
                    </dd>
                    <dt><label for="contact_mail">E-post (*)</label></dt>
                    <dd>
                        <input type="text" name="contact_mail" id="contact_mail" size="50">
                            <xsl:attribute name="value"></xsl:attribute>
                        </input>
                    </dd>
                    <dt><label for="contact_mail2">Gjenta e-post (*)</label></dt>
                    <dd>
                        <input type="text" name="contact_mail2" id="contact_mail2" size="50">
                            <xsl:attribute name="value"></xsl:attribute>
                        </input>
                    </dd>                    
                </fieldset>
                <fieldset>
                    <br />
                    <dt>
                        <label for="office">
                            Hvilket kulturkontor skal motta registreringen (*) 
                            <a href="javascript:void(0);">
                                <xsl:attribute name="onclick">
                                    alert('<xsl:value-of select="php:function('lang', 'help_new_activity_office')" />');return false;
                                </xsl:attribute>
                                <img alt="Hjelp" src="{helpImg}" />
                            </a>
                        </label>
                    </dt>
                </fieldset>
            </dl>
        </form>
    </div>
    <script type="text/javascript">
        var org_id = "";
        <xsl:if test="">
            var group_id = "";
            var availableGroupsURL = phpGWLink('activitycalendarfrontend/', {menuaction: 'activitycalendarfrontend.uiactivity.get_organization_groups', orgid: org_id, groupid: group_id}, true);
        </xsl:if>
        <xsl:if test="">
            var availableGroupsURL = phpGWLink('activitycalendarfrontend/', {menuaction: 'activitycalendarfrontend.uiactivity.get_organization_groups', orgid: org_id}, true);
        </xsl:if>
    </script>
</xsl:template>
