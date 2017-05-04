{% extends "layouts/base.volt" %}

{% block main %}
  <h2 style="margin-top:0;">RMA records</h2>
  <div class="well">
    <form class="form-inline" role="form" method="POST">
      <div class="form-group col-xs">
        <input class="form-control" name="" placeholder="" type="text">
      </div>
      <div class="form-group col-xs">
        <label for="sel1" class="control-label">Date:</label>
        <select class="form-control" id="sel1" name="date">
          <option value="all">All</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-filter"></span> Filter </button>
    </form>
  </div>

  {% if records is not empty %}
  <table id="rmatbl" class="table table-bordered table-hover">
    <thead>
      <tr>
        <th>Date In</th>
        <th>Product</th>
        <th>PartNum</th>
        <th>Our Rec#</th>
        <th>Status</th>
        <th>Supplier</th>
        <th>Order ID</th>
        <th>After Checked</th>
        <th>Date Shipout</th>
        <th>Ship Info</th>
        <th>Supplier Recv Rec#</th>
        <th>DONE</th>
      </tr>
    </thead>
    <tbody>
    {% for row in records %}
      <tr data-id="{{ row['id'] }}">
        <td>{{ row['date_in'] }}</td>
        <td>{{ row['product_desc'] }}</td>
        <td>{{ row['partnum'] }}</td>
        <td>{{ row['our_recnum'] }}</td>
        <td>{{ row['status'] }}</td>
        <td>{{ row['supplier'] }}</td>
        <td>{{ row['order_id'] }}</td>
        <td>{{ row['after_checked'] }}</td>
        <td>{{ row['date_shipout'] }}</td>
        <td>{{ row['ship_method'] }}</td>
        <td>{{ row['supplier_recv_recnum'] }}</td>
        <td>{{ row['done'] }}</td>
      </tr>
    {% endfor %}
    </tbody>
  </table>
  {% endif %}
{% endblock %}

{% block csscode %}
  #rmatbl td, #rmatbl th { vertical-align: middle; }
  .main-container { width: 100%; }
{% endblock %}
