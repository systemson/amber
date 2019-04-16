<div class="col-sm-4 offset-sm-4 bg-light px-5 py-3">
	<form class="form-signin" action="/login" method="POST">
		<h1 class="h3 mb-3 font-weight-normal">Please sign in</h1>
		<label for="email" class="sr-only">Email address</label>
		<input type="email" id="email" name="email" class="form-control" placeholder="Email address" required autofocus>
		<label for="password" class="sr-only">Password</label>
		<input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
		<input type="hidden" name="_csrf" value="<?= Amber\Framework\Container\Facades\Csrf::token(); ?>">
		<div class="checkbox mb-3">
			<label>
				<input type="checkbox" value="remember-me"> Remember me
			</label>
		</div>
		<button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>	
	</form>
</div>
