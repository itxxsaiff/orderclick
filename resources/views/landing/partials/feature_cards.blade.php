{{--
    Feature cards from Admin > Features (home "Why Order Click" and each system section).
    $ocFeats  collection of Features
    Cards are centred and wide, so one or two features read as a deliberate highlight
    instead of a lone small box at the start of an empty grid.
--}}
@php $ocFeatCount = $ocFeats->count(); @endphp
<div class="ocl-feats {{ $ocFeatCount === 1 ? 'ocl-feats--single' : '' }}">
    @foreach ($ocFeats as $f)
        <div class="ocl-feat">
            <span class="ocl-feat__ic">
                @if ($f->hasImage())
                    <img src="{{ helper::image_path($f->image) }}" alt="">
                @else
                    <i class="fa-solid fa-star"></i>
                @endif
            </span>
            <div>
                <h4>{{ $f->title }}</h4>
                @if (trim(strip_tags((string) $f->description)) !== '')
                    <p>{{ \Illuminate\Support\Str::limit(strip_tags($f->description), $ocFeatCount === 1 ? 220 : 120) }}</p>
                @endif
            </div>
        </div>
    @endforeach
</div>
