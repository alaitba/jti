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
                        <a href="#" data-url="{{ route('admin.settings.localisation.groups.items.create', ['groupId' => $group->id]) }}"
                           data-type="modal" data-modal="largeModal"
                           class="m-portlet__nav-link m-portlet__nav-link--icon handle-click"
                           data-container="body"
                           data-toggle="m-tooltip"
                           data-placement="top"
                           title="Создать перевод">
                            <i class="la la-plus"></i>
                        </a>
                    </li>

                </ul>
            </div>
        </div>


        <table class="table table-bordered ajax-content"
               data-url="{{ route('admin.settings.localisation.groups.items.list', ['groupId' => $group->id]) }}">
            <thead>
            <tr>
                <th class="text-center" width="50">#</th>
                <th>Название</th>
                <th>Код вставки</th>
                <th class="text-center" width="50"><i class="fa fa-bars" aria-hidden="true"></i></th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>

        <div class="pagination_placeholder"></div>
    </div>
@stop