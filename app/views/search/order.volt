{% extends "layouts/base.volt" %}

{% block main %}
  <h3 style="margin-top:0;">Order Information</h3>
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
      <tr>
        <td>{{ order['date'] }}</td>
        <td>{{ order['channel'] }}</td>
        <td>{{ order['order_id'] }}</td>
        <td>{{ item['sku'] }}</td>
        <td>{{ item['qty'] }}</td>
        <td>{{ item['price'] }}</td>
        <td>{{ address['buyer'] }}</td>
        <td>{{ order['express'] == 0 ? '' : 'Yes' }}</td>
      </tr>
      {% endfor %}
      <tr><td colspan="8"></td></tr>
      <tr>
        <th class="active">Address</th>
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
