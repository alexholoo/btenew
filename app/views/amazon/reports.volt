{% extends "layouts/base.volt" %}

{% block main %}
  <h3 style="margin-top:0;">Amazon Reports</h3>

  {% if error is not empty %}
    <p class="text-danger">{{ error }}</p>
  {% endif %}

  {% if reports is not empty %}
  <table class="table table-bordered table-hover">
    <thead>
      <tr>
        <th>#</th>
        <th>Report File (US)</th>
        <th>Report File (CA)</th>
        <th>Date Time</th>
      </tr>
    </thead>
    <tbody>

    {% for report in reports -%}
      <tr>
        <td>{{ loop.index }}</td>
        {% if report['filetime'] -%}
        <td><a href="/amazon/reports/{{ report['file_us'] }}">{{ report['file_us'] }}</a></td>
        <td><a href="/amazon/reports/{{ report['file_ca'] }}">{{ report['file_ca'] }}</a></td>
        {% else -%}
        <td>{{ report['file_us'] }}</td>
        <td>{{ report['file_ca'] }}</td>
        {% endif -%}
        <td>{{ report['filetime'] }}</td>
      </tr>
    {% endfor -%}

    </tbody>
  </table>
  {% endif %}
{% endblock %}

{% block jscode %}
{% endblock %}

{% block docready %}
$('[data-toggle="tooltip"]').tooltip();
{% endblock %}
