{% extends "layouts/base.volt" %}

{% block main %}
  <h2>SKU Information</h2>
  <div class="well">
    <form class="form-inline" role="form" method="POST">
      <div class="form-group col-xs-5 col-lg-5">
        <input class="form-control" name="sku" placeholder="Enter SKU/UPC/MPN" value="" type="text" autofocus style="width:100%">
      </div>
      <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span> Search </button>
    </form>
  </div>

  {% if data is not empty %}
  <p>SKU information for <b>{{ sku }}</b></p>
  <table class="table table-bordered table-hover">
    <tbody>
      <tr class="active">
        <th align="left">Name</th>
        <th align="left" colspan="3">{{ data['name'] }}</th>
      </tr>
      <tr>
        <th align="left">SKU</th>
        <td align="left">{{ data['SKU'] }}</td>
        <th align="left">Recommended</th>
        <td align="left">{{ data['recommended_pn'] }}</td>
      </tr>
      <tr>
        <th align="left">Quantity</th>
        <td align="left">{{ data['overall_qty'] }}</td>
        <th align="left">Best Cost</th>
        <td align="left">$ {{ data['best_cost'] }}</td>
      </tr>
      <tr>
        <th align="left">Dimension</th>
        <td align="left">{{ data['Width'] }} x {{ data['Length'] }} x {{ data['Depth'] }} inch</td>
        <th align="left">Weight</th>
        <td align="left">{{ data['Weight'] }} lb</td>
      </tr>
      <tr>
        <th align="left">Mfr/UPC/MPN</th>
        <td align="left">{{ data['Manufacturer'] }}</td>
        <td align="left">{{ data['UPC'] }}</td>
        <td align="left">{{ data['MPN'] }}</td>
      </tr>
      <tr class="active">
        <td align="center" colspan="4"><b>Price &amp; Availability</b></td>
      </tr>
      <tr>
        <th align="left">SYNNEX</th>
        <td align="left">{{ data['syn_pn'] }}</td>
        <td align="left">{% if data['syn_cost'] is not empty %}$ {% endif %}{{ data['syn_cost'] }}</td>
        <td align="left">{{ data['syn_qty'] }}</td>
      </tr>
      <tr>
        <th align="left">TECHDATA </th>
        <td align="left">{{ data['td_pn'] }}</td>
        <td align="left">{% if data['td_cost'] is not empty %}$ {% endif %}{{ data['td_cost'] }}</td>
        <td align="left">{{ data['td_qty'] }}</td>
      </tr>
      <tr>
        <th align="left">INGRAM</th>
        <td align="left">{{ data['ing_pn'] }}</td>
        <td align="left">{% if data['ing_cost'] is not empty %}$ {% endif %}{{ data['ing_cost'] }}</td>
        <td align="left">{{ data['ing_qty'] }}</td>
      </tr>
      <tr>
        <th align="left">D&amp;H</th>
        <td align="left">{{ data['dh_pn'] }}</td>
        <td align="left">{% if data['dh_cost'] is not empty %}$ {% endif %}{{ data['dh_cost'] }}</td>
        <td align="left">{{ data['dh_qty'] }}</td>
      </tr>
      <tr>
        <th align="left">ASI</th>
        <td align="left">{{ data['asi_pn'] }}</td>
        <td align="left">{% if data['asi_cost'] is not empty %}$ {% endif %}{{ data['asi_cost'] }}</td>
        <td align="left">{{ data['asi_qty'] }}</td>
      </tr>
      <tr>
        <th align="left">TAK</th>
        <td align="left">{{ data['tak_pn'] }}</td>
        <td align="left">{% if data['tak_cost'] is not empty %}$ {% endif %}{{ data['tak_cost'] }}</td>
        <td align="left">{{ data['tak_qty'] }}</td>
      </tr>
      <tr>
        <th align="left">EPROM</th>
        <td align="left">{{ data['ep_pn'] }}</td>
        <td align="left">{% if data['ep_cost'] is not empty %}$ {% endif %}{{ data['ep_cost'] }}</td>
        <td align="left">{{ data['ep_qty'] }}</td>
      </tr>
      <tr>
        <th align="left">BTE</th>
        <td align="left">{{ data['BTE_PN'] }}</td>
        <td align="left">{% if data['BTE_cost'] is not empty %}$ {% endif %}{{ data['BTE_cost'] }}</td>
        <td align="left">{{ data['BTE_qty'] }}</td>
      </tr>
      <tr class="active">
        <th align="left">Note</th>
        <td align="left" colspan="3">{{ data['note'] }}</td>
      </tr>
    </tbody>
  </table>
  {% else %}
    No information found.
  {% endif %}
{% endblock %}

{% block jscode %}
{% endblock %}
