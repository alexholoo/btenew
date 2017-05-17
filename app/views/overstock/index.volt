{% extends "layouts/base.volt" %}

{% block main %}
  <h2 style="margin-top:0;">Overstock</h2>

  <header class="well clearfix" id="searchbox">
    <form role="form" method="post">
      <div class="col-sm-4">
        <input autofocus type="text" class="form-control" name="keyword" autofocus placeholder="Enter SKU">
      </div>

      <div class="col-sm-2">
        <button type="submit" class="btn btn-primary" id="btn1"><span class="glyphicon glyphicon-search"></span> Search </button>
      </div>

      <div class="col-sm-2">
        <a href="/overstock/add" class="btn btn-info" role="button"><span class="glyphicon glyphicon-plus"></span> Add </a>
      </div>
    </form>
  </header>

  <div>
    <ul class="pagination pull-left" style="margin: 10px 0 0 0;">
      <li>Page: {{ page.current }} of {{ page.total_pages }} (Total {{ page.total_items }} items)</li>
    </ul>

    <div class="pagination pull-right" style="margin: 0 0 10px 20px;">
      <select class="form-control" id="pagesel" name="pagesel">
        {% for p in 1..page.total_pages %}
        <option value="{{ p }}" {% if p == page.current %}selected{% endif %}>{{ p }}</option>
        {% endfor %}
      </select>
    </div>

    <ul class="pagination pull-right" style="margin: 0 0 10px 0;">
      <li><a href="/overstock/"><span class="glyphicon glyphicon-fast-backward"></span></a></li>
      <li><a href="/overstock/?page={{ page.before }}"><span class="glyphicon glyphicon-backward"></span></a></li>
      <li><a href="/overstock/?page={{ page.next }}"><span class="glyphicon glyphicon-forward"></span></a></li>
      <li><a href="/overstock/?page={{ page.last }}"><span class="glyphicon glyphicon-fast-forward"></span></a></li>
    </ul>
  </div>

  {% if page.items is not empty %}
  <table id="overstocktbl" class="table table-bordered table-hover">
    <thead>
      <tr>
        <th>SKU</th>
        <th>Condition</th>
        <th>Cost</th>
        <th>Qty</th>
        <th>Product</th>
        <th>MPN</th>
        <th>UPC</th>
        <th>Note</th>
        {# <th>Weight</th> #}
      </tr>
    </thead>
    <tbody>
    {% for row in page.items %}
      <tr data-id="{{ row['id'] }}">
        <td class="sku{% if row['updatedon'] == today %} warning{% endif %}" nowrap><a href="/overstock/viewchange/{{ row['id'] }}">{{ row['sku'] }}</a></td>
        <td>{{ row['condition'] }}</td>
        <td>{{ row['cost'] }}</td>
        <td>{{ row['qty'] }}</t>
        <td>{{ row['title'] }}</t>
        <td class="mpn">{{ row['mpn'] }}</td>
        <td class="upc">{{ row['upc'] }}</td>
        <td class="note">{{ row['note'] }}</td>
        {# <td>{{ row['weight'] }}</td> #}
      </tr>
    {% endfor %}
    </tbody>
  </table>

  <div>
    <ul class="pagination pull-left" style="margin: 0;">
      <li>Page: {{ page.current }} of {{ page.total_pages }}</li>
    </ul>

    <ul class="pagination pull-right" style="margin: 0 0 10px 0;">
      <li><a href="/overstock/">First</a></li>
      <li><a href="/overstock/?page={{ page.before }}">Prev</a></li>
      <li><a href="/overstock/?page={{ page.next }}">Next</a></li>
      <li><a href="/overstock/?page={{ page.last }}">Last</a></li>
    </ul>
  </div>

  {% endif %}
{% endblock %}

{% block csscode %}
  .main-container { width: 100%; }
  #overstocktbl td { vertical-align: middle; }
  .upc, .mpn, .note { cursor: pointer; }
  .upc:hover, .mpn:hover, .note:hover { text-decoration: underline; }
{% endblock %}

{% block jscode %}
{% endblock %}

{% block docready %}
  $('#pagesel').change(function() {
    window.location = '/overstock/?page=' + $(this).val();
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

  // click upc to view sku list
  $('.mpn').click(function() {
    $('tr').removeClass('info');

    var self = $(this);

    var tr = self.closest('tr');
    var mpn = self.text();

    tr.addClass('info');

    var modal = new bte.SkuListModal(mpn, 'MPN');
    modal.show();
  });

  // click note to edit note
  $('.note').click(function() {
    $('tr').removeClass('info');

    var self = $(this);
    var tr = self.closest('tr');

    var id = tr.data('id');
    var note = tr.find('.note');

    tr.addClass('info');

    var modal = new bte.EditOverstockNoteModal({ id: id, note: note.text() });
    modal.success = function(data) {
        showToast('Your change has benn saved', 1000);
        note.text(data);
    };
    modal.failure = function(message) {
        showError(message);
        tr.addClass('danger');
    };
    modal.show();
  });
/*
  // click sku to display change log
  $('.sku').click(function() {
    $('tr').removeClass('info');

    var self = $(this);
    var tr = self.closest('tr');
    var sku = self.text();

    tr.addClass('info');

    var modal = new bte.OverstockChangeLogModal(sku);
    modal.show();
  });
*/
{% endblock %}
