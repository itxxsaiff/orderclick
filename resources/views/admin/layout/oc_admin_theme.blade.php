{{--
  Order Click — Modern Admin Theme overlay (light mode only).
  Scoped under html.light so the existing dark mode (.dark body …) is left completely untouched.
  Loaded after the panel's style.css so it wins. Structure/classes are unchanged — this is pure styling.
--}}
<style>
    html.light {
        --oc: #1f9d55; --oc-dark: #17864a; --oc-soft: #e9f6ef; --oc-ring: rgba(31, 157, 85, .16);
        --oc-line: #e6ecea; --oc-muted: #94a0ad;
    }

    /* ---------- App background: soft, so white surfaces lift off it ---------- */
    html.light body {
        background:
            radial-gradient(1200px 460px at 78% -8%, #e3f1ea 0%, transparent 60%),
            linear-gradient(180deg, #eef3f2 0%, #f4f7f7 100%) !important;
        background-attachment: fixed !important;
    }
    html.light .main-content, html.light .main-content-rtl, html.light .page-content { background: transparent !important; }

    /* ---------- Header ---------- */
    html.light .page-topbar {
        background: rgba(255, 255, 255, .88) !important;
        -webkit-backdrop-filter: saturate(180%) blur(9px); backdrop-filter: saturate(180%) blur(9px);
        border-bottom: 1px solid var(--oc-line) !important;
        box-shadow: 0 10px 30px -22px rgba(20, 40, 30, .45);
    }

    /* ---------- Sidebar ---------- */
    html.light .sidebar {
        background: #ffffff !important;
        border-right: 1px solid var(--oc-line);
        box-shadow: 0 20px 50px -38px rgba(20, 40, 30, .5);
    }
    html.light .sidebar h6 {
        font-size: 10.5px !important; letter-spacing: .09em; color: var(--oc-muted) !important; font-weight: 600 !important;
    }
    html.light .sidebar .navbar-nav .nav-item .nav-link {
        color: #3b4653 !important; font-weight: 500; border-radius: 12px !important; margin: 1px 6px; transition: .18s ease;
    }
    html.light .sidebar .navbar-nav .nav-item .nav-link:hover {
        background: var(--oc-soft) !important; color: var(--oc-dark) !important; transform: translateX(2px);
    }
    html.light .sidebar .navbar-nav .nav-item .nav-link.active,
    html.light .sidebar .navbar-nav .multimenu .active-main {
        background: linear-gradient(135deg, var(--oc) 0%, var(--oc-dark) 100%) !important;
        color: #fff !important;
        box-shadow: 0 14px 26px -12px var(--oc-ring);
    }
    /* icon chip inside the active item stays visible on the green pill */
    html.light .sidebar .nav-link.active .sidebariconbox,
    html.light .sidebar .nav-link.active .sidebariconbox1 {
        background: rgba(255, 255, 255, .22) !important; color: #fff !important;
    }
    html.light .sidebar .nav-link.active i { color: #fff !important; }

    /* ---------- Cards ---------- */
    html.light .card {
        border: 1px solid var(--oc-line) !important;
        border-radius: 16px !important;
        box-shadow: 0 12px 34px -24px rgba(20, 40, 30, .38) !important;
        transition: transform .18s ease, box-shadow .18s ease;
    }
    html.light .card:hover { box-shadow: 0 22px 48px -28px rgba(20, 40, 30, .42) !important; }
    /* dashboard stat cards keep their pastel fills — just add lift, drop the border */
    html.light [class*="deshcard"], html.light .desh_left { border: 0 !important; }
    html.light [class*="deshcard"]:hover { transform: translateY(-3px); }
    html.light .dashboard-card .card-icon { box-shadow: 0 12px 22px -12px rgba(0, 0, 0, .28); }

    /* ---------- Tables ---------- */
    html.light .table > :not(caption) > * > * { padding: 13px 16px; }
    html.light .table > thead > tr > th, html.light .table thead th {
        background: #f4f7f6 !important; color: #67727f !important;
        text-transform: uppercase; font-size: 11px; letter-spacing: .045em; font-weight: 600;
        border-bottom: 1px solid var(--oc-line) !important; white-space: nowrap;
    }
    html.light .table tbody tr { transition: background .15s ease; }
    html.light .table tbody tr:hover > * { background: var(--oc-soft) !important; }
    html.light .table td, html.light .table th { border-color: #eef2f1 !important; vertical-align: middle; }

    /* ---------- Buttons & inputs: gentle polish ---------- */
    html.light .btn { border-radius: 10px; }
    html.light .btn-primary, html.light .btn-secondary, html.light .btn-success { box-shadow: 0 12px 22px -14px rgba(0, 0, 0, .38); }
    html.light .form-control, html.light .form-select { border-radius: 10px; border-color: var(--oc-line); }
    html.light .form-control:focus, html.light .form-select:focus { border-color: var(--oc); box-shadow: 0 0 0 3px var(--oc-ring); }

    /* ---------- Header icon chips ---------- */
    html.light .header_icon_box { border-radius: 11px; box-shadow: 0 10px 18px -12px rgba(0, 0, 0, .3); }
</style>
