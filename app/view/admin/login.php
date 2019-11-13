<div class="card">
    <div class="card-header">Login</div>
    <div class="card-body">
        <form action="<?= $link['controller'] ?>/login" method="POST">
            <div class="form-group row">
                <label for="email_address" class="col-md-4 col-form-label text-md-right">Username</label>
                <div class="col-md-6">
                <input type="text" id="username" value="<?= $_POST['username'] ?>" class="form-control" name="username" required autofocus>
                </div>
            </div>

            <div class="form-group row">
                <label for="password" class="col-md-4 col-form-label text-md-right">Password</label>
                <div class="col-md-6">
                    <input type="password" id="password" name="password" class="form-control" name="password" required>
                </div>
            </div>

            <div class="col-md-6 offset-md-4">
                <button type="submit" name="submit" class="btn btn-primary">Login</button>
            </div>
        </div>
    </form>
</div>
