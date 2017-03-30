{% extends "layouts/base.volt" %}

{% block main %}
<header class="jumbotron subhead clearfix" id="searchbox">
  <form role="form" method="post">

      <div class="col-sm-12">
        <h2 style="margin-top: 0;">Inventory search</h2>
      </div>

      <div class="col-sm-10">
        <input autofocus required type="text" class="form-control" name="keyword" autofocus placeholder="Enter PartNumber/UPC/Location/Quantity">
      </div>

      <div class="col-sm-2">
        <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span> Search </button>
      </div>

      <div class="col-sm-12">
        <label class="radio-inline">
          <input type="radio" name="searchby" value="partnum" {% if searchby == 'partnum' %}checked{% endif %}>Part number
        </label>
        <label class="radio-inline">
          <input type="radio" name="searchby" value="upc" {% if searchby == 'upc' %}checked{% endif %}>UPC
        </label>
        <label class="radio-inline">
          <input type="radio" name="searchby" value="location" {% if searchby == 'location' %}checked{% endif %}>Location
        </label>
        <label class="radio-inline">
          <input type="radio" name="searchby" value="qty" {% if searchby == 'qty' %}checked{% endif %}>Quantity
        </label>
      </div>

  </form>
</header>

{% if data is not empty %}
  Search result for <b>{{ keyword }}</b> in <b>{{ searchby }}</b>:
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

    {% for inventory in data %}
      <tr>
        <td><b>{{ loop.index }}</b></td>
        <td>{{ inventory['partnum'] }}</td>
        <td>{{ inventory['upc'] }}</td>
        <td>{{ inventory['location'] }}</td>
        <td>{{ inventory['qty'] }}</td>
        <td>{{ inventory['sn'] }}</td>
        <td>{{ inventory['note'] }}</td>
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
