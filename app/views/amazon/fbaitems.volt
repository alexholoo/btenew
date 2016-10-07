{% extends "layouts/base.volt" %}

{% block main %}
  <h2>Amazon FBA items generator</h2>
  <div class="well clearfix">
    <form class="form-inline" role="form" method="POST">
      <div class="form-group">
        <input type="text" class="form-control" name="mpn" placeholder="MPN" data-toggle="tooltip" title="MPN" value="{{mpn}}" {% if retry %}style="border:1px solid red"{% endif %}>
      </div>
      <div class="form-group">
        <input type="text" class="form-control" name="upc" placeholder="UPC" data-toggle="tooltip" title="UPC" value="{{upc}}">
      </div>
      <div class="form-group">
        <input type="text" class="form-control" name="cost" placeholder="Cost (CAD)" data-toggle="tooltip" title="Cost (CAD)" value="{{cost}}">
      </div>
      <div class="form-group">
        <input type="text" class="form-control" name="us_floor" placeholder="US_floor (USD)" data-toggle="tooltip" title="US_Floor (USD)" value="{{us_floor}}">
      </div>
      <div class="form-group">
        <input type="text" class="form-control" name="ca_floor" placeholder="CA_floor (CAD)" data-toggle="tooltip" title="CA_Floor (CAD)" value="{{ca_floor}}">
      </div>
      <div class="form-group">
        <select class="form-control" name="condition" data-toggle="tooltip" title="Condition">
          <option value="New" {% if condition == "New" %}selected{% endif %}>New</option>
          <option value="Open Box" {% if condition == "Open Box" %}selected{% endif %}>Open Box</option>
          <option value="Refurbished" {% if condition == "Refurbished" %}selected{% endif %}>Refurbished</option>
        </select>
      </div>
      {% if retry %}<input type="hidden" name="retry" value="1">{% endif %}
      <div class="form-group">
        <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-tags"></span>&nbsp; Enter </button>
      </div>
    </form>
  </div>

  {% if items is not empty %}
  <table class="table table-bordered table-hover">
    <thead>
      <tr>
        <th>Part Number</th>
        <th>Notes</th>
        <th>US_Floor</th>
        <th>CA_Floor</th>
        <th>Title</th>
        <th>Cost</th>
        <th>Condition</th>
        <th>MPN</th>
        <th>Source</th>
        <th>Source-SKU</th>
        <th>ID</th>
        <th>UPC Code</th>
      </tr>
    </thead>
    <tbody>

    {% for item in items %}
      <tr>
        <td>{{ item['partnum'] }}</td>
        <td>{{ item['notes'] }}</td>
        <td>{{ item['us_floor'] }}</td>
        <td>{{ item['ca_floor'] }}</td>
        <td>{{ item['title'] }}</td>
        <td>{{ item['cost'] }}</td>
        <td>{{ item['condition'] }}</td>
        <td>{{ item['mpn'] }}</td>
        <td>{{ item['source'] }}</td>
        <td>{{ item['source_sku'] }}</td>
        <td class="delete">
          <button class="btn btn-xs btn-danger"><span class="glyphicon glyphicon-remove"></span></button>
        </td>
        <td>{{ item['upc'] }}</td>
      </tr>
    {% endfor %}

    </tbody>
  </table>
  <button onclick="copyToClipboard()" class="btn btn-primary"><span class="glyphicon glyphicon-copy"></span>&nbsp; Copy To Clipboard</button>
  <textarea id="output" style="display:none;"></textarea>
  {% endif %}
{% endblock %}

{% block jscode %}
function copyToClipboard() {
    var text = '';
    $('table').find('tbody tr').each(function() {
        $(this).find('td').each(function() {
            if ($(this).hasClass('delete')) {
              text += "\t";
            } else {
              text += $(this).html() + "\t";
            }
        });
        text += "\n";
    });

    var textarea = $('#output');
    textarea.show();
    textarea.val(text);
    textarea.select();
    document.execCommand('copy');
    textarea.hide();
}
{% endblock %}

{% block docready %}
$('[data-toggle="tooltip"]').tooltip();

$('.delete button').click(function() {
    var tr = $(this).closest('tr');
    var index = tr.index();
    tr.remove();
    ajaxCall('/ajax/fbaitem/delete', { index: index });
});
{% endblock %}
