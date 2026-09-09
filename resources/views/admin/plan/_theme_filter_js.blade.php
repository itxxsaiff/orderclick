{{-- Shows only the selected system's themes, clears incompatible selections. Called by _system_fields applySystem(). --}}
<script>
    window.ocFilterPlanThemes = function (sys) {
        var any = false;
        document.querySelectorAll('.oc-theme-card[data-theme-system]').forEach(function (card) {
            var match = card.getAttribute('data-theme-system') === sys;
            card.style.display = match ? '' : 'none';
            var cb = card.querySelector('input[name="themecheckbox[]"]');
            if (cb) { cb.disabled = !match; if (!match && cb.checked) cb.checked = false; }
            if (match) any = true;
        });
        var note = document.querySelector('.oc-no-theme');
        if (note) note.style.display = any ? 'none' : '';
        // Nothing exists for this system yet (Service has no themes). Leave the stored selection
        // alone rather than silently clearing the plan's themes on save.
        if (!any) {
            document.querySelectorAll('.oc-theme-card input[name="themecheckbox[]"]').forEach(function (c) { c.disabled = false; });
            return;
        }
        // if nothing is selected for this system, auto-select the first visible theme
        var checkedAny = Array.prototype.some.call(document.querySelectorAll('.oc-theme-card[data-theme-system] input[name="themecheckbox[]"]'), function (c) { return c.checked && !c.disabled; });
        if (!checkedAny && any) {
            var first = document.querySelector('.oc-theme-card[data-theme-system="' + sys + '"] input[name="themecheckbox[]"]');
            if (first) first.checked = true;
        }
    };
</script>
