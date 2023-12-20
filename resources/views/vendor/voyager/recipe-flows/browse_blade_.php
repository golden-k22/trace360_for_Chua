@extends('voyager::master')

@section('page_title', $dataType->getTranslatedAttribute('display_name_plural') . ' ' . __('voyager::bread.order'))

@section('page_header')
    <h1 class="page-title">
        <i class="voyager-list"></i>{{ $dataType->getTranslatedAttribute('display_name_plural') }} {{ __('voyager::bread.order') }}
    </h1>
    @can('add', app($dataType->model_name))
        <a href="/admin/recipe-flows/new-order/{{$results[0]->fr_rec_id}}" class="btn btn-success btn-add-new" style="background-color: chocolate">
            <i class="voyager-plus"></i> <span style="color: yellow">Add New Flow</span>
        </a>
    @endcan
@stop

@section('content')
    <div class="page-content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-heading">
                        <p class="panel-title" style="color:#777">{{ __('voyager::generic.drag_drop_info') }}</p>
                    </div>

                    <div class="panel-body" style="padding:30px;">
                        <div class="dd">
                            <ol class="dd-list">
                                @if(count($results) === 0)
                                    No Data!
                                @else
                                    @foreach ($results as $result)
                                        <li class="dd-item" data-id="{{ $result->id }}">
                                            <div class="pull-right item_actions">
                                                <a href="javascript:confirm({!! $result->id !!})" title="Delete" class="btn btn-sm btn-danger pull-right delete" >
                                                    <i class="voyager-trash item_actions" ></i> <span class="hidden-xs hidden-sm item_actions">Delete</span>
                                                </a>
                                                <a href="/admin/recipe-flows/{!! $result->id !!}/edit" title="Edit" class="btn btn-sm btn-primary pull-right edit">
                                                    <i class="voyager-edit item_actions"></i> <span class="hidden-xs hidden-sm item_actions">Edit</span>
                                                </a>
                                                <a href="/admin/recipe-flows/{!! $result->id !!}" title="View" class="btn btn-sm btn-warning pull-right view" class="a">
                                                    <i class="voyager-eye item_actions"></i> <span class="hidden-xs hidden-sm item_actions">View</span>
                                                </a>
                                            </div>
                                            <div class="dd-handle" style="height:inherit; position: relative;">

                                                @if (isset($dataRow->details->view))
                                                    @include($dataRow->details->view, ['row' => $dataRow, 'dataType' => $dataType, 'dataTypeContent' => $result, 'content' => $result->{$display_column}, 'action' => 'order'])
                                                @elseif($dataRow->type == 'image')
                                                    <span>
                                                    <img src="@if( !filter_var($result->{$display_column}, FILTER_VALIDATE_URL)){{ Voyager::image( $result->{$display_column} ) }}@else{{ $result->{$display_column} }}@endif" style="height:100px">
                                                </span>
                                                @else
                                                    <span>{{ $result->{$display_column} }}</span>
                                                @endif
                                               {{-- <div style="position: absolute; top: 0; right: 0; padding: 2px 5px;">
                                                    <a href="javascript:confirm({!! $result->id !!})" title="Delete" class="btn btn-sm btn-danger pull-right delete">
                                                        <i class="voyager-trash"></i> <span class="hidden-xs hidden-sm">Delete</span>
                                                    </a>
                                                    <a href="/admin/recipe-flows/{!! $result->id !!}/edit" title="Edit" class="btn btn-sm btn-primary pull-right edit">
                                                        <i class="voyager-edit"></i> <span class="hidden-xs hidden-sm">Edit</span>
                                                    </a>
                                                    <a href="/admin/recipe-flows/{!! $result->id !!}" title="View" class="btn btn-sm btn-warning pull-right view">
                                                        <i class="voyager-eye"></i> <span class="hidden-xs hidden-sm">View</span>
                                                    </a>
                                                </div>--}}
                                            </div>
                                        </li>
                                    @endforeach
                                @endif
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Single delete modal --}}
    <div class="modal modal-danger fade" tabindex="-1" id="delete_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><i class="voyager-trash"></i> {{ __('voyager::generic.delete_question') }} {{ strtolower($dataType->getTranslatedAttribute('display_name_singular')) }}?</h4>
                </div>
                <div class="modal-footer">
                    <form action="{{ URL::to('/admin/recipe-flows/delete/'.$result->id) }}" id="delete_form" method="POST">
                        {{ csrf_field() }}
                        <input type="submit" class="btn btn-danger pull-right delete-confirm" value="{{ __('voyager::generic.delete_confirm') }}">
                    </form>
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

@stop

@section('javascript')

    <script>
        $(document).ready(function () {
            $('.dd').nestable({
                expandBtnHTML: '',
                collapseBtnHTML: ''
            });

            $('.dd').nestable({
                maxDepth: 1
            });

            /**
             * Reorder items
             */
            $('.dd').on('change', function (e) {
                e.preventDefault();
                $.post('{{ route('voyager.'.$dataType->slug.'.order') }}', {
                    order: JSON.stringify($('.dd').nestable('serialize')),
                    _token: '{{ csrf_token() }}'
                }, function (data) {
                    toastr.success("{{ __('voyager::bread.updated_order') }}");
                });

            });

        });

        function confirm(id) {

            $('#delete_modal').modal('show');
        }
    </script>
@stop
