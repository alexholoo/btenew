{% extends "layouts/base.volt" %}

{% block main %}
<div class="container">
  <h2>Bar Code</h2>
  <p>This page helps adding barcode to order sheets</p>
  {% if error is not empty %}
    <div class="alert alert-danger alert-dismissable">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
      <strong>Error!</strong> {{ error }}
    </div>
  {% endif %}
  <form method="POST" enctype="multipart/form-data">
  <table class="table table-bordered">
    <tbody>
      <tr>
        <th>Amazon Order File: </th>
        <td><input type="file" name="amazonOrderFile"></td>
      </tr>
      <tr>
        <th>Newegg Order File: </th>
        <td><input type="file" name="neweggOrderFile"></td>
      </tr>
      <tr>
        <th>eBay Order File: </th>
        <td><input type="file" name="ebayOrderFile"></td>
      </tr>
      <tr>
        <th>Rakuten Order File: </th>
        <td><input type="file" name="rakutenOrderFile"></td>
      </tr>
      <tr>
        <th>Bestbuy Order File: </th>
        <td><input type="file" name="bestbuyOrderFile"></td>
      </tr>
    </tbody>
  </table>
  <input type="submit" class="btn btn-primary" value="Add Barcodes">
  </form>
</div>
{% endblock %}
