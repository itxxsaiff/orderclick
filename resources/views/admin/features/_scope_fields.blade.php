{{-- Applies To + Display Order.
     "All Systems" features show in the general block on the landing page; a system-specific
     feature shows inside that system's section. Marketing content only. --}}
@php
    $ocF = $feature ?? null;
    $ocApplies = old('applies_to', $ocF->applies_to ?? 'all');
    $ocOrder = old('reorder_id', $ocF->reorder_id ?? ($nextOrder ?? 1));
@endphp

<div class="form-group col-md-6">
    <label class="form-label">{{ trans('labels.applies_to') }}<span class="text-danger"> *</span></label>
    <select class="form-select" name="applies_to" required>
        @foreach (\App\Models\Features::appliesToOptions() as $ocK => $ocLabel)
            <option value="{{ $ocK }}" {{ $ocApplies === $ocK ? 'selected' : '' }}>{{ $ocLabel }}</option>
        @endforeach
    </select>
    <small class="text-muted">{{ trans('messages.applies_to_hint') }}</small>
</div>

<div class="form-group col-md-6">
    <label class="form-label">{{ trans('labels.display_order') }}<span class="text-danger"> *</span></label>
    <input type="number" min="1" class="form-control" name="reorder_id" value="{{ $ocOrder }}" required>
    <small class="text-muted">{{ trans('messages.display_order_hint') }}</small>
</div>
