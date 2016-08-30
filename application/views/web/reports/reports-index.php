<style type='text/css'>
<? 
// An example: How to apply styles to different cells: The filter row for 'first name' will be bold,
// while each 'first name' itself will be blue  
?>
tr.orca_grid-filters-row .class-for-first-name { font-weight: bold; }
tbody td.class-for-first-name {  color: blue; border-color: black; }
</style>
		
		<!-- BEGIN PAGE CONTENT-->
			
			
		<div class="container">
			
			<div class="panel panel-default">
			  <div class="panel-body">

					<form method='get'>

					  <div class="form-group">
						<label for="machine_id">Machine name</label>
						<?=form_dropdown('machine_id', $user_machines, $this->input->get('machine_id'),  ' class="form-control" id="machine_id" ')?>
					  </div>
					  <div class="form-group">
						<label for="theperiod">Period</label>
						<?=form_dropdown('theperiod', my_config_item('GROUPING_options'), $this->input->get('theperiod'),  ' class="form-control" id="theperiod" ')?>
					  </div>
					  
					  <div class="form-group">
						<label for="sql_time_from">From</label>
						<?=form_input(array('name' => 'sql_time_from', 'type' => 'text', 'class' => 'form-control jqDatePicker', 'id' =>'sql_time_from', 'value' => $this->input->get('sql_time_from')))?>
						
					  </div>
					  <div class="form-group">
						<label for="sql_time_until">Until</label>
						
						<?=form_input(array('name' => 'sql_time_until', 'type' => 'text', 'class' => 'form-control jqDatePicker', 'id' =>'sql_time_until', 'value' => $this->input->get('sql_time_until')))?>
					  </div>
					  
					  <button type="submit" class="btn btn-default">Submit</button>			

					</form>
								
				
			  </div>
			  

			  <?php if ($this->input->get('theperiod')) { ?>
				<?=$orca_grid_container?>
			  <?php } ?>
				
			</div>
				
	
		</div>
		<!-- END PAGE CONTENT-->
			
