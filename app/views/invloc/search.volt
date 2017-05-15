{% extends "layouts/base.volt" %}

{% block main %}
  <h3 style="margin-top: 0;">Inventory Location Search</h3>
  <header class="well clearfix" id="searchbox">
    <form role="form" method="post">
      <div class="col-sm-6">
        <input autofocus required type="text" class="form-control" name="keyword" autofocus placeholder="Enter keyword to search">
      </div>

      <div class="col-sm-2">
        <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span> Search </button>
      </div>

      <div class="col-sm-12">
        <label class="radio-inline">
          <input type="radio" name="searchby" value="partnum" {% if searchby == 'partnum' %}checked{% endif %}>Part number
        </label>
        <label class="radio-inline">
          <input type="radio" name="searchby" value="sku" {% if searchby == 'sku' %}checked{% endif %}>SKU
        </label>
        <label class="radio-inline">
          <input type="radio" name="searchby" value="upc" {% if searchby == 'upc' %}checked{% endif %}>UPC
        </label>
        <label class="radio-inline">
          <input type="radio" name="searchby" value="location" {% if searchby == 'location' %}checked{% endif %}>Location
        </label>
        <label class="radio-inline">
          <input type="radio" name="searchby" value="note" {% if searchby == 'note' %}checked{% endif %}>Note
        </label>
      </div>
    </form>
  </header>

  {% if data is not empty %}
  <p>Search result for <b>{{ keyword }}</b> in <b>{{ searchby }}</b>:(only first 20 rows)</p>
  <table class="table table-bordered table-hover">
    <thead>
      <tr>
        <th>#</th>
        <th>Part Number</th>
        <th>UPC</th>
        <th>Location</th>
        <th>Qty</th>
        <th>SN #</th>
        <th>Note</th>
        <!-- <th>Action</th> -->
      </tr>
    </thead>
    <tbody>

    {% for item in data %}
      <tr data-id="{{ item['id'] }}">
        <td><b>{{ loop.index }}</b></td>
        <td class="partnum">{{ item['partnum'] }}</td>
        <td class="upc">{{ item['upc'] }}</td>
        <td class="location">{{ item['location'] }}</td>
        <td class="qty">{{ item['qty'] }}</td>
        <td class="sn">{{ item['sn'] }}</td>
        <td class="note">{{ item['note'] }}</td>
        <!--
        <td>
          <a href="#" class="btn btn-xs btn-info"><span class="glyphicon glyphicon-edit"></span> Edit </a>
          <a href="#" class="btn btn-xs btn-danger"><span class="glyphicon glyphicon-remove"></span> Delete </a>
        </td>
        -->
      </tr>
    {% endfor  %}

    </tbody>
  </table>
  {% else %}
    {% if keyword is not empty %}
      No inventory information found for <b>{{ keyword }}</b> as <b>{{ searchby }}</b>.
    {% endif %}
  {% endif %}
{% endblock %}

{% block csscode %}
.upc, .note { cursor: pointer; }
.upc:hover, .note:hover { text-decoration: underline; }
{% endblock %}

{% block jscode %}
{% endblock %}

{% block docready %}
  // click note to edit note
  $('.note, .sn').click(function() {
    $('tr').removeClass('info');

    var self = $(this);
    var tr = self.closest('tr');

    var id = tr.data('id');
    var note = tr.find('.note');
    var sn = tr.find('.sn');

    tr.addClass('info');

    var modal = new bte.EditInvlocNoteModal({ id: id, note: note.text(), sn: sn.text() });
    modal.success = function(data) {
        showToast('Your change has benn saved', 1000);
        note.text(data.note);
        sn.text(data.sn);
    };
    modal.failure = function(message) {
        showError(message);
        tr.addClass('danger');
    };
    modal.show();
  });

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
{% endblock %}