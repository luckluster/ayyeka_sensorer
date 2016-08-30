// Global JS code

// Init code
$(document).ready(function() {
	// date picker material - show date picker on inputs with this class:
	$(".jqDatePicker").datepicker({
		dateFormat: "yy-mm-dd"
	});

	// For all the pages which use the grid.
	Orca_grid.init();
});
