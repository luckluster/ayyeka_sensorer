<div id='login'>
<form method="POST">


	<div class="container">
		<div class="row">
			<div class="col-md-offset-5 col-md-3">
				<div class="form-login">
				<h4>Welcome back.</h4>
				<?php if ($message) { ?>
				<div class="alert alert-danger" role="alert"><?=$message?></div>
				<? } ?>
				
				<?=form_input('username', $this->input->post('username', true), 'id="userName" class="form-control input-sm chat-input" placeholder="username"');?>
				</br>
				<?=form_password('password', $this->input->post('password', true), ' id="userPassword" class="form-control input-sm chat-input" placeholder="password"');?>
				</br>
				<div class="wrapper">
				<span class="group-btn">     
					<input type='submit' class="btn btn-primary btn-md" name='login' value='login'>
				</span>
				</div>
				</div>
			
			</div>
		</div>
	</div>

</form>
</div>


<?

// old
/*

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
*/
