{% extends "layouts/base.volt" %}

{% block main %}
  <h2 style="margin-top:0;">RMA records</h2>
  <!--
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
  -->

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
      <li><a href="/rma/records"><span class="glyphicon glyphicon-fast-backward"></span></a></li>
      <li><a href="/rma/records?page={{ page.before }}"><span class="glyphicon glyphicon-backward"></span></a></li>
      <li><a href="/rma/records?page={{ page.next }}"><span class="glyphicon glyphicon-forward"></span></a></li>
      <li><a href="/rma/records?page={{ page.last }}"><span class="glyphicon glyphicon-fast-forward"></span></a></li>
    </ul>
  </div>

  {% if page.items is not empty %}
  <table id="rmatbl" class="table table-bordered table-hover">
    <thead>
      <tr>
        <th>Date In</th>
        <th>Rec#</th>
        <th>PartNum</th>
        <th>Product</th>
        <th>Status</th>
        <th>After Checked</th>
        <th>DONE</th>
      </tr>
    </thead>
    <tbody>
    {% for row in page.items %}
      <tr data-id="{{ row['id'] }}">
        <td>{{ row['date_in'] }}</td>
        <td>{{ row['our_recnum'] }}</td>
        <td>{{ row['partnum'] }}</td>
        <td>{{ row['product_desc'] }}</td>
        <td>{{ row['status'] }}</td>
        <td>{{ row['after_checked'] }}</td>
        <td>{{ row['done'] }}</td>
      </tr>
    {% endfor %}
    </tbody>
  </table>

  <div>
    <ul class="pagination pull-left" style="margin: 0;">
      <li>Page: {{ page.current }} of {{ page.total_pages }}</li>
    </ul>

    <ul class="pagination pull-right" style="margin: 0 0 10px 0;">
      <li><a href="/rma/records">First</a></li>
      <li><a href="/rma/records?page={{ page.next }}">Next</a></li>
      <li><a href="/rma/records?page={{ page.before }}">Prev</a></li>
      <li><a href="/rma/records?page={{ page.last }}">Last</a></li>
    </ul>
  </div>
  {% endif %}
{% endblock %}

{% block csscode %}
  #rmatbl td, #rmatbl th { vertical-align: middle; }
  tbody tr { cursor: pointer; }
  .order-id { cursor: pointer; }
  .order-id:hover { text-decoration: underline; }
{% endblock %}

{% block docready %}
  $('#pagesel').change(function() {
    window.location = '/rma/records?page=' + $(this).val();
  })

  // click on table row
  $('tbody tr').click(function() {
    var id = $(this).data('id');
    window.location = '/rma/detail/' + id;
  });

  // click on order id
  $('.order-id').click(function() {
    $('tr').removeClass('info');

    var tr = $(this).closest('tr');
    tr.addClass('info');

    var orderId = $(this).text();
    var modal = new bte.OrderInfoModal(orderId);
    modal.show();
  });
{% endblock %}
