  <!-- $Id$ -->
	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="edit_template">
				<xsl:apply-templates select="edit_template"/>
			</xsl:when>
			<xsl:when test="edit_hour">
				<xsl:apply-templates select="edit_hour"/>
			</xsl:when>
			<xsl:when test="list_template_hour">
				<xsl:apply-templates select="list_template_hour"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="list"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="list">
		<xsl:apply-templates select="menu"/>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<tr>
				<td>
					<xsl:call-template name="chapter_filter"/>
				</td>
				<td>
					<xsl:call-template name="user_id_filter"/>
				</td>
				<td align="right">
					<xsl:call-template name="search_field"/>
				</td>
			</tr>
			<tr>
				<td colspan="3" width="100%">
					<xsl:call-template name="nextmatchs"/>
				</td>
			</tr>
		</table>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:apply-templates select="table_header_template"/>
			<xsl:choose>
				<xsl:when test="values_template[template_id]!=''">
					<xsl:apply-templates select="values_template"/>
				</xsl:when>
			</xsl:choose>
		</table>
		<table align="left">
			<xsl:choose>
				<xsl:when test="lookup !=''">
					<xsl:apply-templates select="table_done"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:apply-templates select="table_add"/>
				</xsl:otherwise>
			</xsl:choose>
		</table>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="table_add">
		<tr>
			<td height="50">
				<xsl:variable name="add_action">
					<xsl:value-of select="add_action"/>
				</xsl:variable>
				<xsl:variable name="lang_add">
					<xsl:value-of select="lang_add"/>
				</xsl:variable>
				<form method="post" action="{$add_action}">
					<input type="submit" name="add" value="{$lang_add}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_add_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</form>
			</td>
		</tr>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="table_done">
		<tr>
			<td height="50">
				<xsl:variable name="done_action">
					<xsl:value-of select="done_action"/>
				</xsl:variable>
				<xsl:variable name="lang_done">
					<xsl:value-of select="lang_done"/>
				</xsl:variable>
				<form method="post" action="{$done_action}">
					<input type="submit" name="done" value="{$lang_done}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_done_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</form>
			</td>
		</tr>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="table_header_template">
		<xsl:variable name="sort_name">
			<xsl:value-of select="sort_name"/>
		</xsl:variable>
		<xsl:variable name="sort_template_id">
			<xsl:value-of select="sort_template_id"/>
		</xsl:variable>
		<tr class="th">
			<td class="th_text" width="10%" align="right">
				<a href="{$sort_template_id}">
					<xsl:value-of select="lang_template_id"/>
				</a>
			</td>
			<td class="th_text" width="10%" align="right">
				<a href="{$sort_name}">
					<xsl:value-of select="lang_name"/>
				</a>
			</td>
			<td class="th_text" width="30%" align="left">
				<xsl:value-of select="lang_descr"/>
			</td>
			<td class="th_text" width="20%" align="left">
				<xsl:value-of select="lang_chapter"/>
			</td>
			<td class="th_text" width="5%" align="right">
				<xsl:value-of select="lang_owner"/>
			</td>
			<td class="th_text" width="10%" align="right">
				<xsl:value-of select="lang_entry_date"/>
			</td>
			<xsl:choose>
				<xsl:when test="//lookup !=''">
					<td class="th_text" width="5%" align="center">
						<xsl:value-of select="lang_select"/>
					</td>
				</xsl:when>
				<xsl:otherwise>
					<td class="th_text" width="5%" align="center">
						<xsl:value-of select="lang_view"/>
					</td>
					<td class="th_text" width="5%" align="center">
						<xsl:value-of select="lang_edit"/>
					</td>
					<td class="th_text" width="5%" align="center">
						<xsl:value-of select="lang_delete"/>
					</td>
				</xsl:otherwise>
			</xsl:choose>
		</tr>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="values_template">
		<xsl:variable name="lang_view_statustext">
			<xsl:value-of select="lang_view_statustext"/>
		</xsl:variable>
		<xsl:variable name="lang_edit_statustext">
			<xsl:value-of select="lang_edit_statustext"/>
		</xsl:variable>
		<xsl:variable name="lang_delete_statustext">
			<xsl:value-of select="lang_delete_statustext"/>
		</xsl:variable>
		<tr>
			<xsl:attribute name="class">
				<xsl:choose>
					<xsl:when test="@class">
						<xsl:value-of select="@class"/>
					</xsl:when>
					<xsl:when test="position() mod 2 = 0">
						<xsl:text>row_off</xsl:text>
					</xsl:when>
					<xsl:otherwise>
						<xsl:text>row_on</xsl:text>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:attribute>
			<td align="right">
				<xsl:value-of select="template_id"/>
			</td>
			<td align="left">
				<xsl:value-of select="name"/>
			</td>
			<td align="left">
				<xsl:value-of select="descr"/>
			</td>
			<td align="left">
				<xsl:value-of select="chapter"/>
			</td>
			<td align="left">
				<xsl:value-of select="owner"/>
			</td>
			<td align="right">
				<xsl:value-of select="entry_date"/>
			</td>
			<xsl:choose>
				<xsl:when test="//lookup !=''">
					<xsl:variable name="form_action_select">
						<xsl:value-of select="form_action_select"/>
					</xsl:variable>
					<form method="post" action="{$form_action_select}">
						<td valign="top">
							<input type="hidden" name="template_id" value="{template_id}"/>
							<input type="hidden" name="workorder_id" value="{workorder_id}"/>
							<xsl:variable name="lang_select">
								<xsl:value-of select="lang_select"/>
							</xsl:variable>
							<input type="submit" name="select" value="{$lang_select}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_select_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</form>
				</xsl:when>
				<xsl:otherwise>
					<td align="center">
						<xsl:variable name="link_view">
							<xsl:value-of select="link_view"/>
						</xsl:variable>
						<a href="{$link_view}" onMouseover="window.status='{$lang_view_statustext}';return true;" onMouseout="window.status='';return true;">
							<xsl:value-of select="text_view"/>
						</a>
					</td>
					<td align="center">
						<xsl:variable name="link_edit">
							<xsl:value-of select="link_edit"/>
						</xsl:variable>
						<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_statustext}';return true;" onMouseout="window.status='';return true;">
							<xsl:value-of select="text_edit"/>
						</a>
					</td>
					<td align="center">
						<xsl:variable name="link_delete">
							<xsl:value-of select="link_delete"/>
						</xsl:variable>
						<a href="{$link_delete}" onMouseover="window.status='{$lang_delete_statustext}';return true;" onMouseout="window.status='';return true;">
							<xsl:value-of select="text_delete"/>
						</a>
					</td>
				</xsl:otherwise>
			</xsl:choose>
		</tr>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="list_template_hour">
		<xsl:apply-templates select="menu"/>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:choose>
				<xsl:when test="msgbox_data != ''">
					<tr>
						<td align="left" colspan="3">
							<xsl:call-template name="msgbox"/>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
		</table>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<tr>
				<td align="right">
					<xsl:call-template name="search_field"/>
				</td>
			</tr>
			<tr>
				<td colspan="3" width="100%">
					<xsl:call-template name="nextmatchs"/>
				</td>
			</tr>
		</table>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:apply-templates select="table_header_template_hour"/>
			<xsl:choose>
				<xsl:when test="values_template_hour[counter]!=''">
					<xsl:apply-templates select="values_template_hour"/>
				</xsl:when>
			</xsl:choose>
		</table>
		<hr noshade="noshade" width="100%" align="center" size="1"/>
		<table align="left">
			<xsl:apply-templates select="table_add"/>
			<xsl:apply-templates select="table_done"/>
		</table>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="table_header_template_hour">
		<xsl:variable name="sort_billperae">
			<xsl:value-of select="sort_billperae"/>
		</xsl:variable>
		<xsl:variable name="sort_building_part">
			<xsl:value-of select="sort_building_part"/>
		</xsl:variable>
		<tr class="th">
			<td class="th_text" width="5%" align="right">
				<xsl:value-of select="lang_record"/>
			</td>
			<td class="th_text" width="10%" align="right">
				<a href="{$sort_building_part}">
					<xsl:value-of select="lang_building_part"/>
				</a>
			</td>
			<td class="th_text" width="10%" align="right">
				<xsl:value-of select="lang_code"/>
			</td>
			<td class="th_text" width="30%" align="left">
				<xsl:value-of select="lang_descr"/>
			</td>
			<td class="th_text" width="5%" align="right">
				<xsl:value-of select="lang_unit"/>
			</td>
			<td class="th_text" width="10%" align="right">
				<a href="{$sort_billperae}">
					<xsl:value-of select="lang_billperae"/>
				</a>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_edit"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_delete"/>
			</td>
		</tr>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="values_template_hour">
		<xsl:variable name="lang_edit_statustext">
			<xsl:value-of select="lang_edit_statustext"/>
		</xsl:variable>
		<xsl:variable name="lang_delete_statustext">
			<xsl:value-of select="lang_delete_statustext"/>
		</xsl:variable>
		<xsl:choose>
			<xsl:when test="new_grouping=1">
				<tr>
					<td class="th_text" align="center" colspan="10" width="100%">
						<xsl:value-of select="grouping_descr"/>
					</td>
				</tr>
			</xsl:when>
		</xsl:choose>
		<tr>
			<xsl:attribute name="class">
				<xsl:choose>
					<xsl:when test="@class">
						<xsl:value-of select="@class"/>
					</xsl:when>
					<xsl:when test="position() mod 2 = 0">
						<xsl:text>row_off</xsl:text>
					</xsl:when>
					<xsl:otherwise>
						<xsl:text>row_on</xsl:text>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:attribute>
			<td align="right">
				<xsl:value-of select="record"/>
			</td>
			<td align="right">
				<xsl:value-of select="building_part"/>
			</td>
			<td align="left">
				<xsl:value-of select="code"/>
			</td>
			<td align="left">
				<xsl:value-of select="hours_descr"/>
				<br/>
				<xsl:value-of select="remark"/>
			</td>
			<td align="left">
				<xsl:value-of select="unit"/>
			</td>
			<td align="right">
				<xsl:value-of select="billperae"/>
			</td>
			<td align="center">
				<xsl:variable name="link_edit">
					<xsl:value-of select="link_edit"/>
				</xsl:variable>
				<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_statustext}';return true;" onMouseout="window.status='';return true;">
					<xsl:value-of select="text_edit"/>
				</a>
			</td>
			<td align="center">
				<xsl:variable name="link_delete">
					<xsl:value-of select="link_delete"/>
				</xsl:variable>
				<a href="{$link_delete}" onMouseover="window.status='{$lang_delete_statustext}';return true;" onMouseout="window.status='';return true;">
					<xsl:value-of select="text_delete"/>
				</a>
			</td>
		</tr>
	</xsl:template>

	<!-- add / edit  -->
	<xsl:template match="edit_template">
            <script type="text/javascript">
			self.name="first_Window";
			<xsl:value-of select="lookup_functions"/>
            </script>
            <div id="tab-content">
                <xsl:value-of disable-output-escaping="yes" select="tabs"/>
                <div class="yui-content">
                    <div id="general">
                        <div align="left">
                            <dl>
				<xsl:choose>
					<xsl:when test="msgbox_data != ''">
							<dt>
								<xsl:call-template name="msgbox"/>
							</dt>
					</xsl:when>
				</xsl:choose>
                            </dl>
				<xsl:variable name="form_action">
					<xsl:value-of select="form_action"/>
				</xsl:variable>
				<form method="post" class="pure-form pure-form-aligned" name="form" action="{$form_action}">
					<xsl:choose>
						<xsl:when test="value_template_id !=''">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_template_id"/>
								</label>
									<xsl:value-of select="value_template_id"/>
							</div>
						</xsl:when>
					</xsl:choose>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_chapter"/>
						</label>
							<xsl:call-template name="chapter_select"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_name"/>
						</label>
							<input type="text" name="values[name]" value="{value_name}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_name_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_descr"/>
						</label>
							<textarea cols="60" rows="4" name="values[descr]" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_descr_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
								<xsl:value-of select="value_descr"/>
							</textarea>
					</div>
					<div class="pure-control-group">
							<xsl:variable name="lang_save">
								<xsl:value-of select="lang_save"/>
							</xsl:variable>
							<input type="submit" class="pure-button pure-button-primary" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_save_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
					</div>
				</form>
				<xsl:choose>
					<xsl:when test="value_template_id !=''">
						<div class="pure-control-group">
								<xsl:variable name="add_action">
									<xsl:value-of select="add_action"/>
								</xsl:variable>
								<xsl:variable name="lang_add">
									<xsl:value-of select="lang_add"/>
								</xsl:variable>
								<form method="post" action="{$add_action}">
									<input type="submit" class="pure-button pure-button-primary" name="add" value="{$lang_add}" onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_add_statustext"/>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
									</input>
								</form>
                                                </div>
					</xsl:when>
				</xsl:choose>
				<div class="pure-control-group">
						<xsl:variable name="done_action">
							<xsl:value-of select="done_action"/>
						</xsl:variable>
						<xsl:variable name="lang_done">
							<xsl:value-of select="lang_done"/>
						</xsl:variable>
						<form method="post" action="{$done_action}">
							<input type="submit" class="pure-button pure-button-primary" name="done" value="{$lang_done}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_done_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</form>
				</div>
                        </div>
                    </div>
                </div>
            </div>    
	</xsl:template>

	<!-- New template-->
	<xsl:template xmlns:php="http://php.net/xsl" match="edit_hour">
		<script type="text/javascript">
                        self.name="first_Window";
			<xsl:value-of select="lookup_functions"/>
			function ns3420_lookup()
			{
				TINY.box.show({iframe:'<xsl:value-of select="ns3420_link"/>', boxid:"frameless",width:750,height:450,fixed:false,maskid:"darkmask",maskopacity:40, mask:true, animate:true, close: true});
			}
		</script>
                <div id="tab-content">
                    <xsl:value-of disable-output-escaping="yes" select="tabs"/>
                    <div id="general">
                        <div align="left">
                                    <dl>
                                        <xsl:choose>
                                                <xsl:when test="msgbox_data != ''">
                                                                <dt>
                                                                        <xsl:call-template name="msgbox"/>
                                                                </dt>
                                                </xsl:when>
                                        </xsl:choose>
                                    </dl>
                                        <xsl:variable name="form_action">
                                                <xsl:value-of select="form_action"/>
                                        </xsl:variable>
                                        <form method="post" class="pure-form pure-form-aligned" name="form" action="{$form_action}">
                                                <xsl:choose>
                                                        <xsl:when test="value_hour_id !=''">
                                                                <xsl:choose>
                                                                        <xsl:when test="value_activity_num =''">
                                                                                <div class="pure-control-group">
                                                                                        <label>
                                                                                                <xsl:value-of select="lang_copy_hour"/>
                                                                                        </label>
                                                                                                <input type="checkbox" name="values[copy_hour]" value="True" onMouseout="window.status='';return true;">
                                                                                                        <xsl:attribute name="onMouseover">
                                                                                                                <xsl:text>window.status='</xsl:text>
                                                                                                                <xsl:value-of select="lang_copy_hour_statustext"/>
                                                                                                                <xsl:text>'; return true;</xsl:text>
                                                                                                        </xsl:attribute>
                                                                                                </input>
                                                                                </div>
                                                                        </xsl:when>
                                                                </xsl:choose>
                                                                <div class="pure-control-group">
                                                                        <label>
                                                                                <xsl:value-of select="lang_hour_id"/>
                                                                        </label>
                                                                                <xsl:value-of select="value_hour_id"/>
                                                                </div>
                                                        </xsl:when>
                                                </xsl:choose>
                                                <xsl:choose>
                                                        <xsl:when test="value_activity_num !=''">
                                                                <div class="pure-control-group">
                                                                        <label>
                                                                                <xsl:value-of select="lang_activity_num"/>
                                                                        </label>
                                                                                <xsl:value-of select="value_activity_num"/>
                                                                </div>
                                                        </xsl:when>
                                                </xsl:choose>
                                                <div class="pure-control-group">
                                                        <label>
                                                                <xsl:value-of select="lang_template"/>
                                                        </label>
                                                                <xsl:value-of select="value_template_name"/>
                                                                <xsl:text> [ </xsl:text>
                                                                <xsl:value-of select="value_template_id"/>
                                                                <xsl:text> ]</xsl:text>
                                                </div>
                                                <xsl:choose>
                                                        <xsl:when test="value_activity_num=''">
                                                                <div class="pure-control-group">
                                                                        <label>
                                                                                <xsl:value-of select="lang_chapter"/>
                                                                        </label>
                                                                                <xsl:call-template name="chapter_select"/>
                                                                </div>
                                                                <div class="pure-control-group">
                                                                        <label>
                                                                                <xsl:value-of select="lang_grouping"/>
                                                                        </label>
                                                                                <xsl:call-template name="grouping_select"/>
                                                                </div>
                                                                <div class="pure-control-group">
                                                                        <label>
                                                                                <xsl:value-of select="lang_new_grouping"/>
                                                                        </label>
                                                                                <input type="text" name="values[new_grouping]" value="{value_new_grouping}" onMouseout="window.status='';return true;">
                                                                                        <xsl:attribute name="onMouseover">
                                                                                                <xsl:text>window.status='</xsl:text>
                                                                                                <xsl:value-of select="lang_new_grouping_statustext"/>
                                                                                                <xsl:text>'; return true;</xsl:text>
                                                                                        </xsl:attribute>
                                                                                </input>
                                                                </div>
                                                        </xsl:when>
                                                </xsl:choose>
                                                <div class="pure-control-group">
                                                        <label>
                                                                <xsl:value-of select="php:function('lang', 'building part')"/>
                                                        </label>
                                                                <select name="values[building_part_id]">
                                                                        <xsl:attribute name="title">
                                                                                <xsl:value-of select="php:function('lang', 'select building part')"/>
                                                                        </xsl:attribute>
                                                                        <option value="0">
                                                                                <xsl:value-of select="php:function('lang', 'select building part')"/>
                                                                        </option>
                                                                        <xsl:apply-templates select="building_part_list/options"/>
                                                                </select>
                                                </div>
                                                <xsl:choose>
                                                        <xsl:when test="value_activity_num !=''">
                                                                <div class="pure-control-group">
                                                                        <label>
                                                                                <xsl:value-of select="lang_ns3420"/>
                                                                        </label>
                                                                                <xsl:value-of select="value_ns3420_id"/>
                                                                                <input type="hidden" name="ns3420_id" value="{value_ns3420_id}"/>
                                                                </div>
                                                                <div class="pure-control-group">
                                                                        <label>
                                                                                <xsl:value-of select="lang_descr"/>
                                                                        </label>
                                                                                <textarea cols="60" rows="4" name="values[descr]" onMouseout="window.status='';return true;">
                                                                                        <xsl:attribute name="onMouseover">
                                                                                                <xsl:text>window.status='</xsl:text>
                                                                                                <xsl:value-of select="lang_descr_statustext"/>
                                                                                                <xsl:text>'; return true;</xsl:text>
                                                                                        </xsl:attribute>
                                                                                        <xsl:value-of select="value_descr"/>
                                                                                </textarea>
                                                                </div>
                                                        </xsl:when>
                                                        <xsl:otherwise>
                                                                <div class="pure-control-group">
                                                                        <label>
                                                                                <a href="javascript:ns3420_lookup()" onMouseover="window.status='{lang_ns3420_statustext}';return true;" onMouseout="window.status='';return true;">
                                                                                        <xsl:value-of select="lang_ns3420"/>
                                                                                </a>
                                                                        </label>
                                                                                <input type="text" name="ns3420_id" value="{value_ns3420_id}" onClick="ns3420_lookup();" readonly="readonly">
                                                                                        <xsl:attribute name="onMouseover">
                                                                                                <xsl:text>window.status='</xsl:text>
                                                                                                <xsl:value-of select="lang_ns3420_statustext"/>
                                                                                                <xsl:text>'; return true;</xsl:text>
                                                                                        </xsl:attribute>
                                                                                </input>
                                                                </div>
                                                                <div class="pure-control-group">
                                                                        <label></label>
                                                                        <div style="display: inline-block; vertical-align: top;">
                                                                                <textarea cols="40" rows="4" name="ns3420_descr" onMouseout="window.status='';return true;">
                                                                                        <xsl:attribute name="onMouseover">
                                                                                                <xsl:text>window.status='</xsl:text>
                                                                                                <xsl:value-of select="lang_descr_statustext"/>
                                                                                                <xsl:text>'; return true;</xsl:text>
                                                                                        </xsl:attribute>
                                                                                        <xsl:value-of select="value_descr"/>
                                                                                </textarea>
                                                                        </div>
                                                                </div>
                                                                <div class="pure-control-group">
                                                                        <label>
                                                                                <xsl:value-of select="lang_tolerance"/>
                                                                        </label>
                                                                                <xsl:call-template name="tolerance_select"/>
                                                                </div>
                                                        </xsl:otherwise>
                                                </xsl:choose>
                                                <div class="pure-control-group">
                                                        <label>
                                                                <xsl:value-of select="lang_unit"/>
                                                        </label>
                                                                <xsl:call-template name="unit_select"/>
                                                </div>
                                                <div class="pure-control-group">
                                                        <label>
                                                                <xsl:value-of select="lang_dim_d"/>
                                                        </label>
                                                                <xsl:call-template name="dim_d_select"/>
                                                </div>
                                                <div class="pure-control-group">
                                                        <label>
                                                                <xsl:value-of select="lang_quantity"/>
                                                        </label>
                                                                <input type="text" name="values[quantity]" value="{value_quantity}" onMouseout="window.status='';return true;">
                                                                        <xsl:attribute name="onMouseover">
                                                                                <xsl:text>window.status='</xsl:text>
                                                                                <xsl:value-of select="lang_quantity_statustext"/>
                                                                                <xsl:text>'; return true;</xsl:text>
                                                                        </xsl:attribute>
                                                                </input>
                                                </div>
                                                <div class="pure-control-group">
                                                        <label>
                                                                <xsl:value-of select="lang_billperae"/>
                                                        </label>
                                                                <input type="text" name="values[billperae]" value="{value_billperae}" onMouseout="window.status='';return true;"><xsl:attribute name="onMouseover"><xsl:text>window.status='</xsl:text><xsl:value-of select="lang_billperae_statustext"/><xsl:text>'; return true;</xsl:text></xsl:attribute></input><xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]

                                                </div>
                                                <div class="pure-control-group">
                                                        <label>
                                                                <xsl:value-of select="lang_total_cost"/>
                                                        </label>
                                                        <xsl:value-of select="value_total_cost"/><xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
                                                        
                                                </div>
                                                <div class="pure-control-group">
                                                        <label>
                                                                <xsl:value-of select="lang_remark"/>
                                                        </label>
                                                                <textarea cols="60" rows="4" name="values[remark]" onMouseout="window.status='';return true;">
                                                                        <xsl:attribute name="onMouseover">
                                                                                <xsl:text>window.status='</xsl:text>
                                                                                <xsl:value-of select="lang_remark_statustext"/>
                                                                                <xsl:text>'; return true;</xsl:text>
                                                                        </xsl:attribute>
                                                                        <xsl:value-of select="value_remark"/>
                                                                </textarea>
                                                </div>
                                                <div class="pure-control-group">
                                                                <xsl:variable name="lang_save">
                                                                        <xsl:value-of select="lang_save"/>
                                                                </xsl:variable>
                                                                <input type="submit" class="pure-button pure-button-primary" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
                                                                        <xsl:attribute name="onMouseover">
                                                                                <xsl:text>window.status='</xsl:text>
                                                                                <xsl:value-of select="lang_save_statustext"/>
                                                                                <xsl:text>'; return true;</xsl:text>
                                                                        </xsl:attribute>
                                                                </input>
                                                </div>
                                        </form>
                                        <div class="pure-control-group">
                                                        <xsl:variable name="done_action">
                                                                <xsl:value-of select="done_action"/>
                                                        </xsl:variable>
                                                        <xsl:variable name="lang_done">
                                                                <xsl:value-of select="lang_done"/>
                                                        </xsl:variable>
                                                        <form method="post" action="{$done_action}">
                                                                <input type="submit" class="pure-button pure-button-primary" name="done" value="{$lang_done}" onMouseout="window.status='';return true;">
                                                                        <xsl:attribute name="onMouseover">
                                                                                <xsl:text>window.status='</xsl:text>
                                                                                <xsl:value-of select="lang_done_statustext"/>
                                                                                <xsl:text>'; return true;</xsl:text>
                                                                        </xsl:attribute>
                                                                </input>
                                                        </form>
                                        </div>
                        </div>
                    </div>
                </div>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="options">
		<option value="{id}">
			<xsl:if test="selected != 0">
				<xsl:attribute name="selected" value="selected"/>
			</xsl:if>
			<xsl:value-of disable-output-escaping="yes" select="name"/>
		</option>
	</xsl:template>
