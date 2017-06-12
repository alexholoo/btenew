{% extends "layouts/base.volt" %}

{% block main %}
<h3 style="margin-top: 0;">Shipping rate</h3>
<header class="well clearfix" id="searchbox">
  <form role="form" method="post">

      <div class="col-sm-6">
        <input autofocus required type="text" pattern=".{4,}" class="form-control" name="order_id" autofocus placeholder="Order Number">
      </div>

      <div class="col-sm-4">
        <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span> Query </button>
      </div>
  </form>
</header>

{% if data is not empty %}
  <table class="table table-bordered table-hover">
    <thead>
      <tr>
        <th>Order ID</th>
        <th>SKU</th>
        <th>Ship To</th>
        <th>Dimension</th>
        <th>Weight</th>
        <th>Carrier</th>
        <th>Ship Method</th>
        <th>Shipping Rate</th>
      </tr>
    </thead>
    <tbody>

    {% for shipment in data %}
    <tr>
      <td class="order-id"><a href="javascript:void(0)" data-order-id="{{ shipment['order_id'] }}">{{ shipment['order_id'] }}</a></td>
      <td><a href="/search/sku?sku={{ shipment['sku'] }}" target="_blank">{{ shipment['sku'] }}</a></td>
      <td>{{ shipment['ship_to'] }}</td>
      <td>{{ shipment['dimension'] }}</td>
      <td>{{ shipment['weight'] }}</td>
      <td>{{ shipment['carrier'] }}</td>
      <td>{{ shipment['ship_method'] }}</td>
      <td>{{ shipment['rate'] }}</td>
    </tr>
    {% endfor %}

    </tbody>
  </table>

{% else %}
  {% if keyword is not empty %}
    No shipment information found</b>.
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
