/* Order Click — product detail: priced variations, extras, qty, add / buy-now (vanilla). */
(function () {
  var root = document.querySelector('[data-detail]');
  if (!root) return;

  var base = parseFloat(root.getAttribute('data-base-price') || '0');
  var priceOut = root.querySelector('[data-detail-price]');
  var addLabelPrice = root.querySelectorAll('[data-detail-total]');
  var qtyInput = root.querySelector('[data-detail-qty]');
  var sym = root.getAttribute('data-symbol') || '';

  function money(n) { return sym + Number(n).toFixed(2); }

  function selectedVariant() {
    var r = root.querySelector('input[name="oc-variant"]:checked');
    if (!r) return null;
    return { name: r.getAttribute('data-vname'), price: parseFloat(r.getAttribute('data-vprice')) };
  }
  function selectedOptions() {
    // attribute options (variants_json) — grouped radios named oc-opt-*
    var names = [];
    root.querySelectorAll('.oc-opt:checked').forEach(function (o) { names.push(o.value); });
    return names;
  }
  function selectedExtras() {
    var ids = [], names = [], prices = [], total = 0;
    root.querySelectorAll('.oc-extra:checked').forEach(function (e) {
      ids.push(e.value);
      names.push(e.getAttribute('data-ename'));
      prices.push(e.getAttribute('data-eprice'));
      total += parseFloat(e.getAttribute('data-eprice') || '0');
    });
    return { ids: ids, names: names, prices: prices, total: total };
  }

  function unitPrice() {
    var v = selectedVariant();
    return v ? v.price : base;
  }

  function recalc() {
    var qty = Math.max(1, parseInt(qtyInput ? qtyInput.value : '1', 10) || 1);
    var extras = selectedExtras();
    var line = (unitPrice() + extras.total) * qty;
    if (priceOut) priceOut.textContent = money(unitPrice());
    addLabelPrice.forEach(function (el) { el.textContent = money(line); });
  }

  // wire inputs
  root.querySelectorAll('input[name="oc-variant"], .oc-opt, .oc-extra').forEach(function (el) {
    el.addEventListener('change', recalc);
  });
  root.querySelectorAll('[data-detail-step]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var cur = parseInt(qtyInput.value || '1', 10) || 1;
      qtyInput.value = btn.getAttribute('data-detail-step') === 'up' ? cur + 1 : Math.max(1, cur - 1);
      recalc();
    });
  });

  // gallery thumbs
  root.querySelectorAll('[data-thumb]').forEach(function (t) {
    t.addEventListener('click', function () {
      var main = root.querySelector('[data-gallery-main]');
      if (main) main.src = t.getAttribute('data-thumb');
      root.querySelectorAll('[data-thumb]').forEach(function (x) { x.classList.remove('active'); });
      t.classList.add('active');
    });
  });

  function submit(buynow, btn) {
    var v = selectedVariant();
    var opts = selectedOptions();
    var extras = selectedExtras();
    var qty = Math.max(1, parseInt(qtyInput ? qtyInput.value : '1', 10) || 1);
    var variantName = [v ? v.name : null].concat(opts).filter(Boolean).join(', ');

    var fd = new FormData();
    fd.append('vendor_id', root.getAttribute('data-vendor'));
    fd.append('item_id', root.getAttribute('data-item'));
    fd.append('item_name', root.getAttribute('data-name'));
    fd.append('item_image', root.getAttribute('data-image') || '');
    fd.append('item_price', unitPrice());
    fd.append('item_original_price', root.getAttribute('data-orig') || unitPrice());
    fd.append('qty', qty);
    fd.append('tax', root.getAttribute('data-tax') || 0);
    fd.append('variants_name', variantName);
    fd.append('extras_id', extras.ids.join('| '));
    fd.append('extras_name', extras.names.join('| '));
    fd.append('extras_price', extras.prices.join('| '));
    fd.append('min_order', root.getAttribute('data-min') || 0);
    fd.append('max_order', root.getAttribute('data-max') || 0);
    fd.append('stock_management', root.getAttribute('data-stock') || 0);
    fd.append('buynow', buynow);

    var old = btn.innerHTML;
    btn.disabled = true; btn.classList.add('is-loading');
    fetch(OC.addUrl, { method: 'POST', headers: { 'X-CSRF-TOKEN': OC.token, 'X-Requested-With': 'XMLHttpRequest' }, body: fd })
      .then(function (r) { return r.json(); })
      .then(function (d) {
        btn.disabled = false; btn.classList.remove('is-loading'); btn.innerHTML = old;
        if (d.status === 0) { if (window.ocToast) ocToast(d.message || 'Could not add'); return; }
        if (buynow == 1 && d.checkouturl) { window.location.href = d.checkouturl; return; }
        if (typeof d.totalcart !== 'undefined') {
          document.querySelectorAll('.cart-count').forEach(function (e) { e.textContent = d.totalcart; e.style.display = d.totalcart > 0 ? '' : 'none'; });
        }
        if (window.ocToast) ocToast(root.getAttribute('data-name') + ' added to cart');
        // open the live drawer
        var open = document.querySelector('[data-open="cartDrawer"]');
        if (open) open.click();
      })
      .catch(function () { btn.disabled = false; btn.classList.remove('is-loading'); btn.innerHTML = old; if (window.ocToast) ocToast('Could not reach the server'); });
  }

  root.querySelectorAll('[data-detail-add]').forEach(function (b) { b.addEventListener('click', function () { submit(0, b); }); });
  root.querySelectorAll('[data-detail-buynow]').forEach(function (b) { b.addEventListener('click', function () { submit(1, b); }); });

  recalc();
})();
