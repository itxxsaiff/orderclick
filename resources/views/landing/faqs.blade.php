@extends('landing.layout.default')
@section('content')
<section class="breadcrumb-sec bg-change-mode">
    <div class="container">
        <nav>
            <ol class="breadcrumb d-flex m-0 text-capitalize">
                <li class="breadcrumb-item">
                    <a href="{{ URL::to('/#home') }}" class="text-dark color-changer">
                        {{ trans('labels.home') }}
                    </a>
                </li>
                <li
                    class="breadcrumb-item active {{ session()->get('direction') == 2 ? 'breadcrumb-item-right' : 'breadcrumb-item-left' }}">
                    {{ trans('landing.faq_section_title') }}
                </li>
            </ol>
        </nav>
    </div>
</section>
<section class="my-5">
    <div class="container faq-container faq">
        <div class="sec-title mb-5">
            <h2 class="faq-title color-changer">{{ trans('landing.faq_section_title') }}</h2>
            <h5 class="faq-subtitle col-md-12 sub-title">
                {{ trans('landing.faq_section_description') }}
            </h5>
        </div>
        <div>
            <div class="accordion" id="accordionExample">
                @foreach ($allfaqs as $key => $faq)
                <div class="accordion-item bg-transparent border-0 {{ $key == 0 ? ' pt-0' : ' pt-4' }}">
                    <h2 class="accordion-header" id="heading-{{ $key }}">
                        <button
                            class="{{ session()->get('direction') == 2 ? 'accordion-button-rtl' : 'accordion-button' }} justify-content-between border rounded-3 {{ $key == 0 ? '' : 'collapsed' }}"
                            type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $key }}"
                            aria-expanded="true" aria-controls="collapse-{{ $key }}">
                            {{ $faq->question }}
                        </button>
                    </h2>
                    <div id="collapse-{{ $key }}"
                        class="accordion-collapse border border rounded-2 collapse mt-2 {{ $key == 0 ? 'show' : '' }}"
                        aria-labelledby="heading-{{ $key }}" data-bs-parent="#accordionExample">
                        <div class="accordion-body rounded-1">
                            <p class="faq-accordion-lorem-text color-changer pt-3">
                                {{ $faq->answer }}
                            </p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endsection