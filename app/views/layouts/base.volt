<!DOCTYPE html>
<html lang="en-us">
<head>
  <meta charset="utf-8">
  <!--<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">-->
  <title>{% block title %}{% if pageTitle is defined %}{{ pageTitle }} &bull; {% endif %}BTE Intranet{% endblock %}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

  {% block cssfile %}
    {{ stylesheet_link('/lib/bootstrap/3.3.7/css/bootstrap.min.css') }}
    {{ stylesheet_link('/lib/jquery/plugins/jquery.toast.min.css') }}
    {{ stylesheet_link('/lib/font-awesome/4.7.0/css/font-awesome.min.css') }}
    {{ assets.outputCss() }}
  {% endblock %}

  <style type="text/css">
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

  <div class="container main-container" style="margin-top:60px;">
    <?php $this->flashSession->output(); ?>
    {% block main %}{% endblock %}
  </div>

  {# loading icon placholder #}
  <div id="loading" style="display:none;"></div>
  <div id='toast' style='display:none'></div>

  {% block jsfile %}
    {{ javascript_include('/lib/jquery/jquery-3.1.0.min.js') }}
    {{ javascript_include('/lib/jquery/plugins/jquery.toast.min.js') }}
    {{ javascript_include('/lib/bootstrap/3.3.7/js/bootstrap.min.js') }}
    {{ javascript_include('/lib/layer/layer.js') }}
    {{ assets.outputJs() }}
  {% endblock %}

  <script type="text/javascript">
    {% block jscode %}{% endblock %}
    $(document).ready(function() {
      layer.config({
        type: 1,
        moveType: 1,
        skin: 'layui-layer-molv',
      });
      {% block docready %}{% endblock %}
    });
  </script>
</body>
</html>
