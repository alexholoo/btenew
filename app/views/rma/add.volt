{% extends "layouts/base.volt" %}

{% block main %}
  <h2 style="margin-top:0;">New RMA Record</h2>
{% endblock %}

{% block csscode %}
{% endblock %}

{% block docready %}
  layer.config({
    type: 1,
    moveType: 1,
    skin: 'layui-layer-molv',
  });
{% endblock %}
