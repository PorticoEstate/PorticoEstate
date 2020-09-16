<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<style>
		/* Chrome, Safari, Edge, Opera */
		input::-webkit-outer-spin-button,
		input::-webkit-inner-spin-button {
		-webkit-appearance: none;
		margin: 0;
		}

		/* Firefox */
		input[type=number] {
		-moz-appearance: textfield;
		}
	</style>

	<div id="group-edit-page-content" class="margin-top-content">
		<div class="container wrapper">
			<h1>
				<xsl:value-of select="name" />
			</h1>
			<span class="d-block">
				<xsl:value-of select="when"/>
			</span>
			<xsl:if test="participant_limit > 0">
				<span class="d-block">
					<xsl:value-of select="php:function('lang', 'participant limit')" />:
					<xsl:value-of select="participant_limit" />
				</span>
			</xsl:if>
			<span class="d-block">
				<xsl:value-of select="php:function('lang', 'number of participants')" />:
				<xsl:value-of select="number_of_participants" />
			</span>
			<span class="d-block">
				<xsl:value-of select="participanttext" disable-output-escaping="yes"/>
			</span>
			<form action="{data/form_action}" method="POST" id="form" name="form" class="col-lg-8">
				<div class="row">
					<div class="col-12">
						<xsl:call-template name="msgbox"/>
					</div>
					<div class="col-12">
						<div class="form-group">
							<label for="phone" class="text-uppercase">
								<xsl:value-of select="php:function('lang', 'phone')" />
							</label>
							<input id="phone" name="phone" class="form-control" type="number" min="1" value="{phone}" oninput="check(this)">
								<xsl:attribute name="required">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="placeholder">
									<xsl:value-of select="php:function('lang', 'Minimum 8 digits')" />
								</xsl:attribute>
							</input>
						</div>
					</div>
					<div class="col-12">
						<div class="form-group">
							<label for="quantity" class="text-uppercase">
								<xsl:value-of select="php:function('lang', 'quantity')" />
							</label>
							<input id="quantity" name="quantity" class="form-control" type="number" min="1" value="{quantity}">
								<xsl:attribute name="required">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
							</input>
						</div>
					</div>

					<!--					<div class="col-12">
						<div class="form-group">
							<label for="email" class="text-uppercase">
								<xsl:value-of select="php:function('lang', 'email')" />
							</label>
							<input id="email" name="email" type="email" class="form-control" value="{email}">
							</input>
						</div>
					</div>-->

					<input id="register_type" name="register_type" type="hidden"/>

					<xsl:if test="enable_register_pre = 1">
						<div class="col-12 mt-3 mb-2">
							<button type="submit" value="register_pre" class="btn btn-primary btn-lg col-12 mr-4" onclick="validate_submit('register_pre');">
								<xsl:value-of select="php:function('lang', 'Pre-register')" />
							</button>
						</div>
					</xsl:if>

					<xsl:if test="enable_register_in = 1">
						<div class="col-12 mt-3 mb-2">
							<button type="submit" value="register_in" class="btn btn-primary btn-lg col-12 mr-4" onclick="validate_submit('register_in');">
								<xsl:value-of select="php:function('lang', 'Register in')" />
							</button>
						</div>
						<div class="col-12 mt-3 mb-2">
							<button type="submit" value="register_out" class="btn btn-primary btn-lg col-12 mr-4" onclick="validate_submit('register_out');">
								<xsl:value-of select="php:function('lang', 'Register out')" />
							</button>
						</div>
					</xsl:if>
				</div>
			</form>
		</div>
	</div>
	<script>



	</script>
</xsl:template>