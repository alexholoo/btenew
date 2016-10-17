{% extends "layouts/base.volt" %}

{% block main %}
<div align="center">

    <div align="left">
        <h2>About this Demo</h2>
    </div>

    <div align="left">
    {% if data is not empty %}
        <pre>{{ data }}</pre>
    {% endif %}
    </div>

</div>
{% endblock %}

{% block jscode %}
{% endblock %}
