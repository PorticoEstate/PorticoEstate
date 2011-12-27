  <!-- $Id$ -->
	<xsl:template name="alarm_form">
		<xsl:apply-templates select="alarm_data"/>
	</xsl:template>

	<!-- New template-->
	<xsl:template name="alarm_data">
		<table width="80%" cellpadding="2" cellspacing="2" align="center">
			<xsl:apply-templates select="header"/>
			<xsl:apply-templates select="values"/>
			<xsl:apply-templates select="alter_alarm"/>
			<xsl:call-template name="add_alarm"/>
		</table>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="header">
		<tr class="th">
			<td class="th_text" width="20%" align="left">
				<xsl:value-of select="lang_time"/>
			</td>
			<td class="th_text" width="30%" align="left">
				<xsl:value-of select="lang_text"/>
			</td>
			<td class="th_text" width="10%" align="left">
				<xsl:value-of select="lang_user"/>
			</td>
			<td class="th_text" width="10%" align="center">
				<xsl:value-of select="lang_enabled"/>
			</td>
			<td class="th_text" width="10%" align="center">
				<xsl:value-of select="lang_select"/>
			</td>
		</tr>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="values">
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
			<td align="left">
				<xsl:value-of select="time"/>
			</td>
			<td align="left">
				<pre>
					<xsl:value-of select="text"/>
				</pre>
			</td>
			<td align="left">
				<xsl:value-of select="user"/>
			</td>
			<td align="center">
				<xsl:value-of select="enabled"/>
			</td>
			<td align="center">
				<input type="checkbox" name="values[alarm][{alarm_id}]" value="" onMouseout="window.status='';return true;">
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
						<xsl:value-of select="lang_select_statustext"/>
						<xsl:text>'; return true;</xsl:text>
					</xsl:attribute>
				</input>
			</td>
		</tr>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="alter_alarm">
		<tr height="20">
			<td align="right" valign="bottom" colspan="5">
				<xsl:variable name="lang_enable">
					<xsl:value-of select="lang_enable"/>
				</xsl:variable>
				<input type="submit" name="values[enable_alarm]" value="{$lang_enable}" onMouseout="window.status='';return true;">
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
						<xsl:value-of select="lang_enable_statustext"/>
						<xsl:text>'; return true;</xsl:text>
					</xsl:attribute>
				</input>
				<xsl:variable name="lang_disable">
					<xsl:value-of select="lang_disable"/>
				</xsl:variable>
				<input type="submit" name="values[disable_alarm]" value="{$lang_disable}" onMouseout="window.status='';return true;">
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
						<xsl:value-of select="lang_disable_statustext"/>
						<xsl:text>'; return true;</xsl:text>
					</xsl:attribute>
				</input>
				<xsl:variable name="lang_delete">
					<xsl:value-of select="lang_delete"/>
				</xsl:variable>
				<input type="submit" name="values[delete_alarm]" value="{$lang_delete}" onMouseout="window.status='';return true;">
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
						<xsl:value-of select="lang_delete_statustext"/>
						<xsl:text>'; return true;</xsl:text>
					</xsl:attribute>
				</input>
			</td>
		</tr>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="add_alarm">
		<tr height="20">
			<td valign="top" align="right" colspan="5">
				<xsl:value-of select="lang_add_alarm"/>
				<xsl:text> : </xsl:text>
				<xsl:variable name="lang_day_statustext">
					<xsl:value-of select="lang_day_statustext"/>
				</xsl:variable>
				<select name="values[time][days]" class="forms" onMouseover="window.status='{$lang_day_statustext}'; return true;" onMouseout="window.status='';return true;">
					<option value="">0</option>
					<xsl:apply-templates select="day_list"/>
				</select>
				<xsl:value-of select="lang_day"/>
				<xsl:variable name="lang_hour_statustext">
					<xsl:value-of select="lang_hour_statustext"/>
				</xsl:variable>
				<select name="values[time][hours]" class="forms" onMouseover="window.status='{$lang_hour_statustext}'; return true;" onMouseout="window.status='';return true;">
					<option value="">0</option>
					<xsl:apply-templates select="hour_list"/>
				</select>
				<xsl:value-of select="lang_hour"/>
				<xsl:variable name="lang_minute_statustext">
					<xsl:value-of select="lang_minute_statustext"/>
				</xsl:variable>
				<select name="values[time][mins]" class="forms" onMouseover="window.status='{$lang_minute_statustext}'; return true;" onMouseout="window.status='';return true;">
					<option value="">0</option>
					<xsl:apply-templates select="minute_list"/>
				</select>
				<xsl:value-of select="lang_minute"/>
				<xsl:variable name="lang_user_statustext">
					<xsl:value-of select="lang_user_statustext"/>
				</xsl:variable>
				<select name="values[user_id]" class="forms" onMouseover="window.status='{$lang_user_statustext}'; return true;" onMouseout="window.status='';return true;">
					<option value="">
						<xsl:value-of select="lang_no_user"/>
					</option>
					<xsl:apply-templates select="user_list"/>
				</select>
				<xsl:value-of select="lang_user"/>
				<xsl:variable name="lang_add">
					<xsl:value-of select="lang_add"/>
				</xsl:variable>
				<input type="submit" name="values[add_alarm]" value="{$lang_add}" onMouseout="window.status='';return true;">
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
						<xsl:value-of select="lang_add_statustext"/>
						<xsl:text>'; return true;</xsl:text>
					</xsl:attribute>
				</input>
			</td>
		</tr>
	</xsl:template>

	<!-- day_list -->
	<xsl:template match="day_list">
		<xsl:variable name="id">
			<xsl:value-of select="id"/>
		</xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected">
					<xsl:value-of disable-output-escaping="yes" select="id"/>
				</option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}">
					<xsl:value-of disable-output-escaping="yes" select="id"/>
				</option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<!-- hour_list -->
	<xsl:template match="hour_list">
		<xsl:variable name="id">
			<xsl:value-of select="id"/>
		</xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected">
					<xsl:value-of disable-output-escaping="yes" select="id"/>
				</option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}">
					<xsl:value-of disable-output-escaping="yes" select="id"/>
				</option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>


	<!-- minute_list -->
	<xsl:template match="minute_list">
		<xsl:variable name="id">
			<xsl:value-of select="id"/>
		</xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected">
					<xsl:value-of disable-output-escaping="yes" select="id"/>
				</option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}">
					<xsl:value-of disable-output-escaping="yes" select="id"/>
				</option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>


	<!-- user_list is loaded separately -->
