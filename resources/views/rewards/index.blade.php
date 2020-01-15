@extends('layouts.master')
@section('content')
<div class="m-portlet">
    <div class="m-portlet__head">
        <div class="m-portlet__head-caption">
            <div class="m-portlet__head-title">
                <span class="m-portlet__head-icon"><i class="la la-gift"></i></span>
                <h3 class="m-portlet__head-text">Призы</h3>
            </div>
        </div>
        <div class="m-portlet__head-tools">
          {{--  Последнее обновление: {{ $lastUpdate }} --}}
        </div>
    </div>
    <div class="m-portlet__body">

        <table class="table table-bordered ajax-content" data-url="{{ route('admin.rewards.list') }}" id="rewardsTable">
            <thead>
            <tr class="nowrap">
                <th>CRM ID</th>
                <th>Название</th>
                <th>Цена</th>
                <th>Количество</th>
                <th>Описание</th>
                <th>Фото</th>
                <th><i class="la la-edit"></i></th>
            </tr>
            </thead>
            <tbody>

            </tbody>
        </table>

        <div class="pagination_placeholder"></div>


    </div>
</div>
@stop
