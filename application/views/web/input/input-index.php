<?
// for inputting material, yup
?>

<div class='container'>
	<div class="panel panel-default">
	  <div class="panel-body">

			<h2>Add manually a value for a machine</h2>
		
			<form method='post' action='<?=site_url('/input/receive')?>'>

			  <div class="form-group">
				<label for="mcn_id">Machine name</label>
				<?=form_dropdown('mcn_id', $user_machines, null,  ' class="form-control" id="mcn_id" ')?>
			  </div>
			  <div class="form-group">
				<label for="input_value">Value</label>
				<input type="text" class="form-control" id="input_value" placeholder="value">
			  </div>
			  <div class="form-group">
				<label for="input_ts">Timestamp</label>
				<input type="text" class="form-control" id="input_value" placeholder="timestamp">
				<p class="help-block">Leave empty for current time</p>
			  </div>
			  <button type="submit" class="btn btn-default">Submit</button>			

			</form>
						
		
	  </div>
	</div>
</div>