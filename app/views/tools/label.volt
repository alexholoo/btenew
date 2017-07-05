{% extends "layouts/base.volt" %}

{% block main %}
<div class="hidden-print">
<h2>Print Barcode Label</h2>
<div class="well clearfix">
  <form class="form-inline" role="form" method="POST">
    <div class="form-group col-xs-3">
      <input class="form-control" type="text" placeholder="Enter SKU" name="sku" style="width:100%">
    </div>
    <div class="form-group col-xs-3">
      <input class="form-control" type="text" name="condition" placeholder="Condition" list="condlist" style="width:100%">
      <datalist id="condlist">
        <select>
          <option value="New">
          <option value="Open Box">
          <option value="Used">
          <option value="Refurb">
        </select>
      </datalist>
    </div>
    <div class="form-group col-xs">
      <button type="submit" class="btn btn-primary">
        <span class="glyphicon glyphicon-barcode"></span> Print Label
      </button>
    </div>
  </form>
</div>
</div>

{% if data is not empty %}
<div id="label">
  <h4>{{ data['name'] }}</h4><br>
  <p>{{ data['sku'] }}</p><br>
  <p>Condition: {{ data['condition'] }}</p><br>
  <p>UPC: {{ data['upc'] }}</p>
  <p align="center"><img id="barcode"></p>
</div>
{% endif %}
{% endblock %}

{% block csscode %}
#label {
  width: 6in;
  height: 4in;
  border: 1px black solid;
  padding: 20px;
}
@media print {
  @page { size:  6in 4in; margin: 0mm; }
  body { margin: 0px; }
  .main-container { margin: 0px; }
  #label {
    width: 6in;
    height: 4in;
    border: none;
    padding-top: 20px;
  }
}
{% endblock %}

{% block jsfile %}
  {{ super() }}
  {{ javascript_include('/lib/JsBarcode.all.min.js') }}
{% endblock %}

{% block jscode %}
{% endblock %}

{% block docready %}
  {% if data is not empty %}
    JsBarcode("#barcode", "{{ data['upc'] }}", {
      format: "upc",
      width: 2,
      height: 40,
      fontSize: 12,
    });
    window.print();
  {% endif %}
{% endblock %}
