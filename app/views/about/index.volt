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
      <a href="javascript:;" id="about">Click Me</a>
    </div>

</div>
{% endblock %}

{% block jscode %}
{% endblock %}

{% block docready %}
$('#about').on('click', function(){
  var modal = new bte.OrderDetailModal('701-5568212-2791469');
  modal.show();
});
{% endblock %}
