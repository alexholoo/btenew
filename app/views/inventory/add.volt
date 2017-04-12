{% extends "layouts/base.volt" %}

{% block main %}
  <h3 style="margin-top:0;">Inventory Add</h3>
  <div class="well clearfix">
    <form class="form-inline" role="form" method="POST">
      <div class="form-group">
        <input type="text" class="form-control" name="partnum" placeholder="Part Number" data-toggle="tooltip" title="Part Number" value="{{partnum}}" autofocus>
      </div>
      <div class="form-group">
        <input type="text" class="form-control" name="upc" placeholder="UPC" data-toggle="tooltip" title="UPC" value="{{upc}}">
      </div>
      <div class="form-group">
        <input type="text" class="form-control" name="location" placeholder="Location" data-toggle="tooltip" title="Location" value="{{location}}">
      </div>
      <div class="form-group">
        <input type="text" class="form-control" name="qty" placeholder="Quantity" data-toggle="tooltip" title="Quantity" value="{{qty}}">
      </div>
      <div class="form-group">
        <input type="text" class="form-control" name="sn" placeholder="SN #" data-toggle="tooltip" title="SN #" value="{{sn}}">
      </div>
      <div class="form-group">
        <input type="text" class="form-control" name="note" placeholder="Note" data-toggle="tooltip" title="Note" value="{{note}}">
      </div>
      <div class="form-group">
        <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-tags"></span>&nbsp; Enter </button>
      </div>
    </form>
  </div>

  {% if error is not empty %}
    <p class="text-danger">{{ error }}</p>
  {% endif %}

  {% if items is not empty %}
  <table class="table table-bordered table-hover">
    <thead>
      <tr>
        <th class="text-center">#</th>
        <th>Part Number</th>
        <th>UPC</th>
        <th>Location</th>
        <th>Qty</th>
        <th>SN #</th>
        <th>Note</th>
        <!--
        <th class="text-center">Action</th>
        -->
      </tr>
    </thead>
    <tbody>

    {% for item in items %}
      <tr data-id="{{ item['id'] }}">
        <td class="text-center"><b>{{ loop.index }}</b></td>
        <td>{{ item['partnum'] }}</td>
        <td>{{ item['upc'] }}</td>
        <td>{{ item['location'] }}</td>
        <td>{{ item['qty'] }}</td>
        <td>{{ item['sn'] }}</td>
        <td>{{ item['note'] }}</td>
        <!--
        <td class="text-center fit-to-text">
          <a href="#" class="btn btn-xs btn-danger"><span class="glyphicon glyphicon-remove"></span></a>
        </td>
        -->
      </tr>
    {% endfor %}

    </tbody>
  </table>
  {% endif %}
{% endblock %}

{% block csscode %}
  .main-container { width: 1250px; }
  .fit-to-text { width:1%; white-space:nowrap; }
{% endblock %}

{% block jscode %}
{% endblock %}

{% block docready %}
$('[data-toggle="tooltip"]').tooltip();
{% endblock %}
