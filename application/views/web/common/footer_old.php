<? if (!arr_get_value($params, 'minimal_headerfooter')) { ?>



		</div>  <? // end of div class: "page-content" from header ?>
	</div>
	<!-- END CONTENT -->
</div>
<!-- END CONTAINER -->
<!-- BEGIN FOOTER -->
<div class="footer">
	<div class="footer-inner">
		<?=date("Y")?> &copy; ORCA by 3fishmedia
	</div>
	<div class="footer-tools">
		<span class="go-top">
			<i class="fa fa-angle-up"></i>
		</span>
	</div>
</div>

<? }  // minimal_headerfooter ?>
<!-- END FOOTER -->
<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
    <script src="<?=base_url('/assets/admin/plugins/respond.min.js')?>"></script>
    <script src="<?=base_url('/assets/admin/plugins/excanvas.min.js')?>"></script> 
    <![endif]-->
<script src="<?=base_url('assets/admin/plugins/jquery-1.10.2.min.js')?>" type="text/javascript"></script>
<script src="<?=base_url('assets/admin/plugins/jquery-migrate-1.2.1.min.js')?>" type="text/javascript"></script>
<!-- IMPORTANT! Load jquery-ui-1.10.3.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
<script src="<?=base_url('assets/admin/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js')?>" type="text/javascript"></script>
<script src="<?=base_url('assets/admin/plugins/bootstrap/js/bootstrap.min.js')?>" type="text/javascript"></script>
<script src="<?=base_url('assets/admin/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js')?>" type="text/javascript"></script>
<script src="<?=base_url('assets/admin/plugins/jquery-slimscroll/jquery.slimscroll.min.js')?>" type="text/javascript"></script>
<script src="<?=base_url('assets/admin/plugins/jquery.blockui.min.js')?>" type="text/javascript"></script>
<script src="<?=base_url('assets/admin/plugins/jquery.cokie.min.js')?>" type="text/javascript"></script>
<script src="<?=base_url('assets/admin/plugins/uniform/jquery.uniform.min.js')?>" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="<?=base_url('assets/admin/plugins/select2/select2.min.js')?>"></script>
<script type="text/javascript" src="<?=base_url('assets/admin/plugins/data-tables/jquery.dataTables.js')?>"></script>
<script type="text/javascript" src="<?=base_url('assets/admin/plugins/data-tables/DT_bootstrap.js')?>"></script>
<script type="text/javascript" src="<?=base_url('assets/admin/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js')?>"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="<?=base_url('assets/admin/scripts/core/app.js')?>"></script>
<script src="<?=base_url('assets/admin/scripts/core/datatable.js')?>"></script>
<script src="<?=base_url('assets/admin/scripts/custom/ecommerce-orders.js')?>"></script>
<script src="<?=base_url('assets/orca_grid/orca_grid.js')?>"></script>
<script src="<?=base_url('assets/js/json2.js')?>"></script>
<?
// Load additional scripts if specified in params
if (isset($params['scripts']) && is_array($params['scripts'])) {
	foreach ($params['scripts'] as $script_details) {
		echo "<script src='".arr_get_value($script_details, 'script')."'></script>\n";
	}
} 
?>
<!-- END PAGE LEVEL SCRIPTS -->
<script>
jQuery(document).ready(function($) {    
	App.init();
	//EcommerceOrders.init();
	Orca_grid.init();

	<?
	// Echo additional init lines if specified in params
	if (isset($params['scripts']) && is_array($params['scripts'])) {
		foreach ($params['scripts'] as $script_details) {
			if (isset($script_details['init_line'])) {
				echo $script_details['init_line']."\n";
			}
		}
	} 
	?>
           
});
</script>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>