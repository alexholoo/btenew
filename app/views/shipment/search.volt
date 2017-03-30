{% extends "layouts/base.volt" %}

{% block main %}
<header class="jumbotron subhead clearfix" id="searchbox">
  <form role="form" method="post">

      <div class="col-sm-12">
        <h2 style="margin-top: 0;">Shipment search</h2>
      </div>

      <div class="col-sm-10">
        <input autofocus required type="text" pattern=".{3,}" title="3 characters minimum" class="form-control" name="keyword" autofocus placeholder="Enter order id or tracking number">
      </div>

      <div class="col-sm-2">
        <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span> Search </button>
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
        <th>Source</th>
      </tr>
    </thead>
    <tbody>

    <tr>
      <td>{{ data['order_id'] }}</td>
      <td>{{ data['ship_date'] }}</td>
      <td>{{ data['carrier_code'] }}</td>
      <td>{{ data['tracking_number'] }}</td>
      <td>{{ data['ship_method'] }}</td>
      <td>{{ data['sender'] }}</td>
    </tr>

    </tbody>
  </table>

{% else %}
  {% if keyword is not empty %}
    No shipment information found for <b>{{ keyword }}</b>.
  {% endif %}
{% endif %}

{% endblock %}
