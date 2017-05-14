<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    ×</button>
                <h4 class="modal-title" id="myModalLabel">
                    Login</h4>
            </div>
            <?php
                if (isset($_POST['logout'])) {
                // logout button pressed
                    unset($_SESSION['logged_user']);
                    unset($_POST['logout']);
                }

                if (isset($_POST['login'])) {
                // logged_user not set, but email and password were entered
                    $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
                    require_once __DIR__.'/../../config.php';
                    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
                    $password = hash("sha256", ($_POST['password'] . SALT));
                    $result = $mysqli->query("SELECT password, userID FROM users WHERE email = '$email'");
                    $row = $result->fetch_row();

                    if ($row[0] === $password) {
                        $_SESSION['logged_user'] = $row[1];
                        unset($_POST['login']);
                        echo 
                            '<script type="text/javascript">
                                window.location ="' . DASHBOARD_URL . '";
                            </script>';
                            
                    } else {
            ?>
                        <script type='text/javascript'>
                            alert('Login failed. Try again.');
                            $('#loginModal').modal();
                        </script>
            <?php
                    }
                } 
            ?>
            <div class="modal-body">
                <div class="row">
                    <form role="form" class="form-horizontal" id='login-form' method='POST'>
                        <div class="form-group">
                            <label for="email" class="col-sm-2 control-label"> Email </label>
                            <div class="col-sm-10">
                                <div class="row">
                                    <div class="col-md-9">
                                        <input type="email" name="email" class="form-control" placeholder="Email" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1" class="col-sm-2 control-label"> Password </label>
                            <div class="col-sm-10">
                                <div class="row">
                                    <div class="col-md-9">
                                        <input type="password" name="password" class="form-control" placeholder="Password" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" id='LoginErrorMessage' hidden>
                            <div class="col-sm-5">
                            </div>
                            <div class="col-sm-4">
                                <p> Error! </p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                            </div>
                            <div class="col-sm-2">
                                    <button type="submit" name = "login" class="btn btn-primary btn-sm">
                                        Login</button>
                            </div>
                            <div class="col-sm-2">
                                    <button type="button" data-dismiss="modal" class="btn btn-default btn-sm">
                                        Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>