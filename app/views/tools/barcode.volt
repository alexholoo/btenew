{% extends "layouts/base.volt" %}

{% block main %}
<div class="container">
  <h2>Bar Code</h2>
  <p>This page helps adding barcode to order sheets</p>
  <form method="POST">
  <table class="table table-bordered">
    <thead>
      <tr>
        <th class="text-center">#</th>
        <th>Filename</th>
        <th>Output</th>
        <th style="text-align: center;">Status</th>
      </tr>
    </thead>
    <tbody>
    {% for info in fileinfo %}
      <tr>
        <td class="text-center"><input type="checkbox" checked></input></td>
        <td{% if not info['exists'] %} class="danger"{% endif %}>{{ info['filename'] }}</td>
        <td>{{ info['output'] }}</td>
        <td class="text-center">{% if info['created'] %}<span class="label label-success"><span class="glyphicon glyphicon-ok"></span></span>{% endif %}</td>
      </tr>
    {% endfor %}
    </tbody>
  </table>
  <button type="submit" class="btn btn-primary">Add Barcodes</button>
  </form>
</div>
{% endblock %}
