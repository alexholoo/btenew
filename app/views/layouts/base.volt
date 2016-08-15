<!DOCTYPE html>
<html lang="en-us">
<head>
    <meta charset="utf-8">
    <!--<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">-->
    <title>{% block title %}BTE Intranet{% if pageTitle is defined %} &bull; {{ pageTitle }}{% endif %}{% endblock %}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    {% block cssfile %}
      {{ stylesheet_link('/lib/bootstrap/3.3.7/css/bootstrap.min.css') }}
    {% endblock %}

    <style type="text/css">
      {# body { font-family: Verdana,sans-serif; } #}
      {% block csscode %}{% endblock %}
    </style>

    <!-- FAVICONS -->
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <!-- GOOGLE FONT -->
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,300,400,700">
</head>
<body>
    {% include "partials/navigation.volt" %}

    {% block sidebar %}{% endblock %}

    <div class="container main-container">
        <?php $this->flashSession->output(); ?>
        {% block main %}{% endblock %}
    </div>

    {# loading icon placholder #}
    <div id="loading" style="display:none;"></div>
    <div class='toast' style='display:none'></div>

    {% block jsfile %}
      {{ javascript_include('/lib/jquery/jquery-3.1.0.min.js') }}
      {{ javascript_include('/lib/bootstrap/3.3.7/js/bootstrap.min.js') }}
    {% endblock %}

    <script type="text/javascript">
        {% block jscode %}{% endblock %}
        $(document).ready(function() {
            {% block docready %}{% endblock %}
        });
    </script>
</body>
</html>
