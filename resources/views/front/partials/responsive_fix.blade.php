{{-- Small-screen hardening — keeps every theme from overflowing horizontally down to ~320px.
     Loaded after the theme stylesheet so it wins. Uses overflow-x:clip (not hidden) so sticky headers keep working. --}}
<style>
    html, body { max-width: 100%; overflow-x: clip; }
    img, svg, video, iframe, table { max-width: 100%; height: auto; }
    h1, h2, h3, h4, h5, p, a, span, li, button, label, td, th { overflow-wrap: break-word; word-break: break-word; }
    /* let flex/grid children shrink instead of forcing the row wider than the screen */
    .container > *, .grid > *, [class*="grid"] > *, [class*="row"] > * { min-width: 0; }
    @media (max-width: 480px) {
        .container { padding-inline: 15px !important; }
    }
    @media (max-width: 360px) {
        .container { padding-inline: 11px !important; }
    }
</style>
