{% extends "layouts/base.volt" %}

{% block main %}
<h3 style="margin-top: 0;">Shipment report</h3>

{% if data is not empty %}
  <table class="table table-bordered table-hover">
    <thead>
      <tr>
        <th>#</th>
        <th>Order ID</th>
        <th>Tracking #</th>
        <th>Carrier</th>
        <th>Source</th>
        <th>Ship Date</th>
      </tr>
    </thead>
    <tbody>

    {% for shipment in data %}
    <tr>
      <td>{{ loop.index }}</td>
      <td class="order-id">{{ shipment['order_id'] }}</td>
      <td>{{ shipment['tracking_number'] }}</td>
      <td>{{ shipment['carrier'] }}</td>
      <td>{{ shipment['site'] }}</td>
      <td>{{ shipment['createdon'] }}</td>
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
{% endblock %}
