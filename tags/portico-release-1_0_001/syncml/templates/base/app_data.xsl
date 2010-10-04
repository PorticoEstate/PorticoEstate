<!-- $Id$ -->

<!-- level 1 -->

<xsl:template match="list_databases">
	<div>
		<table width="100%">
			<thead>
				<xsl:apply-templates select="table_header" />
			</thead>
			<tbody>
				<xsl:apply-templates select="table_rows" />
			</tbody>
		</table>
	</div>
	<hr />
	<div>
		<a>
			<xsl:attribute name="href">
				<xsl:value-of select="add_url" />
			</xsl:attribute>
			Add database
		</a>
	</div>
</xsl:template>

<xsl:template match="edit_database">
	<form method="post">
		<xsl:attribute name="action">
			<xsl:value-of select="action_url" />
		</xsl:attribute>
		<dl>
			<dt>URI</dt>
			<dd><input type="text" size="40" name="values[uri]" /></dd>
			<dt>Source</dt>
			<dd>
				<select namne="values[app]">
					<option name="1">Notes</option>
					<option name="2">Addressbook</option>
					<option name="3">Todo</option>
				</select>
			</dd>
			<dt>Username</dt>
			<dd>
				<input type="text" size="40" name="values[cred_user]" />
				<br />
				(Or leave blank to disable the credential requirement for
				this database)
			</dd>
			<dt>Password</dt>
			<dd>
				<input type="password" size="40" name="values[cred_passwd]" />
				<br />
				(Or leave blank to disable the credential requirement for
				this database)
			</dd>
		</dl>
		<div>
			<p>
				<input type="hidden" name="database_id">
					<xsl:attribute name="value">
						<xsl:value-of select="database_id" />
					</xsl:attribute>
				</input>
				<input type="submit" name="submit[add]" value="Add" />
				&nbsp;
				<input type="submit" name="submit[cancel]" value="Cancel" />
			</p>
		</div>
	</form>
</xsl:template>

<!-- level 2 -->

<xsl:template match="table_header">
	<tr>
		<th>
			<xsl:value-of select="lang_database_id"/>
		</th>
		<th>
			<xsl:value-of select="lang_database_uri"/>
		</th>
		<th>
			<xsl:value-of select="lang_source_name"/>
		</th>
		<th>
			<xsl:value-of select="lang_creds_req"/>
		</th>
		<th>
			<xsl:value-of select="lang_edit"/>
		</th>
		<th>
			<xsl:value-of select="lang_remove"/>
		</th>
	</tr>
</xsl:template>

<xsl:template match="table_rows">
	<tr>
		<td>
			<xsl:value-of select="database_id"/>
		</td>
		<td align="center">
			<xsl:value-of select="database_uri"/>
		</td>
		<td align="center">
			<xsl:value-of select="source_name"/>
		</td>
		<td align="center">
			<xsl:value-of select="creds_req"/>
		</td>
		<td align="center">
			<a>
				<xsl:attribute name="href">
					<xsl:value-of select="edit_url" />
				</xsl:attribute>
				<xsl:value-of select="lang_edit"/>
			</a>
		</td>
		<td align="center">
			<a>
				<xsl:attribute name="href">
					<xsl:value-of select="remove_url" />
				</xsl:attribute>
				<xsl:value-of select="lang_remove"/>
			</a>
		</td>
	</tr>
</xsl:template>

