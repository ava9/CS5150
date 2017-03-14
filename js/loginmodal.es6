function writeloginmodal() {
  var loginmodalhtml = `<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    ×</button>
                <h4 class="modal-title" id="myModalLabel">
                    Login/Register</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs">
                            <li class="active"> <a href="#Login" data-toggle="tab">Login</a> </li>
                            <li> <a href="#Registration" data-toggle="tab">Registration</a> </li>
                        </ul>
                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div class="tab-pane active" id="Login">
                                <form role="form" class="form-horizontal">
                                    <div class="form-group">
                                        <label for="email" class="col-sm-2 control-label"> Email </label>
                                    <div class="col-sm-10">
                                        <div class="row">
                                            <div class="col-md-9">
                                                <input type="email" id="email" class="form-control" placeholder="Email" />
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1" class="col-sm-2 control-label"> Password </label>
                                    <div class="col-sm-10">
                                        <div class="row">
                                            <div class="col-md-9">
                                                <input type="password" class="form-control" placeholder="Password" />
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-2"> </div>
                                        <div class="col-sm-10">
                                            <button type="submit" class="btn btn-primary btn-sm"> Submit </button>
                                            <a href="javascript:;"> Forgot your password? </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane" id="Registration">
                                <form role="form" class="form-horizontal">
                                    <div class="form-group">
                                        <label for="name" class="col-sm-2 control-label"> Name </label>
                                        <div class="col-sm-10">
                                            <div class="row">
                                                <div class="col-md-9">
                                                    <input type="text" class="form-control" placeholder="Name" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                    <div class="form-group">
                                        <label for="email" class="col-sm-2 control-label"> Email </label>
                                        <div class="col-sm-10">
                                        <div class="row">
                                            <div class="col-md-9">
                                                <input type="email" id="email" class="form-control" placeholder="Email" />
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="mobile" class="col-sm-2 control-label"> Mobile </label>
                                        <div class="col-sm-10">
                                        <div class="row">
                                            <div class="col-md-9">
                                                <input type="tel" id="mobile" class="form-control" placeholder="Mobile" />
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="password" class="col-sm-2 control-label"> Password </label>
                                        <div class="col-sm-10">
                                            <div class="row">
                                                <div class="col-md-9">
                                                    <input type="password" class="form-control" placeholder="Password" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="password" class="col-sm-2 control-label"> Confirm Password </label>
                                        <div class="col-sm-10">
                                            <div class="row">
                                                <div class="col-md-9">
                                                    <input type="password" class="form-control" placeholder="Password" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <div class="row">
                                    <div class="col-sm-2"> </div>
                                    <div class="col-sm-10">
                                        <button type="button" class="btn btn-primary btn-sm">
                                            Register</button>
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
    </div>
</div>

`;
	document.write(loginmodalhtml);
}