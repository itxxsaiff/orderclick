@extends('landing.layout.default')
@section('content')
<div class="ocl ocl-page">
    <div class="ocl-pagehero">
        <div class="wrap">
            <div class="crumbs"><a href="{{ URL::to('/') }}">{{ trans('labels.home') }}</a> &nbsp;/&nbsp; {{ trans('landing.blogs') }}</div>
            <h1>{{ trans('landing.blog_section_title') }}</h1>
            <p>{{ trans('landing.blog_section_description') }}</p>
        </div>
    </div>
    <div class="ocl-content">
        <div class="wrap">
            @if (count($blogs) > 0)
                <div class="ocl-blogs">
                    @foreach ($blogs as $blog)
                        <a href="{{ URL::to('blog_details-' . $blog->id) }}" class="ocl-blog">
                            <div class="ocl-blog__img" style="background-image:url('{{ url(env('ASSETSPATHURL') . 'admin-assets/images/blog/' . $blog->image) }}');"></div>
                            <div class="ocl-blog__body">
                                <h4>{{ $blog->title }}</h4>
                                <p>{{ \Illuminate\Support\Str::limit(strip_tags((string) @$blog->description), 110) }}</p>
                                <span class="go">{{ trans('landing.read_more_2') }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
                <div style="margin-top:34px;">{{ $blogs->links() }}</div>
            @else
                <div class="ocl-empty">{{ trans('landing.no_blog_posts_yet') }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
