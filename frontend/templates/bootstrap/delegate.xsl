<xsl:template match="section" xmlns:php="http://php.net/xsl">
	<xsl:param name="template_set"/>
	<xsl:choose>
		<xsl:when test="msgbox_data != ''">
			<xsl:call-template name="msgbox"/>
		</xsl:when>
	</xsl:choose>
	
	<xsl:variable name="unit_leader" select="//header/org_unit[ORG_UNIT_ID = //selected_org_unit]/LEADER"></xsl:variable>
	<xsl:choose>
		<xsl:when test="//selected_org_unit = 'all' or $unit_leader = '1'">
			<div id="accordion">
				<div class="card mt-1">
					<div class="card-header" id="headingDelegat">
						<button class="btn collapsed btn-light w-100 text-left" data-toggle="collapse" data-target="#collapseDelegat" aria-expanded="false" aria-controls="collapseDelegat">
							<h5>
								Ny delegat
							</h5>
						</button>
					</div>

					<div id="collapseDelegat" class="collapse" aria-labelledby="headingDelegat" data-parent="#accordion">
						<div class="card-body">

							<div class="row mt-3">
								<div class="col-md-3" >
									<xsl:choose>
										<xsl:when test="number_of_delegates &lt; delegate_limit">
											<img src="frontend/templates/base/images/16x16/group_add.png" class="list_image"/>
											<xsl:value-of select="php:function('lang', 'find_user')"/>
											<xsl:variable name="btn_add">
												<xsl:value-of select="php:function('lang', 'btn_add')"/>
											</xsl:variable>
											<xsl:variable name="btn_search">
												<xsl:value-of select="php:function('lang', 'btn_search')"/>
											</xsl:variable>
											<form ENCTYPE="multipart/form-data" name="form" method="post" action="{form_action}">
												<input type="hidden" id="account_id" name="account_id" value="{search/account_id}"/>
												<dl>
													<dt>
														<xsl:value-of select="php:function('lang', 'username')"/>
													</dt>
													<dd>
														<input type="text" id="username" name="username" value="{search/username}"/>
														<input type="button" class="pure-button pure-button-active" name="search" value="{$btn_search}" onclick="searchUser()"/>
														<div class="loading"></div>
														<div id='custom_message' class='custom-message'/>

													</dd>
													<dt>
														<xsl:value-of select="php:function('lang', 'firstname')"/>
													</dt>
													<dd>
														<input type="text" id="firstname" name="firstname" readonly="" value="{search/firstname}" style="background-color: #CCCCCC;"/>
													</dd>
													<dt>
														<xsl:value-of select="php:function('lang', 'lastname')"/>
													</dt>
													<dd>
														<input type="text" id="lastname" name="lastname" readonly="" value="{search/lastname}" style="background-color: #CCCCCC;"/>
													</dd>
													<dt>
														<xsl:value-of select="php:function('lang', 'email')"/>
													</dt>
													<dd>
														<input type="text" id="email" name="email" readonly="" value="{search/email}" style="background-color: #CCCCCC;"/>
													</dd>
													<dt></dt>
													<dd>
														<input type="submit" class="btn btn-info" name="add" id="add" value="{$btn_add}" style="display: none;"/>
													</dd>
												</dl>
											</form>
										</xsl:when>
										<xsl:otherwise>
											<xsl:value-of select="error_message"/>
										</xsl:otherwise>
									</xsl:choose>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</xsl:when>
	</xsl:choose>
	<div class="row mt-3">
		
		<xsl:choose>
			<xsl:when test="//selected_org_unit != 'all'">
				<div class="col-md-6" >
					<h3>
						<xsl:value-of select="php:function('lang', 'delegates_for_res_unit')"/> (<xsl:value-of select="number_of_delegates"/>)</h3>
					<xsl:choose>
						<xsl:when test="not(normalize-space(delegate)) and (count(delegate) &lt;= 1)">
							<em style="margin-left: 1em; ">
								<xsl:value-of select="php:function('lang', 'no_delegates')"/>
							</em>
						</xsl:when>
						<xsl:otherwise>
							<xsl:variable name="btn_remove">
								<xsl:value-of select="php:function('lang', 'btn_remove')"/>
							</xsl:variable>
							<table class="table table-hover">
								<thead>
									<tr>
										<th>
											<xsl:value-of select="php:function('lang', 'name')"/>
										</th>
										<xsl:if test="$unit_leader = '1'">
											<th>
												<xsl:value-of select="php:function('lang', 'btn_remove')"/>
											</th>
										</xsl:if>
									</tr>
								</thead>
 
								<xsl:for-each select="delegate">
									<xsl:sort select="account_lastname"/>
									<tr>
										<td>
											<xsl:value-of select="account_lastname"/>, <xsl:value-of select="account_firstname"/>
											(<xsl:value-of select="account_lid"/>)

										</td>
										<td>
											<xsl:if test="$unit_leader = '1'">
												<form ENCTYPE="multipart/form-data" name="form" method="post" action="{form_action}">
													<input type="hidden" name="account_id" value="{account_id}"/>
													<input type="submit" class="btn btn-info" name="remove_specific" value="{$btn_remove}"/>
												</form>
											</xsl:if>
										</td>
									</tr>
								</xsl:for-each>
							</table>
						</xsl:otherwise>
					</xsl:choose>
				</div>
			</xsl:when>
			<xsl:otherwise>
				<div class="col-md-6" >
					<h3 style="color: red;">
						<xsl:value-of select="php:function('lang', 'deletage_to_all_res_units')"/>
					</h3>
				</div>
			</xsl:otherwise>
		</xsl:choose>
								
		<xsl:choose>
			<xsl:when test="normalize-space(//user_delegate) != ''">
				<div class="col-md-6" >
					<h3>
						<xsl:value-of select="php:function('lang', 'delegates_for_user')"/> (<xsl:value-of select="number_of_user_delegates"/>)</h3>
					<xsl:choose>
						<xsl:when test="not(normalize-space(user_delegate)) and (count(user_delegate) &lt;= 1)">
							<em style="margin-left: 1em;">
								<xsl:value-of select="php:function('lang', 'no_delegates')"/>
							</em>
						</xsl:when>
						<xsl:otherwise>
							<xsl:variable name="btn_remove">
								<xsl:value-of select="php:function('lang', 'btn_remove')"/>
							</xsl:variable>
							<table class="table table-hover">
								<thead>
									<tr>
										<th>
											<xsl:value-of select="php:function('lang', 'name')"/>
										</th>
										<th>
											<xsl:value-of select="php:function('lang', 'btn_remove')"/>
										</th>
									</tr>
								</thead>
								<xsl:for-each select="user_delegate">
									<xsl:sort select="account_lastname"/>
									<td>
										<xsl:value-of select="account_lastname"/>, <xsl:value-of select="account_firstname"/>
										(<xsl:value-of select="account_lid"/>)
									</td>
									<td>
										<form ENCTYPE="multipart/form-data" name="form" method="post" action="{form_action}">
											<input type="hidden" name="account_id" value="{account_id}"/>
											<input type="submit" class="btn btn-info" name="remove" value="{$btn_remove}"/>
										</form>
									</td>
								</xsl:for-each>
							</table>
						</xsl:otherwise>
					</xsl:choose>
				</div>
			</xsl:when>
		</xsl:choose>
	</div>
</xsl:template>


