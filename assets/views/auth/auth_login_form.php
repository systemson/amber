<div class="col-sm-4 offset-sm-4 bg-light px-5 py-3">
	<skHasErrors>
		<skForeach="$erros as $error">
			<h1>Error <skEcho>$error</skEcho></h1>
		</skForeach>
	</skHasErrors>
	<form class="form-signin" action="/login" method="POST">


		<skCsrf>

		<h1 class="h3 mb-3 font-weight-normal">Please sign in</h1>
		<label for="email" class="sr-only">Email address</label>
		<input type="email" id="email" name="email" class="form-control" placeholder="Email address" required autofocus>
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
