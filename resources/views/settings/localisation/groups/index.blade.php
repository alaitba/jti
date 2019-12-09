@extends('layouts.master')

@section('content')
    <div class="m-portlet">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        {{ $title }}
                    </h3>
                </div>
            </div>

            <div class="m-portlet__head-tools">
                <ul class="m-portlet__nav">

                    <li class="m-portlet__nav-item">
                        <a href="#" data-url="{{ route('admin.settings.localisation.groups.create') }}"
                           data-type="modal" data-modal="regularModal"
                           class="m-portlet__nav-link m-portlet__nav-link--icon handle-click"
                           data-container="body"
                           data-toggle="m-tooltip"
                           data-placement="top"
                           title="Создать руппу">
                            <i class="la la-plus"></i>
                        </a>
                    </li>

                </ul>
            </div>
        </div>


        <table class="table table-bordered ajax-content"
               data-url="{{ route('admin.settings.localisation.groups.list') }}">
            <thead>
            <tr>
                <th class="text-center" width="50">#</th>
                <th>Название</th>
                <th width="50" class="text-center"><i class="la la-language"></i></th>
                <th class="text-center" width="50"><i class="fa fa-bars" aria-hidden="true"></i></th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>


    </div>
@stop