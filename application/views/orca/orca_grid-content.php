<?
/**
 * Grid content-  
 * contains the controls for changing page/RPP  (to be added soon)
 * and the grid data itself
 * 
 * @see also - orca_grid.js and orca_grid.css
 */
?>
<div class='orca_grid-pagination'>
	<?
	// We have to build a little complicated string, out of language strings
	
	// Start with the page controls
	// Check if we are in the first page...
	
	$paging_controls_html = "";
	
	$is_disabled =  $page == 0 ? " disabled='disabled' " : "";
	$paging_controls_html .= "<button rel='0' class='orca_grid-set-page' $is_disabled>&lt;&lt;</button>";
	$paging_controls_html .= "<button rel='".($page-1)."' class='orca_grid-set-page' $is_disabled>&lt</button>";
	
	$paging_controls_html .= "<input type='text' size='4' class='orca_grid-current-page' value='".($page+1)."' />";

	$is_disabled =  (($page+1) >= $page_count)   ? " disabled='disabled' " : "";
	$paging_controls_html .= "<button rel='".($page+1)."' class='orca_grid-set-page' $is_disabled>&gt;</button>";
	$paging_controls_html .= "<button rel='".($page_count-1)."' class='orca_grid-set-page' $is_disabled>&gt;&gt;</button>";
	
	
	
	$html = Orca_grid::lang_r2('orca_grid__page_[[x]]_of_[[page_count]]', array(
			'[[x]]' => $paging_controls_html,
			'[[page_count]]' => $page_count	
		) 
	);
	

	$html .= " | ";
	
	
	// Dropdown of records per page (including 'all')
	$dropdown_html = form_dropdown('orca_grid_rpp', $rpp_array, $rpp, "class='orca_grid-rpp'");
	
	$html .= Orca_grid::lang_r2('orca_grid__view_[[dropdown]]_records',  '[[dropdown]]', $dropdown_html);
	
	$html .= " | ".lang_r2('orca_grid__found_total_[[record_count]]_records', '[[record_count]]', $record_count);
	
			
	echo $html;  // Finally
	?>
</div>

<table class='orca_grid-table' border>
	<thead>
		<? // Display the table title - the fields ?>
		<tr class='orca_grid-order-row'>
			<? foreach ($grid_params['fields'] as $field_name => $field_options) { ?>
				<? 
				$title = arr_get_value($field_options, 'title', $field_name);
				$sorting_link_start = $sorting_link_end = $sort_dir_mark = "";
				if ($other_order_field = arr_get_value($field_options, 'order_field')) {
					
					// Find the name of the order field to use:  if it quals TRUE then use the current field name instead
					$this_order_field_name = ($other_order_field == true ? $field_name : $other_order_field_name);

					$sorting_link_start = "<a class='orca_grid-change-order' rel='".htmlspecialchars($this_order_field_name, ENT_QUOTES)."' href='#'>";
					$sorting_link_end = "</a>";
					
					
					if ($this_order_field_name === $order_field) {
						// This is the current ordering field - so mark it
						$sort_dir_mark = ($order_dir == 'A' ? ' &uArr; ' : ' &dArr; '); 	
					}
				}
				$th_style = arr_get_value($field_options, 'th_style') ? " style='{$field_options['th_style']}' " : '';
				$th_class = arr_get_value($field_options, 'html_class') ? " class='".htmlspecialchars($field_options['html_class'], ENT_QUOTES)."' " : '';
				?>
				<th<?=$th_style.$th_class?>><?=$sorting_link_start . htmlspecialchars($title) . $sort_dir_mark . $sorting_link_end?></th>
			<? } ?>
		</tr>
		<? // Display the filters, if applicable ?>
		<? if ($enable_filters_row) { ?>
			<tr class='orca_grid-filters-row' <?=$hide_filters ? "style='display :none'" : ""?> valign='bottom'>
			<?
			// Figure the current filters values
			// See orca_grid.txt for format notes (search for 'filters')
			$filters_input = json_decode(arr_get_value($grid_vars, 'filters'), true);
			
			//my_print_r($filters);
			 
			?>
			<? foreach ($grid_params['fields'] as $field_name => $field_options) { ?>
				<? 
				$filter_param = arr_get_value($field_options, 'filter', array()); 
				$filter_type = arr_get_value($filter_param, 'type'); 
				// For values such as 'value', 'min' and 'max':
				$this_filter_values = arr_get_value($filters_input, $field_name, array());
				
				// Filter value (for dropdown and search) - 
				$value = arr_get_value($this_filter_values, 'value');
				// If no value, use the default value
				if (!isset($filters_input[$field_name])) {
					$value = arr_get_value($filter_param, 'default_value');
				}
				$td_class = arr_get_value($field_options, 'html_class') ? " class='".htmlspecialchars($field_options['html_class'], ENT_QUOTES)."' " : '';
				?>
				<td<?=$td_class?>>
				<? if ($filter_type) { ?>
					
					<? if ($filter_type == "equals" || $filter_type == "contains") { ?>
						<?=$filter_type == 'contains' ? l("orca_grid__label__contains") : l("orca_grid__label__equals")?>: 
						<br /> 
						<? // show the string... ?>
						<? // rel='value' is not a mistake! it means that that this is the filter value  ?>
						<? // (there could be other filter components, such as 'min' or 'max' or 'contains_or_equals') ?>
						<?=form_input($field_name, $value, "class='orca_grid-filter' rel='value' ")?>	
					<? } elseif ($filter_type == 'dropdown') { ?>
						<?=form_dropdown($field_name, arr_get_value($filter_param, 'values', array()), $value, 
						"class='orca_grid-filter' rel='value' ")?> 
					<? } ?>
					
				<? } ?> 
				<? if ($field_name == $column_for_filters_control_buttons) { ?>
					<? if (arr_get_value($grid_params, 'filters_control_new_line')) { ?><br /><? } ?>
					<button class='orca_grid-apply-filters'><?=l("orca_grid__button__apply_filters")?></button>
					<button class='orca_grid-reset-filters'><?=l("orca_grid__button__reset_filters")?></button>	
				<? } ?>
				</td>
			<? } // foreach grid field ?>
			</tr> 
			<? if ($hide_filters) { ?>
				<tr class='orca_grid-show-filters-row'>
				<? // Add an empty row containing only one TD with a button titled "show filters" ?>
				<? foreach ($grid_params['fields'] as $field_name => $field_options) { ?>
					<? 
					$td_class = arr_get_value($field_options, 'html_class') ? " class='".htmlspecialchars($field_options['html_class'], ENT_QUOTES)."' " : '';
					?>
					<td<?=$td_class?>>
					<? if ($field_name == $column_for_filters_control_buttons) { ?>
						<? if (arr_get_value($grid_params, 'filters_control_new_line')) { ?><br /><? } ?>
						<button class='orca_grid-show-filters'><?=l("orca_grid__button__show_filters")?></button>	
					<? } ?>
					</td>				
				<? } ?>
				
				
			<? } ?>
			
		<? } // enable filters row ?>
	</thead>
	<tbody>
		<? // Display actual data ?>
		<? $counter = 0; ?>
		<? foreach ($record_data as $row) { ?>
			<? 
			$counter++;
			?>
			
			<tr <?=$counter % 2 ?  "" : " class='alternate' "?>>
				<? foreach ($grid_params['fields'] as $field_name => $field_options) { ?>
					<? 
					$val = arr_get_value($row, $field_name);
					$type = arr_get_value($field_options, 'type');

					// Check if we have to put extra data in the TD element of current field:
					// TD id:
					$extra_td_html_arr = array();
					if (arr_get_value($field_options, 'td_id'))  {
						$extra_td_html_arr []= "id='".htmlspecialchars(  replace_bracketed_strings_with_fields($field_options['td_id'] , $row) )."'";
					}
					
					// Class: Check for a 'global' class for column, and a specific class for this TD
					$class_arr = array();
					if (arr_get_value($field_options, 'td_class'))  {
						$class_arr []= htmlspecialchars(  replace_bracketed_strings_with_fields($field_options['td_class'] , $row) , ENT_QUOTES);
					}
					if (arr_get_value($field_options, 'html_class')) {
						$class_arr []= htmlspecialchars($field_options['html_class'], ENT_QUOTES);
					}
					
					//my_print_r($class_arr);
					
					if ($class_arr) {
						$extra_td_html_arr []= "class='".implode(" ",$class_arr)."'";
					}
					
					$extra_td_html = $extra_td_html_arr ? " ".implode(" ", $extra_td_html_arr) : "";
					
					
					?>

					<? if ($type == 'key_value') { ?>
						<? // if [[val]] shows then something is wrong ?>
						<td<?=$extra_td_html?>><?=arr_get_value(arr_get_value($field_options, 'values'), $val, "[".$val."]")?></td>
					<? } elseif ($type == 'url') { ?>
						<? 
						$url = '<a href="';
						$url .= replace_bracketed_strings_with_fields(arr_get_value($field_options, 'url',  "url is missing!"), $row, true);
 					  	if (arr_get_value($field_options, 'url_new_window')) {
							$url .= '" target="_blank"';
						}
						$url .= '">';
 					  	$url .= replace_bracketed_strings_with_fields(arr_get_value($field_options, 'url_caption', "url caption is missing!"), $row);
						$url .= "</a>";
						?>
						<td<?=$extra_td_html?>><?=$url?></td>
					<? } elseif ($type == 'callback') { ?>
						<? $val = call_user_func($field_options['field_callback_function'], $row); ?>
						<td<?=$extra_td_html?>><?=$val?></td>
					<? } elseif ($type == 'custom_text') { ?>
						<? $val = replace_bracketed_strings_with_fields(arr_get_value($field_options, 'custom_text'), $row); ?>
						<td<?=$extra_td_html?>><?=$val?></td>
					<? } elseif ($type == 'date' || $type == 'datetime') { ?>
						<? 
						if ($type == 'date') {
							// Take off the time, if any
							$val = arr_get_value(explode(' ', $val), 0);
						}
						if (isset($grid_params['date_format_callback'])) {
							$val = call_user_func($grid_params['date_format_callback'], $val);
						}
						?>
						<td<?=$extra_td_html?>><?=$val?></td>
					<? } else { ?>
						<? 
						if (!arr_get_value($field_options, 'raw'))  $val = htmlspecialchars($val, ENT_QUOTES);
						?>
						<td<?=$extra_td_html?>><?=$val?></td>
					<? } ?>
				<? } ?>
			</tr>
		<? } ?>
	</tbody>
</table>

<?
/**
 * Process replaceables from the URL - fields inside double brackes, 
 * for example "Edit the user [[usr_firstname]] [[usr_lastname]]" gets replaced to "Edit the user Shuki Havakuku"
 * (assuming $row contains the fields 'usr_fistname' and 'usr_lastname')
 * @param string $string
 * @param array  $row
 * @param boolean $use_urlencode - call urlencode for every string which is changed 
 * 	(good if you intend to use the string as an URL) 
 * @return string
 */
function replace_bracketed_strings_with_fields($string, $row, $use_urlencode = false) {
	// 
	$string = preg_replace_callback("|\[\[.*?\]\]|" ,
		function ($matches) use ($row, $use_urlencode) {
			// Take the field name out of the [[ ]]
			$field_name = substr($matches[0], 2, -2); 
			$ret = arr_get_value($row, $field_name, '['.$field_name.']');   // [field_name] is returned if that field is not found!
			if ($use_urlencode) { $ret = urlencode($ret); }
			return $ret;
		},
		$string
	);

	return $string;

}
?>