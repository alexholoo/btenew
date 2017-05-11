{% extends "layouts/base.volt" %}

{% block main %}
  <h2 style="margin-top:0;">Overstock</h2>

  <header class="well clearfix" id="searchbox">
    <form role="form" method="post">
      <div class="col-sm-4">
        <input autofocus required type="text" class="form-control" name="keyword" autofocus placeholder="Enter SKU/UPC/MPN">
      </div>

      <div class="col-sm-2">
        <button type="submit" class="btn btn-primary" id="btn1"><span class="glyphicon glyphicon-search"></span> Search </button>
      </div>
    </form>
  </header>

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
        <th>Weight</th>
      </tr>
    </thead>
    <tbody>
    {% for row in page.items %}
      <tr data-id="{{ row['id'] }}">
        <td class="sku" nowrap>{{ row['sku'] }}</td>
        <td>{{ row['condition'] }}</td>
        <td>{{ row['cost'] }}</td>
        <td>{{ row['qty'] }}</t>
        <td>{{ row['title'] }}</t>
        <td class="mpn">{{ row['mpn'] }}</td>
        <td class="upc"><a href="javascript:;">{{ row['upc'] }}</a></td>
        <td class="note">{{ row['note'] }}</td>
        <td>{{ row['weight'] }}</td>
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
  .mpn, .note { cursor: pointer; }
{% endblock %}

{% block jscode %}
function editNoteHtml(data) {
  var note = data.note;
  return `<div style="padding: 20px;">
     <label for="note">Note</label> (Max 200 chars)<br />
     <textarea id="note" maxlength="200" style="width: 440px; height: 80px; resize: none;">${note}</textarea>
   </div>`;
}

function editNote(data, success, fail, done) {
  layer.open({
    title: 'Edit Note',
    area: ['480px', 'auto'],
    btn: ['Save', 'Cancel'],
    yes: function(index, layero) {
      var note = layero.find('#note').val();
      var sn = layero.find('#sn').val();

      data.note = note;

      ajaxCall('/overstock/note', data, success, fail);
      layer.close(index);
    },
    end: function(index, layero) {
      done();
    },
    content: editNoteHtml(data)
  })
}

function skuListHtml(skus, upc) {
  var content = '';

  for (var i=0; i<skus.length; i++) {
    content += `<li>${skus[i]}</li>`;
  }

  return `<div style="padding: 20px; font-size: 20px;">
     SKUs for <label>${upc}</label><br />
     <ul>${content}</ul>
   </div>`;
}

function skuListForUPC(upc, done) {
  ajaxCall('/api/query/upc/' + upc, { upc: upc },
    function(data) {
      layer.open({
        title: false,
        area: ['400px', 'auto'],
        shadeClose: true,
        end: function(index, layero) { done(); },
        content: skuListHtml(data, upc)
      })
    },
    function(message) {
      done();
      showError(message);
    }
  );
}

function skuListForMPN(mpn, done) {
  ajaxCall('/api/query/mpn/' + mpn, { mpn: mpn },
    function(data) {
      layer.open({
        title: false,
        area: ['400px', 'auto'],
        shadeClose: true,
        end: function(index, layero) { done(); },
        content: skuListHtml(data, mpn)
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

    skuListForUPC(upc, function() {});
  });

  // click upc to view sku list
  $('.mpn').click(function() {
    $('tr').removeClass('info');

    var self = $(this);

    var tr = self.closest('tr');
    var mpn = self.text();

    tr.addClass('info');

    skuListForMPN(mpn, function() {});
  });

  // click note to edit note
  $('.note').click(function() {
    $('tr').removeClass('info');

    var self = $(this);
    var tr = self.closest('tr');

    var id = tr.data('id');
    var note = tr.find('.note');

    tr.addClass('info');

    editNote({ id: id, note: note.text() },
      function(data) {
        showToast('Your change has benn saved', 1000);
        note.text(data.note);
      },
      function(message) {
        showError(message);
        tr.addClass('danger');
      },
      function() {}
    );
  });

{% endblock %}
