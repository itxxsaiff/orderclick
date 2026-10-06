{{-- Home page of the AI design engine: the hero, then the sections in the order the design plan sets. --}}
@include('front.template-20.sections.hero')
@foreach ($tDesign['sections'] as $ocSection)
    @switch($ocSection)
        @case('trust')      @include('front.template-20.sections.trust') @break
        @case('categories') @include('front.template-20.sections.categories') @break
        @case('products')   @include('front.template-20.sections.products', ['ocKey' => 'products']) @break
        @case('offers')     @include('front.template-20.sections.products', ['ocKey' => 'offers', 'ocItems' => $topdealsproducts ?? []]) @break
        @case('services')   @include('front.template-20.sections.services') @break
        @case('team')       @include('front.template-20.sections.team') @break
        @case('about')      @include('front.template-20.sections.about') @break
        @case('steps')      @include('front.template-20.sections.steps') @break
        @case('reviews')    @include('front.template-20.sections.reviews') @break
        @case('cta')        @include('front.template-20.sections.cta') @break
    @endswitch
@endforeach
