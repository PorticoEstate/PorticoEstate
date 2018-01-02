<!-- $Id$ -->

	<xsl:template name="app_data">
		<xsl:apply-templates select="list"/>
	</xsl:template>
	
	<xsl:template match="list">
		<xsl:choose>
			<xsl:when test="menu != ''">
				<xsl:apply-templates select="menu"/> 
			</xsl:when>
		</xsl:choose>
		<div style="width: 100%; height: 65px; background-color: #EEEEEE; font-family: Arial, Helvetica, Sans-Serif; font-size: 9pt;">
			This example demonstrates a simple Hello World example
		</div>

		<form name="frmForm">
			<h1>Hello World</h1>
			<input type="button" value="Simple Hello World" onClick="HelloWorld();"></input>
			<input type="button" value="Parameters Hello World" onClick="HelloWorldParams(prompt('Firstname:'), prompt('Lastname:'));"></input>
			<input type="button" value="Array Hello World" onClick="HelloWorldArray( new Array( prompt('Firstname:'), prompt('Lastname:') ));"></input>
		</form>


		<div id="sitemgr_site_nnv">
			<div id="sitemgr_site_nnv_folder_list">
				<ul class="folder_list expandable">
					<li><a href="javascript:getFolder('/home');" class="folder_name">/home</a>
						<ul>
							<li>
							</li>
						</ul>
					</li>
				</ul>
			</div>
			<div id="sitemgr_site_nnv_file_list">
			</div>
		</div>


	</xsl:template>

