/* ==========================================================================
   Order Click — Restaurant Template :: main.js
   Pure vanilla, no dependencies. Every behaviour is data-attribute driven so
   the markup can be moved into Blade partials without touching this file.
   ========================================================================== */
(function () {
  'use strict';

  var $  = function (s, c) { return (c || document).querySelector(s); };
  var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };

  /* ----------------------------------------------------------------
     Theme (light / dark) — persisted
     ---------------------------------------------------------------- */
  var THEME_KEY = 'oc-theme';
  function setTheme(t) {
    document.documentElement.setAttribute('data-theme', t);
    try { localStorage.setItem(THEME_KEY, t); } catch (e) {}
  }
  (function initTheme() {
    var saved;
    try { saved = localStorage.getItem(THEME_KEY); } catch (e) {}
    if (!saved) {
      saved = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }
    document.documentElement.setAttribute('data-theme', saved);
  })();

  document.addEventListener('click', function (e) {
    var t = e.target.closest('[data-theme-toggle]');
    if (!t) return;
    setTheme(document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark');
  });

  /* ----------------------------------------------------------------
     Sticky header shadow
     ---------------------------------------------------------------- */
  var header = $('.site-header');
  if (header) {
    var onScroll = function () { header.classList.toggle('is-stuck', window.scrollY > 8); };
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
  }

  /* ----------------------------------------------------------------
     Overlay helper (mobile nav / modals / cart drawer)
     ---------------------------------------------------------------- */
  function openOverlay(el) {
    if (!el) return;
    el.classList.add('open');
    document.body.classList.add('no-scroll');
    var f = el.querySelector('[data-autofocus]');
    if (f) setTimeout(function () { f.focus(); }, 240);
  }
  function closeOverlay(el) {
    if (!el) return;
    el.classList.remove('open');
    if (!$('.modal.open, .drawer.open, .mobile-nav.open')) document.body.classList.remove('no-scroll');
  }

  document.addEventListener('click', function (e) {
    var opener = e.target.closest('[data-open]');
    if (opener) { e.preventDefault(); openOverlay($('#' + opener.getAttribute('data-open'))); return; }

    var closer = e.target.closest('[data-close]');
    if (closer) {
      e.preventDefault();
      closeOverlay(closer.closest('.modal, .drawer, .mobile-nav'));
      return;
    }
    if (e.target.matches('.modal__scrim, .drawer__scrim, .mobile-nav__scrim')) {
      closeOverlay(e.target.parentElement);
    }
  });

  document.addEventListener('keydown', function (e) {
    if (e.key !== 'Escape') return;
    var open = $('.modal.open') || $('.drawer.open') || $('.mobile-nav.open');
    if (open) closeOverlay(open);
  });

  /* ----------------------------------------------------------------
     Toasts
     ---------------------------------------------------------------- */
  var ICON_CHECK = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>';
  function toast(msg) {
    var wrap = $('.toast-wrap');
    if (!wrap) {
      wrap = document.createElement('div');
      wrap.className = 'toast-wrap';
      document.body.appendChild(wrap);
    }
    var el = document.createElement('div');
    el.className = 'toast';
    el.innerHTML = '<span class="ic">' + ICON_CHECK + '</span><span>' + msg + '</span>';
    wrap.appendChild(el);
    setTimeout(function () {
      el.classList.add('hide');
      setTimeout(function () { el.remove(); }, 320);
    }, 2600);
  }
  window.ocToast = toast;

  /* ----------------------------------------------------------------
     Cart badge (demo only — Laravel will own the real state)
     ---------------------------------------------------------------- */
  function bumpCart(n) {
    $$('.cart-count').forEach(function (b) {
      var v = parseInt(b.textContent, 10) || 0;
      b.textContent = Math.max(0, v + n);
      b.animate(
        [{ transform: 'scale(1)' }, { transform: 'scale(1.45)' }, { transform: 'scale(1)' }],
        { duration: 380, easing: 'cubic-bezier(.16,1,.3,1)' }
      );
    });
  }

  document.addEventListener('click', function (e) {
    var add = e.target.closest('[data-add-to-cart]');
    if (!add) return;
    e.preventDefault();
    bumpCart(1);
    toast((add.getAttribute('data-add-to-cart') || 'Item') + ' added to cart');
  });

  /* Wishlist toggle */
  document.addEventListener('click', function (e) {
    var w = e.target.closest('.wish');
    if (!w) return;
    e.preventDefault();
    w.classList.toggle('on');
    toast(w.classList.contains('on') ? 'Saved to favourites' : 'Removed from favourites');
  });

  /* ----------------------------------------------------------------
     Quantity steppers
     ---------------------------------------------------------------- */
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-qty]');
    if (!btn) return;
    var input = btn.parentElement.querySelector('input');
    if (!input) return;
    var min = parseInt(input.getAttribute('min') || '1', 10);
    var val = parseInt(input.value, 10) || min;
    val += btn.getAttribute('data-qty') === 'up' ? 1 : -1;
    input.value = Math.max(min, val);
    input.dispatchEvent(new Event('change', { bubbles: true }));
  });

  /* ----------------------------------------------------------------
     Tabs
     ---------------------------------------------------------------- */
  $$('[data-tabs]').forEach(function (group) {
    group.addEventListener('click', function (e) {
      var tab = e.target.closest('.tab');
      if (!tab) return;
      var target = tab.getAttribute('data-tab');
      $$('.tab', group).forEach(function (t) { t.classList.toggle('active', t === tab); });
      var scope = group.parentElement;
      $$('.tab-panel', scope).forEach(function (p) {
        p.classList.toggle('active', p.getAttribute('data-panel') === target);
      });
    });
  });

  /* ----------------------------------------------------------------
     Accordion
     ---------------------------------------------------------------- */
  document.addEventListener('click', function (e) {
    var head = e.target.closest('.acc-head');
    if (!head) return;
    var item = head.parentElement;
    var acc = item.parentElement;
    var wasOpen = item.classList.contains('open');
    if (!acc.hasAttribute('data-acc-multi')) {
      $$('.acc-item', acc).forEach(function (i) { i.classList.remove('open'); });
    }
    item.classList.toggle('open', !wasOpen);
  });

  /* ----------------------------------------------------------------
     Product gallery (detail page)
     ---------------------------------------------------------------- */
  $$('[data-gallery]').forEach(function (g) {
    var main = $('[data-gallery-main]', g);
    g.addEventListener('click', function (e) {
      var thumb = e.target.closest('[data-src]');
      if (!thumb || !main) return;
      main.style.opacity = '0';
      setTimeout(function () {
        main.src = thumb.getAttribute('data-src');
        main.style.opacity = '1';
      }, 160);
      $$('[data-src]', g).forEach(function (t) { t.classList.toggle('active', t === thumb); });
    });
    if (main) main.style.transition = 'opacity .25s ease';
  });

  /* ----------------------------------------------------------------
     Grid / list view toggle + category filter (menu page)
     ---------------------------------------------------------------- */
  $$('[data-view-toggle]').forEach(function (grp) {
    grp.addEventListener('click', function (e) {
      var b = e.target.closest('button[data-view]');
      if (!b) return;
      $$('button', grp).forEach(function (x) { x.classList.toggle('active', x === b); });
      var list = $(grp.getAttribute('data-view-toggle'));
      if (list) list.classList.toggle('list-view', b.getAttribute('data-view') === 'list');
    });
  });

  $$('[data-filter-group]').forEach(function (grp) {
    grp.addEventListener('click', function (e) {
      var chip = e.target.closest('[data-filter]');
      if (!chip) return;
      $$('[data-filter]', grp).forEach(function (c) { c.classList.toggle('active', c === chip); });
      var key = chip.getAttribute('data-filter');
      var shown = 0;
      $$('[data-cat]').forEach(function (card) {
        var match = key === 'all' || card.getAttribute('data-cat') === key;
        card.style.display = match ? '' : 'none';
        if (match) shown++;
      });
      var counter = $('[data-result-count]');
      if (counter) counter.textContent = shown;
    });
  });

  /* Mobile filter panel */
  document.addEventListener('click', function (e) {
    var t = e.target.closest('[data-toggle-filters]');
    if (!t) return;
    var panel = $(t.getAttribute('data-toggle-filters'));
    if (panel) panel.classList.toggle('show');
  });

  /* ----------------------------------------------------------------
     Quick search (client-side demo filter over the result list)
     ---------------------------------------------------------------- */
  var qsInput = $('[data-quick-search]');
  if (qsInput) {
    qsInput.addEventListener('input', function () {
      var q = qsInput.value.trim().toLowerCase();
      $$('[data-qs-item]').forEach(function (item) {
        var hit = !q || item.getAttribute('data-qs-item').toLowerCase().indexOf(q) > -1;
        item.style.display = hit ? '' : 'none';
      });
    });
  }

  /* ----------------------------------------------------------------
     Checkout: delivery vs pickup field switching
     ---------------------------------------------------------------- */
  $$('[data-mode-switch]').forEach(function (input) {
    input.addEventListener('change', function () {
      var mode = input.value;
      $$('[data-mode-only]').forEach(function (block) {
        block.style.display = block.getAttribute('data-mode-only') === mode ? '' : 'none';
      });
    });
  });

  /* ----------------------------------------------------------------
     Scroll reveal
     ---------------------------------------------------------------- */
  var reveals = $$('.reveal');
  if (reveals.length && 'IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (!en.isIntersecting) return;
        en.target.classList.add('in');
        io.unobserve(en.target);
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -60px' });
    reveals.forEach(function (el, i) {
      el.style.transitionDelay = (i % 4) * 70 + 'ms';
      io.observe(el);
    });
  } else {
    reveals.forEach(function (el) { el.classList.add('in'); });
  }

  /* ----------------------------------------------------------------
     Count-up stats
     ---------------------------------------------------------------- */
  var counters = $$('[data-count]');
  if (counters.length && 'IntersectionObserver' in window) {
    var cio = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (!en.isIntersecting) return;
        var el = en.target;
        var end = parseFloat(el.getAttribute('data-count'));
        var suffix = el.getAttribute('data-suffix') || '';
        var start = null;
        var step = function (ts) {
          if (!start) start = ts;
          var p = Math.min((ts - start) / 1400, 1);
          var eased = 1 - Math.pow(1 - p, 3);
          var v = end * eased;
          el.textContent = (end % 1 ? v.toFixed(1) : Math.round(v).toLocaleString()) + suffix;
          if (p < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
        cio.unobserve(el);
      });
    }, { threshold: 0.5 });
    counters.forEach(function (el) { cio.observe(el); });
  }

  /* ----------------------------------------------------------------
     Table-of-contents highlight (legal pages)
     ---------------------------------------------------------------- */
  var tocLinks = $$('.toc a');
  if (tocLinks.length && 'IntersectionObserver' in window) {
    var sections = tocLinks.map(function (a) { return $(a.getAttribute('href')); }).filter(Boolean);
    var tio = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (!en.isIntersecting) return;
        tocLinks.forEach(function (a) {
          a.classList.toggle('active', a.getAttribute('href') === '#' + en.target.id);
        });
      });
    }, { rootMargin: '-20% 0px -70%' });
    sections.forEach(function (s) { tio.observe(s); });
  }

  /* ----------------------------------------------------------------
     Demo forms — prevent real submit, show feedback
     ---------------------------------------------------------------- */
  $$('[data-demo-form]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      toast(form.getAttribute('data-demo-form') || 'Submitted successfully');
      var modal = form.closest('.modal');
      if (modal) closeOverlay(modal);
      if (!modal) form.reset();
    });
  });
})();
