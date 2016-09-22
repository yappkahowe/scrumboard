<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse-top" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <router-link class="navbar-brand cursive" to="/">{{ app_name() }}</router-link>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="navbar-collapse-top">
            <ul class="nav navbar-nav">
                <li><router-link to="/my-tasks">My Tasks</router-link></li>
                <li><router-link to="/my-teammates">My Teammates</router-link></li>
            </ul>

            <ul class="nav navbar-nav navbar-right">
                <li><router-link to="/my-profile"><img :src="user.avatar_url" class="profile-photo"> @{{ user.name }}</router-link></li>
            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>