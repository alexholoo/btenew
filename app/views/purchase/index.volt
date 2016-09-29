{% extends "layouts/base.volt" %}

{% block main %}
  <h2>Purchase assistant</h2>
  <div class="well">
    <form class="form-inline" role="form" method="POST">
      <div class="form-group col-xs-3">
        <input class="form-control" name="orderId" placeholder="Order number" type="text">
      </div>
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
      <tr data-order-id="{{ purchase['order_id'] }}" data-qty="{{ purchase['qty'] }}">
        <td{% if purchase['express'] %} class="text-danger"{% endif %}>{{ purchase['date'] }}</td>
        <td class="order-id"><a href="javascript:void(0)">{{ purchase['order_id'] }}</a></td>
        <td class="qty">{{ purchase['qty'] }}</td>
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
  {{ orders | length }} orders found.
  {% else %}
    No purchase information found.
  {% endif %}
{% endblock %}

{% block jscode %}
function purchaseNoteHtml(data) {
  return `<div style="padding: 20px;">
     <table class="table table-condensed">
       <tr><td><b>SKU: </b></td><td>${data.sku ? data.sku : '-'}</td></tr>
       <tr><td><b>Branch: </b></td><td>${data.branch ? data.branch: '-'}</td></tr>
       <tr><td><b>Qty: </b></td><td>${data.qty? data.qty: '-'}</td></tr>
     </table>
     <label for="comment">Purchase note</label><br />
     <textarea id="comment" style="width: 440px; height: 80px; resize: none;">${ (data.sku.substr(0, 2) == 'DH') ? 'Drop ship' : ''}</textarea>
   </div>`;
}

function makePurchase(data, success, fail, done) {
  layer.open({
    title: 'Confirmation',
    area: ['480px', 'auto'],
    btn: ['Purchase', 'Cancel'],
    yes: function(index, layero) {
      var comment = layero.find('#comment').val();
      data.comment = comment;
      ajaxCall('/ajax/make/purchase', data, success, fail);
      layer.close(index);
    },
    end: function(index, layero) {
      done();
    },
    content: purchaseNoteHtml(data)
  })
}

function priceAvailHtml(items) {
  var content = '';

  for (var i=0; i<items.length; i++) {
    for (var a=0; a<items[i].avail.length; a++) {
      content += `<tr data-sku="${items[i].sku}" data-branch="${items[i].avail[a].branch}" data-branch-code="${items[i].avail[a].code}">
        <td><input type="radio" name="skubranch"></td>
        <td>${a==0 ? items[i].sku : '&nbsp;'}</td>
        <td>${a==0 ? items[i].price : '&nbsp;'}</td>
        <td>${items[i].avail[a].branch}</td>
        <td>${items[i].avail[a].qty}</td>
      </tr>`;
    }
  }

  return `<div style="padding: 20px;">
    <table class="table table-bordered table-condensed">
    <thead>
      <tr>
        <th align="left">&nbsp;</th>
        <th align="left">PartNum</th>
        <th align="left">Price</th>
        <th align="left">Branch</th>
        <th align="left">Qty</th>
      </tr>
    </thead>
    <tbody>
      ${content}
    </tbody>
    </table>
    </div>`;
}

function getPriceAvail(data, selected, done) {
  ajaxCall('/ajax/price/avail', { sku: data },
    function(res) {
      layer.open({
        title: 'Price and Availability',
        area: ['600px', 'auto'],
        btn: ['OK', 'Cancel'],
        yes: function(index, layero) {
          var radio = layero.find('input[type=radio]:checked');
          if (radio.length) {
            var tr = radio.closest('tr');
            var sku = tr.data('sku');
            var branch = tr.data('branch');
            var code = tr.data('branch-code');
            selected({sku: sku, branch: branch, code: code});
          }
          layer.close(index);
        },
        success: function(layero, index){
          layero.find('table tr').click(function(){
            $(this).find('input[type=radio]').prop('checked', true);
          });
        },
        end: function(index, layero) { done(); },
        content: priceAvailHtml(res)
      })
      done();
    },
    function(message) {
      done();
      showError(message);
    }
  );
}

function orderDetailHtml(order) {
  return `<div style="padding: 20px 20px 0 20px;">
    <table class="table table-bordered table-condensed">
    <caption>Order ID: <b>${order.orderId}</b></caption>
    <thead>
      <tr>
        <th>Date</th>
        <th>Market</th>
        <th>SKU</th>
        <th>Price</th>
        <th>Qty</th>
        <th>Express</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>${order.date}</td>
        <td>${order.channel}</td>
        <td>${order.sku}</td>
        <td>${order.price}</td>
        <td>${order.qty}</td>
        <td>${order.express == 1 ? 'Yes' : '&nbsp;'}</td>
      </tr>
    </tbody>
    </table>

    <table class="table table-condensed">
    <caption>Customer Information</caption>
    <tbody>
      <tr><td><b>Name</b></td><td>${order.buyer}</td></tr>
      <tr><td><b>Address</b></td><td>${order.address}</td></tr>
      <tr><td><b>&nbsp;</b></td><td>${order.city}, ${order.province}, ${order.postalcode}, ${order.country}</td></tr>
      <tr><td><b>Phone</b></td><td>${order.phone}</td></tr>
      <tr><td><b>Email</b></td><td>${order.email}</td></tr>
    </table>
    </div>`;
}

function getOrderDetail(orderId, done) {
  ajaxCall('/ajax/order/detail', { orderId: orderId },
    function(data) {
      layer.open({
        title: false,
        area: ['550px', 'auto'],
        shadeClose: true,
        end: function(index, layero) {
          done();
        },
        content: orderDetailHtml(data)
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
    $('tr').removeClass('info');

    var tr = $(this).closest('tr');
    var orderId = tr.data('order-id');
    var sku = tr.data('sku');
    var qty = tr.data('qty');
    var branch = tr.data('branch');
    var code = tr.data('code');

    if (!sku) {
        sku = tr.find('select').val();
    }

    tr.addClass('info');

    makePurchase({ order_id: orderId, sku: sku, branch: branch, code: code, qty: qty },
      function() {
        showToast('Order purchased successfully');
        tr.remove();
      },
      function(message) {
        showError(message);
        tr.addClass('danger');
      },
      function() {
        /*tr.removeClass('info');*/
      }
    );
  });

  // click on sku button
  $('.sku button').click(function() {
    $('tr').removeClass('info');

    var tr = $(this).closest('tr');
    var td = $(this).parent();

    var sku = [];
    td.find("select option").each(function() {
        sku.push($(this).val());
    });

    tr.addClass('info');

    var loading = layer.load(1, { shade: false });

    getPriceAvail(sku,
      function(sel) {
        if (sel.sku) {
          tr.data(sel);
          tr.find('select').val(sel.sku);
        }
      },
      function() {
        layer.close(loading);
        /*tr.removeClass('info');*/
      }
    );
  });

  // click on order id
  $('.order-id a').click(function() {
    $('tr').removeClass('info');

    var tr = $(this).closest('tr');
    var orderId = tr.data('order-id');

    tr.addClass('info');

    getOrderDetail(orderId, function() {
      /*tr.removeClass('info');*/
    });
  });
{% endblock %}
