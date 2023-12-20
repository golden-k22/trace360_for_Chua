@extends('voyager::master')
@section('css')
    @include('voyager::physical-devices.styles')
    @yield('custom-css')
@stop
@section('page_title', __('voyager::generic.view').' '.$dataType->getTranslatedAttribute('display_name_singular'))
@php
    $is_second=isset($is_second)? $is_second:2;
    $active_tab=isset($active_tab)? $active_tab:"recipes";
@endphp

@section('content')

    <div id="gradient_bg"></div>
    <div class="page-content compass container-fluid">
        <ul class="nav nav-tabs">
            <li @if(empty($active_tab) || (isset($active_tab) && $active_tab == 'recipes')){!! 'class="active"' !!}@endif>
                <a href="{{ url('admin/recipes/'.$categoryId) }}"><i class="voyager-book"></i> Recipe details</a>
            </li>

            <li @if($active_tab == 'recipe-flows'){!! 'class="active"' !!}@endif>
                <a href="{{ url('admin/recipe-flows/category/'.$categoryId) }}"><i class="voyager-book"></i> Recipe Flows</a>
            </li>

            <li @if($active_tab == 'ingredient-lists'){!! 'class="active"' !!}@endif>
                <a href="{{ url('admin/ingredient-lists/category/'.$categoryId) }}"><i class="voyager-book"></i>Ingredient Lists</a>
            </li>
        </ul>


        <div class="tab-content tab-pane fade in">
            @yield('sub-content')
        </div>
    </div>

@stop

