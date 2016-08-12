{% extends "layouts/base.volt" %}

{% block main %}
<header class="jumbotron subhead clearfix" id="searchbox">
  <form role="form" method="post">
    
      <div class="col-sm-12">
        <h2 style="margin-top: 0;">Shipment search</h2>
      </div>

      <div class="col-sm-10">  
        <input autofocus required type="text" pattern=".{3,}" title="3 characters minimum" class="form-control" name="keyword" placeholder="Enter order number or customer name/address">
      </div>
      
      <div class="col-sm-2">  
        <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span> Search </button>
      </div>

      <div class="col-sm-12">  
        <label class="radio-inline">
          <input type="radio" name="searchby" value="order_id" {% if searchby == 'order_id' %}checked{% endif %}>Order number
        </label>
        <label class="radio-inline">
          <input type="radio" name="searchby" value="name_or_address" {% if searchby == 'name_or_address' %}checked{% endif %}>Customer name or address
        </label>
      </div>

  </form>
</header>

{% if data is not empty %}
  Search result for <b>{{ keyword }}</b>:
  <table class="table table-bordered table-hover">
    <thead>
      <tr>
        <th>Order ID</th>
        <th>Ship Date</th>
        <th>Carrier</th>
        <th>Tracking #</th>
        <th>Ship Method</th>
        <th>Ship Address</th>
      </tr>
    </thead>
    <tbody>

    {% for shipment in data %}
      <tr>
        <td>{{ shipment['order_id'] }}</td>
        <td>{{ shipment['ship_date'] }}</td>
        <td>{{ shipment['carrier_code'] }}</td>
        <td>{{ shipment['tracking_number'] }}</td>
        <td>{{ shipment['ship_method'] }}</td>
        <td>{{ shipment['shipping_address'] }}</td>
      </tr>
    {% endfor  %}

    </tbody>
  </table>

{% else %}
  {% if keyword is not empty %}
    No shipment information found for <b>{{ keyword }}</b>.
  {% endif %}
{% endif %}

{% endblock %}
