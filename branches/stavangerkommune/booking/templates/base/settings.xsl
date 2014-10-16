<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">

        <xsl:call-template name="msgbox"/>
        <xsl:call-template name="yui_booking_i18n"/>

        <dl class="form">
            <dt class="heading">
                <xsl:value-of select="php:function('lang', 'Booking system settings')"/>
            </dt>
        </dl>

        <form action="" method="POST">

            <dl class="form">
                <dt>
                    <label for="field_user_can_delete_bookings">
                        <xsl:value-of select="php:function('lang', 'Frontend users can delete bookings/events')"/>
                    </label>
                </dt>
                <dd>
                    <select id="field_user_can_delete_bookings" name="config_data[user_can_delete_bookings]">
                        <option value="no">
                            <xsl:if test="config_data/user_can_delete_bookings='no'">
                                <xsl:attribute name="selected">checked</xsl:attribute>
                            </xsl:if>
                            <xsl:value-of select="php:function('lang', 'No')"/>
                        </option>
                        <option value="yes">
                            <xsl:if test="config_data/user_can_delete_bookings='yes'">
                                <xsl:attribute name="selected">checked</xsl:attribute>
                            </xsl:if>
                            <xsl:value-of select="php:function('lang', 'Yes')"/>
                        </option>
                    </select>
                </dd>
                <dd>
                    <xsl:value-of select="php:function('lang', 'Events is deleted from database')"/>
                </dd>
                <dd>
                    <select id="field_user_can_delete_events" name="config_data[user_can_delete_events]">
                        <option value="no">
                            <xsl:if test="config_data/user_can_delete_events='no'">
                                <xsl:attribute name="selected">checked</xsl:attribute>
                            </xsl:if>
                            <xsl:value-of select="php:function('lang', 'No')"/>
                        </option>
                        <option value="yes">
                            <xsl:if test="config_data/user_can_delete_events='yes'">
                                <xsl:attribute name="selected">checked</xsl:attribute>
                            </xsl:if>
                            <xsl:value-of select="php:function('lang', 'Yes')"/>
                        </option>
                    </select>
                </dd>
                <dt>
                    <label for="field_user_can_delete_allocations">
                        <xsl:value-of select="php:function('lang', 'Frontend users can delete allocations')"/>
                    </label>
                </dt>
                <dd>
                    <select id="field_user_can_delete_allocations" name="config_data[user_can_delete_allocations]">
                        <option value="no">
                            <xsl:if test="config_data/user_can_delete_allocations='no'">
                                <xsl:attribute name="selected">checked</xsl:attribute>
                            </xsl:if>
                            <xsl:value-of select="php:function('lang', 'No')"/>
                        </option>
                        <option value="yes">
                            <xsl:if test="config_data/user_can_delete_allocations='yes'">
                                <xsl:attribute name="selected">checked</xsl:attribute>
                            </xsl:if>
                            <xsl:value-of select="php:function('lang', 'Yes')"/>
                        </option>
                        <option value="never">
                            <xsl:if test="config_data/user_can_delete_allocations='never'">
                                <xsl:attribute name="selected">checked</xsl:attribute>
                            </xsl:if>
                            <xsl:value-of select="php:function('lang', 'No action')"/>
                        </option>
                    </select>
                </dd>
                <dt>
                    <label for="field_extra_schedule">
                        <xsl:value-of select="php:function('lang', 'Activate extra kalendar field on building')"/>
                    </label>
                </dt>
                <dd>
                    <select id="field_extra_schedule" name="config_data[extra_schedule]">
                        <option value="no">
                            <xsl:if test="config_data/extra_schedule='no'">
                                <xsl:attribute name="selected">checked</xsl:attribute>
                            </xsl:if>
                            <xsl:value-of select="php:function('lang', 'No')"/>
                        </option>
                        <option value="yes">
                            <xsl:if test="config_data/extra_schedule='yes'">
                                <xsl:attribute name="selected">checked</xsl:attribute>
                            </xsl:if>
                            <xsl:value-of select="php:function('lang', 'Yes')"/>
                        </option>
                    </select>
                </dd>

                <dt>
                    <label for="field_extra_schedule_ids">
                        <xsl:value-of select="php:function('lang', 'Ids that should be included in the calendar')"/>
                    </label>
                </dt>
                <dd>
                    <input id="field_extra_schedule_ids" type="text" name="config_data[extra_schedule_ids]">
                        <xsl:attribute name="value">
                            <xsl:value-of select="config_data/extra_schedule_ids"/>
                        </xsl:attribute>
                    </input>
                </dd>
                <dt class="heading">
                    <xsl:value-of
                            select="php:function('lang', 'Split mail if building contains swiming pools resources')"/>
                </dt>
                <dt>
                    <label for="field_split_pool">
                        <xsl:value-of select="php:function('lang', 'Split mail when building has swiming pool')"/>
                    </label>
                </dt>
                <dd>
                    <select id="field_split_pool" name="config_data[split_pool]">
                        <option value="no">
                            <xsl:if test="config_data/split_pool='no'">
                                <xsl:attribute name="selected">checked</xsl:attribute>
                            </xsl:if>
                            <xsl:value-of select="php:function('lang', 'No')"/>
                        </option>
                        <option value="yes">
                            <xsl:if test="config_data/split_pool='yes'">
                                <xsl:attribute name="selected">checked</xsl:attribute>
                            </xsl:if>
                            <xsl:value-of select="php:function('lang', 'Yes')"/>
                        </option>
                    </select>
                </dd>
                <dt>
                    <label for="field_split_pool_ids">
                        <xsl:value-of select="php:function('lang', 'activities that uses swimming pools')"/>
                    </label>
                </dt>
                <dd>
                    <input id="field_split_pool_ids" type="text" name="config_data[split_pool_ids]">
                        <xsl:attribute name="value">
                            <xsl:value-of select="config_data/split_pool_ids"/>
                        </xsl:attribute>
                    </input>
                </dd>
                <dt>
                    <label for="fieldsplit_pool2_ids">
                        <xsl:value-of select="php:function('lang', 'other activities')"/>
                    </label>
                </dt>
                <dd>
                    <input id="field_split_pool2_ids" type="text" name="config_data[split_pool2_ids]">
                        <xsl:attribute name="value">
                            <xsl:value-of select="config_data/split_pool2_ids"/>
                        </xsl:attribute>
                    </input>
                </dd>
                <dt>
                    <label for="fieldsplit_pool3_ids">
                        <xsl:value-of select="php:function('lang', 'activities that all should get except those in the next field.')"/>
                    </label>
                </dt>
                <dd>
                    <input id="field_split_pool3_ids" type="text" name="config_data[split_pool3_ids]">
                        <xsl:attribute name="value">
                            <xsl:value-of select="config_data/split_pool3_ids"/>
                        </xsl:attribute>
                    </input>
                </dd>
                <dt>
                    <label for="fieldsplit_pool4_ids">
                        <xsl:value-of select="php:function('lang', 'activities that never should get mail')"/>
                    </label>
                </dt>
                <dd>
                    <input id="field_split_pool4_ids" type="text" name="config_data[split_pool4_ids]">
                        <xsl:attribute name="value">
                            <xsl:value-of select="config_data/split_pool4_ids"/>
                        </xsl:attribute>
                    </input>
                </dd>
                <dt class="heading">
                    <xsl:value-of
                            select="php:function('lang', 'Who get cancelation mails')"/>
                </dt>
                <dt>
                    <label for="field_mail_users_season">
                        <xsl:value-of select="php:function('lang', 'Users of current season or users the last 300 days')"/>
                    </label>
                </dt>
                <dd>
                    <select id="field_mail_users_season" name="config_data[mail_users_season]">
                        <option value="no">
                            <xsl:if test="config_data/mail_users_season='no'">
                                <xsl:attribute name="selected">checked</xsl:attribute>
                            </xsl:if>
                            <xsl:value-of select="php:function('lang', 'Last 300 days')"/>
                        </option>
                        <option value="yes">
                            <xsl:if test="config_data/mail_users_season='yes'">
                                <xsl:attribute name="selected">checked</xsl:attribute>
                            </xsl:if>
                            <xsl:value-of select="php:function('lang', 'Season')"/>
                        </option>
                    </select>
                </dd>
                <dt class="heading">
                    <xsl:value-of select="php:function('lang', 'Email warnings')"/>
                </dt>
                <dt>
                    <label for="field_cancelation_email_addresses">
                        <xsl:value-of select="php:function('lang', 'Cancelation Email Addresses')"/>
                    </label>
                </dt>
                <dd>
                    <xsl:value-of
                            select="php:function('lang', 'One e-mail pr. line.')"/>
                </dd>
                <dd>
                    <textarea id="field_emails" class="full-width" name="config_data[emails]">
                        <xsl:value-of select="config_data/emails"/>
                    </textarea>
                </dd>

                <dt class="heading">
                    <xsl:value-of select="php:function('lang', 'Billing sequence numbers')"/>
                </dt>
                <dd>
                    <xsl:value-of
                            select="php:function('lang', 'Do not change these values unless you know what they are.')"/>
                </dd>
                <dt>
                    <label for="field_internal_billing_sequence_number">
                        <xsl:value-of select="php:function('lang', 'Current internal billing sequence number')"/>
                    </label>
                </dt>
                <dd>
                    <input type="number" name="billing[internal]">
                        <xsl:attribute name="value">
                            <xsl:value-of select="billing/internal"/>
                        </xsl:attribute>
                    </input>
                </dd>
            </dl>

            <div class="form-buttons">
                <input type="submit">
                    <xsl:attribute name="value">
                        <xsl:value-of select="php:function('lang', 'Save')"/>
                    </xsl:attribute>
                </input>
            </div>
        </form>
    </div>
</xsl:template>
