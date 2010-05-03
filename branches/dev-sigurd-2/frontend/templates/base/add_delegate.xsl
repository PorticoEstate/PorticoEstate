<xsl:template match="delegate_data" xmlns:php="http://php.net/xsl">
	<form>
		<div>
			<label for="username">Brukernavn
				<input type="text" name="username"/>
				<input type="submit" name="Search" value="Søk"/>
			</label>
		</div>
		<div>
			<ul>
				<li>
					<label for="firstname"> Fornavn
						<input type="text" name="firstname"/>
					</label>
				</li>
				<li>
					<label for="lastname"> Etternavn
						<input type="text" name="lastname"/>
					</label>
				</li>
				<li>
					<label for="email"> E-post
						<input type="text" name="email"/>
					</label>
				</li>
				
				<li>
					<label for="password1"> Passord
						<input type="password" name="password1"/>
					</label>
				</li>
				<li>
					<label for="password2"> Gjenta passord
						<input type="password" name="password2"/>
					</label>
				</li>
			</ul>
		</div>
	</form>
</xsl:template>


