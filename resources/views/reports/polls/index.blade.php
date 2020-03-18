@extends('layouts.master')
@section('content')
<div class="m-portlet">
    <div class="m-portlet__head">
        <div class="m-portlet__head-caption">
            <div class="m-portlet__head-title">
                <span class="m-portlet__head-icon"><i class="la la-question-circle"></i></span>
                <h3 class="m-portlet__head-text">Опросы</h3>
            </div>
        </div>
    </div>
    <div class="m-portlet__body">

        <table class="table table-bordered ajax-content" data-url="{{ route('admin.reports.polls.list') }}" id="pollsTable">
            <thead>
            <tr class="nowrap">
                <th width="100" class="text-center">ID</th>
                <th>Ф.И.О.</th>
                <th>Опрос</th>
                <th>Дата</th>
                <th width="50" class="text-center"><i class="la la-eye"></i></th>
            </tr>
            </thead>
            <tbody>

            </tbody>
        </table>

        <div class="pagination_placeholder"></div>

    </div>
</div>
@stop
