{% extends "layouts/base.volt" %}

{% block main %}
<h3 style="margin-top: 0;">Shipment search</h3>
<header class="well clearfix" id="searchbox">
  <form role="form" method="post">
      <div class="col-sm-6">
        <input autofocus required type="text" pattern=".{4,}" title="4 characters minimum" class="form-control" name="keyword" autofocus placeholder="Enter last 4+ digits of order id">
      </div>

      <div class="col-sm-4">
        <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span> Search </button>
      </div>
  </form>
</header>

{% if data is not empty %}
  <p>Search result for <b>{{ keyword }}</b>:</p>
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

    {% for tracking in data %}
    <tr>
      <td class="order-id"><a href="javascript:void(0)" data-order-id="{{ tracking['order_id'] }}">{{ tracking['order_id'] }}</a></td>
      <td>{{ tracking['ship_date'] }}</td>
      <td>{{ tracking['carrier_code'] }}</td>
      <td{% if tracking['shipped'] %} class="success"{% endif %}>{{ tracking['tracking_number'] }}</td>
      <td>{{ tracking['ship_method'] }}</td>
      <td>{{ tracking['sender'] }}</td>
    </tr>
    {% endfor %}

    </tbody>
  </table>

{% else %}
  {% if keyword is not empty %}
    No shipment information found for <b>{{ keyword }}</b>.
  {% endif %}
{% endif %}

{% endblock %}

{% block jscode %}
{% endblock %}

{% block docready %}
  // click on order id
  $('.order-id a').click(function() {
    var orderId = $(this).data('order-id');

    var modal = new bte.OrderInfoModal(orderId);
    modal.show();
  });
{% endblock %}
