<div class="row-fluid thumbnail">
	<div id="logo">
		<img src="<?=base_url('assets/img/logo.png')?>" alt="Logo">
	</div>
	<form class="form-horizontal"  method="POST">
		<?=form_hidden("go", 1)?>
	  <fieldset>
	    <div id="legend">
	    	<div class="page-header">
	      		<h1><?=lang('login_heading')?></h1>
	      		<small class="label"><?=lang('login_subheading');?></small>
	      	</div>
	      	
	    </div>
	    <div class="control-group">
	    	<div id="infoMessage" class="label label-warning"><?php echo $message;?></div>
	    </div>
	    
	    <div class="control-group">
	      <!-- Username -->
	      <label class="control-label"  for="username">Username</label>    
	      <div class="controls">
	        <?=form_input('username', $this->input->post('username', true));?>
	      </div>
	    </div>
	 
	    <div class="control-group">
	      <!-- Password-->
	      <label class="control-label" for="password">Password</label>
	      <div class="controls">
	        <?=form_password('password', $this->input->post('password', true));?>
	      </div>
	    </div>
	 
	 
	    <div class="control-group">
	      <!-- Button -->
	      <div class="controls">
	        <button class="btn btn-success">Login</button>
	      </div>
	    </div>
	  </fieldset>
	</form>
	<p>
		<a href="forgot_password">
			<button class="btn">Forgot your password?</button>
		</a>
		
	</p>

</div>