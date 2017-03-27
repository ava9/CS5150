<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    ×</button>
                <h4 class="modal-title" id="myModalLabel">
                    Login</h4>
            </div>
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