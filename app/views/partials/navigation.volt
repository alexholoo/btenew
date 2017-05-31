<nav class="navbar navbar-inverse navbar-fixed-top">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>                        
      </button>
      <a class="navbar-brand" href="#">BTE</a>
    </div>
    <div class="collapse navbar-collapse" id="myNavbar">
      <ul class="nav navbar-nav">
        <li class="active"><a href="/">Home</a></li>
        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#">Purchase <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="/purchase/assist">Purchase assistance</a></li>
          </ul>
        </li>
        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#">Search <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="/search/order">Order Information</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="/search/sku">SKU Information</a></li>
            <li><a href="/search/priceavail">Price & Availability</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="/shipment/search">Shipment search</a></li>
            <li><a href="/search/address">Address Information</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="/rma/records">RMA Records</a></li>
          </ul>
        </li>
        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#">Inventory <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="/invloc/search">Inventory Search</a></li>
            <li><a href="/invloc/add">Inventory Loading</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="/overstock">Overstock Items</a></li>
            <li><a href="/overstock/viewlog">View Overstock Log</a></li>
            <li><a href="/overstock/viewchange">View Overstock Deduction</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="/inventory">BTE Inventory</a></li>
            <li><a href="/inventory/viewchange">View Inventory Deduction</a></li>
          </ul>
        </li>
        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#">Amazon <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="/amazon/reports">Amazon Reports</a></li>
            <li><a href="/amazon/fbaitems">Generate FBA lines</a></li>
          </ul>
        </li>
        <li><a href="/about">About</a></li>
      </ul>
      {% if userLoggedIn %}
      <ul class="nav navbar-nav navbar-right">
        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#"><span class="glyphicon glyphicon-user"></span> Profile <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li class="dropdown-header"><big>Username: <b>{{ auth.getUsername() }}</b></big></li>
            <li role="separator" class="divider"></li>
            {% if auth.isAdmin() %}
            <li class="disabled"><a href="/user/manage">User Management</a></li>
            <li role="separator" class="divider"></li>
            {% endif %}
            <li><a href="/user/changepassword">Change Password</a></li>
            <li><a href="/user/logout">Logout</a></li>
          </ul>
        </li>
      </ul>
      {% endif %}
    </div>
  </div>
</nav>
