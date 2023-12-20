@extends('voyager::recipes.tab_layout')


@section('sub-content')
    <div class="container-fluid">
        @can('add', app($dataType->model_name))
            <a href="/admin/{{$dataType->slug}}/new-order/{{$fr_rec_id}}" class="btn btn-success btn-add-new" style="background-color: chocolate">
                <i class="voyager-plus"></i> <span style="color: yellow">Add New Flow</span>
            </a>
        @endcan
        @can('delete', app($dataType->model_name))
            @if($usesSoftDeletes)
                <input type="checkbox" @if ($showSoftDeleted) checked @endif id="show_soft_deletes" data-toggle="toggle" data-on="{{ __('voyager::bread.soft_deletes_off') }}" data-off="{{ __('voyager::bread.soft_deletes_on') }}">
            @endif
        @endcan

        <a href="{{ route('voyager.recipes.index') }}" class="btn btn-warning">
            <i class="glyphicon glyphicon-list"></i> <span
                    class="hidden-xs hidden-sm">{{ __('voyager::generic.return_to_list') }}</span>
        </a>
    </div>


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
                                    <style>
                                        .dd-empty {
                                            display: none;
                                        }
                                    </style>
                                    No Data!
                                @else
                                    @foreach ($results as $result)
                                        <style>
                                            .order-item {
                                                white-space: nowrap;
                                                width: 40%;
                                                overflow: hidden;
                                                text-overflow:ellipsis;
                                            }
                                            /*.order-item:hover {
                                                overflow: visible;
                                            }*/
                                            .item_actions {
                                                top: 0 !important;
                                                margin-top: 8px;
                                            }
                                        </style>
                                        <li class="row dd-item" data-id="{{ $result->id }}">
                                            <div class="pull-right item_actions">
                                                @can('delete', app($dataType->model_name))
                                                    @if(is_null($result->deleted_at))
                                                        <a href="javascript:confirm({!! $result->id !!})" title="Delete" class="btn btn-sm btn-danger pull-right delete" >
                                                            <i class="voyager-trash item_actions" ></i> <span class="hidden-xs hidden-sm item_actions">Delete</span>
                                                        </a>
                                                    @else
                                                        <a href="/admin/{{$dataType->slug}}/{!! $result->id !!}/restore" title="Restore" class="btn btn-sm btn-success pull-right restore" >
                                                            <i class="voyager-trash item_actions" ></i> <span class="hidden-xs hidden-sm item_actions">Restore</span>
                                                        </a>
                                                    @endif
                                                @endcan
                                                @can('edit', app($dataType->model_name))
                                                    <a href="/admin/{{$dataType->slug}}/{!! $result->id !!}/edit" title="Edit" class="btn btn-sm btn-primary pull-right edit">
                                                        <i class="voyager-edit item_actions"></i> <span class="hidden-xs hidden-sm item_actions">Edit</span>
                                                    </a>
                                                @endcan
                                                @can('read', app($dataType->model_name))
                                                    @php
                                                    $id=$result->id;
                                                    $fr_rec_name=DB::table('recipes')->select('name')->where('id', $result->fr_rec_id )->pluck('name')->first();
                                                    $process_order=$result->process_order;
                                                    $qty=$result->qty;
                                                    $previous_step=$result->previous_step;
                                                    $next_step=$result->next_step;
                                                    $duration=$result->duration;
                                                    $fr_process_step=DB::table('process_steps')->select('process_name')->where('id', $result->fr_process_step )->pluck('process_name')->first();
                                                    $fr_measure=DB::table('measures')->select('symbol')->where('id', $result->fr_measure )->pluck('symbol')->first();
                                                    $fr_time_param=DB::table('time_params')->select('time_param')->where('id', $result->fr_time_param )->pluck('time_param')->first();
                                                    $queue=$result->queue;
                                                    $desc_test=$result->desc_test;
                                                    $created_at=$result->created_at;
                                                    $updated_at=$result->updated_at;
                                                    @endphp
                                                <a  data-id="{{$id}}"
                                                    data-fr_rec_name="{{$fr_rec_name}}"
                                                    data-process_order="{{$process_order}}"
                                                    data-qty="{{$qty}}"
                                                    data-previous_step="{{$previous_step}}"
                                                    data-next_step="{{$next_step}}"
                                                    data-duration="{{$duration}}"
                                                    data-fr_process_step="{{$fr_process_step}}"
                                                    data-fr_measure="{{$fr_measure}}"
                                                    data-fr_time_param="{{$fr_time_param}}"
                                                    data-queue="{{$queue}}"
                                                    data-desc_test="{{$desc_test}}"
                                                    data-created_at="{{$created_at}}"
                                                    data-updated_at="{{$updated_at}}"
                                                        title="View" class="btn btn-sm btn-warning pull-right view">
                                                    <i class="voyager-eye item_actions"></i> <span class="hidden-xs hidden-sm item_actions">View</span>
                                                </a>
                                                @endcan
                                            </div>
                                            <div class="dd-handle">
                                                <span class="col-md-8 order-item ">{{ $result->desc_test }}</span>
                                                <span class="col-md-2">{{$result->qty}}{{$result->quantityUnit}}</span>
                                                <span >{{$result->duration}}{{$result->timeUnit}}</span>
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
                    <form action="#" id="delete_form" method="POST">
                        {{ method_field('DELETE') }}
                        {{ csrf_field() }}
                        <input type="submit" class="btn btn-danger pull-right delete-confirm" value="{{ __('voyager::generic.delete_confirm') }}">
                    </form>
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->



    <div class="modal fade" id="view-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" align="center"><b>View Recipe Flow</b></h4>
                </div>
                <div class="modal-body">
                    <form role="form" action="/edit_user">
                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                        <div class="box-body">

                            <div class="form-group">
                                <label class="col-form-label" for="modal-view-id">ID </label>
                                <input type="text" name="modal-view-id" class="form-control" id="modal-view-id" value="ID" readonly="readonly">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" for="modal-view-recid">Recipe </label>
                                <input type="text" name="modal-view-recid" class="form-control" id="modal-view-recid" value="ID" readonly="readonly">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" for="modal-view-processorder">Process Order </label>
                                <input type="text" name="modal-view-processorder" class="form-control" id="modal-view-processorder" value="ID" readonly="readonly">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" for="modal-view-quantity">Quantity </label>
                                <input type="text" name="modal-view-quantity" class="form-control" id="modal-view-quantity" value="ID" readonly="readonly">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" for="modal-view-previous">Previous Step </label>
                                <input type="text" name="modal-view-previous" class="form-control" id="modal-view-previous" value="ID" readonly="readonly">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" for="modal-view-next">Next Step </label>
                                <input type="text" name="modal-view-next" class="form-control" id="modal-view-next" value="ID" readonly="readonly">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" for="modal-view-duration">Duration </label>
                                <input type="text" name="modal-view-duration" class="form-control" id="modal-view-duration" value="ID" readonly="readonly">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" for="modal-view-prostep">Process Step </label>
                                <input type="text" name="modal-view-prostep" class="form-control" id="modal-view-prostep" value="ID" readonly="readonly">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" for="modal-view-measures">Measures </label>
                                <input type="text" name="modal-view-measures" class="form-control" id="modal-view-measures" value="ID" readonly="readonly">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" for="modal-view-timeparam">Time Param </label>
                                <input type="text" name="modal-view-timeparam" class="form-control" id="modal-view-timeparam" value="ID" readonly="readonly">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" for="modal-view-queue">Queue </label>
                                <input type="text" name="modal-view-queue" class="form-control" id="modal-view-queue" value="ID" readonly="readonly">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" for="modal-view-desc">Desc Test </label>
                                <input type="text" name="modal-view-desc" class="form-control" id="modal-view-desc" value="ID" readonly="readonly">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" for="modal-view-created">Created At </label>
                                <input type="text" name="modal-view-created" class="form-control" id="modal-view-created" value="ID" readonly="readonly">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" for="modal-view-updated">Updated At </label>
                                <input type="text" name="modal-view-updated" class="form-control" id="modal-view-updated" value="ID" readonly="readonly">
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary pull-right" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

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
            $('#delete_form')[0].action = '{{ route('voyager.'.$dataType->slug.'.destroy', '__id') }}'.replace('__id', id);
{{--            $('#delete_form')[0].action =  "/admin/{{$dataType->slug}}/delete/" + id;--}}
            $('#delete_modal').modal('show');
        }


        $('.view').on('click', function (e) {

            $('#modal-view-id').val($(this).data('id'));
            $('#modal-view-recid').val($(this).data('fr_rec_name'));
            $('#modal-view-processorder').val($(this).data('process_order'));
            $('#modal-view-quantity').val($(this).data('qty'));
            $('#modal-view-previous').val($(this).data('previous_step'));
            $('#modal-view-next').val($(this).data('next_step'));
            $('#modal-view-duration').val($(this).data('duration'));
            $('#modal-view-prostep').val($(this).data('fr_process_step'));
            $('#modal-view-measures').val($(this).data('fr_measure'));
            $('#modal-view-timeparam').val($(this).data('fr_time_param'));
            $('#modal-view-queue').val($(this).data('queue'));
            $('#modal-view-desc').val($(this).data('desc_test'));
            $('#modal-view-created').val($(this).data('created_at'));
            $('#modal-view-updated').val($(this).data('updated_at'));
            $('#view-modal').modal('show');
        });


        @if($usesSoftDeletes)
        $(function() {
            $('#show_soft_deletes').change(function() {
                let url = "/admin/{{$dataType->slug}}/category/{{ $fr_rec_id }}?softDelete=0";
                if ($(this).prop('checked')) {
                    url = "/admin/{{$dataType->slug}}/category/{{ $fr_rec_id }}?softDelete=1";
                }
                window.location.assign(url);
            })
        });
        @endif
    </script>
@stop
