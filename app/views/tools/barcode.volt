{% extends "layouts/base.volt" %}

{% block main %}
<div class="container">
  <h2>Bar Code</h2>
  <p>This page helps adding barcode to order sheets</p>
  <form method="POST" enctype="multipart/form-data">
    <div class="form-group">
        <label>Select Order File (PDF)</label>
        <input type="file" name="fname">
    </div>

    <fieldset>
    <div class="form-group">
        <input type="radio" name="ftype" value="amazonOrderFile" checked>
        <label>Amazon Order File: </label>
    </div>

    <div class="form-group">
        <input type="radio" name="ftype" value="neweggOrderFile">
        <label>Newegg Order File: </label>
    </div>

    <div class="form-group">
        <input type="radio" name="ftype" value="ebayOrderFile">
        <label>eBay Order File: </label>
    </div>

    <div class="form-group">
        <input type="radio" name="ftype" value="rakutenOrderFile">
        <label>Rakuten Order File: </label>
    </div>

    <div class="form-group">
        <input type="radio" name="ftype" value="bestbuyOrderFile">
        <label>Bestbuy Order File: </label>
    </div>
    </fieldset>

    <input type="submit" class="btn btn-success" value="Add Barcodes">
  </form>
</div>
{% endblock %}

{% block csscode %}
{% endblock %}

{% block jscode %}
{% endblock %}

{% block docready %}
{% endblock %}
