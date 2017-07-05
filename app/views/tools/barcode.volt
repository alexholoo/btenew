{% extends "layouts/base.volt" %}

{% block main %}
<div class="container">
  <h2>Bar Code</h2>
  <p>This page helps adding barcode to order sheets</p>
  <form method="POST" enctype="multipart/form-data">
    <div class="row">
      <div class="col-xs-6">
        <div class="form-group">
          <input type="file" name="fname" id="input01">
        </div>
      </div>
      <div class="col-xs-2">
        <div class="form-group">
          <button type="submit" class="btn btn-primary">
            <span class="glyphicon glyphicon-plus"></span> Add Barcodes
          </button>
        </div>
      </div>
    </div><br>

    <div class="row">
      <div class="col-xs-8">
        <div class="form-group">
          <input type="radio" name="ftype" value="amazonOrderFile" checked>
          <label>Amazon Order File: </label>
        </div>

        <div class="form-group">
          <input type="radio" name="ftype" value="neweggOrderFile" disabled>
          <label>Newegg Order File: </label>
        </div>

        <div class="form-group">
          <input type="radio" name="ftype" value="ebayOrderFile" disabled>
          <label>eBay Order File: </label>
        </div>

        <div class="form-group">
          <input type="radio" name="ftype" value="rakutenOrderFile" disabled>
          <label>Rakuten Order File: </label>
        </div>

        <div class="form-group">
          <input type="radio" name="ftype" value="bestbuyOrderFile" disabled>
          <label>Bestbuy Order File: </label>
        </div>
      </div>
    </div>

  </form>
</div>
{% endblock %}

{% block csscode %}
{% endblock %}

{% block jsfile %}
  {{ super() }}
  {{ javascript_include('/lib/bootstrap-filestyle.min.js') }}
{% endblock %}

{% block jscode %}
{% endblock %}

{% block docready %}
  $('#input01').filestyle();
{% endblock %}
