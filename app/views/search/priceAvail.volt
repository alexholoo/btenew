{% extends "layouts/base.volt" %}

{% block main %}
  <h3 style="margin-top:0;">Price & Availability</h3>
  <div class="well clearfix">
    <form class="form-inline" role="form" method="POST">
      <div class="form-group col-xs-5 col-lg-5">
        <input class="form-control" name="sku" placeholder="Part number" value="{{ sku }}" type="text" autofocus style="width:100%">
      </div>
      <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span> Search </button>
    </form>
  </div>

  {% if data is not empty %}
  <table class="table table-bordered table-hover">
    <thead>
      <tr>
        <th>PartNum</th>
        <th>Price</th>
        <th>Branch</th>
        <th>Qty</th>
      </tr>
    </thead>
    <tbody>
    {% for item in data %}
        {% if item['avail'] is not empty %}
          {% for info in item['avail'] %}
            {% if loop.first %}
              <tr>
                {% if item['sku'] == sku %}<td><b>{{ item['sku'] }}</b></td>{% else %}<td>{{ item['sku'] }}</td>{% endif %}
                <td>{{ item['price'] }}</td>
                <td>{{ info['branch'] }}</td>
                <td>{{ info['qty'] }}</td>
              </tr>
            {% else %}
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>{{ info['branch'] }}</td>
                <td>{{ info['qty'] }}</td>
              </tr>
            {% endif %}
          {% endfor %}
        {% else %}
          <tr>
            {% if item['sku'] == sku %}<td><b>{{ item['sku'] }}</b></td>{% else %}<td>{{ item['sku'] }}</td>{% endif %}
            <td>{{ item['price'] }}</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        {% endif %}
      </tr>
    {% endfor %}
    </tbody>
  </table>
  {% else %}
    No information found.
  {% endif %}
{% endblock %}

{% block jscode %}

function priceAvailHtml(items) {
  var content = '';

  for (var i=0; i<items.length; i++) {
    for (var a=0; a<items[i].avail.length; a++) {
      content += `<tr>
        <td><input type="radio" name="skubranch"></td>
        <td>${a==0 ? items[i].sku : '&nbsp;'}</td>
        <td>${a==0 ? items[i].price : '&nbsp;'}</td>
        <td>${items[i].avail[a].branch}</td>
        <td>${items[i].avail[a].qty}</td>
      </tr>`;
    }
  }

  return `<div style="padding: 20px;">
    <table class="table table-bordered table-condensed">
    </table>
    </div>`;
}
{% endblock %}
