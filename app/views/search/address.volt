{% extends "layouts/base.volt" %}

{% block main %}
  <h3 style="margin-top:0;">Order Information</h3>
  <div class="well">
    <form class="form-inline" role="form" method="POST">
      <div class="form-group col-xs-5 col-lg-5">
        <input class="form-control" name="key" placeholder="Enter Order ID" value="" type="text" autofocus style="width:100%">
      </div>
      <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span> Search </button>
    </form>
  </div>

  {% if orders is not empty %}
  <p>Address information for <b>{{ key }}</b></p>
  {% for order in orders %}
  <table class="table table-bordered table-hover">
    <tbody>
      <tr class="active">
        <th align="left">Date</th>
        <th align="left">Channel</th>
        <th align="left">Order ID</th>
        <th align="left">SKU</th>
        <th align="left">Qty</th>
        <th align="left">Price</th>
        <th align="left">Buyer</th>
      </tr>
      {% for item in order['items'] %}
      <tr>
        <td align="left">{{ order['date'] }}</td>
        <td align="left">{{ order['channel'] }}</td>
        <td align="left">{{ order['order_id'] }}</td>
        <td align="left">{{ item['sku'] }}</td>
        <td align="left">{{ item['qty'] }}</td>
        <td align="left">{{ item['price'] }}</td>
        <td align="left">{{ order['address']['buyer'] }}</td>
      </tr>
      {% endfor %}
      <tr>
        <th align="left" class="active">Address</th>
        <td colspan="2">{{ order['address']['address'] }}</td>
        <td>{{ order['address']['city'] }}</td>
        <td>{{ order['address']['province'] }}</td>
        <td>{{ order['address']['postalcode'] }}</td>
        <td>{{ order['address']['country'] }}</td>
      </tr>
    </tbody>
  </table>
  {% endfor %}

  {% else %}
    No order information found.
  {% endif %}
{% endblock %}

{% block docready %}
{% endblock %}

{% block jscode %}
{% endblock %}
