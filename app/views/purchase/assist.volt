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
        <th>Action</th>
      </tr>
    </thead>
    <tbody>

    {% for purchase in orders %}
      <tr data-id="{{ purchase['id'] }}" data-order-id="{{ purchase['order_id'] }}">
        <td{% if purchase['express'] %} class="text-danger"{% endif %}>{{ purchase['date'] }}</td>
        <td class="order-id"><a href="javascript:void(0)">{{ purchase['order_id'] }}</a></td>
        <td>{{ purchase['qty'] }}</td>
        <td>{{ purchase['notes'] }}</td>
        <td>
          {% if purchase['status'] == 'purchased' %}
            {{ purchase['actual_sku'] }}
          {% else %}
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
          {% endif %}
        </td>
        <td>{{ purchase['dimension'] }}</td>
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

{% block jscode %}
function makePurchase(data, success, fail) {
  $.post('/purchase/order', data,
    function(res) {
      if (res.status == 'OK') {
        success();
        showToast('Order purchased successfully');
      } else {
        fail();
        showError(res.message);
      }
    },
    'json'
  )
  .fail(function() {
    alert("error");
  });
}
{% endblock %}

{% block docready %}
  $('.action button').click(function() {
    // TODO: show loading
    var tr = $(this).closest('tr');
    var orderId = tr.data('order-id');
    var sku = tr.find('select').val();

    tr.addClass('info');

    layer.open({
      type: 1,
      area: ['480px', '240px'],
      title: 'Input',
      skin: 'layui-layer-molv',
      moveType: 1,
      btn: ['Purchase', 'Cancel'],
      yes: function(index, layero) {
        var comment = layero.find('#comment').val();
        makePurchase({ 'order_id': orderId, 'sku': sku, 'comment': comment },
            function() { tr.remove(); },
            function() { tr.addClass('danger'); }
        );
        layer.close(index);
      },
      end: function(index, layero) {
        tr.removeClass('info');
      },
      content: '<div style="padding: 20px;">' +
               '<label for="comment">Purchase note</label><br />' +
               '<textarea id="comment" style="width: 440px; height: 80px; resize: none;"></textarea>' +
               '</div>'
    })
  });
{% endblock %}
