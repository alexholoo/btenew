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

  {% if orders is not empty %}
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

    {% for purchase in orders %}
      <tr data-id="{{ purchase['id'] }}" data-order-id="{{ purchase['order_id'] }}">
        <td>{{ purchase['date'] }}</td>
        <td class="order-id"><a href="javascript:void(0)">{{ purchase['order_id'] }}</a></td>
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
        <td class="action">
          {% if purchase['related_sku'] is not empty and purchase['status'] != 'purchased' %}
            <button class="btn btn-xs btn-info"><span class="glyphicon glyphicon-shopping-cart"></span> Go </button>
          {% endif %}
        </td>
      </tr>
    {% endfor %}

    </tbody>
  </table>
  {{ orders | length }} rows found.
  {% else %}
    No purchase information found.
  {% endif %}
{% endblock %}

{% block csscode %}
  .toast {
    position: fixed;
    right: 30px;
    top: 60px;
    background-color: #008800;
    color: #F0F0F0;
    font-family: Calibri;
    font-size: 20px;
    padding: 10px;
    text-align: center;
    border-radius: 2px;
    -webkit-box-shadow: 5px 5px 20px -1px rgba(56, 56, 56, 1);
    -moz-box-shadow: 5px 5px 20px -1px rgba(56, 56, 56, 1);
    box-shadow: 5px 5px 20px -1px rgba(56, 56, 56, 1);
  }
  .error {
    background-color: #880000;
  }
{% endblock %}

{% block jscode %}
function showToast(msg) {
  $('.toast').removeClass('error').text(msg).fadeIn(400).delay(3000).fadeOut(400);
}
function showError(msg) {
  $('.toast').addClass('error').text(msg).fadeIn(400).delay(3000).fadeOut(400);
}
{% endblock %}

{% block docready %}
  $('.action button').click(function() {
    // TODO: show loading
    var row = $(this).closest('tr')
    var sku = row.find('select').val();

    $.post('/purchase/order',
        { 'order_id': row.data('order-id'), 'sku': sku },
        function(data) {
          if (data.status == 'OK') {
            row.remove();
            showToast('Order purchased successfully');
          } else {
            row.addClass('danger');
            showError(data.message);
          }
        },
        'json'
    )
    .fail(function() {
      alert("error");
    });
  })
{% endblock %}
