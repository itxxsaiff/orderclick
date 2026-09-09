/* Order Click — menu page filters: category, price, search, sort, view toggle (all client-side). */
(function () {
  var grid = document.getElementById('productGrid');
  if (!grid) return;
  var cards = Array.prototype.slice.call(grid.querySelectorAll('.product-card'));

  var state = { cats: null, maxPrice: Infinity, q: '', sort: 'popular' };

  function activeCats() {
    var boxes = document.querySelectorAll('[data-cat-filter]');
    if (!boxes.length) return null;
    var on = [];
    boxes.forEach(function (b) { if (b.checked) on.push(b.getAttribute('data-cat-filter')); });
    // if every box is checked treat as "no category filter"
    return on.length === boxes.length ? null : on;
  }

  function apply() {
    var shown = 0;
    cards.forEach(function (card) {
      var cat = card.getAttribute('data-cat');
      var price = parseFloat(card.getAttribute('data-price') || '0');
      var name = (card.getAttribute('data-name') || '').toLowerCase();
      var ok = true;
      if (state.cats && state.cats.indexOf(cat) === -1) ok = false;
      if (price > state.maxPrice) ok = false;
      if (state.q && name.indexOf(state.q) === -1) ok = false;
      card.style.display = ok ? '' : 'none';
      if (ok) shown++;
    });
    var counter = document.querySelector('[data-menu-count]');
    if (counter) counter.textContent = shown;
    var empty = document.querySelector('[data-menu-empty]');
    if (empty) empty.style.display = shown ? 'none' : '';
  }

  function sortCards(mode) {
    var visible = cards.slice();
    visible.sort(function (a, b) {
      var pa = parseFloat(a.getAttribute('data-price') || '0');
      var pb = parseFloat(b.getAttribute('data-price') || '0');
      var ra = parseFloat(a.getAttribute('data-pop') || '0');
      var rb = parseFloat(b.getAttribute('data-pop') || '0');
      if (mode === 'price-asc') return pa - pb;
      if (mode === 'price-desc') return pb - pa;
      if (mode === 'rating') return rb - ra;
      return 0; // popular = original order
    });
    visible.forEach(function (c) { grid.appendChild(c); });
  }

  // Category checkboxes
  document.querySelectorAll('[data-cat-filter]').forEach(function (b) {
    b.addEventListener('change', function () { state.cats = activeCats(); syncChips(); apply(); });
  });

  // Chips (single-select quick category)
  function syncChips() {}
  document.querySelectorAll('[data-menu-chip]').forEach(function (chip) {
    chip.addEventListener('click', function () {
      document.querySelectorAll('[data-menu-chip]').forEach(function (c) { c.classList.remove('active'); });
      chip.classList.add('active');
      var key = chip.getAttribute('data-menu-chip');
      state.cats = key === 'all' ? null : [key];
      // reflect into checkboxes
      document.querySelectorAll('[data-cat-filter]').forEach(function (b) {
        b.checked = key === 'all' ? true : b.getAttribute('data-cat-filter') === key;
      });
      apply();
    });
  });

  // Price range
  var range = document.querySelector('[data-price-filter]');
  if (range) {
    var out = document.querySelector('[data-price-out]');
    var sym = range.getAttribute('data-symbol') || '';
    range.addEventListener('input', function () {
      state.maxPrice = parseFloat(range.value);
      if (out) out.textContent = sym + parseFloat(range.value).toFixed(2);
      apply();
    });
  }

  // Search (page head + also drives nothing else)
  document.querySelectorAll('[data-menu-search]').forEach(function (inp) {
    inp.addEventListener('input', function () { state.q = inp.value.trim().toLowerCase(); apply(); });
  });

  // Sort
  var sortSel = document.querySelector('[data-menu-sort]');
  if (sortSel) sortSel.addEventListener('change', function () { sortCards(sortSel.value); });

  // Reset
  var reset = document.querySelector('[data-menu-reset]');
  if (reset) reset.addEventListener('click', function () {
    state = { cats: null, maxPrice: Infinity, q: '' , sort: 'popular'};
    document.querySelectorAll('[data-cat-filter]').forEach(function (b) { b.checked = true; });
    if (range) { range.value = range.max; if (out) out.textContent = (range.getAttribute('data-symbol') || '') + parseFloat(range.max).toFixed(2); }
    document.querySelectorAll('[data-menu-search]').forEach(function (i) { i.value = ''; });
    document.querySelectorAll('[data-menu-chip]').forEach(function (c, i) { c.classList.toggle('active', i === 0); });
    if (sortSel) sortSel.value = 'popular';
    sortCards('popular');
    apply();
  });

  // View toggle (grid / list)
  document.querySelectorAll('[data-menu-view]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      document.querySelectorAll('[data-menu-view]').forEach(function (b) { b.classList.remove('active'); });
      btn.classList.add('active');
      grid.classList.toggle('as-list', btn.getAttribute('data-menu-view') === 'list');
    });
  });

  // Mobile filters toggle
  var mob = document.querySelector('[data-menu-filtertoggle]');
  if (mob) mob.addEventListener('click', function () {
    var p = document.getElementById('filtersPanel');
    if (p) p.classList.toggle('show');
  });

  apply();
})();
