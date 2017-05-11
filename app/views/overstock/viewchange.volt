{% extends "layouts/base.volt" %}

{% block main %}
  <h2 style="margin-top:0;">Overstock Change</h2>

  <div>
    <ul class="pagination pull-left" style="margin: 10px 0 0 0;">
      <li>Page: {{ page.current }} of {{ page.total_pages }}</li>
    </ul>

    <div class="pagination pull-right" style="margin: 0 0 10px 20px;">
      <select class="form-control" id="pagesel" name="pagesel">
        {% for p in 1..page.total_pages %}
        <option value="{{ p }}" {% if p == page.current %}selected{% endif %}>{{ p }}</option>
        {% endfor %}
      </select>
    </div>

    <ul class="pagination pull-right" style="margin: 0 0 10px 0;">
      <li><a href="/overstock/viewchange"><span class="glyphicon glyphicon-fast-backward"></span></a></li>
      <li><a href="/overstock/viewchange?page={{ page.before }}"><span class="glyphicon glyphicon-backward"></span></a></li>
      <li><a href="/overstock/viewchange?page={{ page.next }}"><span class="glyphicon glyphicon-forward"></span></a></li>
      <li><a href="/overstock/viewchange?page={{ page.last }}"><span class="glyphicon glyphicon-fast-forward"></span></a></li>
    </ul>
  </div>

  {% if page.items is not empty %}
  <table id="overstocktbl" class="table table-bordered table-hover">
    <thead>
      <tr>
        <th>Order Date</th>
        <th>Channel</th>
        <th>Order ID</th>
        <th>Change</th>
        <th>SKU</th>
        <th>Title</th>
        <th>Qty</th>
        <th>UPC</th>
      </tr>
    </thead>
    <tbody>
    {% for row in page.items %}
      <tr>
        <td nowrap{% if row['order_date'] == today %} class="warning"{% endif %}>{{ row['order_date'] }}</td>
        <td>{{ row['channel'] }}</td>
        <td class="order-id"><a href="javascript:;">{{ row['order_id'] }}</a></td>
        <td>{{ row['change'] }}</t>
        <td class="sku">{{ row['sku'] }}</td>
        <td>{{ row['title'] }}</td>
        <td>{{ row['qty'] }}</td>
        <td class="upc"><a href="javascript:;">{{ row['upc'] }}</a></td>
      </tr>
    {% endfor %}
    </tbody>
  </table>

  <div>
    <ul class="pagination pull-left" style="margin: 0;">
      <li>Page: {{ page.current }} of {{ page.total_pages }}</li>
    </ul>

    <ul class="pagination pull-right" style="margin: 0 0 10px 0;">
      <li><a href="/overstock/viewchange">First</a></li>
      <li><a href="/overstock/viewchange?page={{ page.before }}">Prev</a></li>
      <li><a href="/overstock/viewchange?page={{ page.next }}">Next</a></li>
      <li><a href="/overstock/viewchange?page={{ page.last }}">Last</a></li>
    </ul>
  </div>

  {% endif %}
{% endblock %}

{% block csscode %}
  #overstocktbl td { vertical-align: middle; }
{% endblock %}

{% block docready %}
  $('#pagesel').change(function() {
    window.location = '/overstock/viewchange?page=' + $(this).val();
  })

  // click upc to view sku list
  $('.upc').click(function() {
    $('tr').removeClass('info');

    var self = $(this);

    var tr = self.closest('tr');
    var upc = self.text();

    tr.addClass('info');

    var modal = new bte.SkuListModal(upc, 'UPC');
    modal.show();
  });

  // click on order id
  $('.order-id').click(function() {
    $('tr').removeClass('info');

    var tr = $(this).closest('tr');
    var orderId = tr.data('order-id');

    tr.addClass('info');

    var modal = new bte.OrderInfoModal(orderId);
    modal.show();
  });
{% endblock %}
