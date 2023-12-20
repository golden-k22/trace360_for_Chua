@extends('voyager::master')
@section('css')
    @include('voyager::physical-devices.styles')
    @yield('customcss')
@stop
@section('page_title', __('voyager::generic.view').' '.$dataType->getTranslatedAttribute('display_name_singular'))

@php
    $is_second=isset($is_second)? $is_second:2;
    $active_tab=isset($active_tab)? $active_tab:"ingredient"
@endphp

@section('content')
    <div id="gradient_bg"></div>
    <div class="page-content compass container-fluid">
        <ul class="nav nav-tabs">
            <li @if(empty($active_tab) || (isset($active_tab) && $active_tab == 'ingredient')){!! 'class="active"' !!}@endif>
                <a href="{{ url('admin/ingredients/'.$categoryId) }}"><i class="voyager-book"></i> Ingredient details</a>
            </li>

            <li @if($active_tab == 'lotcode'){!! 'class="active"' !!}@endif>
                <a href="{{ url('admin/ingredients/'.$categoryId.'/lot-codes') }}"><i class="voyager-book"></i> Lot Codes</a>
            </li>
        </ul>


        <div class="tab-content">
            @yield('sub-content')
        </div>
    </div>

@stop

