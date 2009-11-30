<!-- $Id$ -->

	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="index">
				<xsl:apply-templates select="index" />
			</xsl:when>
			<xsl:when test="config">
				<xsl:apply-templates select="config" />
			</xsl:when>
			<xsl:when test="edit">
				<xsl:apply-templates select="edit" />
			</xsl:when>
			<xsl:when test="help">
				<xsl:apply-templates select="help" />
			</xsl:when>
			<xsl:when test="history">
				<xsl:apply-templates select="history" />
			</xsl:when>
			<xsl:when test="view">
				<xsl:apply-templates select="view" />
			</xsl:when>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="index">
		<!--<xsl:variable name="form_action" select="form_action" />
		<form enctype="multipart/form-data" action="{$form_action}" method="POST">-->

		<form>
			<xsl:attribute name="action"> <xsl:value-of select="form/action"/> </xsl:attribute>
			<xsl:attribute name="method"> <xsl:value-of select="form/method"/></xsl:attribute>
			<xsl:attribute name="enctype"> <xsl:value-of select="form/enctype"/></xsl:attribute>
			<xsl:attribute name="id"> <xsl:value-of select="form/id"/></xsl:attribute>
			<xsl:attribute name="name"> <xsl:value-of select="form/name"/></xsl:attribute>

		<table width="100%" border="0" cellpadding="2" cellspacing="2">	
			<tr>
				<td colspan="5" align="center" width="100%">
					<xsl:apply-templates select="img_dir/widget" />
					<xsl:apply-templates select="help_dir/widget" />
					<font size="+1" color="maroon"><b><xsl:value-of select="current_dir" /></b></font>
					<xsl:apply-templates select="img_vers/widget" />
					<font size="+1" color="maroon"><b><xsl:value-of select="current_vers" /></b></font>
				</td>
			</tr>
			<tr>
				<td width="5%">
					<nobr>
						<xsl:apply-templates select="img_up/widget" />
						<xsl:apply-templates select="help_up/widget" />
					</nobr>
				</td>
				<td  width="5%">
					<nobr>
						<xsl:apply-templates select="img_home/widget" />
						<xsl:apply-templates select="help_home/widget" />
					</nobr>
				</td>
				<td align="center" valign="middle" width="40%">
					<nobr>
						<b><xsl:value-of select="lang_create_folder" /></b>
						<xsl:apply-templates select="create_folder/widget" />
						<xsl:apply-templates select="img_create_folder/widget" />
						<xsl:apply-templates select="help_create_folder/widget" />
					</nobr>
				</td>
				<td align="center" valign="middle" width="40%">
					<nobr>
						<b><xsl:value-of select="lang_create_file" /></b>
						<xsl:apply-templates select="create_file/widget" />
						<xsl:apply-templates select="img_create_file/widget" />
						<xsl:apply-templates select="help_create_file/widget" />
					</nobr>
				</td>
				<td width="10%" align="right">
					<nobr>
						<xsl:apply-templates select="img_refresh/widget" />
						<xsl:apply-templates select="help_refresh/widget" />
					</nobr>
				</td>
			</tr>
			<tr>
				<td colspan="5">
					<xsl:apply-templates select="files" />
				</td>
			</tr>
			<tr height="20">
				<td colspan="5" />
			</tr>
			<xsl:if test='rename'>
				<tr>
					<td>
						<xsl:apply-templates select="rename/img_ok/widget" />
					</td>
					<td colspan="4">
						<xsl:apply-templates select="rename/img_cancel/widget" />
					</td>
				</tr>
			</xsl:if>
			<tr>
				<td colspan="5">
					<table border="0">
						<tr>
							<td>
								<xsl:apply-templates select="menu/widget" /><xsl:apply-templates select="help_menu/widget" />
							</td>
							<td>
								<xsl:apply-templates select="dir_menu/widget" /><xsl:apply-templates select="help_dir_list/widget" />
							</td>
							<td width="50">&nbsp;</td>
							<td>
								<xsl:apply-templates select="img_dl/widget" /><xsl:apply-templates select="help_dl/widget" /><b><xsl:value-of select="lang_dl" /></b>
							</td>
							<xsl:if test="execute">
								<td width="50">&nbsp;</td>
								<td>
									<xsl:apply-templates select="command_line/widget" /><xsl:apply-templates select="help_command_line/widget" />
								</td>
								<td>
									<xsl:apply-templates select="execute/widget" /><xsl:apply-templates select="help_execute/widget" />
								</td>
							</xsl:if>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="th">
				<td colspan="6" align="center">
					<xsl:value-of select="lang_show"/>
					<xsl:for-each select="show_upload_boxes">
						<xsl:apply-templates />
					</xsl:for-each>
					<xsl:value-of select="lang_upload_fields"/>
					<xsl:apply-templates select="help_show_upload_fields/widget" />
				</td>
			</tr>
			<tr>
				<td colspan="5">
					<table align="center" cellpadding="2" cellspacing="2">
						<tr>
							<td align="center"><b><xsl:value-of select="lang_file"/></b><xsl:apply-templates select="help_upload_file/widget" /></td>
							<td align="center"><b><xsl:value-of select="lang_comment"/></b><xsl:apply-templates select="help_upload_comment/widget" /></td>
							<td><xsl:apply-templates select="img_upload/widget"/><xsl:apply-templates select="help_upload_files/widget" /><b><xsl:value-of select="lang_upload" /></b></td>
						</tr>
							<xsl:apply-templates select="uploads" />
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="5" align="center">
					<xsl:apply-templates select="summary" />
				</td>
			</tr>
			<xsl:if test='lang_close'>
		 			<tr>
					<td align="center" colspan="5">
						<input type="button">
							<xsl:attribute name="value"><xsl:value-of select="lang_close"/></xsl:attribute>
							<xsl:attribute name="onClick"><xsl:text>window.close();</xsl:text></xsl:attribute>
						</input>
					</td>
				</tr>
			</xsl:if>
			<xsl:apply-templates select="add_moz_sidebar" />

			<xsl:apply-templates select="body_data" />
	
			<p><xsl:value-of select="msg" /></p>

		</table>
		</form>
	</xsl:template>

<!--These templates print out the file list-->
	<xsl:template match="files">
			<table width="100%" border="0" cellpadding="2" cellspacing="2">
				<tr class="th">
				<xsl:for-each select="file_attributes">
					<td>
						<xsl:apply-templates select="widget"/><xsl:apply-templates select="help/widget"/>
					</td>
				</xsl:for-each>
				</tr>
				<xsl:apply-templates select="file" />
			</table>
	</xsl:template>
	
	<!--The template for each file -->
	<xsl:template match="file">
		<tr>
			<xsl:choose>
				<xsl:when test="position() mod 2 = 0">
					<xsl:attribute name="class">row_on</xsl:attribute>
				</xsl:when>
				<xsl:otherwise>
					<xsl:attribute name="class">row_off</xsl:attribute>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:for-each select="*">
				<td>
					<xsl:choose>
						<xsl:when test='name(./*)="widget" or name(./*/*)="widget"'>
							<xsl:apply-templates select="." />
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="." />
						</xsl:otherwise>
					</xsl:choose>
				</td>
			</xsl:for-each>
		</tr>
	</xsl:template>

	<!--The template for compare two files -->
	<xsl:template match="compare">
			<table width="100%" border="0" cellpadding="2" cellspacing="2">
				<tr>
					 <xsl:apply-templates select="diff" />
				</tr>
			</table>
	</xsl:template>

	<xsl:template match="diff">
  		 <xsl:for-each select="tablediff">
	<div class="difflines">
		<xsl:apply-templates select="../tablediff" />
	</div>
		 </xsl:for-each>
	</xsl:template>


<!--Prints out the buttons-->
	<xsl:template match="buttons">
		<xsl:for-each select="button">
			<xsl:apply-templates select="widget" />
		</xsl:for-each>
	</xsl:template>

<!--The widgets that handle uploads-->
	<xsl:template match="uploads">
		<xsl:for-each select="table_row">
			<xsl:call-template name="table_row"/>
		</xsl:for-each>
	</xsl:template>

<!-- summary info -->
	<xsl:template match="summary">
		<table border="0">
			<tr>
				<td><b><xsl:value-of select="lang_files" /></b>:&nbsp;</td>
				<td><xsl:value-of select="file_count" /></td>&nbsp;
				<td><b><xsl:value-of select="lang_files_total" /></b>:&nbsp;</td>
				<td><xsl:value-of select="files_total" /></td>&nbsp;
			</tr>
			<tr>
				<td><b><xsl:value-of select="lang_space" /></b>:&nbsp;</td>
				<td><xsl:value-of select="usage" /></td>
				<td>
					<xsl:if test="unused != ''"><b><xsl:value-of select="lang_unused" /></b>:&nbsp;</xsl:if>
				</td>
				<td><xsl:value-of select="unused" /></td>
			</tr>
		</table>
	</xsl:template>

	<!--Debug output-->
	<xsl:template match="body_data">
		<div style="border:1px dashed #000000; background-color: yellow;">
			<b>Debug output</b><br/>
			<xsl:copy-of select="." />
		</div>
	</xsl:template>
	
	<!--This bit of Javascript will create a mozilla sidebar -->
	<xsl:template match="add_moz_sidebar">
		<script language="JavaScript">
			<xsl:text disable-output-escaping="yes">
				function addMozillaPanel()
				{
					var getURL_ID = document.getElementById("getURL");
						if ((typeof window.sidebar == "object") &amp;&amp; (typeof window.sidebar.addPanel == "function")) 
						{ 
							window.sidebar.addPanel ("</xsl:text>
							<xsl:value-of select="label" />		
							<xsl:text disable-output-escaping="yes">", 
							window.location.href+"&amp;template=moz_sidebar&amp;noheader=1",""); 
						}
				}
			</xsl:text>
		</script>
		<a href="javascript:addMozillaPanel();"><xsl:value-of select="link_label" /></a>
	</xsl:template>

	<xsl:template match="config">
		<center>
		<xsl:if test="error != ''">
			<b><xsl:value-of disable-output-escaping="yes" select="error"/></b>
		</xsl:if>
		<xsl:variable name="action_url" select="action_url" />
		<form action="{$action_url}" method="POST">
 				<xsl:apply-templates select="table"/>
		</form>
		</center>
	</xsl:template>

	<xsl:template match="help">
		<table>
			<tr>
				<td><b><xsl:value-of select="title" /></b></td>
			</tr>
			<tr>
				<td><xsl:value-of disable-output-escaping="yes" select="msg" /></td>
			</tr>
			<tr height="50" valign="bottom">
				<td align="center">
					<input type="button">
						<xsl:attribute name="value"><xsl:value-of select="lang_close"/></xsl:attribute>
						<xsl:attribute name="onClick"><xsl:text>window.close();</xsl:text></xsl:attribute>
					</input>
				</td>
			</tr>
		</table>
	</xsl:template>

	<xsl:template match="history">
		<!--<xsl:variable name="form_action" select="form_action" />
		<form enctype="multipart/form-data" action="{$form_action}" method="POST">-->
		<form>
			<xsl:attribute name="action"> <xsl:value-of select="form/action"/> </xsl:attribute>
			<xsl:attribute name="method"> <xsl:value-of select="form/method"/></xsl:attribute>
			<xsl:attribute name="enctype"> <xsl:value-of select="form/enctype"/></xsl:attribute>
			<xsl:attribute name="id"> <xsl:value-of select="form/id"/></xsl:attribute>
			<xsl:attribute name="name"> <xsl:value-of select="form/name"/></xsl:attribute>
		<table width="100%">
			<tr>
				<td colspan="2" align="center">
					<b><xsl:value-of select="title"/></b><br />
				</td>
			</tr>
			<tr>
				<td colspan="2" align="left" valign="middle">
					<nobr>
						<b><xsl:value-of select="lang_from_rev" /></b>
						<xsl:apply-templates select="from/widget" />
			 			<b><xsl:value-of select="lang_to_rev" /></b>
						<xsl:apply-templates select="to/widget" />
						<xsl:apply-templates select="img_search/widget" />
						<xsl:apply-templates select="file/widget" />
						<xsl:apply-templates select="vers/widget" />
						<xsl:apply-templates select="mime_type/widget" />
					</nobr>
				</td>

			</tr>
			<tr>
				<td colspan="2" align="left">
					<nobr>
						<xsl:apply-templates select="collapse/widget"/>

					</nobr>
				</td>
			</tr>

			<tr>
				<td colspan="2">
					<xsl:apply-templates select="table" />
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<xsl:apply-templates select="img_compare/widget" />
				</td>

			</tr>
			<tr>
				<td align="left">
					<xsl:apply-templates select="img_first/widget" />
					<xsl:apply-templates select="img_prev/widget" />
				</td>
				<td align="right">
					<xsl:apply-templates select="img_next/widget" />
					<xsl:apply-templates select="img_last/widget" />
				</td>

			</tr>
			<tr>
				<td colspan="2" align="center">
					<nobr>
						<font size="+1"><xsl:apply-templates select="page/widget" /></font>
					</nobr>
				</td>
			</tr>


			<tr height="50" valign="bottom">
				<td colspan="2" align="center">
					<input type="button">
						<xsl:attribute name="value"><xsl:value-of select="lang_close"/></xsl:attribute>
						<xsl:attribute name="onClick"><xsl:text>window.close();</xsl:text></xsl:attribute>
					</input>
				</td>
			</tr>
		</table>
	 </form>
	</xsl:template>

	<xsl:template match="view">
		<xsl:value-of disable-output-escaping="yes" select="."/>
	</xsl:template>
