@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
        	<div class="panel panel-default">
                <div class="panel-heading">Posts</div>

                <div class="panel-body posts">
                    <ul>
                  @foreach ($PostList as $post)
                             @if($post->Visible)
                                   <li><h2><a href="/projects/LaravelBlog/public/post/{{ $post->id }}">{{ $post->title }}</a></h2><p>{{  $PostExcerpts["ById"][$post->id] }}</p></li>
                              @endif
                   @endforeach
                    </ul>

                    <div class="col-xs-4 col-xs-offset-4">
                        {!! $PostList->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection