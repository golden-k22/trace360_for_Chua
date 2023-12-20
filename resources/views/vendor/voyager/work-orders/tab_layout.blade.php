@extends('voyager::master')
@section('css')
    @include('voyager::physical-devices.styles')
    @yield('custom-css')
@stop
@section('page_title', __('voyager::generic.view').' '.$dataType->getTranslatedAttribute('display_name_singular'))
@php
    $is_second=isset($is_second)? $is_second:2;
    $active_tab=isset($active_tab)? $active_tab:"work-orders";
@endphp

@section('content')
    <div id="gradient_bg"></div>
    <div class="page-content compass container-fluid">
        <ul class="nav nav-tabs">
            <li @if(empty($active_tab) || (isset($active_tab) && $active_tab == 'work-orders')){!! 'class="active"' !!}@endif>
                <a href="{{ url('admin/work-orders/'.$categoryId) }}"><i class="voyager-book"></i> Work Order Detail</a>
            </li>

            <li @if($active_tab == 'work-order-items'){!! 'class="active"' !!}@endif>
                <a href="{{ url('admin/work-orders/'.$categoryId.'/work-order-items') }}"><i class="voyager-book"></i>Work Order Items</a>
            </li>

            <li @if($active_tab == 'production-orders'){!! 'class="active"' !!}@endif>
                <a href="{{ url('admin/work-orders/'.$categoryId.'/production-orders') }}"><i class="voyager-book"></i>Production Orders</a>
            </li>
        </ul>


        <div class="tab-content tab-pane fade in">
            @yield('sub-content')
        </div>
    </div>

@stop

