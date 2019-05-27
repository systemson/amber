<nav class="navbar bg-light  navbar-light flex-md-nowrap">
    <div class="container p-0">
        <a class="navbar-brand col-sm-3 col-md-2" href="/"><skAppname></a>
        <ul class="navbar-nav px-3">
            <skAuthCheck>
                <li class="nav-item text-nowrap">
                    <form action="/logout" method="POST">
                        <skCsrf>
                        <button class="btn btn-sm btn-danger">Sign out</button>
                    </form>
                </li>
            <skElse>
                <li class="nav-item text-nowrap">
                    <a class="btn btn-sm btn-primary" href="/login">Sign in</a>
                </li>
            </skAuthCheck>
        </ul>
    </div>
</nav>
