@extends('layouts.master')
@section('content')
<div class="m-portlet">
    <div class="m-portlet__head">
        <div class="m-portlet__head-caption">
            <div class="m-portlet__head-title">
                <span class="m-portlet__head-icon"><i class="la la-trademark"></i></span>
                <h3 class="m-portlet__head-text">Бренды</h3>
            </div>
        </div>
    </div>
    <div class="m-portlet__body">

        <table class="table table-bordered ajax-content" data-url="{{ route('admin.brands.list') }}" id="brandsTable">
            <thead>
            <tr class="nowrap">
                <th>ID</th>
                <th>Бренд</th>
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
