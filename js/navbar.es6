function writenav() {
  var navbarhtml = `<div class="mdl-layout mdl-js-layout mdl-layout--fixed-header">
                      <header class="mdl-layout__header">
                        <div class="mdl-layout__header-row">
                          <!-- Title -->
                          <span class="mdl-layout-title .mdl-color--white" style="font-size:50px;padding-top:20px">PorchFest</span>
                        </div>
                        <!-- Tabs -->
                        <div class="mdl-layout__tab-bar mdl-js-ripple-effect">
                          <a href="#" class="mdl-layout__tab is-active">About</a>
                          <a href="#" class="mdl-layout__tab">Contact</a>
                          <div class="mdl-layout-spacer"></div>
                          <a href="./login.html" class="mdl-layout__tab">Login / Register</a>
                        </div>
                      </header>
                    </div>`;
	document.write(navbarhtml);
}