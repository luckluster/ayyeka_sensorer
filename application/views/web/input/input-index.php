<?
// for inputting material, yup
?>

<div class='container'>
	<div class="panel panel-default">
	  <div class="panel-body">

			<h2>Add manually a value for a machine</h2>
		
			<form method='post' action='<?=site_url('/input/receive_data')?>'>

			  <div class="form-group">
				<label for="machine_id">Machine name</label>
				<?=form_dropdown('machine_id', $user_machines, null,  ' class="form-control" id="machine_id" ')?>
			  </div>
			  <div class="form-group">
				<label for="input_value">Value</label>
				<input name='value' type="text" class="form-control" id="input_value" placeholder="value">
			  </div>
			  <div class="form-group">
				<label for="sql_time">Timestamp</label>
				<input name="sql_time" type="text" class="form-control" id="sql_time" placeholder="Timestamp">
				<p class="help-block">Use SQL timestamp. Leave empty for current time</p>
			  </div>
			  <button type="submit" class="btn btn-default">Submit</button>			

			</form>
						
		
	  </div>
	</div>
</div>