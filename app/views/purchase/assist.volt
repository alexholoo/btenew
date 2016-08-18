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
        <td class="sku" nowrap style="white-space:nowrap">
          {% if purchase['status'] == 'purchased' %}
            {{ purchase['actual_sku'] }}
          {% else %}
            {% if purchase['related_sku'] is not empty %}
              <select style="min-width: 85%; max-width: 85%;">
                {% for sku in purchase['related_sku'] %}
                  <option value="{{ sku }}"{% if sku == purchase['supplier_sku'] %} selected{% endif %}>{{ sku }}</option>
                {% endfor %}
              </select>
              <button class="btn btn-xs btn-warning">{{ purchase['related_sku'] | length }}</button>
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
function makePurchase(data, success, fail, done) {
  layer.open({
    title: 'Input',
    area: ['480px', '240px'],
    btn: ['Purchase', 'Cancel'],
    yes: function(index, layero) {
      var comment = layero.find('#comment').val();
      data.comment = comment;
      ajaxCall('/purchase/make', data, success, fail);
      layer.close(index);
    },
    end: function(index, layero) {
      done();
    },
    content: '<div style="padding: 20px;">' +
             '<label for="comment">Purchase note</label><br />' +
             '<textarea id="comment" style="width: 440px; height: 80px; resize: none;"></textarea>' +
             '</div>'
  })
}

function getPriceAvail(data, done) {
  ajaxCall('/purchase/priceAvail', { sku: data },
    function(data) {
      layer.open({
        title: 'Price and Availability',
        area: ['640px', '400px'],
        btn: ['OK', 'Cancel'],
        yes: function(index, layero) {
          layer.close(index);
        },
        end: function(index, layero) {
          done();
        },
        content: '<div style="padding: 20px;">' +
                 data.join('<br>') +
                 '</div>'
      })
    },
    function(message) {
      done();
      showError(message);
    }
  );
}

function getOrderDetail(orderId, done) {
  ajaxCall('/purchase/orderDetail', { orderId: orderId },
    function(data) {
      layer.open({
        title: 'Order Info',
        area: ['600px', '400px'],
        btn: ['Close'],
        yes: function(index, layero) {
          layer.close(index);
        },
        end: function(index, layero) {
          done();
        },
        content: '<div style="padding: 20px;">' +
                 data +
                 '</div>'
      })
    },
    function(message) {
      done();
      showError(message);
    }
  );
}
{% endblock %}

{% block docready %}
  layer.config({
    type: 1,
    moveType: 1,
    skin: 'layui-layer-molv',
  });

  // click on action button
  $('.action button').click(function() {
    var tr = $(this).closest('tr');
    var orderId = tr.data('order-id');
    var sku = tr.find('select').val();

    tr.addClass('info');

    makePurchase({ 'order_id': orderId, 'sku': sku },
      function() {
        showToast('Order purchased successfully');
        tr.remove();
      },
      function(message) {
        showError(message);
        tr.addClass('danger');
      },
      function() {
        tr.removeClass('info');
      }
    );
  });

  // click on sku button
  $('.sku button').click(function() {
    var tr = $(this).closest('tr');
    var td = $(this).parent();

    var sku = [];
    td.find("select option").each(function() {
        sku.push($(this).val());
    });

    tr.addClass('info');

    getPriceAvail(sku, function() {
      tr.removeClass('info');
    });
  });

  // click on order id
  $('.order-id a').click(function() {
    var tr = $(this).closest('tr');
    var orderId = tr.data('order-id');

    tr.addClass('info');

    getOrderDetail(orderId, function() {
      tr.removeClass('info');
    });
  });
{% endblock %}
