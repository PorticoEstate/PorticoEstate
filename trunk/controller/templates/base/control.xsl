<!-- item  -->

<xsl:template match="data" xmlns:php="http://php.net/xsl">

<xsl:call-template name="yui_booking_i18n"/>
<div class="identifier-header">
<h1><img src="{img_go_home}" /> 
		<xsl:value-of select="php:function('lang', 'Control')" />
</h1>
</div>

<div class="yui-content">
		<div id="details">
			<form action="#" method="post">
				<input type="hidden" name="id" value = "{value_id}">
				</input>
				<dl class="proplist-col">
					<dt>
						<label for="title">Tittel</label>
					</dt>
					<dd>
						<input type="text" name="title" id="title" value="" />
					</dd>
					<dt>
						<label for="description">Beskrivelse</label>
					</dt>
					<dd>
						<textarea cols="70" rows="5" name="description" id="description" value="" /></textarea>
					</dd>
					<dt>
						<label for="start_date">Startdato</label>
					</dt>
					<dd>
						<?php
							$start_date = "-";
							$start_date_yui = date('Y-m-d');
							$start_date_cal = $GLOBALS['phpgw']->yuical->add_listener('start_date', $start_date);
						
							echo $start_date_cal;
						?>
					</dd>
					<dt>
						<label for="end_date">Sluttdato</label>
					</dt>
					<dd>
						<?php
							$end_date = "";
							$end_date_yui = date('Y-m-d');
							$end_date_cal =  $GLOBALS['phpgw']->yuical->add_listener('end_date', $end_date);
						
							echo $end_date_cal;
						?>
					</dd>
					<dt>
						<label>Frekvenstype</label>
					</dt>
					<dd>
						<select id="repeat_type" name="repeat_type">
							<option value="0">Ikke angitt</option>
							<option value="1">Daglig</option>
							<option value="2">Ukentlig</option>
							<option value="3">Månedlig pr dato</option>
							<option value="4">Månedlig pr dag</option>
							<option value="5">Årlig</option>
						</select>
					</dd>
					<dt>
						<label>Frekvens</label>
					</dt>
					<dd>
						<input size="2" type="text" name="repeat_interval" value="" />
					</dd>
					<dt>
						<label>Prosedyre</label>
					</dt>
					<dd>
						<select id="procedure" name="procedure">
							<xsl:apply-templates select="procedure_options_array/options"/>
						</select>
					</dd>
				</dl>
				<div class="form-buttons">
					<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
					<input type="submit" name="save_control" value="{$lang_save}" title = "{$lang_save}">
					</input>
				</div>
				
			</form>
						
		</div>
	</div>
</xsl:template>
	
<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected != 0">
			<xsl:attribute name="selected" value="selected" />
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>

