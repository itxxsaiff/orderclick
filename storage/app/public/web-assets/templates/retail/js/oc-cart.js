/* Order Click — real cart wiring for the restaurant template (add / qty / remove / live drawer). */
(function () {
  function setCount(n) {
    document.querySelectorAll('.cart-count').forEach(function (e) {
      e.textContent = n;
      e.style.display = (Number(n) > 0) ? '' : 'none';
    });
  }
  function toast(m) { if (window.ocToast) window.ocToast(m); }

  function openDrawer() {
    var d = document.getElementById('cartDrawer');
    if (d) { d.classList.add('open'); document.body.classList.add('no-scroll'); }
  }

  function refreshDrawer(open) {
    fetch(OC.fragUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(function (r) { return r.json(); })
      .then(function (d) {
        var wrap = document.getElementById('ocCartWrap');
        if (wrap) wrap.innerHTML = d.html;
        setCount(d.count);
        var sub = document.querySelector('.oc-cart-sub');
        if (sub) sub.textContent = d.count + (d.count === 1 ? ' item' : ' items');
        if (open) openDrawer();
      }).catch(function () {});
  }

  window.ocAdd = function (btn) {
    if (btn.dataset.hasvar === '1' && btn.dataset.detail) { window.location = btn.dataset.detail; return; }
    var fd = new FormData();
    fd.append('vendor_id', OC.vendor);
    fd.append('item_id', btn.dataset.id);
    fd.append('item_name', btn.dataset.name);
    fd.append('item_image', btn.dataset.image || '');
    fd.append('item_price', btn.dataset.price);
    fd.append('qty', 1);
    fd.append('tax', btn.dataset.tax || 0);
    fd.append('buynow', 0);
    fd.append('extras_name', ''); fd.append('extras_price', ''); fd.append('extras_id', ''); fd.append('variants_name', '');
    btn.classList.add('is-loading');
    fetch(OC.addUrl, { method: 'POST', headers: { 'X-CSRF-TOKEN': OC.token, 'X-Requested-With': 'XMLHttpRequest' }, body: fd })
      .then(function (r) { return r.json(); })
      .then(function (d) {
        btn.classList.remove('is-loading');
        if (d.status === 0) { toast(d.message || 'Could not add'); return; }
        if (typeof d.totalcart !== 'undefined') setCount(d.totalcart);
        toast((btn.dataset.name || 'Item') + ' added to cart');
        refreshDrawer(true);
      }).catch(function () { btn.classList.remove('is-loading'); toast('Could not reach the server'); });
  };

  function ocQty(cartId, itemId, qty, type) {
    var fd = new FormData();
    fd.append('cart_id', cartId); fd.append('item_id', itemId); fd.append('qty', qty); fd.append('type', type);
    fetch(OC.qtyUrl, { method: 'POST', headers: { 'X-CSRF-TOKEN': OC.token, 'X-Requested-With': 'XMLHttpRequest' }, body: fd })
      .then(function (r) { return r.json(); }).then(function () { refreshDrawer(false); }).catch(function () {});
  }
  function ocRemove(cartId) {
    var fd = new FormData(); fd.append('cart_id', cartId);
    fetch(OC.delUrl, { method: 'POST', headers: { 'X-CSRF-TOKEN': OC.token, 'X-Requested-With': 'XMLHttpRequest' }, body: fd })
      .then(function (r) { return r.json(); }).then(function () { toast('Removed from cart'); refreshDrawer(false); }).catch(function () {});
  }

  document.addEventListener('click', function (e) {
    if (e.target.closest('[data-open="cartDrawer"]')) { refreshDrawer(false); return; }
    var qb = e.target.closest('[data-oc-qty]');
    if (qb) {
      e.preventDefault();
      var row = qb.closest('[data-cart-id]');
      var inp = row.querySelector('input');
      var cur = parseInt(inp.value || '1', 10);
      var up = qb.getAttribute('data-oc-qty') === 'up';
      var nq = up ? cur + 1 : Math.max(1, cur - 1);
      if (nq === cur) return;
      inp.value = nq;
      ocQty(row.getAttribute('data-cart-id'), row.getAttribute('data-item-id'), nq, up ? 'plus' : 'minus');
      return;
    }
    var rb = e.target.closest('[data-oc-remove]');
    if (rb) { e.preventDefault(); ocRemove(rb.closest('[data-cart-id]').getAttribute('data-cart-id')); return; }
  });
})();
