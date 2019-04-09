<nav class="navbar bg-light  navbar-light flex-md-nowrap">
	<div class="container p-0">
		<a class="navbar-brand col-sm-3 col-md-2" href="/"><?= getenv('APP_NAME'); ?></a>
		<ul class="navbar-nav px-3">
			<?php if(Amber\Framework\Container\Facades\Auth::check()): ?>
				<li class="nav-item text-nowrap">
					<p><?= Amber\Framework\Container\Facades\Auth::name(); ?></p>
				</li>
				<li class="nav-item text-nowrap">
					<a class="nav-link" href="/logout">Sign out</a>
				</li>
			<?php else: ?>
				<li class="nav-item text-nowrap">
					<a class="nav-link" href="/login">Sign in</a>
				</li>
			<?php endif; ?>
		</ul>
	</div>
</nav>
