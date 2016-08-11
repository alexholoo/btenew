{% extends "layouts/base.volt" %}

{% block main %}
<div align="center" class="well">

	{{ form('class': 'form-search') }}

	<div align="left">
		<h2>Forgot Password?</h2>
	</div>

		{{ form.render('email') }}
		{{ form.render('Send') }}

		<hr>

	</form>

</div>
{% endblock %}
