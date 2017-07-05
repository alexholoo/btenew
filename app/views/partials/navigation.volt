<nav class="navbar navbar-inverse">
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
        {% if userLoggedIn %}
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
            <li><a href="/shipment/rate">Shipping Rates</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="/rma/records">RMA Records</a></li>
          </ul>
        </li>
        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#">Inventory <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="/inventory/search">Inventory Search</a></li>
            <li><a href="/inventory/add">Inventory Loading</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="/overstock">Overstock Items</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="/overstock/add">New Overstock</a></li>
            <li><a href="/overstock/viewlog">Overstock Log</a></li>
          </ul>
        </li>
        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#">Amazon <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="/amazon/reports">Amazon Reports</a></li>
            <li><a href="/amazon/fbaitems">Generate FBA lines</a></li>
          </ul>
        </li>
        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#">Tools<span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="/tools/barcode">Add Barcodes to Order</a></li>
            <li><a href="/tools/label">Print Barcode Label</a></li>
          </ul>
        </li>
        <li><a href="/about">About</a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#"><span class="glyphicon glyphicon-user"></span> Profile <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="/user/changepassword">Change Password</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="/user/logout">Logout</a></li>
          </ul>
        </li>
      </ul>
        {% endif %}
    </div>
  </div>
</nav>
