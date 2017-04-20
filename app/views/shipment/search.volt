{% extends "layouts/base.volt" %}

{% block main %}
<header class="well clearfix" id="searchbox">
  <form role="form" method="post">

      <div class="col-sm-12">
        <h3 style="margin-top: 0;">Shipment search</h3>
      </div>

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
      <td>{{ tracking['tracking_number'] }}</td>
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
function orderDetailHtml(order) {
  return `<div style="padding: 20px 20px 0 20px;">
    <table class="table table-bordered table-condensed">
    <caption>Order ID: <b>${order.order_id}</b></caption>
    <thead>
      <tr>
        <th>Date</th>
        <th>Market</th>
        <th>SKU</th>
        <th>Price</th>
        <th>Qty</th>
        <th>Express</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>${order.date}</td>
        <td>${order.channel}</td>
        <td><a href="/search/sku?sku=${order.items[0].sku}" target="_blank">${order.items[0].sku}</a></td>
        <td>${order.items[0].price}</td>
        <td>${order.items[0].qty}</td>
        <td>${order.express == 1 ? 'Yes' : '&nbsp;'}</td>
      </tr>
    </tbody>
    </table>

    <p class="text-primary">${order.items[0].product}</p>

    <table class="table table-condensed">
    <caption>Customer Information</caption>
    <tbody>
      <tr><td><b>Name</b></td><td>${order.address.buyer}</td></tr>
      <tr><td><b>Address</b></td><td>${order.address.address}</td></tr>
      <tr><td><b>&nbsp;</b></td><td>${order.address.city}, ${order.address.province}, ${order.address.postalcode}, ${order.address.country}</td></tr>
      <tr><td><b>Phone</b></td><td>${order.address.phone}</td></tr>
      <tr><td><b>Email</b></td><td>${order.address.email}</td></tr>
    </table>
    </div>`;
}

function getOrderDetail(orderId, done) {
  ajaxCall('/ajax/order/info', { orderId: orderId },
    function(data) {
      layer.open({
        title: false,
        area: ['550px', 'auto'],
        shadeClose: true,
        end: function(index, layero) {
          done();
        },
        content: orderDetailHtml(data)
      })
    },
    function(message) {
      done();
      showError(message);
    }
  );
}
{% endblock %}

{% block docready %}
  layer.config({
    type: 1,
    moveType: 1,
    skin: 'layui-layer-molv',
  });

  // click on order id
  $('.order-id a').click(function() {
    var orderId = $(this).data('order-id');

    getOrderDetail(orderId, function() {
      /*tr.removeClass('info');*/
    });
  });
{% endblock %}
