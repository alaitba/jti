@extends('layouts.master')
@section('content')
<div class="m-portlet">
    <div class="m-portlet__head">
        <div class="m-portlet__head-caption">
            <div class="m-portlet__head-title">
                <span class="m-portlet__head-icon"><i class="la la-users"></i></span>
                <h3 class="m-portlet__head-text">Зарегистрированные продавцы</h3>
            </div>
        </div>
    </div>
    <div class="m-portlet__body">

        <table class="table table-bordered" id="partnersTable">
            <thead>
            <tr class="nowrap">
                <th>Код ТТ</th>
                <th>Телефон</th>
                <th>OS</th>
                <th>Первый вход</th>
                <th>Последний вход</th>
                <th>Статус</th>
            </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>
@stop
@push('modules')
    <script src="/core/adminLTE/assets/vendors/custom/datatables/datatables.bundle.js"></script>
    <script>
        $(document).ready( function () {
            $('#partnersTable').DataTable({
                ajax: '/reports/partners/get-list',
                columns: [
                    { data: 'current_tradepoint' },
                    { data: 'mobile_phone' },
                    { data: 'platform' },
                    { data: 'created_at' },
                    { data: 'updated_at' },
                    { data: 'status' }
                ],
                pageLength: 25,
                orderCellsTop: true,
                //dom: '<"pull-left"B><"pull-right"f><"clearfix">rt<"pull-left"i><"pull-right"p><"pull-right"l><"clearfix">',
                dom: '<"d-flex justify-content-between"Bf>t<"d-flex justify-content-between"ilp>',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        filename: 'Зарегистрированные продавцы',
                        createEmptyCells: true
                    },
                    {
                        extend: 'csv',
                        filename: 'Зарегистрированные продавцы',
                    }
                ]
            });
        } );
    </script>
@endpush
@push('css')
    <link href="/core/adminLTE/assets/vendors/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />
    <style type="text/css">
        table.dataTable th:before, table.dataTable th:after {
            font-size: 1.5em !important;
            bottom: 0.5rem !important;
        }
    </style>
@endpush
