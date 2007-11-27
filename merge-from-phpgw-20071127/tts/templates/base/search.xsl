	<xsl:template match="search">
	<!-- not working yet 
		<div>
			<form action="{url/simple_action}" method="post">
				<fieldset>
					<legend><xsl:value-of select="lang/search" /></legend>
					<div>
						<select>
							<option>subject</option>
							<option>description</option>
						</select>

						<select>
							<option>is</option>
							<option>contains</option>
							<option>starts with</option>
						</select>

						<input type="text" />
						<input type="button" value="{lang/search}" />
					</div>
				</fieldset>
			</form>
		</div>
		-->

		<!-- still working on this too
		<div>
			<form action="{url/saved}" method="post">
				<fieldset id="tts_saved_searches">
					<legend><xsl:value-of select="lang/saved_searches" /></legend>
					<select>
						<option value="db_id">Search</option>
					</select>
					<input type="submit" name="go" value="{lang/go}" />
					<input type="submit" name="edit" value="{lang/edit}" />
				</fieldset>
			</form>
		</div>
		-->

		<div>
			<form action="{url/advanced}" method="post">
				<fieldset>
					<legend><xsl:value-of select="lang/advanced" /></legend>
					<div>
						<label for="tts_search_name"><xsl:value-of select="lang/search_name" /></label>
						<input type="text" name="tts_search_name" id="tts_search_name" /><br />
					</div>

					<div><xsl:value-of select="lang/find_all" /></div>

					<button type="button" onclick="addNewCriteria();">
						+
						<xsl:value-of select="lang/add" />
					</button>
					
					<label for="tts_search_type"><xsl:value-of select="lang/find_all" /></label>
					<select name="tts_search_type" id="tts_search_type">
						<option value="AND"><xsl:value-of select="lang/if_all" /></option>
						<option value="OR"><xsl:value-of select="lang/if_any" /></option>
					</select><br />

					<h2><xsl:value-of select="lang/search_criteria" /></h2>
					<div id="tts_search_adv_criteria"></div>

					<!--<input type="submit" name="tts_search_save" id="tts_search_save" value="Save" onclick="saveAdvSearch(); return false;" />-->
					<input type="submit" name="tts_search_cancel" id="tts_search_cancel" value="Cancel" onclick="window.location='http://google.com';"/>
					<input type="submit" name="tts_search_ok" id="tts_search_ok" value="{lang/search}" onclick="submitAdvSearch(); return false;"/>
				</fieldset>

				<div id="tts_search_results">
				</div>

			</form>
		</div>
	</xsl:template>
