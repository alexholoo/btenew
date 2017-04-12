{% extends "layouts/base.volt" %}

{% block main %}
<header class="well clearfix" id="searchbox">
  <form role="form" method="post">

      <div class="col-sm-12">
        <h3 style="margin-top: 0;">Inventory Location Search</h3>
      </div>

      <div class="col-sm-10">
        <input autofocus required type="text" class="form-control" name="keyword" autofocus placeholder="Enter PartNumber/UPC/Location/Note">
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
          <input type="radio" name="searchby" value="note" {% if searchby == 'note' %}checked{% endif %}>Note
        </label>
      </div>

  </form>
</header>

{% if data is not empty %}
  <p>Search result for <b>{{ keyword }}</b> in <b>{{ searchby }}</b>:</p>
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
        <td>{{ item['partnum'] }}</td>
        <td>{{ item['upc'] }}</td>
        <td>{{ item['location'] }}</td>
        <td>{{ item['qty'] }}</td>
        <td>{{ item['sn'] }}</td>
        <td>{{ item['note'] }}</td>
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
