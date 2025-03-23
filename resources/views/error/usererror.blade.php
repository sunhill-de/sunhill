@extends('sunhill::basic.html')

@section('title')
{{ __('User error') }}
@endsection

@section('body')
<div class="errorblock">
There was an user error. The message is:

<div class="errormessage">{{ __($error_message) }}</div>

@if(!empty($return_link))<a href="{{ $return_link }}">Go back...</a>&nbsp;*&nbsp; @endif<a href="/">{{ __('Back to mainpage') }}</a>
</div>
@endsection