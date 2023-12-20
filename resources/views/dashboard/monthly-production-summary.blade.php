@extends('voyager::master')

@section('content')
    <div id="monthly-production-summary">Monthly Production Summary</div>
@stop
@section('javascript')
    <script src={{asset('js/monthlyProductionSummary.js')}}></script>
@stop