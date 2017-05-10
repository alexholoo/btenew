{% extends "layouts/base.volt" %}

{% block main %}
  <h2 style="margin-top:0;">Overstock Log</h2>

  {% if data is not empty %}
  <table class="table table-bordered table-hover">
    <thead>
      <tr>
        <th>#</th>
        <th>Time</th>
        <th>SKU</th>
        <th>Condition</th>
        <th>Cost</th>
        <th>Qty</th>
        <th>Product</th>
        <th>MPN</th>
        <th>UPC</th>
        <th>Weight</th>
      </tr>
    </thead>
    <tbody>
    {% for row in data %}
      <tr>
        <td>{{ row['id'] }}</td>
        <td>{{ row['datetime'] }}</td>
        <td>{{ row['sku'] }}</td>
        <td>{{ row['condition'] }}</td>
        <td>{{ row['cost'] }}</td>
        <td>{{ row['qty'] }}</t>
        <td>{{ row['title'] }}</t>
        <td>{{ row['mpn'] }}</td>
        <td>{{ row['upc'] }}</td>
        <td>{{ row['weight'] }}</td>
      </tr>
    {% endfor %}
    </tbody>
  </table>

  {% endif %}
{% endblock %}

{% block csscode %}
{% endblock %}

{% block docready %}
{% endblock %}
