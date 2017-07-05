{% extends "layouts/base.volt" %}

{% block main %}
<div class="hidden-print">
<h2>Print Barcode Label</h2>
<div class="well clearfix">
  <form class="form-inline" role="form" method="POST">
    <div class="form-group col-xs-5">
      <input class="form-control" type="text" placeholder="Enter SKU" name="sku" style="width:100%">
    </div>
    <div class="form-group col-xs">
      <button type="submit" class="btn btn-primary">
        <span class="glyphicon glyphicon-plus"></span> Make Label
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
  padding: 0 20px;
}
@media print {
  @page { size:  6in 4in; margin: 0mm; }
  body { margin: 0px; }
  #label {
    width: 6in;
    height: 4in;
    border: none;
    padding: none;
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
JsBarcode("#barcode", "{{ data['upc'] }}", {
  format: "upc",
  width: 3,
  height: 40,
  fontSize: 12,
});
window.print();
{% endblock %}
