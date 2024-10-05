@extends('framework::basic.html')
@section('title')
404 - {{ __('Page not found') }}
@endsection

@section('body')
{{ __('The requested page was not found') }}
<a href="/">{{ __('Back to mainpage') }}</a>
@endsection