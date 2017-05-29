{% extends "layouts/base.volt" %}

{% block main %}
<header class="jumbotron subhead" id="overview">
	<div class="hero-unit">
		<h1>Welcome!</h1>
		<p class="lead">This is a awesome website</p>

        {% if not userLoggedIn %}
		<div align="right">
          <a href="/user/login" class="btn btn-primary btn-large"><span class="glyphicon glyphicon-user"></span> User Login </a>
		</div>
        {% endif %}
	</div>
</header>
{% endblock %}
