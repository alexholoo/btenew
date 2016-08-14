{% extends "layouts/base.volt" %}

{% block main %}
{% if data is not empty %}
  <h2>Purchase assistance</h2>
  <h3>TOOD: filter</h3>
  <table class="table table-bordered table-hover">
    <thead>
      <tr>
        <th>Date</th>
        <th>Order ID</th>
        <th>Qty</th>
        <th>MPN</th>
        <th>Note</th>
        <th>Related SKU</th>
        <th>Decision</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>

    {% for purchase in data %}
      <tr{% if purchase['stock_status'] == 'overstock' %} class="warning"{% endif %} data-id="{{ purchase['id'] }}">
        <td{% if purchase['express'] %} class="danger"{% endif %}>{{ purchase['date'] }}</td>
        <td>{{ purchase['order_id'] }}</td>
        <td>{{ purchase['qty'] }}</td>
        <td>{{ purchase['mpn'] }}</td>
        <td>{{ purchase['notes'] }}</td>
        <td>
          {% if purchase['related_sku'] is not empty %}
            <select style="min-width: 85%;">
              {% for sku in purchase['related_sku'] %}
                <option value="{{ sku }}"{% if sku == purchase['supplier_sku'] %} selected{% endif %}>{{ sku }}</option>
              {% endfor %}
            </select>
            <span class="badge">{{ purchase['related_sku'] | length }}</span>
          {% else %}
            &nbsp;
          {% endif %}
        </td>
        <td>{{ purchase['dimension'] }}</td>
        <td>
          <!-- TODO: hide the button if purchase was made -->
          {% if loop.index % 3 %}
            <a href="#" class="btn btn-xs btn-info"><span class="glyphicon glyphicon-shopping-cart"></span> Go </a>
          {% endif %}
        </td>
      </tr>
    {% endfor %}

    </tbody>
  </table>

{% else %}
  No purchase information found.
{% endif %}

{% endblock %}
