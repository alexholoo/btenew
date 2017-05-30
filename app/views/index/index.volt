{% extends "layouts/base.volt" %}

{% block main %}
{{ flash.output() }}
<header class="jumbotron subhead" style="margin-top:40px;">
  <div class="hero-unit">
    <h1>Welcome!</h1>
    <p class="lead">Everything is awesome.</p>

    {% if not userLoggedIn %}
    <div align="right">
      <a href="/user/login" class="btn btn-primary btn-large"><span class="glyphicon glyphicon-user"></span> User Login </a>
    </div>
    {% endif %}
  </div>
</header>
{% endblock %}
