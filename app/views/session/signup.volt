{% extends "layouts/base.volt" %}

{% block main %}
<div align="center">

	{{ form('class': 'form-search') }}

		<div align="left">
			<h2>Sign Up</h2>
		</div>

        <div class="form-group">
			{{ form.label('name') }}
			{{ form.render('name') }}
			{{ form.messages('name') }}
        </div>

        <div class="form-group">
			{{ form.label('email') }}
			{{ form.render('email') }}
			{{ form.messages('email') }}
        </div>

        <div class="form-group">
			{{ form.label('password') }}
			{{ form.render('password') }}
			{{ form.messages('password') }}
        </div>

        <div class="form-group">
			{{ form.label('confirmPassword') }}
			{{ form.render('confirmPassword') }}
			{{ form.messages('confirmPassword') }}
        </div>

        <div class="form-group">
			{{ form.render('terms') }}
			{{ form.label('terms') }}
			{{ form.messages('terms') }}
        </div>

        <div class="form-group">
			{{ form.render('Sign Up') }}
        </div>

		{{ form.render('csrf', ['value': security.getToken()]) }}
		{{ form.messages('csrf') }}

		<hr>

	</form>

</div>
{% endblock %}
