<xsl:template match="data" xmlns:php="http://php.net/xsl">

	<div id="systemmessage-page-content" class="margin-top-content">
        	<div class="container wrapper">
				<div class="location">
					<span>
						<a><xsl:attribute name="href">
								<xsl:value-of select="php:function('get_phpgw_link', '/bookingfrontend/index.php', 'menuaction:bookingfrontend.uisearch.index')"/>
							</xsl:attribute>
							<xsl:value-of select="php:function('lang', 'Home')" />
						</a>
					</span>
					<span><xsl:value-of select="php:function('lang', 'Message')" /></span>										
				</div>

            	<div class="row">					

					<form action="" method="POST" id="form" name="form" class="col-md-8">
						<div class="col">
							<xsl:if test="not(system_message/id)">
								<h5><xsl:value-of select="php:function('lang', 'New System Message')" /></h5>
							</xsl:if>
							<xsl:if test="system_message/id">
								<h5><xsl:value-of select="php:function('lang', 'Edit System Message')" /></h5>
							</xsl:if>
						</div>

						<div class="col mb-4">
							<xsl:call-template name="msgbox"/>
						</div>

						<div class="col-12">
							<div class="form-group">
								<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Title')" />*</label>
								<input name="title" class="form-control" type="text" value="{system_message/title}">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please enter a title')" />
									</xsl:attribute>
								</input>
							</div>
						</div>

						<div class="col-12">
							<div class="form-group">
								<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Message')" />*</label>
								<textarea id="field-message" class="form-control" name="message" type="text">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please enter a message')" />
									</xsl:attribute>
									<xsl:value-of select="system_message/message"/>
								</textarea>
							</div>
						</div>

						<div class="col-12">
							<div class="form-group">
								<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Name')" />*</label>
								<input name="name" class="form-control" type="text" value="{system_message/name}" >
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please enter a name')" />
									</xsl:attribute>
								</input>
							</div>
						</div>

						<div class="col-12">
							<div class="form-group">
								<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Phone')" /></label>
								<input name="phone" class="form-control" type="text" value="{system_message/phone}" />
							</div>
						</div>

						<div class="col-12">
							<div class="form-group">
								<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Email')" /></label>
								<input name="email" class="form-control" type="text" value="{system_message/email}" />
							</div>
						</div>

						<div class="col-12">
							<div class="form-group">
								<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Created')" /></label>
								<input id="inputs" class="form-control" name="created" readonly="true" type="text">
									<xsl:attribute name="value">
										<xsl:value-of select="system_message/created"/>
									</xsl:attribute>
								</input>
							</div>
						</div>						

						<div class="col mt-5">
							<xsl:if test="not(system_message/id)">
								<input type="submit" class="btn btn-light mr-4" value="{php:function('lang', 'Save')}"/>
							</xsl:if>
							<xsl:if test="system_message/id">
								<input type="submit" class="btn btn-light mr-4" value="{php:function('lang', 'Save')}"/>
							</xsl:if>
							<a class="cancel" href="{system_message/cancel_link}">
								<xsl:value-of select="php:function('lang', 'Cancel')" />
							</a>
						</div>
					</form>
            	</div>         
            
        	</div>
    	
	</div>
    <div class="push"></div>

</xsl:template>
