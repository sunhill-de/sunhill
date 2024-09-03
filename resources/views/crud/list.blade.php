@extends('framework::basic.navigation')

@section('content')
<div class="table-head">
@if(!empty($filters))
<div class="element">
<select class="filter" name="filter" id="filter">
 <option value="none">{{ __('(no filter)') }}</option>
@foreach ($filters as $filter)
 <option value="{{ $filter->value }}">{{ __($filter->name) }}</option>
@endforeach
</select>
</div>
@endif
@if($search)
<div class="element"><input name="search_str" id="search_str"><button id="submit_search" class="search">{{ __('search') }}</button></div>
@endif
</div>
<table class="data">
 <th>
@foreach ($columns as $column)
  <td class="{{ $column->class }}"><x-optional_link :entry="$column"/></td>
@endforeach
 </th>
@forelse ($datarows as $row)
 <tr>
@foreach ($row as $column)
@switch($column->class)
@case('group')
  <td class="group"><input type="checkbox" name="group[]" value="{{$column->data}}"></td>
@break  
@default
  <td class="{{ $column->class}}"><x-optional_link :entry="$column->data"/></td>
@endswitch  
@endforeach
 </tr>
@empty
 <tr><td colspan="100">{{ __('No entries.') }}</td></tr>
@endforelse
</table>
<div class="table-foot">
@if(!empty($group_actions))
 <div class="element">
  <div class="group_actions">
@foreach($group_actions as $action)
   <div class="element"><button id="{{ $action->id }}" class="group">{{ $action->title }}</button></div>
@endforeach  
  </div>
 </div>
@endif
</div>
@if(!empty($pagination))
<nav role="paginator">
<ul>
@foreach ($pagination as $page)
  <li>
@switch ($page->type)
@case('link')
   <a href="{{ $page->link }}">{{ $page->title }}</a>
@break
@case('ellipse')
   <div class="ellipse">...</div>
@break
@case('current')
   <div class="active-page">{{ $page->title }}</div>
@break      
@endswitch  
  </li>
@endforeach
 </ul> 
</nav>
@endif
@endsection
