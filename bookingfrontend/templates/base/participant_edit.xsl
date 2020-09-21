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
			<span class="d-block">
				<xsl:value-of select="php:function('lang', 'number of participants')" />:
				<xsl:value-of select="number_of_participants" />
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

					<div class="col-12 mt-3 mb-2">
						<input type="submit" class="btn btn-light mr-4" value="{php:function('lang', 'Add')}"/>
					</div>
				</div>
			</form>
		</div>
	</div>
	<script>
		function check(input)
		{
		value = input.value;

		var phoneno = /^\d{8}$/;
		if(value.match(phoneno))
		{
		input.setCustomValidity('');
		}
		else
		{
		input.setCustomValidity('Must be at least 8 digits');
		}
		}
	</script>
</xsl:template>