{% extends "layouts/base.volt" %}

{% block main %}
<header class="jumbotron subhead" id="overview">
	<div class="hero-unit">
		<h1>Welcome!</h1>
		<p class="lead">This is a awesome website</p>

		<div align="right">
            {{ link_to('#', '<i class="icon-ok icon-white"></i> Create an Account', 'class': 'btn btn-primary btn-large') }}
		</div>
	</div>
</header>
{% endblock %}