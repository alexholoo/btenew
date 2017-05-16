{% extends "layouts/base.volt" %}

{% block main %}
  <h2 style="margin-top:0;">Overstock Log</h2>

  <header class="well clearfix" id="searchbox">
    <form role="form" method="post">
      <div class="col-sm-4">
        <input autofocus type="text" class="form-control" name="keyword" autofocus placeholder="Enter SKU">
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
      <li><a href="/overstock/viewlog"><span class="glyphicon glyphicon-fast-backward"></span></a></li>
      <li><a href="/overstock/viewlog?page={{ page.before }}"><span class="glyphicon glyphicon-backward"></span></a></li>
      <li><a href="/overstock/viewlog?page={{ page.next }}"><span class="glyphicon glyphicon-forward"></span></a></li>
      <li><a href="/overstock/viewlog?page={{ page.last }}"><span class="glyphicon glyphicon-fast-forward"></span></a></li>
    </ul>
  </div>

  {% if page.items is not empty %}
  <table id="overstocktbl" class="table table-bordered table-hover">
    <thead>
      <tr>
        <th>Time</th>
        <th>SKU</th>
        <th>Condition</th>
        <th>Cost</th>
        <th>Qty</th>
        <th>Product</th>
        <th>MPN</th>
        <th>UPC</th>
        {# <th>Weight</th> #}
      </tr>
    </thead>
    <tbody>
    {% for row in page.items %}
      <tr>
        <td nowrap{% if row['date'] == today %} class="text-danger"{% endif %}>{{ row['datetime'] }}</td>
        <td>{{ row['sku'] }}</td>
        <td>{{ row['condition'] }}</td>
        <td>{{ row['cost'] }}</td>
        <td>{{ row['qty'] }}</t>
        <td>{{ row['title'] }}</t>
        <td>{{ row['mpn'] }}</td>
        <td>{{ row['upc'] }}</td>
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
      <li><a href="/overstock/viewlog">First</a></li>
      <li><a href="/overstock/viewlog?page={{ page.before }}">Prev</a></li>
      <li><a href="/overstock/viewlog?page={{ page.next }}">Next</a></li>
      <li><a href="/overstock/viewlog?page={{ page.last }}">Last</a></li>
    </ul>
  </div>

  {% endif %}
{% endblock %}

{% block csscode %}
  #overstocktbl td { vertical-align: middle; }
{% endblock %}

{% block docready %}
  $('#pagesel').change(function() {
    window.location = '/overstock/viewlog?page=' + $(this).val();
  })
{% endblock %}
