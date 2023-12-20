@extends('voyager::master')
@section('css')
    {{--<link rel="stylesheet" href="https://pyscript.net/alpha/pyscript.css" />--}}
    {{--<link rel="stylesheet" href={{asset('css/pyscript.css')}} />--}}
@stop
@section('content')
    <div id="statistical-process-control">Statistical Process Control</div>
@stop
@section('javascript')

{{--    <script defer src={{asset('js/pyscript.js')}}></script>--}}
    {{--<script defer src="https://pyscript.net/alpha/pyscript.js"></script>--}}

    <script src={{asset('js/statisticalProcessControl.js')}}></script>

@stop