<skHasErrors>
	<div class="col-sm-4 offset-sm-4 bg-light p-3">
	  	<div class="alert alert-danger" role="alert">
	  		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
    			<span aria-hidden="true">&times;</span>
  			</button>
        	<h4 class="alert-heading"><skTranslate>messages.errors</skTranslate>:</h4>
			<ul>
				<skForeach="$errors as $error">
					<li><skEcho>$error</skEcho></li>
				</skForeach>
			</ul>
		</div>
	</div>
</skHasErrors>

<div class="col-sm-4 offset-sm-4 bg-light px-5 py-3">
	<form class="form-signin" action="/login" method="POST">
		<skCsrf>

		<h1 class="h3 mb-3 font-weight-normal">Please sign in</h1>
		<label for="email" class="sr-only">Email address</label>
		<input type="text" id="email" name="email" class="form-control" placeholder="Email address" required autofocus>
		<label for="password" class="sr-only">Password</label>
		<input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
		<div class="checkbox mb-3">
			<label>
				<input type="checkbox" value="remember-me"> Remember me
			</label>
		</div>
		<button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>	
	</form>
</div>
