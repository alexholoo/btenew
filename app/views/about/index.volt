{% extends "layouts/base.volt" %}

{% block main %}
<div align="center">

    <div align="left">
      <h2>About</h2>
    </div>

    <div align="left">
      <pre>
      Server: {{ host }} {{ serverIP }}<br>
      Client: {{ clientIP }}<br>
      Browser: {{ userAgent }}<br>
      </pre>
      <a href="javascript:;" id="link1">Order Info</a><br>
      <a href="javascript:;" id="link2">Price Avail</a><br>
      <a href="javascript:;" id="link3">Purchase</a><br>
    </div>

</div>
{% endblock %}

{% block jscode %}
{% endblock %}

{% block docready %}
layer.config({
  type: 1,
  moveType: 1,
  skin: 'layui-layer-molv',
});

$('#link1').on('click', function(){
  var modal = new bte.OrderDetailModal('701-5568212-2791469');
  modal.show();
});

$('#link2').on('click', function(){
  var modal = new bte.PriceAvailModal(['ING-50089U', 'SYN-5756548', 'AS-192375']);
  modal.show();
});

$('#link3').on('click', function(){
  var modal = new bte.PurchaseModal({sku: 'ING-50089U', branch: 'Markham', qty: '2'});
  modal.show();
});
{% endblock %}
