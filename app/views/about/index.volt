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
    </div>

</div>
{% endblock %}

{% block jscode %}

class PriceAvailModal {
    constructor(sku) {
        this.sku = sku;
        this.onClose = function() { };
        this.onSelected = function(data) { };
    }

    set done(val) {
        this.onClose = val;
    }

    set selected(val) {
        this.onSelected = val;
    }

    yes(index, layero) {
        var radio = layero.find('input[type=radio]:checked');
        if (radio.length) {
            var tr = radio.closest('tr');
            var sku = tr.data('sku');
            var branch = tr.data('branch');
            var code = tr.data('branch-code');

            this.onSelected({sku: sku, branch: branch, code: code});
        }
        layer.close(index);
    }

    end(index, layero) {
    }

    success(layero, index){
        layero.find('table tr').click(function(){
            $(this).find('input[type=radio]').prop('checked', true);
        });
    }

    content(items) {
        var content = '';

        for (var i=0; i<items.length; i++) {
            for (var a=0; a<items[i].avail.length; a++) {
                content += `<tr data-sku="${items[i].sku}" data-branch="${items[i].avail[a].branch}" data-branch-code="${items[i].avail[a].code}">
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
            <thead>
            <tr>
            <th>&nbsp;</th>
            <th>PartNum</th>
            <th>Price</th>
            <th>Branch</th>
            <th>Qty</th>
            </tr>
            </thead>
            <tbody>
            ${content}
            </tbody>
            </table>
            </div>`;
    }

    show() {
        var self = this;

        var ajaxCall = new bte.AjaxCall('/ajax/price/avail', { sku: this.sku });

        ajaxCall.success = function(data) {
            layer.open({
                title:   'Price and Availability',
                area:    ['600px', 'auto'],
                btn:     ['OK', 'Cancel'],
                yes:     (index, layero) => { self.yes(index, layero) },
                success: (index, layero) => { self.success(index, layero) },
                end:     (index, layero) => { self.end(index, layero) },
                content: self.content(data)
            })
            self.onClose();
        };

        ajaxCall.failure = function(message) {
            self.onClose();
            showError(message);
        };

        ajaxCall.exec();
    }
}

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
  //var modal = new bte.PriceAvailModal(['ING-50089U']);
  var modal = new PriceAvailModal(['ING-50089U']);
  modal.show();
});
{% endblock %}
