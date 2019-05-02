<nav class="navbar bg-light  navbar-light flex-md-nowrap">
    <div class="container p-0">
        <a class="navbar-brand col-sm-3 col-md-2" href="/"><sketch-appname></a>
        <ul class="navbar-nav px-3">
            <?php if (Amber\Framework\Container\Facades\Auth::check()) : ?>
                <li class="nav-item text-nowrap">
                    <form action="/logout" method="POST">
                        <input type="hidden" name="_csrf" value="<?= Amber\Framework\Container\Facades\Csrf::token(); ?>">
                        <button class="btn btn-sm btn-danger">Sign out</button>
                    </form>
                </li>
            <?php else : ?>
                <li class="nav-item text-nowrap">
                    <a class="btn btn-sm btn-primary" href="/login">Sign in</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
