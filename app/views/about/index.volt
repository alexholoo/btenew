{% extends "layouts/base.volt" %}

{% block main %}
<div align="center">

    <div align="left">
      <h2>About</h2>
    </div>

    <div align="left">
      <pre>
      Server: {{ host }} {{ serverIP }}<br>
      Client: {{ clientIP }}<br>
      Browser: {{ userAgent }}<br>
      </pre>
    </div>

</div>
{% endblock %}

{% block jscode %}
{% endblock %}
