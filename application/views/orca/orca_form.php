	<div class="tab-content no-space">
		<div class="tab-pane active" id="tab_general">
			<div class="form-body" id="<?php echo $form->form_id; ?>">
			
				<?=form_open_multipart($this->uri->uri_string());?>
			
				<?php if (validation_errors()) { ?>
				<div class="og-error ui-state-error ui-corner-all">
				    <ul>
				<?php echo validation_errors(); ?>
				    </ul>
				</div>
				<?php } ?>
				
				
				<? foreach ($form->fields as $key => $column) {?>
					<div class="form-group">
						<label class="col-md-2 control-label"><?=arr_get_value($column,'header');?>
						<? if (arr_get_value($column,'required')){?>
							<span class="required">*</span>						
						<? } ?>
						</label>
						<div class="col-md-10">
						<?
				        switch (arr_get_value($column,'form_control')){
				            // Short text input
				            case 'text':
				                echo 	form_input('og_field_' . $key, $form->formdata[$key],
				                				   'id="og_field_' . $key . '" class="og-short form-control '
				                				   .arr_get_value($column,'class').'"') . "\n";
				            break;
				            // Checkbox
				            case 'checkbox':
				                echo 	'<input class="og-checkbox" type="checkbox"'.
				                			'id="og_field_' . $key . '" name="og_field_' . $key 
				                			. '" value="1"' . ($form->formdata[$key] ? ' checked="checked"' : '')  
				                			. 'class="'.arr_get_value($column,'class').'" />' . "\n";
				            break;
				
				            // Dropdown - @todo (complete $form->formdata[$key]['options'])
				            case 'dropdown':
				                echo 	form_dropdown('og_field_' . $key, $form->formdata[$key]['options'], 
				                					  $form->formdata[$key], 'id="og_field_' . $key 
				                					  . '" class="og-long form-control '.arr_get_value($column,'class').'"') . "\n";
				            break;
				
				            // Text
				            case 'textarea':
				                echo 	form_textarea('og_field_' . $key, $form->formdata[$key],
				                		 			  'id="og_field_' . $key . '" class="form-control '
				                		 			  .arr_get_value($column,'class').'"') . "\n";
				            break;
				
				            // Date
				            case 'datepicker':
				                echo 	form_input('og_field_' . $key, $form->formdata[$key],
				                				   'id="og_field_' . $key . '" class="og-long og-datepicker form-control '
				                				   .arr_get_value($column,'class').'" data-og-date-format="'.$column['date_format'] . '"') . "\n";
				            break;
				
				            // Datetime
				            case 'datetimepicker':
				                echo 	form_input('og_field_' . $key, $form->formdata[$key], 
				                				   'id="og_field_' . $key . '" class="og-long og-datetimepicker form-control '
				                				   .arr_get_value($column,'class').'" data-og-date-format="' .  $column['date_format'] . '" 
				                				   data-og-time-format="' . $column['time_format'] . '"') . "\n";
				            break;
				
				            // Time
				            case 'timepicker':
				                echo 	form_input('og_field_' . $key, $form->formdata[$key],
				                				   'id="og_field_' . $key . '" class="og-long og-timepicker form-control '
				                				   .arr_get_value($column,'class').'" data-og-time-format="' . $column['time_format'] . '"') . "\n";
				            break;
				
				            // File upload - @todo
				            case 'file':
					            echo '<input type="file" name="og_field_' . $key . '" id="og_field_' . $key . '" />' . "\n";
				                echo '<input type="hidden" value="' . $form->formdata[$key] . '" name="og_field_' . $key . '" />';
				                if ($form->formdata[$key])
				                {
				                    echo '<table cellpadding="0" cellspacing="0" class="og-files"><tr>';
				                    echo '<td class="ui-widget-content">' . $form->formdata[$key] . '</td>';
				                    echo '<td class="ui-widget-content">'
				                    	 . '<input type="submit" value="1" name="og_delete_file_' . $key . '" class="ui-icon ui-icon-trash og-icon-button" />'
				                    	 . '</td>';
				                    echo '</tr></table>' . "\n";
				                }
				            break;
				        }//switch?>
				        </div>
				    <? }//foreach?>

			    <?php echo form_submit('og_form_submit', lang('og_save'), 'class="og-button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"'); ?>
			    <?php echo form_submit('og_form_cancel', lang('og_cancel'), 'class="og-button og-form-cancel ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"'); ?>
			    <div class="og-clear"></div>
			
			    <?php echo form_close(); ?>
				
				
								
				
				
				<!-- EXAMPLES
				<div class="form-group">
					<label class="col-md-2 control-label">Name:
					<span class="required">
						 *
					</span>
					</label>
					<div class="col-md-10">
						<input type="text" class="form-control" name="product[name]" placeholder="">
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-2 control-label">Description:
					<span class="required">
						 *
					</span>
					</label>
					<div class="col-md-10">
						<textarea class="form-control" name="product[description]"></textarea>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-2 control-label">Short Description:
					<span class="required">
						 *
					</span>
					</label>
					<div class="col-md-10">
						<textarea class="form-control" name="product[short_description]"></textarea>
						<span class="help-block">
							 shown in product listing
						</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-2 control-label">Categories:
					<span class="required">
						 *
					</span>
					</label>
					<div class="col-md-10">
						<div class="form-control height-auto">
							<div class="scroller" style="height:275px;" data-always-visible="1">
								<ul class="list-unstyled">
									<li>
										<label><input type="checkbox" name="product[categories][]" value="1">Mens</label>
										<ul class="list-unstyled">
											<li>
												<label><input type="checkbox" name="product[categories][]" value="1">Footwear</label>
											</li>
											<li>
												<label><input type="checkbox" name="product[categories][]" value="1">Clothing</label>
											</li>
											<li>
												<label><input type="checkbox" name="product[categories][]" value="1">Accessories</label>
											</li>
											<li>
												<label><input type="checkbox" name="product[categories][]" value="1">Fashion Outlet</label>
											</li>
										</ul>
									</li>
									<li>
										<label><input type="checkbox" name="product[categories][]" value="1">Football Shirts</label>
										<ul class="list-unstyled">
											<li>
												<label><input type="checkbox" name="product[categories][]" value="1">Premier League</label>
											</li>
											<li>
												<label><input type="checkbox" name="product[categories][]" value="1">Football League</label>
											</li>
											<li>
												<label><input type="checkbox" name="product[categories][]" value="1">Serie A</label>
											</li>
											<li>
												<label><input type="checkbox" name="product[categories][]" value="1">Bundesliga</label>
											</li>
										</ul>
									</li>
									<li>
										<label><input type="checkbox" name="product[categories][]" value="1">Brands</label>
										<ul class="list-unstyled">
											<li>
												<label><input type="checkbox" name="product[categories][]" value="1">Adidas</label>
											</li>
											<li>
												<label><input type="checkbox" name="product[categories][]" value="1">Nike</label>
											</li>
											<li>
												<label><input type="checkbox" name="product[categories][]" value="1">Airwalk</label>
											</li>
											<li>
												<label><input type="checkbox" name="product[categories][]" value="1">Kangol</label>
											</li>
										</ul>
									</li>
								</ul>
							</div>
						</div>
						<span class="help-block">
							 select one or more categories
						</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-2 control-label">Available Date:
					<span class="required">
						 *
					</span>
					</label>
					<div class="col-md-10">
						<div class="input-group input-large date-picker input-daterange" data-date="10/11/2012" data-date-format="mm/dd/yyyy">
							<input type="text" class="form-control" name="product[available_from]">
							<span class="input-group-addon">
								 to
							</span>
							<input type="text" class="form-control" name="product[available_to]">
						</div>
						<span class="help-block">
							 availability daterange.
						</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-2 control-label">Tax Class:
					<span class="required">
						 *
					</span>
					</label>
					<div class="col-md-10">
						<select class="table-group-action-input form-control input-medium" name="product[tax_class]">
							<option value="">Select...</option>
							<option value="1">None</option>
							<option value="0">Taxable Goods</option>
							<option value="0">Shipping</option>
							<option value="0">USA</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-2 control-label">Status:
					<span class="required">
						 *
					</span>
					</label>
					<div class="col-md-10">
						<select class="table-group-action-input form-control input-medium" name="product[status]">
							<option value="">Select...</option>
							<option value="1">Published</option>
							<option value="0">Not Published</option>
						</select>
					</div>
				</div>
			</div>
		</div>
		<div class="tab-pane" id="tab_meta">
			<div class="form-body">
				<div class="form-group">
					<label class="col-md-2 control-label">Meta Title:</label>
					<div class="col-md-10">
						<input type="text" class="form-control maxlength-handler" name="product[meta_title]" maxlength="100" placeholder="">
						<span class="help-block">
							 max 100 chars
						</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-2 control-label">Meta Keywords:</label>
					<div class="col-md-10">
						<textarea class="form-control maxlength-handler" rows="8" name="product[meta_keywords]" maxlength="1000"></textarea>
						<span class="help-block">
							 max 1000 chars
						</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-2 control-label">Meta Description:</label>
					<div class="col-md-10">
						<textarea class="form-control maxlength-handler" rows="8" name="product[meta_description]" maxlength="255"></textarea>
						<span class="help-block">
							 max 255 chars
						</span>
					</div>
				</div>
				-->
				
			</div>
		</div>
	</div>