{% extends "layouts/base.volt" %}

{% block main %}
  <h2>Purchase assistant</h2>
  <div class="well">
    <form class="form-inline" role="form" method="POST">
      <div class="form-group col-xs-3">
        <label for="sel1" class="control-label">Date:</label>
        <select class="form-control" id="sel1" name="date">
          <option value="all">All</option>
          {% for d in orderDates %}
          <option value="{{ d }}"{% if d == date %} selected{% endif %}>{{ d }}</option>
          {% endfor %}
        </select>
      </div>
      <div class="form-group col-xs-3">
        <label for="sel2" class="control-label">Purchase:</label>
        <select class="form-control" id="sel2" name="stage">
          <option value="all">All</option>
          <option value="pending"{% if stage == 'pending' %} selected{% endif %}>Pending</option>
          <option value="purchased"{% if stage == 'purchased' %} selected{% endif %}>Purchased</option>
        </select>
      </div>
      <div class="checkbox col-xs-2">
        <label><input type="checkbox" name="overstock" value="1"{% if overstock == 1 %} checked{% endif %}> Overstock </label>
      </div>
      <div class="checkbox col-xs-2">
        <label><input type="checkbox" name="express" value="1"{% if express == 1 %} checked{% endif %}> Express </label>
      </div>
      <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-filter"></span> Filter </button>
    </form>
  </div>

  {% if data is not empty %}
  <table class="table table-bordered table-hover">
    <thead>
      <tr>
        <th>Date</th>
        <th>Order ID</th>
        <th>Qty</th>
        <th>Note</th>
        <th>Related SKU</th>
        <th>Decision</th>
        <th>Info</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>

    {% for purchase in data %}
      <tr data-id="{{ purchase['id'] }}">
        <td>{{ purchase['date'] }}</td>
        <td>{{ purchase['order_id'] }}</td>
        <td>{{ purchase['qty'] }}</td>
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
          {% if purchase['stock_status'] == 'overstock' %}
            <span class="label label-success"><span class="glyphicon glyphicon-home"></span></span>
          {% endif %}
          {% if purchase['express'] %}
            <span class="label label-danger"><span class="glyphicon glyphicon-flash"></span></span>
          {% endif %}
        </td>
        <td>
          {% if purchase['related_sku'] is not empty and purchase['status'] != 'purchased' %}
            <a href="#" class="btn btn-xs btn-info"><span class="glyphicon glyphicon-shopping-cart"></span> Go </a>
          {% endif %}
        </td>
      </tr>
    {% endfor %}

    </tbody>
  </table>
  {{ data | length }} rows found.
  {% else %}
    No purchase information found.
  {% endif %}
{% endblock %}
