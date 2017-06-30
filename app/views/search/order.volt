{% extends "layouts/base.volt" %}

{% block main %}
  <h3 style="margin-top:0;">Order Information</h3>
  <div class="well clearfix">
    <form class="form-inline" role="form" method="POST">
      <div class="col-sm-5">
        <input class="form-control" name="kwd" placeholder="Enter OrderNO/SKU" value="" type="text" autofocus style="width:100%">
      </div>

      <div class="col-sm-2">
        <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span> Search </button>
      </div>

      <div class="col-sm-12">
        <label class="radio-inline">
          <input type="radio" name="searchby" value="orderid" {% if searchby == 'orderid' %}checked{% endif %}>Order Number
        </label>

        <label class="radio-inline">
          <input type="radio" name="searchby" value="sku" {% if searchby == 'sku' %}checked{% endif %}>SKU
        </label>
      </div>
    </form>
  </div>

  {% if order is not empty %}
  <p>Order information for <b>{{ id }}</b></p>
  <table class="table table-bordered table-hover">
    <tbody>
      <tr class="active">
        <th>Date</th>
        <th>Channel</th>
        <th>Order ID</th>
        <th>SKU</th>
        <th>Qty</th>
        <th>Price</th>
        <th>Buyer</th>
        <th>Express</th>
      </tr>
      {% for item in items %}
      <tr data-order-id="{{ order['order_id'] }}">
        <td>{{ order['date'] }}</td>
        <td>{{ order['channel'] }}</td>
        <td class="order-id"><a href="javascript:void(0)">{{ order['order_id'] }}</a></td>
        <td><a href="/search/sku?sku={{ item['sku'] }}" target="_blank">{{ item['sku'] }}</a></td>
        <td>{{ item['qty'] }}</td>
        <td>{{ item['price'] }}</td>
        <td>{{ address['buyer'] }}</td>
        <td>{{ order['express'] == 0 ? '' : 'Yes' }}</td>
      </tr>
      {% endfor %}
    </tbody>
  </table>

  {% else %}
    No order information found.
  {% endif %}
{% endblock %}

{% block docready %}
{% endblock %}

{% block jscode %}
  $('.order-id a').click(function() {
    var tr = $(this).closest('tr');
    var orderId = tr.data('order-id');
    var modal = new bte.OrderDetailModal(orderId);
    modal.show();
  });
{% endblock %}
