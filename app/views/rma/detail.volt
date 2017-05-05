{% extends "layouts/base.volt" %}

{% block main %}
  <h2 style="margin-top:0;">RMA Detail</h2>
{% endblock %}

{% block csscode %}
  .order-id { cursor: pointer; }
  .order-id:hover { text-decoration: underline; }
{% endblock %}

{% block docready %}
  layer.config({
    type: 1,
    moveType: 1,
    skin: 'layui-layer-molv',
  });

  // click on order id
  $('.order-id').click(function() {
    var orderId = $(this).text();
    var modal = new bte.OrderInfoModal(orderId);
    modal.show();
  });
{% endblock %}
