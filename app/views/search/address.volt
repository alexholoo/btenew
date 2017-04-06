{% extends "layouts/base.volt" %}

{% block main %}
  <h3 style="margin-top:0;">Address Information</h3>
  <div class="well">
    <form class="form-inline" role="form" method="POST">
      <div class="form-group col-xs-5 col-lg-5">
        <input class="form-control" name="key" placeholder="Enter Last 4+ Digits of Order ID" pattern=".{4,}" type="text" autofocus required style="width:100%">
      </div>
      <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span> Search </button>
    </form>
  </div>

  {% if orders is not empty %}
  <p>Address information for <b>{{ key }}</b></p>
  {% for order in orders %}
  <table class="table table-bordered">
    <tbody>
      <tr class="active">
        <th class="closebtn" style="text-align:center;cursor:pointer;"><span class="glyphicon glyphicon-remove"></span></th>
        <th>Date</th>
        <th>Channel</th>
        <th>Order ID</th>
        <th>SKU</th>
        <th>Qty</th>
        <th>Price</th>
      </tr>
      {% for item in order['items'] %}
      <tr>
        <th class="active">Order</th>
        <td>{{ order['date'] }}</td>
        <td>{{ order['channel'] }}</td>
        <td>{{ order['order_id'] }}</td>
        <td>{{ item['sku'] }}</td>
        <td>{{ item['qty'] }}</td>
        <td>{{ item['price'] }}</td>
      </tr>
      {% endfor %}
      <tr>
        <th class="active">Buyer</th>
        <td colspan="2" data-container="body" data-toggle="tooltip" title="Click to copy">{{ order['address']['buyer'] }}</td>
        <th class="active">Phone</th>
        <td colspan="3" data-container="body" data-toggle="tooltip" title="Click to copy">{{ order['address']['phone'] }}</td>
      </tr>
      <tr>
        <th class="active">Address</th>
        <td colspan="2" data-toggle="tooltip" data-container="body" title="Click to copy">{{ order['address']['address'] }}</td>
        <td data-toggle="tooltip" data-container="body" title="Click to copy">{{ order['address']['city'] }}</td>
        <td data-toggle="tooltip" data-container="body" title="Click to copy">{{ order['address']['province'] }}</td>
        <td data-toggle="tooltip" data-container="body" title="Click to copy">{{ order['address']['postalcode'] }}</td>
        <td data-toggle="tooltip" data-container="body" title="Click to copy">{{ order['address']['country'] }}</td>
      </tr>
    </tbody>
  </table>
  <textarea id="output" style="display:none;"></textarea>
  {% endfor %}

  {% else %}
    No order information found.
  {% endif %}
{% endblock %}

{% block docready %}
$('table td').click(function() {
    var text = $(this).html();
    console.log(text);

    var textarea = $('#output');
    textarea.show();
    textarea.val(text);
    textarea.select();
    document.execCommand('copy');
    textarea.hide();

    $(this).selectText();
});

$('.closebtn').click(function() {
    var table = $(this).closest('table');
    table.remove();
});

$('[data-toggle="tooltip"]').tooltip();
{% endblock %}

{% block jscode %}
jQuery.fn.selectText = function() {
    var obj = this[0];
    if (document.body.createTextRange) {
        var range = obj.offsetParent.createTextRange();
        range.moveToElementText(obj);
        range.select();
    } else if (window.getSelection) {
        var selection = obj.ownerDocument.defaultView.getSelection();
        var range = obj.ownerDocument.createRange();
        range.selectNodeContents(obj);
        selection.removeAllRanges();
        selection.addRange(range);
    }
    return this;
}
{% endblock %}
