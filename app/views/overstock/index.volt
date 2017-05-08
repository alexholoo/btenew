{% extends "layouts/base.volt" %}

{% block main %}
<h3 style="margin-top: 0;">New Overstock Items</h3>
<header class="well clearfix" id="searchbox">
  <form role="form" method="post">

      <div class="col-sm-5">
        <input autofocus required type="text" class="form-control" name="keyword" autofocus placeholder="Enter SKU/UPC">
      </div>

      <div class="col-sm-2">
        <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> Submit </button>
      </div>

      <div class="col-sm-2">
        <button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-check"></span> Overstock </button>
      </div>

      <!--
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
      </div>
      -->

  </form>
</header>

{% if items is not empty %}
  <table class="table table-bordered table-hover">
    <thead>
      <tr>
        <th>#</th>
        <th>SKU</th>
        <th>Title</th>
        <th>Cost</th>
        <th>Condition</th>
        <th>Qty</th>
        <th>MPN</th>
        <th>Weight(lbs)</th>
        <th>UPC</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>

    {% for item in items %}
      <tr>
        <td><b class="index">{{ loop.index }}</b></td>
        <td>{{ item['sku'] }}</td>
        <td>{{ item['title'] }}</td>
        <td>{{ item['cost'] }}</td>
        <td>{{ item['condition'] }}</td>
        <td>{{ item['qty'] }}</td>
        <td>{{ item['mpn'] }}</td>
        <td>{{ item['weight'] }}</td>
        <td>{{ item['upc'] }}</td>
        <td class="text-center delete">
          <a href="javascript:;" class="btn btn-xs btn-danger"><span class="glyphicon glyphicon-remove"></span></a>
        </td>
      </tr>
    {% endfor  %}

    </tbody>
  </table>
{% endif %}
{% endblock %}

{% block csscode %}
{% endblock %}

{% block jscode %}
{% endblock %}

{% block docready %}
  $('.delete a').click(function() {
    var tr = $(this).closest('tr');
    var index = tr.index();
    tr.remove();
    ajaxCall('/overstock/delete', { index: index }, function() {
      $('tbody tr').each(function() {
        $(this).find('.index').text($(this).index() + 1);
      });
    });
  });
{% endblock %}
