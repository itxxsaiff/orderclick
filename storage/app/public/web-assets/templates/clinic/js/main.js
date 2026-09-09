/* ==========================================================================
   Order Click — Clinic / Doctor Template :: main.js
   Vanilla, dependency-free, data-attribute driven. No accounts anywhere.
   ========================================================================== */
(function () {
  'use strict';

  var $  = function (s, c) { return (c || document).querySelector(s); };
  var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };

  /* ---------------- Theme ---------------- */
  var THEME_KEY = 'oc-clinic-theme';
  (function initTheme() {
    var saved;
    try { saved = localStorage.getItem(THEME_KEY); } catch (e) {}
    if (!saved) saved = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    document.documentElement.setAttribute('data-theme', saved);
  })();
  document.addEventListener('click', function (e) {
    if (!e.target.closest('[data-theme-toggle]')) return;
    var next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', next);
    try { localStorage.setItem(THEME_KEY, next); } catch (err) {}
  });

  /* ---------------- Sticky header ---------------- */
  var header = $('.site-header');
  if (header) {
    var onScroll = function () { header.classList.toggle('is-stuck', window.scrollY > 8); };
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
  }

  /* ---------------- Overlays ---------------- */
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
    if (closer) { e.preventDefault(); closeOverlay(closer.closest('.modal, .drawer, .mobile-nav')); return; }
    if (e.target.matches('.modal__scrim, .drawer__scrim, .mobile-nav__scrim')) closeOverlay(e.target.parentElement);
  });
  document.addEventListener('keydown', function (e) {
    if (e.key !== 'Escape') return;
    var open = $('.modal.open') || $('.drawer.open') || $('.mobile-nav.open');
    if (open) closeOverlay(open);
  });

  /* ---------------- Toast ---------------- */
  var ICON_CHECK = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>';
  function toast(msg) {
    var wrap = $('.toast-wrap');
    if (!wrap) { wrap = document.createElement('div'); wrap.className = 'toast-wrap'; document.body.appendChild(wrap); }
    var el = document.createElement('div');
    el.className = 'toast';
    el.innerHTML = '<span class="ic">' + ICON_CHECK + '</span><span>' + msg + '</span>';
    wrap.appendChild(el);
    setTimeout(function () { el.classList.add('hide'); setTimeout(function () { el.remove(); }, 320); }, 2600);
  }
  window.ocToast = toast;

  function bumpCount(n) {
    $$('.cart-count').forEach(function (b) {
      b.textContent = Math.max(0, (parseInt(b.textContent, 10) || 0) + n);
      b.animate([{ transform: 'scale(1)' }, { transform: 'scale(1.5)' }, { transform: 'scale(1)' }],
        { duration: 380, easing: 'cubic-bezier(.16,1,.3,1)' });
    });
  }

  /* ---------------- Start an appointment with a doctor ---------------- */
  document.addEventListener('click', function (e) {
    var add = e.target.closest('[data-book-doctor]');
    if (!add) return;
    e.preventDefault();
    bumpCount(1);
    toast('Appointment started with ' + (add.getAttribute('data-book-doctor') || 'the doctor'));
  });

  /* ----------------------------------------------------------------
     Booking picker — consultation type → doctor → day → time
     Keeps [data-summary-*] fields in sync so the summary panel is live.
     ---------------------------------------------------------------- */
  function setSummary(key, value) {
    $$('[data-summary-' + key + ']').forEach(function (el) { el.textContent = value; });
  }

  document.addEventListener('change', function (e) {
    var input = e.target;
    if (!input.matches('input[type="radio"]')) return;

    // specialist
    if (input.name === 'staff') {
      setSummary('staff', input.getAttribute('data-name') || 'Any available');
    }
    // day
    if (input.name === 'day') {
      setSummary('day', input.getAttribute('data-full') || '');
      // a new day means the previously chosen time no longer applies
      $$('input[name="slot"]').forEach(function (s) { s.checked = false; });
      setSummary('time', 'Choose a time');
      var t = $('[data-total-line]');
      if (t) t.classList.add('muted');
    }
    // time slot
    if (input.name === 'slot') {
      setSummary('time', input.getAttribute('data-time') || '');
      var tl = $('[data-total-line]');
      if (tl) tl.classList.remove('muted');
      var cta = $('[data-book-cta]');
      if (cta) cta.removeAttribute('disabled');
    }
    // consultation type (in-clinic / video / home visit)
    if (input.name === 'cons') {
      setSummary('cons', input.getAttribute('data-name') || '');
      setSummary('fee', input.getAttribute('data-fee') || '');
      setSummary('duration', input.getAttribute('data-duration') || '');
    }
    // doctor
    if (input.name === 'doctor') {
      setSummary('doctor', input.getAttribute('data-name') || '');
      setSummary('spec', input.getAttribute('data-spec') || '');
    }
    // reason for visit
    if (input.name === 'reason') {
      setSummary('reason', input.getAttribute('data-name') || '');
    }
  });

  /* Pre-fill the summary from whatever is checked on load */
  $$('input[type="radio"]:checked').forEach(function (i) {
    i.dispatchEvent(new Event('change', { bubbles: true }));
  });

  /* ---------------- Tabs ---------------- */
  $$('[data-tabs]').forEach(function (group) {
    group.addEventListener('click', function (e) {
      var tab = e.target.closest('[data-tab]');
      if (!tab) return;
      var target = tab.getAttribute('data-tab');
      $$('[data-tab]', group).forEach(function (t) { t.classList.toggle('active', t === tab); });
      $$('.tab-panel', group.parentElement).forEach(function (p) {
        p.classList.toggle('active', p.getAttribute('data-panel') === target);
      });
    });
  });

  /* ---------------- Accordion ---------------- */
  document.addEventListener('click', function (e) {
    var head = e.target.closest('.acc-head');
    if (!head) return;
    var item = head.parentElement, acc = item.parentElement;
    var wasOpen = item.classList.contains('open');
    if (!acc.hasAttribute('data-acc-multi')) $$('.acc-item', acc).forEach(function (i) { i.classList.remove('open'); });
    item.classList.toggle('open', !wasOpen);
  });

  /* ---------------- Gallery ---------------- */
  $$('[data-gallery]').forEach(function (g) {
    var main = $('[data-gallery-main]', g);
    if (main) main.style.transition = 'opacity .25s ease';
    g.addEventListener('click', function (e) {
      var thumb = e.target.closest('[data-src]');
      if (!thumb || !main) return;
      e.preventDefault();
      main.style.opacity = '0';
      setTimeout(function () { main.src = thumb.getAttribute('data-src'); main.style.opacity = '1'; }, 150);
      $$('[data-src]', g).forEach(function (t) { t.classList.toggle('active', t === thumb); });
    });
  });

  /* ---------------- View toggle + chip filters ---------------- */
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
      var key = chip.getAttribute('data-filter'), shown = 0;
      $$('[data-cat]').forEach(function (card) {
        var match = key === 'all' || card.getAttribute('data-cat') === key;
        card.style.display = match ? '' : 'none';
        if (match) shown++;
      });
      var counter = $('[data-result-count]');
      if (counter) counter.textContent = shown;
    });
  });

  document.addEventListener('click', function (e) {
    var t = e.target.closest('[data-toggle-filters]');
    if (!t) return;
    var panel = $(t.getAttribute('data-toggle-filters'));
    if (panel) panel.classList.toggle('show');
  });

  /* ---------------- Search ---------------- */
  $$('[data-quick-search]').forEach(function (input) {
    input.addEventListener('input', function () {
      var q = input.value.trim().toLowerCase();
      $$('[data-qs-item]').forEach(function (item) {
        var hit = !q || item.getAttribute('data-qs-item').toLowerCase().indexOf(q) > -1;
        item.style.display = hit ? '' : 'none';
      });
    });
  });

  /* ---------------- Mode switches ---------------- */
  $$('[data-mode-switch]').forEach(function (input) {
    input.addEventListener('change', function () {
      var group = input.getAttribute('data-mode-switch') || 'default';
      $$('[data-mode-only]').forEach(function (block) {
        if ((block.getAttribute('data-mode-group') || 'default') !== group) return;
        block.style.display = block.getAttribute('data-mode-only') === input.value ? '' : 'none';
      });
    });
  });

  /* ---------------- Reveal + counters ---------------- */
  var reveals = $$('.reveal');
  if (reveals.length && 'IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (!en.isIntersecting) return;
        en.target.classList.add('in');
        io.unobserve(en.target);
      });
    }, { threshold: 0.1, rootMargin: '0px 0px -60px' });
    reveals.forEach(function (el, i) { el.style.transitionDelay = (i % 4) * 70 + 'ms'; io.observe(el); });
  } else {
    reveals.forEach(function (el) { el.classList.add('in'); });
  }

  var counters = $$('[data-count]');
  if (counters.length && 'IntersectionObserver' in window) {
    var cio = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (!en.isIntersecting) return;
        var el = en.target, end = parseFloat(el.getAttribute('data-count'));
        var suffix = el.getAttribute('data-suffix') || '', start = null;
        var step = function (ts) {
          if (!start) start = ts;
          var p = Math.min((ts - start) / 1400, 1), eased = 1 - Math.pow(1 - p, 3), v = end * eased;
          el.textContent = (end % 1 ? v.toFixed(1) : Math.round(v).toLocaleString()) + suffix;
          if (p < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
        cio.unobserve(el);
      });
    }, { threshold: 0.5 });
    counters.forEach(function (el) { cio.observe(el); });
  }

  /* ---------------- TOC ---------------- */
  var tocLinks = $$('.toc a');
  if (tocLinks.length && 'IntersectionObserver' in window) {
    var sections = tocLinks.map(function (a) { return $(a.getAttribute('href')); }).filter(Boolean);
    var tio = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (!en.isIntersecting) return;
        tocLinks.forEach(function (a) { a.classList.toggle('active', a.getAttribute('href') === '#' + en.target.id); });
      });
    }, { rootMargin: '-20% 0px -70%' });
    sections.forEach(function (s) { tio.observe(s); });
  }

  /* ---------------- Demo forms ---------------- */
  $$('[data-demo-form]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      toast(form.getAttribute('data-demo-form') || 'Submitted');
      var modal = form.closest('.modal');
      if (modal) closeOverlay(modal); else form.reset();
    });
  });
})();
