@extends('framework::basic.hamburger')

@section('body')

@parent
<div class="page" onclick="{{ route($return_link) }}">
@yield('content')
</div>
@endsection