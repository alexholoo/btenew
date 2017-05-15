{% extends "layouts/base.volt" %}

{% block main %}
<div align="center">

    <div align="left">
      <h2>{{ pageTitle }}</h2>
    </div>

    <div align="left">
      <a href="javascript:;" id="link1">Order Info</a><br>
      <a href="javascript:;" id="link2">Price Avail</a><br>
      <a href="javascript:;" id="link3">Purchase</a><br>
      <a href="javascript:;" id="link4">Edit Note #SN</a><br>
      <a href="javascript:;" id="link5">Edit Note Only</a><br>
      <a href="javascript:;" id="link6">Sku List By UPC</a><br>
      <a href="javascript:;" id="link7">Sku List By MPN</a><br>
    </div>

</div>
{% endblock %}

{% block jscode %}
{% endblock %}

{% block docready %}
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

$('#link4').on('click', function(){
  var modal = new bte.EditInvlocNoteModal({ note: 'some text here', sn: 'serial number??'});
  modal.show();
});

$('#link5').on('click', function(){
  var modal = new bte.EditOverstockNoteModal({ note: 'some text here'});
  modal.show();
});

$('#link6').on('click', function(){
  var modal = new bte.SkuListModal('886227985425', 'UPC');
  modal.show();
});

$('#link7').on('click', function(){
  var modal = new bte.SkuListModal('CHROMEBOX-M004U', 'MPN');
  modal.show();
});
{% endblock %}
