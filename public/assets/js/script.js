function showToast(msg, autoHide=false) {
    $.toast({
        heading: 'Success',
        text: msg,
        showHideTransition: 'fade',
        hideAfter: autoHide,
        position: { right: 30, top: 60 },
        icon: 'success'
    })
}

function showError(msg) {
    $.toast({
        heading: 'Error',
        text: msg,
        showHideTransition: 'fade',
        hideAfter: 5000,
        position: { right: 30, top: 60 },
        icon: 'error'
    })
}

window.$E = function(tag) {
    return $(document.createElement(tag || 'div'));
};

String.prototype.format = function () {
    var args = arguments;
    return this.replace(/{(\d+)}/g, function (match, number) {
        return typeof args[number] != 'undefined' ? args[number] : match;
    });
};

String.prototype.repeat = function(n) {
    return Array(n+1).join(this);
}

function ajaxCall(url, data, success, fail) {
    $.post(url, data, function(res) {
        if (res.status == 'OK') {
            success(res.data);
        } else {
            fail(res.message);
        }
    },
    'json'
    ).fail(function() {
        alert("error");
    });
}

var bte = { }

bte.AjaxCall = class AjaxCall {
    constructor(url, data) {
        this.url = url;
        this.data = data;
        this.onSuccess = function() {};
        this.onFailure = function() {};
    }

    set success(val) {
        this.onSuccess = val;
    }

    set failure(val) {
        this.onFailure = val;
    }

    exec() {
        var self = this;
        $.post(self.url, self.data, function(res) {
            if (res.status == 'OK') {
                self.onSuccess(res.data);
            } else {
                self.onFailure(res.message);
            }
          }, 'json'
        ).fail(function() {
          alert("error");
        });
    }
}

bte.OrderDetailModal = class OrderDetailModal {
    constructor(orderId) {
        this.orderId = orderId;
    }

    end(index, layero) { }

    content(order) {
        return `<div style="padding: 20px 20px 0 20px;">
          <table class="table table-bordered table-condensed">
          <caption>Order ID: <b>${order.orderId}</b></caption>
          <thead>
            <tr>
              <th>Date</th>
              <th>Market</th>
              <th>SKU</th>
              <th>Price</th>
              <th>Qty</th>
              <th>Express</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>${order.date}</td>
              <td>${order.channel}</td>
              <td><a href="/search/sku?sku=${order.sku}" target="_blank">${order.sku}</a></td>
              <td>${order.price}</td>
              <td>${order.qty}</td>
              <td>${order.express == 1 ? 'Yes' : '&nbsp;'}</td>
            </tr>
          </tbody>
          </table>

          <p class="text-primary">${order.productName}</p>

          <table class="table table-condensed">
          <caption>Customer Information</caption>
          <tbody>
            <tr><td><b>Name</b></td><td>${order.buyer}</td></tr>
            <tr><td><b>Address</b></td><td>${order.address}</td></tr>
            <tr><td><b>&nbsp;</b></td><td>${order.city}, ${order.province}, ${order.postalcode}, ${order.country}</td></tr>
            <tr><td><b>Phone</b></td><td>${order.phone}</td></tr>
            <tr><td><b>Email</b></td><td>${order.email}</td></tr>
          </table>
          </div>`;
    }

    show() {
        var self = this;

        var ajaxCall = new bte.AjaxCall('/ajax/order/detail', { orderId: self.orderId });

        ajaxCall.success = function(data) {
            //layer.config({
            //    type: 1,
            //    moveType: 1,
            //    skin: 'layui-layer-molv',
            //});
            layer.open({
                title:      false,
                area:       ['550px', 'auto'],
                shadeClose: true,
                end:        (index, layero) => { self.end(index, layero) },
                content:    self.content(data)
            })
        };

        ajaxCall.failure = function(message) {
            showError(message);
        };

        ajaxCall.exec();
    }
}

bte.PriceAvailModal = class PriceAvailModal {
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

bte.utils = { }

/**
 * RFC4122 version 4 compliant unique id creator.
 * Added by https://github.com/tufanbarisyildirim/
 * @returns {String}
 */
bte.utils.newGuid = function () {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
        var r = Math.random() * 16 | 0, v = c === 'x' ? r : (r & 0x3 | 0x8);
        return v.toString(16);
    });
}

bte.utils.showToast = function (msg, autoHide=false) {
    $.toast({
        heading: 'Success',
        text: msg,
        showHideTransition: 'fade',
        hideAfter: autoHide,
        position: { right: 30, top: 60 },
        icon: 'success'
    })
}

bte.utils.showError = function (msg) {
    $.toast({
        heading: 'Error',
        text: msg,
        showHideTransition: 'fade',
        hideAfter: 5000,
        position: { right: 30, top: 60 },
        icon: 'error'
    })
}
