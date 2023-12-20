@extends('voyager::master')

@section('content')
    <div id="current-production-status">Current Production Status</div>
@stop
@section('javascript')
    <script src={{asset('js/currentProductionStatus.js')}}></script>
@stop