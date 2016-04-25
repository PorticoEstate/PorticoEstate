<!-- $Id: support.xsl 4904 2010-02-24 13:32:35Z sigurd $ -->

<xsl:template match="compose_groups" xmlns:php="http://php.net/xsl">
	<form ENCTYPE="multipart/form-data" name="form" method="post" action="{form_action}">
		<table cellpadding="0" cellspacing="0" width="100%">
			<xsl:choose>
				<xsl:when test="msgbox_data != ''">
					<tr>
						<td align="left" colspan="2">
							<xsl:call-template name="msgbox"/>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
		</table>
		
		<div id="groups">
			<h2>
				<xsl:value-of select="php:function('lang', 'groups')" />
			</h2>
			<ul class="group_list">
				<xsl:apply-templates select="group_list" />
			</ul>
		</div>

		<h2>
			<xsl:value-of select="php:function('lang', 'Compose message')" />
		</h2>


		<table>
			<tr class="th">
				<td class="th_text" valign="top">
					<xsl:value-of select="php:function('lang', 'subject')" />
				</td>
				<td>
					<input type="text" name="values[subject]" value='{value_subject}'>
						<xsl:attribute name="size">
							<xsl:text>60</xsl:text>
						</xsl:attribute>
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'subject')" />
						</xsl:attribute>
					</input>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="php:function('lang', 'content')" />
				</td>
				<td>
					<textarea cols="60" rows="10" name="values[content]" wrap="virtual" onMouseout="window.status='';return true;">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'content')" />
						</xsl:attribute>
						<xsl:value-of select="value_content"/>		
					</textarea>
				</td>
			</tr>

			<tr height="50">
				<td>
					<xsl:variable name="lang_send">
						<xsl:value-of select="php:function('lang', 'send')" />
					</xsl:variable>
					<input type="submit" name="values[save]" value="{$lang_send}" title='{$lang_send}'>
					</input>
				</td>
			</tr>

		</table>
	</form>
</xsl:template>

<!-- BEGIN group_list -->
<xsl:template match="group_list">
	<li>
		<xsl:attribute name="class">
			<xsl:choose>
				<xsl:when test="position() mod 2 = 0">
					<xsl:text>row_off</xsl:text>
				</xsl:when>
				<xsl:otherwise>
					<xsl:text>row_on</xsl:text>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:attribute>
		<xsl:choose>
			<xsl:when test="i_am_admin = 1">
				<input type="checkbox" id="account_groups{account_id}" name="account_groups[]" value="{account_id}">
					<xsl:choose>
						<xsl:when test="selected = '1'">
							<xsl:attribute name="checked" value="checked" />
						</xsl:when>
					</xsl:choose>
				</input>
			</xsl:when>
			<xsl:otherwise>
				<input type="checkbox" readonly='true'>
				</input>
			</xsl:otherwise>
		</xsl:choose>

		<xsl:value-of select="account_lid"/>
	</li>
</xsl:template>

