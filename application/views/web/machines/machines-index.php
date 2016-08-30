<?
// for inputting material, yup
?>

<div class='container'>
	<div class="panel panel-default">
	  <div class="panel-body">
			<?php if ($added_new_machine) { ?>
				<div class="alert alert-success" role="alert">New machine added successfully!</div>
			<?php } ?>

			<h2>My machines</h2>
			
			<table class='table'>
				<tr>
					<th>Machine ID</th>
					<th>Machine title</th>
				</tr>
				<?php foreach ($user_machines as $mcn_id => $mcn_title) { ?>
					<tr>
						<td><?=$mcn_id?></td>
						<td><?=htmlspecialchars($mcn_title)?></td>
					</tr>
				<?php } ?>
			</table>
			
			<h4>Add new machine</h4>
		
			<form method='post' action='<?=site_url('/machines/add_machine')?>'>

			  <div class="form-group">
				<label for="machine_title">Machine name</label>
				<input name='machine_title' type="text" class="form-control" id="machine_title" placeholder="new machine name">
			  </div>
			  <button type="submit" class="btn btn-default">Submit</button>			
			</form>
			
			
		
	  </div>
	</div>
</div>