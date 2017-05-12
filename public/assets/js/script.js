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

bte.AjaxCall = class {
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

bte.OrderDetailModal = class {
    constructor(orderId) {
        this.orderId = orderId;
        this.url = '/ajax/order/detail';
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

        var ajaxCall = new bte.AjaxCall(self.url, { orderId: self.orderId });

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

bte.OrderInfoModal = class {
    constructor(orderId) {
        this.orderId = orderId;
        this.url = '/ajax/order/info';
    }

    end(index, layero) { }

    content(order) {
        return `<div style="padding: 20px 20px 0 20px;">
          <table class="table table-bordered table-condensed">
          <caption>Order ID: <b>${order.order_id}</b></caption>
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
              <td><a href="/search/sku?sku=${order.items[0].sku}" target="_blank">${order.items[0].sku}</a></td>
              <td>${order.items[0].price}</td>
              <td>${order.items[0].qty}</td>
              <td>${order.express == 1 ? 'Yes' : '&nbsp;'}</td>
            </tr>
          </tbody>
          </table>

          <p class="text-primary">${order.items[0].product}</p>

          <table class="table table-condensed">
          <caption>Customer Information</caption>
          <tbody>
            <tr><td><b>Name</b></td><td>${order.address.buyer}</td></tr>
            <tr><td><b>Address</b></td><td>${order.address.address}</td></tr>
            <tr><td><b>&nbsp;</b></td><td>${order.address.city}, ${order.address.province}, ${order.address.postalcode}, ${order.address.country}</td></tr>
            <tr><td><b>Phone</b></td><td>${order.address.phone}</td></tr>
            <tr><td><b>Email</b></td><td>${order.address.email}</td></tr>
          </table>
          </div>`;
    }

    show() {
        var self = this;

        var ajaxCall = new bte.AjaxCall(self.url, { orderId: self.orderId });

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

bte.PriceAvailModal = class {
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

bte.PurchaseModal = class {
    constructor(data) {
        this.data = data;
        this.onSuccess = function() {};
        this.onFailure = function() {};
        this.onClose = function() {};
    }

    set success(val) {
        this.onSuccess = val;
    }

    set failure(val) {
        this.onFailure = val;
    }

    set done(val) {
        this.onClose = val;
    }

    getShipMethods(data) {
        var shipMethod = '';

        if (data.sku.substr(0, 3) != 'SYN') {
            return shipMethod;
        }

        var loading = layer.load(1, { shade: false });

        $.ajax({
            type: 'POST',
            url: '/ajax/freight/quote',
            data: data,
            async: false,
            success: function(res) {
                layer.close(loading);
                shipMethod = `
                    <label>Ship Method</label>
                    <select id="ship-method" style="float:right;width:320px;">
                    ${res.data}
                    </select><br><br>`;
            }
        });

        return shipMethod;
    }

    getNotifyEmails(data) {
        var emails = '';

        if (data.sku.substr(0, 2) == 'TD') {
            emails = `<div style="margin-top:15px">
              <label>Email Notification</label>
              <select id="notify-email" style="float:right;width:300px;">
                <option>doris@btecanada.com</option>
              </select></div>`;
        }

        return emails;
    }

    getMaxLength(data) {
        if (data.sku.substr(0, 2) == 'DH') {
            return '58';
        }
        if (data.sku.substr(0, 3) == 'ING') {
            return '35';
        }
        if (data.sku.substr(0, 3) == 'SYN') {
            return '60';
        }
        if (data.sku.substr(0, 2) == 'TD') {
            return '52';
        }
        return '60';
    }

    getPurchaseNote(data) {
        if (data.sku.substr(0, 2) == 'DH') {
            return 'Drop ship';
        }
        return '';
    }

    yes(index, layero) {
        var comment     = layero.find('#comment').val();
        var shipMethod  = layero.find('#ship-method option:selected').val();
        var notifyEmail = layero.find('#notify-email option:selected').text();

        this.data.comment     = comment;
        this.data.shipMethod  = shipMethod;
        this.data.notifyEmail = notifyEmail;

        var ajaxCall = new bte.AjaxCall('/ajax/make/purchase', this.data);

        ajaxCall.success = this.onSuccess;
        ajaxCall.failure = this.onFailure;
        ajaxCall.exec();

        layer.close(index);
    }

    end(index, layero) {
        this.onClose();
    }

    content(data) {
        var shipMethod   = this.getShipMethods(data);
        var notifyEmails = this.getNotifyEmails(data);
        var maxLength    = this.getMaxLength(data);
        var purchaseNote = this.getPurchaseNote(data);

        return `<div style="padding: 20px;">
           <table class="table table-condensed">
             <tr><td><b>SKU: </b></td><td>${data.sku ? data.sku : '-'}</td></tr>
             <tr><td><b>Branch: </b></td><td>${data.branch ? data.branch: '-'}</td></tr>
             <tr><td><b>Qty: </b></td><td>${data.qty? data.qty: '-'}</td></tr>
           </table>
           ${shipMethod}
           <label for="comment">Purchase note</label> (Max ${maxLength} chars)<br />
           <textarea id="comment" maxlength="${maxLength}" style="width: 440px; height: 80px; resize: none;">${purchaseNote}</textarea>
           ${notifyEmails}
         </div>`;
    }

    show() {
        var self = this;
        layer.open({
            title:   'Purchase',
            area:    ['480px', 'auto'],
            btn:     ['Purchase', 'Cancel'],
            yes:     (index, layero) => { self.yes(index, layero) },
            end:     (index, layero) => { self.end(index, layero) },
            content: self.content(self.data)
        });
    }
}

bte.SkuListModal = class {
    constructor(data, searchby) {
        this.data = data;
        this.searchby = searchby;
    }

    content(skus, upc) {
        var content = '';
        var searchby = this.searchby;

        for (var i=0; i<skus.length; i++) {
          content += `<li>${skus[i]}</li>`;
        }

        return `<div style="padding: 20px; font-size: 20px;">
           SKUs for ${searchby} <label>${upc}</label><br />
           <ul>${content}</ul>
         </div>`;
    }

    show() {
        var self = this;
        var upc = self.data;

        var url = '/api/query/upc/';
        if (self.searchby == 'MPN') {
            url = '/api/query/mpn/';
        }

        ajaxCall(url + upc, { upc: upc },
            function(data) {
                layer.open({
                    title:      false,
                    area:       ['400px', 'auto'],
                    shadeClose: true,
                    end:        function(index, layero) { },
                    content:    self.content(data, upc)
                })
            },
            function(message) {
                showError(message);
            }
        );
    }
}

bte.EditInvlocNoteModal = class {
    constructor(data) {
        this.data = data;
        this.onSuccess = function() {};
        this.onFailure = function() {};
        this.onClose = function() {};
    }

    set success(val) {
        this.onSuccess = val;
    }

    set failure(val) {
        this.onFailure = val;
    }

    set done(val) {
        this.onClose = val;
    }

    content(data) {
        var note = data.note;
        var sn = data.sn;
        return `<div style="padding: 20px;">
           <div>
             <label for="sn" style="width: 3em;">SN#</label>
             <input type="text" id="sn" size="60" value="${sn}">
           </div><br>
           <label for="note">Note</label> (Max 80 chars)<br />
           <textarea id="note" maxlength="80" style="width: 440px; height: 80px; resize: none;">${note}</textarea>
         </div>`;
    }

    end(index, layero) {
    }

    yes(index, layero) {
        var self = this;

        var note = layero.find('#note').val();
        var sn = layero.find('#sn').val();

        self.data.sn = sn;
        self.data.note = note;

        ajaxCall('/invloc/update', self.data, self.onSuccess, self.onFailure);
        layer.close(index);
    }

    show() {
        var self = this;

        layer.open({
            title:   'Edit Note',
            area:    ['480px', 'auto'],
            btn:     ['Save', 'Cancel'],
            yes:     (index, layero) => { self.yes(index, layero) },
            end:     (index, layero) => { self.end(index, layero) },
            content: self.content(self.data)
        })
    }
}

bte.EditOverstockNoteModal = class {
    constructor(data) {
        this.data = data;
        this.onSuccess = function() {};
        this.onFailure = function() {};
        this.onClose = function() {};
    }

    set success(val) {
        this.onSuccess = val;
    }

    set failure(val) {
        this.onFailure = val;
    }

    set done(val) {
        this.onClose = val;
    }

    content(data) {
        var note = data.note;
        return `<div style="padding: 20px;">
           <label for="note">Note</label> (Max 200 chars)<br />
           <textarea id="note" maxlength="200" style="width: 440px; height: 100px; resize: none;">${note}</textarea>
         </div>`;
    }

    end(index, layero) {
    }

    yes(index, layero) {
        var self = this;

        var note = layero.find('#note').val();

        self.data.note = note;

        ajaxCall('/overstock/note', self.data, self.onSuccess, self.onFailure);
        layer.close(index);
    }

    show() {
        var self = this;

        layer.open({
            title:   'Edit Note',
            area:    ['480px', 'auto'],
            btn:     ['Save', 'Cancel'],
            yes:     (index, layero) => { self.yes(index, layero) },
            end:     (index, layero) => { self.end(index, layero) },
            content: self.content(self.data)
        })
    }
}

bte.OverstockChangeLogModal = class {
    constructor(sku) {
        this.sku = sku;
    }

    content(data) {
        return `<h2>Coming soon!</h2>`;
    }

    show() {
        var self = this;

        ajaxCall('/api/query/upc/600603127717', { sku: self.sku },
            function(data) {
                layer.open({
                    title:      false,
                    area:       ['400px', 'auto'],
                    shadeClose: true,
                    end:        function(index, layero) { },
                    content:    self.content(data)
                })
            },
            function(message) {
                showError(message);
            }
        );
    }
}

bte.InventoryChangeLogModal = class {
    constructor(sku) {
        this.sku = sku;
    }

    content(data) {
        return `<h2>Coming soon!</h2>`;
    }

    show() {
        var self = this;

        ajaxCall('/api/query/upc/600603127717', { sku: self.sku },
            function(data) {
                layer.open({
                    title:      false,
                    area:       ['400px', 'auto'],
                    shadeClose: true,
                    end:        function(index, layero) { },
                    content:    self.content(data)
                })
            },
            function(message) {
                showError(message);
            }
        );
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

bte.utils.sprintf = function (str) {
    var args = arguments,
        flag = true,
        i = 1;
    str = str.replace(/%s/g, function () {
        var arg = args[i++];
        if (typeof arg === 'undefined') {
            flag = false;
            return '';
        }
        return arg;
    });
    return flag ? str : '';
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
