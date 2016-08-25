<div class='orca_grid-container'>
	<input type='hidden' name='content_url' value='<?=htmlspecialchars($grid_content_function_url, ENT_QUOTES)?>' />
	<? // Take all the grid parameters (which are taken from a JSON object from the GET params) ?>
	<? // and copy them to here ?>
	<?//my_print_r($grid_params)?>
	<? foreach ($grid_vars as $var_name => $val) { ?>
		<? $val = (string)$val; ?>
		<input type='hidden' class='orca_grid_var' name="<?=htmlspecialchars($var_name, ENT_QUOTES)?>" value='<?=htmlspecialchars($val, ENT_QUOTES)?>' />
	<? } ?>
	<input class='orca_grid_var' type='hidden' name='grid_name' value='<?=htmlspecialchars($grid_name, ENT_QUOTES)?>' />
	<div class='orca_grid-content'>
		Hello. I am the grid content!  <? // @TODO: erase this text ?>
	</div>
</div>   <!-- end orca_grid_container DIV -->