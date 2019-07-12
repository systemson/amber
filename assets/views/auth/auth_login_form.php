<skHasErrors>
	<div class="col-sm-4 offset-sm-4 bg-light p-3">
	  	<div class="alert alert-danger" role="alert">
	  		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
    			<span aria-hidden="true">&times;</span>
  			</button>
        	<h4 class="alert-heading"><skTranslate>auth.errors</skTranslate>:</h4>
				<skShowErrors>
					<!--<p><b><skEcho>ucfirst($input)</skEcho></b>:<br><skRaw>implode('.<br>', (array) $error)</skRaw>.</p>-->
				</skShowErrors>
		</div>
	</div>
</skHasErrors>

<div class="col-sm-4 offset-sm-4 bg-light px-5 py-3">
	<form class="form-signin" action="/login" method="POST">
		<skCsrf>

		<h1 class="h3 mb-3 font-weight-normal"><skTranslate>auth.please-sign-in</skTranslate></h1>
		<label for="email" class="sr-only"><skTranslate>auth.email</skTranslate></label>
		<input type="text" id="email" name="email" class="form-control" placeholder="<skTranslate>auth.email</skTranslate>" required autofocus>
		<label for="password" class="sr-only"><skTranslate>auth.password</skTranslate></label>
		<input type="password" id="password" name="password" class="form-control" placeholder="<skTranslate>auth.password</skTranslate>" required>
		<div class="checkbox mb-3">
			<label>
				<input type="checkbox" value="remember-me"> <skTranslate>auth.remember-me</skTranslate>
			</label>
		</div>
		<button class="btn btn-lg btn-primary btn-block" type="submit"><skTranslate>auth.sign-in</skTranslate></button>	
	</form>
</div>
