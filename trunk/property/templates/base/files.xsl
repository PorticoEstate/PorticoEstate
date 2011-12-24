<!-- $Id$ -->
<xsl:template xmlns:php="http://php.net/xsl" name="file_list">
	<tr>
		<td width="19%" align="left" valign="top">
			<xsl:value-of select="php:function('lang', 'files')"></xsl:value-of>
		</td>
		<td>
			<table>
				<tr class="th">
					<td class="th_text" width="85%" align="left">
						<xsl:value-of select="php:function('lang', 'filename')"></xsl:value-of>
					</td>
					<td class="th_text" width="15%" align="center">
						<xsl:choose>
							<xsl:when test="//lang_file_action!=''">
								<xsl:value-of select="lang_file_action"></xsl:value-of>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="php:function('lang', 'Delete file')"></xsl:value-of>
							</xsl:otherwise>
						</xsl:choose>
					</td>
				</tr>
				<xsl:for-each select="files">
					<tr>
						<xsl:attribute name="class">
							<xsl:choose>
								<xsl:when test="@class">
									<xsl:value-of select="@class"></xsl:value-of>
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
							<xsl:variable name="view_file_statustext"><xsl:value-of select="php:function('lang', 'click to view file')"></xsl:value-of></xsl:variable>
							<xsl:choose>
								<xsl:when test="//link_to_files!=''">
									<xsl:variable name="link_to_file"><xsl:value-of select="//link_to_files"></xsl:value-of>/<xsl:value-of select="directory"></xsl:value-of>/<xsl:value-of select="file_name"></xsl:value-of></xsl:variable>
									<a href="{$link_to_file}" target="_blank" title="{$view_file_statustext}"><xsl:value-of select="name"></xsl:value-of></a>
								</xsl:when>
								<xsl:otherwise>
									<xsl:variable name="link_view_file"><xsl:value-of select="//link_view_file"></xsl:value-of>&amp;file_name=<xsl:value-of select="file_name"></xsl:value-of></xsl:variable>
									<a href="{$link_view_file}" target="_blank" title="{$view_file_statustext}"><xsl:value-of select="name"></xsl:value-of></a>
								</xsl:otherwise>
							</xsl:choose>
							<xsl:text> </xsl:text>
						</td>
						<td align="center">
							<input type="checkbox" name="values[file_action][]" value="{name}">
								<xsl:attribute name="title">
									<xsl:choose>
										<xsl:when test="//lang_file_action!=''">
											<xsl:value-of select="//lang_file_action"></xsl:value-of>
										</xsl:when>
										<xsl:otherwise>
											<xsl:value-of select="php:function('lang', 'Check to delete file')"></xsl:value-of>
										</xsl:otherwise>
									</xsl:choose>
								</xsl:attribute>
							</input>
						</td>
					</tr>
				</xsl:for-each>
			</table>
		</td>
	</tr>
</xsl:template>

<xsl:template xmlns:php="http://php.net/xsl" name="file_list_view">
	<tr>
		<td align="left" valign="top">
			<xsl:value-of select="php:function('lang', 'files')"></xsl:value-of>
		</td>
		<td>
			<table>
				<tr class="th">
					<td class="th_text" width="85%" align="left">
						<xsl:value-of select="php:function('lang', 'filename')"></xsl:value-of>
					</td>
				</tr>
				<xsl:for-each select="files">
					<tr>
						<xsl:attribute name="class">
							<xsl:choose>
								<xsl:when test="@class">
									<xsl:value-of select="@class"></xsl:value-of>
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
							<xsl:variable name="view_file_statustext"><xsl:value-of select="php:function('lang', 'click to view file')"></xsl:value-of></xsl:variable>
							<xsl:choose>
								<xsl:when test="//link_to_files!=''">
									<xsl:variable name="link_to_file"><xsl:value-of select="//link_to_files"></xsl:value-of>/<xsl:value-of select="directory"></xsl:value-of>/<xsl:value-of select="file_name"></xsl:value-of></xsl:variable>
									<a href="{$link_to_file}" target="_blank" title="{$view_file_statustext}"><xsl:value-of select="name"></xsl:value-of></a>
								</xsl:when>
								<xsl:otherwise>
									<xsl:variable name="link_view_file"><xsl:value-of select="//link_view_file"></xsl:value-of>&amp;file_name=<xsl:value-of select="file_name"></xsl:value-of></xsl:variable>
									<a href="{$link_view_file}" target="_blank" title="{$view_file_statustext}"><xsl:value-of select="name"></xsl:value-of></a>
								</xsl:otherwise>
							</xsl:choose>
							<xsl:text> </xsl:text>
						</td>
					</tr>
				</xsl:for-each>
			</table>
		</td>
	</tr>
</xsl:template>

<xsl:template xmlns:php="http://php.net/xsl" name="file_upload">
	<tr>
		<td valign="top">
			<xsl:value-of select="php:function('lang', 'upload file')"></xsl:value-of>
		</td>
		<td>
			<input type="file" name="file" size="40">
				<xsl:attribute name="title">
					<xsl:value-of select="php:function('lang', 'Select file to upload')"></xsl:value-of>
				</xsl:attribute>
			</input>
		</td>
	</tr>
	<xsl:choose>
		<xsl:when test="multiple_uploader!=''">
			<tr>
				<td>
					<a href="javascript:fileuploader()">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'upload multiple files')"></xsl:value-of>
						</xsl:attribute>
						<xsl:value-of select="php:function('lang', 'upload multiple files')"></xsl:value-of>
					</a>
				</td>
				<td>
				</td>
			</tr>
		</xsl:when>
	</xsl:choose>
</xsl:template>

<xsl:template xmlns:php="http://php.net/xsl" name="jasper_upload">
	<tr>
		<td valign="top">
			<xsl:value-of select="php:function('lang', 'jasper upload')"></xsl:value-of>
		</td>
		<td>
			<input type="file" name="jasperfile" size="40">
				<xsl:attribute name="title">
					<xsl:value-of select="php:function('lang', 'upload a jasper definition file')"></xsl:value-of>
				</xsl:attribute>
			</input>
		</td>
	</tr>
</xsl:template>
