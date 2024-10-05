@extends('framework::basic.html')

@section('body')
<nav role='navigation'>
  <div id="menuToggle">
    <input type="checkbox" />
    <span></span>
    <span></span>
    <span></span>
    <ul id="menu">
      <a href="#"></a>
      @foreach ($hamburger_entries as $entry)
      <a href="{{ route($entry->route) }}"><li>{{ __($entry->text) }}</li></a>
      @endforeach
      <a href="{{ route('admin.settings') }}"><li><{{ __('settings...') }}</li></a>
    </ul>
  </div>
</nav>
@endsection