{% extends "layouts/base.volt" %}

{% block main %}
  <h2>Order Information</h2>
  <div class="well">
    <form class="form-inline" role="form" method="POST">
      <div class="form-group col-xs-5 col-lg-5">
        <input class="form-control" name="id" placeholder="Enter Order ID" value="" type="text" autofocus style="width:100%">
      </div>
      <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span> Search </button>
    </form>
  </div>

  {% if order is not empty %}
  <p>Order information for <b>{{ id }}</b></p>
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
        <th align="left">Express</th>
      </tr>
      {% for item in items %}
      <tr>
        <td align="left">{{ order['date'] }}</td>
        <td align="left">{{ order['channel'] }}</td>
        <td align="left">{{ order['order_id'] }}</td>
        <td align="left">{{ item['sku'] }}</td>
        <td align="left">{{ item['qty'] }}</td>
        <td align="left">{{ item['price'] }}</td>
        <td align="left">{{ address['buyer'] }}</td>
        <td align="left">{{ order['express'] == 0 ? '' : 'Yes' }}</td>
      </tr>
      {% endfor %}
      <tr><td colspan="8"></td></tr>
      <tr>
        <th align="left" class="active">Address</th>
        <td colspan="2">{{ address['address'] }}</td>
        <td>{{ address['city'] }}</td>
        <td>{{ address['province'] }}</td>
        <td>{{ address['postalcode'] }}</td>
        <td colspan="2">{{ address['country'] }}</td>
      </tr>
    </tbody>
  </table>

  {% else %}
    No order information found.
  {% endif %}
{% endblock %}

{% block docready %}
{% endblock %}

{% block jscode %}
{% endblock %}
