function joinporchfestmodal() {
  var modal = `<!-- Modal -->
            <div id="joinPorchfestModal" class="modal fade" role="dialog">
              <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Sign-up a Band</h4>
                  </div>
                  <div class="modal-body">
                    <form role="form" class="form-horizontal">
                      <div class="form-group">
                          <label for="name" class="col-sm-2 control-label">
                              Band Name</label>
                          <div class="col-sm-10">
                              <div class="row">
                                  <div class="col-md-9">
                                      <input type="text" class="form-control" placeholder="Name" />
                                  </div>
                              </div>
                          </div>
                      </div>
                      <div class="form-group">
                          <label for="name" class="col-sm-2 control-label">
                              Porch Location </label>
                          <div class="col-sm-10">
                              <div class="row">
                                  <div class="col-md-9">
                                      <input type="text" class="form-control" placeholder="Location" />
                                  </div>
                              </div>
                          </div>
                      </div><div class="form-group">
                          <label for="name" class="col-sm-2 control-label">
                              Description </label>
                          <div class="col-sm-10">
                              <div class="row">
                                  <div class="col-md-9">
                                      <input type="text" class="form-control" placeholder="Description" />
                                  </div>
                              </div>
                          </div>
                      </div>
                      <div class="form-group">
                          <label for="name" class="col-sm-2 control-label">
                              Available Times </label>
                          <div class="col-sm-10">
                              <div class="row">
                                  <div class="col-md-9">
                                      <input type="text" class="form-control" placeholder="Times" />
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
                  <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" data-dismiss="modal">Sign-up</button>
                    <button type="reset" class="btn btn-default" data-dismiss="modal">Cancel</button>
                  </div>
                  </form>
                </div>
              </div>
            </div>`;
	document.write(modal);
}