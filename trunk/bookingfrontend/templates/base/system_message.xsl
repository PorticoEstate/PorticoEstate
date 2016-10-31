<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div class="content">

		<dl class="form">
			<dt class="heading">
				<xsl:if test="not(system_message/id)">
					<xsl:value-of select="php:function('lang', 'New System Message')" />
				</xsl:if>
				<xsl:if test="system_message/id">
					<xsl:value-of select="php:function('lang', 'Edit System Message')" />
				</xsl:if>
			</dt>
		</dl>

		<xsl:call-template name="msgbox"/>

		<form action="" method="POST" id="form" name="form">
			<div class="pure-g">
				<div class="pure-u-1">
					<dl class="form-col">
						<dt>
							<label for="field_title">
								<xsl:value-of select="php:function('lang', 'Title')" />
							</label>
						</dt>
						<dd>
							<input name="title" type="text" value="{system_message/title}">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="php:function('lang', 'Please enter a title')" />
								</xsl:attribute>
							</input>
						</dd>
					</dl>
				</div>
			</div>

			<div class="pure-g">
				<div class="pure-u-1 pure-u-lg-4-5">
					<dl class="form-col">
						<dt>
							<label for="field_message">
								<xsl:value-of select="php:function('lang', 'Message')" />
							</label>
						</dt>
						<dd>
							<textarea id="field-message" name="message" type="text">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="php:function('lang', 'Please enter a message')" />
								</xsl:attribute>
								<xsl:value-of select="system_message/message"/>
							</textarea>
						</dd>
					</dl>
				</div>
			</div>

			<div class="pure-g">
				<div class="pure-u-1">
					<dl class="form-col">
						<dt>
							<label for="field_name">
								<xsl:value-of select="php:function('lang', 'Name')" />
							</label>
						</dt>
						<dd>
							<input name="name" type="text" value="{system_message/name}" >
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="php:function('lang', 'Please enter a name')" />
								</xsl:attribute>
							</input>
						</dd>
						<dt>
							<label for="field_phone">
								<xsl:value-of select="php:function('lang', 'Phone')" />
							</label>
						</dt>
						<dd>
							<input name="phone" type="text" value="{system_message/phone}" />
						</dd>
						<dt>
							<label for="field_email">
								<xsl:value-of select="php:function('lang', 'Email')" />
							</label>
						</dt>
						<dd>
							<input name="email" type="text" value="{system_message/email}" />
						</dd>

						<dt>
							<label for="field_time">
								<xsl:value-of select="php:function('lang', 'Created')" />
							</label>
						</dt>
						<dd>
							<input id="inputs" name="created" readonly="true" type="text">
								<xsl:attribute name="value">
									<xsl:value-of select="system_message/created"/>
								</xsl:attribute>
							</input>
						</dd>
					</dl>
				</div>
			</div>
		
			<div class="form-buttons">
				<xsl:if test="not(system_message/id)">
					<input type="submit" value="{php:function('lang', 'Save')}"/>
				</xsl:if>
				<xsl:if test="system_message/id">
					<input type="submit" value="{php:function('lang', 'Save')}"/>
				</xsl:if>
				<a class="cancel" href="{system_message/cancel_link}">
					<xsl:value-of select="php:function('lang', 'Cancel')" />
				</a>
			</div>
		</form>
	</div>

</xsl:template>
