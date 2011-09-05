<?php 	
//include common logic for all templates
	include("common.php");
	phpgwapi_yui::load_widget('tabview');
	phpgwapi_yui::tabview_setup('controller_tabview');
?>

<div class="identifier-header">
<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/actions/go-home.png" /> <?php echo lang('control') ?></h1>
	<div>
		<button onclick="javascript:window.location.href ='<?php echo $cancel_link;?>;'">&laquo;&nbsp;<?php //echo lang('composite_back');?></button><br/>
		<label><?php echo lang('name') ?> </label><?php //echo $composite->get_name() ?>
	</div>
</div>


<div id="controller_tabview" class="yui-navset">
	<ul class="yui-nav">
		<li class="selected"><a href="#details"><em><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/actions/go-home.png" alt="icon" /> <?php echo lang('details') ?></em></a></li>
		
		<?php //if($composite->get_id() > 0) { ?>
		
		<li><a href="#elements"><em><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/mimetypes/x-office-drawing-template.png" alt="icon" /> <?php echo lang('units') ?></em></a></li>
		<li><a href="#controlitems"><em><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/mimetypes/x-office-drawing-template.png" alt="icon" /> <?php echo lang('control_items') ?></em></a></li>

		<?php //} ?>
	</ul>
	
<div class="yui-content">
		<div id="details">
			<form action="#" method="post">
				<input type="hidden" name="id" value="<?php if(!empty($control)){ echo $control->get_id(); } else { echo '0'; }  ?>"/>
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
						<input type="text" name="description" id="description" value="" />
					</dd>
					<dt>
						<label for="start_date">Startdato</label>
					</dt>
					<dd>
						<?php
							$start_date = "-";
							$start_date_yui = date('Y-m-d');
							$start_date_cal = $GLOBALS['phpgw']->yuical->add_listener('date_start', $start_date);
						
							echo $start_date_cal;
						?>
					</dd>
					<dt>
						<label for="end_date">Sluttdato</label>
					</dt>
					<dd>
						<?php
							$end_date = "";
							$end_date_yui;
							$end_date_cal =  $GLOBALS['phpgw']->yuical->add_listener('date_end');
						
							echo $end_date_cal;
						?>
					</dd>
					<dt>
						<label>Dag</label>
					</dt>
					<dd>
						<input type="checkbox" value="0" name="repeat_day[]" title="Mandag">Mandag
						<input type="checkbox" value="1" name="repeat_day[]" title="Tirsdag">Tirsdag
						<input type="checkbox" value="2" name="repeat_day[]" title="Onsdag">Onsdag
						<input type="checkbox" value="3" name="repeat_day[]" title="Torsdag">Torsdag
						<input type="checkbox" value="4" name="repeat_day[]" title="Fredag">Fredag
						<input type="checkbox" value="5" name="repeat_day[]" title="Lørdag">Lørdag
						<input type="checkbox" value="6" name="repeat_day[]" title="Søndag">Søndag
					</dd>
					<dt>
						<label>Type</label>
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
						<label>Intervall</label>
					</dt>
					<dd>
						<input type="text" name="repeat_interval" value="" />
					</dd>
					<dt>
						<label>Prosedyre</label>
					</dt>
					<dd>
						<select id="procedure" name="procedure">
							<?php 
								foreach ($procedure_array as $procedure) {
									echo "<option value='" . $procedure->get_id() . "'>" . $procedure->get_title() . "</option>";
								}
							?>
							
						</select>
					</dd>
				</dl>
				
				<div class="form-buttons">
					<?php
						echo '<input type="submit" name="save_control" value="' . lang('save') . '"/>';
					?>
				</div>
				
			</form>
						
		</div>
		<div id="elements">
				<h3><?php echo lang('included_units') ?></h3>
				<?php 
					$list_form = false; 
					$list_id = 'included_units';
					//$url_add_on = '&amp;control_id='.$control->get_id();
					unset($extra_cols);
					include('unit_list_partial.php');
	
					
	            if ($editable) { 
				    echo '<h3>'.lang('all_locations').'</h3>';
					$list_form = true; 
					$list_id = 'property_uilocations';
					//$url_add_on = '&amp;control_id='.$control->get_id();
					unset($extra_cols);
					$related = array('included_units');
					include('property_location_partial.php');
				}
				?>
		</div>
	</div>
</div>