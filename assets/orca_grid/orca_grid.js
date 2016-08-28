/**
 * JS functions for Orca_grid
 * Written by Yaron Kaplan (AKA Trevize)
 * 
 * 
 * Required to be shown in every page which has a grid.   
 * Assumes that jQuery was loaded by now. Yup
 * You have to call Orca_grid.init() after that.
 * 
 * (c) 2014 by 3fishMedia
 */

var Orca_grid = function ($) {
	
	// Public properties
	this.someTestProp = 'a';
	
	/**
	 * Sets a variable name for this grid - as an hidden input variable.
	 * Adds the input var if not already exists 
	 * @param thisContainer - jquery object - the div container of this grid
	 * @param varName - string - must not contain quotes or ugly characters!
	 * @param varValue - should be string
	 */
	var setVar =  function (thisContainer, varName, varValue) {
		varName = safeParamValue(varName);
		
		// Check if input already exists
		var existingInput = thisContainer.find("input.orca_grid_var[name='"+varName+"']");
		if (existingInput.length) {
			existingInput.val(varValue);
		} else {
			// We will have to add this input
			
			var newInput = $("<input type='hidden' class='orca_grid_var' />");
			newInput.prop("name", varName);
			newInput.val(varValue);
			thisContainer.prepend(newInput);
			thisContainer.prepend("\n");  // For easier debugging
		}
	}

	var getVar = function (thisContainer, varName) {
		varName = safeParamValue(varName);
		var existingInput = thisContainer.find("input.orca_grid_var[name='"+varName+"']");
		if (existingInput.length) {
			return existingInput.val();
		}
	}


	/**
	 * Calls the JS function linked to this grid which will cause the grid to show
	 * @param thisContainer - the jquery object holding the DIV which is the div container
	 */
	var loadGrid = function(thisContainer) {
		
		// Find what we have to load.. and some other vars
		contentUrl = thisContainer.find("input[name='content_url']").val();  
		contentDiv = thisContainer.find("div.orca_grid-content");  // Assume it's always there, as it is the container
		if (!contentUrl) {
			contentDiv.text("error: no content_url was found/defined for this grid."); 
			return;
		}
		
		var getParams = new Object();
		
		// Take all the state variables and pass them to the grid as GET parameters
		thisContainer.find("input.orca_grid_var").each( function() {
			getParams[$(this).prop("name")] = $(this).val();
		});
		
		
		//alert("content url: "+content_url);
		contentDiv.html("Loading...");
		// @TODO: add the current state as parameters..
		$.ajax({
			url: contentUrl,
			data: getParams,
			dataType: "json",
			success: function(result) {
				if (result.ok) {
					contentDiv.html(result.data);
					
					// Copy all the grid vars to the container,
					// in case some new vars were introduced or changed.
					if (result.grid_vars) {
						for (varName in result.grid_vars) {
							setVar(thisContainer, varName, result.grid_vars[varName]);
						}
					}
					
				} else {
					contentDiv.text("error: "+result.error_msg);
				}
			},
			error: function (jqXHR,  textStatus, errorThrown) {
				contentDiv.text("Error: "+jqXHR.responseText+" ("+textStatus+")");
			}
		});
		
	}


	/**
	 * This will change the 'page' var of the grid, and reload it
	 * The 'page' is determined by the 'rel' property of pagination control which was clicked  
	 */
	var setPage = function(thisContainer, newPage) {
		// Find the container of the grid, since everything is there.
		
		setVar(thisContainer, "page", newPage);
		loadGrid(thisContainer);
	}


	/**
	 * Before using some things as parameters, stupify them to prevent nasty attacks
	 * @param value - string
	 * 
	 */
	var safeParamValue = function(value) {
		value = String(value); // force to string?
		return value.replace(/[^A-Za-z0-9_-]/, "");  // Clear ANY thing which may not look like a variable name
	}	
	
	
	// Returned values:
	this.init = function(){	
	
		// Popuplate all the grids on the page
		$("div.orca_grid-container").each (  function(index, elem) {
			thisContainer = $(elem);
			loadGrid(thisContainer);
		});

		
		// Define some LIVE events for the controls which will appear inside the grid container
		
		// Button for changing pages
		$("div.orca_grid-container").on( 'click', 'button.orca_grid-set-page', function() {
			var thisContainer = $(this).parents("div.orca_grid-container");

			setPage( thisContainer, $(this).attr("rel") );  // prop doesn't work! had to use attr
		});

		// Set grid page when the user presses ENTER in the number input box
		$("div.orca_grid-container").on( 'keyup', 'input.orca_grid-current-page', function( event ) {
			if (event.which == 13) {
				var thisContainer = $(this).parents("div.orca_grid-container");
		
				// Since the user sees the page number as 1-based,
				// we need to do -1 to change it to 0-based (as it's stored internally)
				setPage( thisContainer, ( $(this).val() -1 ) ); 
			}
		});

		// Update RPP when the user clicks on the dropdown
		$("div.orca_grid-container").on( 'change', 'select.orca_grid-rpp', function (event) {
			var thisContainer=$(this).parents("div.orca_grid-container");
			
			setVar(thisContainer, 'rpp', $(this).val());
			setVar(thisContainer, 'page', 0); // reset the page
			loadGrid(thisContainer);
		});
		
		// Change sort order
		$("div.orca_grid-container").on( 'click', 'a.orca_grid-change-order', function (event) {
			event.preventDefault();
			
			var thisContainer=$(this).parents("div.orca_grid-container");
			

			var currentOrderField = getVar(thisContainer, 'order_field');
			var newOrderField = $(this).attr('rel');
			var currentOrderDir = getVar(thisContainer, 'order_dir');
			var newOrderDir = "A"; // default
			
			if (currentOrderField == newOrderField) {
				// The order field which was clicked is the current order field - so just change the direction
				newOrderDir = (currentOrderDir == 'A' ? 'D' : 'A');
			} 
			
			setVar(thisContainer, 'order_field', newOrderField);
			setVar(thisContainer, 'order_dir', newOrderDir);
			loadGrid(thisContainer);
		});
		
		// Apply filters
		$("div.orca_grid-container").on( 'click', "button.orca_grid-apply-filters", function (event) {
			var thisContainer=$(this).parents("div.orca_grid-container");

			// Collect all data from filter inputs into a dictionary, to be sent afterwards to the grid rendering function 
			// (See orca_grid.txt - search for 'filters')
			var filterValues = new Object();
			var someValueFound = false;
			thisContainer.find(".orca_grid-filter").each (function () {
				var filterName = String($(this).prop("name"));
				//alert(filterName);
				// Each filter may have several fields, for example: 'min' and 'max'
				var filterFieldName = String($(this).attr("rel"));
				//alert (filterFieldName );
				if (!filterValues[filterName]) {
					filterValues[filterName] = new Object;
				}
				filterValues[filterName][filterFieldName] = $(this).val();
				if ($(this).val().length) {
					someValueFound = true;  // Currently this var is not used 
				}
			});
			//alert (JSON.stringify(filterValues));
			
			// Put that dictionary inside the grid vars - always (even if all values are empty)
			setVar(thisContainer, 'filters', JSON.stringify(filterValues));
			
			// Clear the record count - if we change the filters we need to count them again
			setVar(thisContainer, 'record_count', '');
			setVar(thisContainer, 'page', '');
			
			// Now let's load it again
			loadGrid(thisContainer);
		});
		
		
		// Reset filters (restore them to defaults)
		$("div.orca_grid-container").on( 'click', "button.orca_grid-reset-filters", function (event) {
			var thisContainer=$(this).parents("div.orca_grid-container");

			// Old method: will not work correctly (will use empty filter values, and I want to use the default values, if any)
			//thisContainer.find(".orca_grid-filter").val(""); // Just clear all the filters (
			//thisContainer.find("button.orca_grid-apply-filters").click();  // And now apply the new filters

			// The 'filter' var will be empty - this will make the grid use the default filter values (or empty values if not given)
			setVar(thisContainer, 'filters', '');
			
			// Clear the record count - if we change the filters we need to count them again
			setVar(thisContainer, 'record_count', '');
			setVar(thisContainer, 'page', '');
			
			// Now let's load it again
			loadGrid(thisContainer);
		});
		
		// When changing a filter dropdown, refresh the grid
		$("div.orca_grid-container").on( 'change', 'select.orca_grid-filter', function(event) {
			var thisContainer=$(this).parents("div.orca_grid-container");
			
			thisContainer.find("button.orca_grid-apply-filters").click();
		});
		
		// Show filters (if hidden) and hide the "show filters" row
		$("div.orca_grid-container").on( 'click', "button.orca_grid-show-filters", function(event) {
			var thisContainer=$(this).parents("div.orca_grid-container");
			
			thisContainer.find("tr.orca_grid-show-filters-row").hide();
			thisContainer.find("tr.orca_grid-filters-row").show();
			// Remember this for the next clicks on the grid
			setVar(thisContainer, 'hide_filters', 0);
		});
		
		
		
	}  // init function
	
	return this;
	
	
}(jQuery)  // var Orca_grid


