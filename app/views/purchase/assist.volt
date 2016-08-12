{% extends "layouts/base.volt" %}

{% block main %}
{% if data is not empty %}
  <table class="table table-bordered table-hover">
    <thead>
      <tr>
        <th>Date</th>
        <th>Order ID</th>
        <th>Qty</th>
        <th>Supplier SKU</th>
        <th>MPN</th>
        <th>Note</th>
        <th>Related SKU</th>
        <th>Dimension</th>
      </tr>
    </thead>
    <tbody>

    {% for purchase in data %}
      <tr{% if purchase['stock_status'] == 'overstock' %} class="info"{% endif %} data-id="{{ purchase['id'] }}">
        <td{% if purchase['express'] %} class="warning"{% endif %}>{{ purchase['date'] }}</td>
        <td>{{ purchase['order_id'] }}</td>
        <td>{{ purchase['qty'] }}</td>
        <td>{{ purchase['supplier_sku'] }}</td>
        <td>{{ purchase['mpn'] }}</td>
        <td>{{ purchase['notes'] }}</td>
        <td>
          {% if purchase['related_sku'] is not empty %}
            <select style="min-width: 85%;">
              {% for sku in purchase['related_sku'] %}
                <option value="{{ sku }}"{% if sku == purchase['supplier_sku'] %} selected{% endif %}>{{ sku }}</option>
              {% endfor %}
            </select>
            {{ purchase['related_sku'] | length }}
          {% else %}
            &nbsp;
          {% endif %}
        </td>
        <td>{{ purchase['dimension'] }}</td>
      </tr>
    {% endfor  %}

    </tbody>
  </table>

{% else %}
  No purchase information found.
{% endif %}

{% endblock %}
