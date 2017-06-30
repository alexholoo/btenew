{% extends "layouts/base.volt" %}

{% block main %}
  <h3 style="margin-top:0;">SKU Information</h3>
  <div class="well clearfix">
    <form class="form-inline" role="form" method="POST">
      <div class="form-group col-xs-5 col-lg-5">
        <input class="form-control" name="sku" placeholder="Enter SKU" value="" type="text" autofocus style="width:100%">
      </div>
      <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span> Search </button>
    </form>
  </div>

  {% if data is not empty %}
  <p>SKU information for <b>{{ sku }}</b></p>
  <table class="table table-bordered table-hover">
    <tbody>
      <tr class="active">
        <th>Name</th>
        <th colspan="3">{{ data['name'] }}</th>
      </tr>
      <tr>
        <th class="active">SKU</th>
        <td>{{ data['SKU'] }}</td>
        <th class="active">Recommended</th>
        <td>{{ data['recommended_pn'] }}</td>
      </tr>
      <tr>
        <th class="active">Quantity</th>
        <td>{{ data['overall_qty'] }}</td>
        <th class="active">Best Cost</th>
        <td>$ {{ data['best_cost'] }}</td>
      </tr>
      <tr>
        <th class="active">Dimension</th>
        <td>{{ data['Width'] }} x {{ data['Length'] }} x {{ data['Depth'] }} inch</td>
        <th class="active">Weight</th>
        <td>{{ data['Weight'] }} lb</td>
      </tr>
      <tr>
        <th class="active">MFR/UPC/MPN</th>
        <td data-container="body" data-toggle="tooltip" title="MFR">{{ data['MFR'] }}</td>
        <td data-container="body" data-toggle="tooltip" title="UPC">{{ data['UPC'] }}</td>
        <td data-container="body" data-toggle="tooltip" title="MPN">{{ data['MPN'] }}</td>
      </tr>
      <tr class="active">
        <td align="center" colspan="4"><b>Price &amp; Availability</b></td>
      </tr>
      <tr>
        <th class="active">SYNNEX</th>
        <td>{{ data['syn_pn'] }}</td>
        <td>{% if data['syn_cost'] is not empty %}$ {% endif %}{{ data['syn_cost'] }}</td>
        <td>{{ data['syn_qty'] }}</td>
      </tr>
      <tr>
        <th class="active">TECHDATA</th>
        <td>{{ data['td_pn'] }}</td>
        <td>{% if data['td_cost'] is not empty %}$ {% endif %}{{ data['td_cost'] }}</td>
        <td>{{ data['td_qty'] }}</td>
      </tr>
      <tr>
        <th class="active">INGRAM</th>
        <td>{{ data['ing_pn'] }}</td>
        <td>{% if data['ing_cost'] is not empty %}$ {% endif %}{{ data['ing_cost'] }}</td>
        <td>{{ data['ing_qty'] }}</td>
      </tr>
      <tr>
        <th class="active">D&amp;H</th>
        <td>{{ data['dh_pn'] }}</td>
        <td>{% if data['dh_cost'] is not empty %}$ {% endif %}{{ data['dh_cost'] }}</td>
        <td>{{ data['dh_qty'] }}</td>
      </tr>
      <tr>
        <th class="active">ASI</th>
        <td>{{ data['asi_pn'] }}</td>
        <td>{% if data['asi_cost'] is not empty %}$ {% endif %}{{ data['asi_cost'] }}</td>
        <td>{{ data['asi_qty'] }}</td>
      </tr>
      <tr>
        <th class="active">TAK</th>
        <td>{{ data['tak_pn'] }}</td>
        <td>{% if data['tak_cost'] is not empty %}$ {% endif %}{{ data['tak_cost'] }}</td>
        <td>{{ data['tak_qty'] }}</td>
      </tr>
      <tr>
        <th class="active">EPROM</th>
        <td>{{ data['ep_pn'] }}</td>
        <td>{% if data['ep_cost'] is not empty %}$ {% endif %}{{ data['ep_cost'] }}</td>
        <td>{{ data['ep_qty'] }}</td>
      </tr>
      <tr>
        <th class="active">BTE</th>
        <td>{{ data['BTE_PN'] }}</td>
        <td>{% if data['BTE_cost'] is not empty %}$ {% endif %}{{ data['BTE_cost'] }}</td>
        <td>{{ data['BTE_qty'] }}</td>
      </tr>
      <tr>
        <th class="active">Note</th>
        <td colspan="3">{{ data['note'] }}</td>
      </tr>
    </tbody>
  </table>

  <table class="table table-bordered">
    <tbody>
      <tr class="active">
        <th>Product Description</th>
        <th>Product Image</th>
      </tr>
      <tr>
        <td width="50%">{{ desc }}</td>
        <td width="50%">{% if imgurl is not empty %}<img src="{{ imgurl }}">{% else %}&nbsp;{% endif %}</td>
      </tr>
    </tbody>
  </table>
  {% else %}
    No information found.
  {% endif %}
{% endblock %}

{% block docready %}
$('[data-toggle="tooltip"]').tooltip();
{% endblock %}

{% block jscode %}
{% endblock %}
