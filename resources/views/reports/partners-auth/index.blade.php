@extends('layouts.master')
@section('content')
    <div class="m-portlet">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <span class="m-portlet__head-icon"><i class="la la-sign-in"></i></span>
                    <h3 class="m-portlet__head-text">Авторизация продавцов</h3>
                </div>
            </div>
        </div>
        <div class="m-portlet__body">
            <table class="table table-bordered" id="partnersTable">
                <thead>
                <tr class="nowrap">
                    <th>ФИО</th>
                    <th>Телефон</th>
                    <th>Торговая точка</th>
                    <th>Торговый агент</th>
                    <th>Последний вход</th>
                    <th>Последнее действие</th>
                    <th>OS</th>
                    <th>IP</th>
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
                ajax: '/reports/partner-auth/get-list',
                columns: [
                    { data: 'name' },
                    { data: 'mobile_phone' },
                    { data: 'account_code' },
                    { data: 'trade_agent' },
                    { data: 'login' },
                    { data: 'last_seen' },
                    { data: 'os' },
                    { data: 'ip' }
                ],
                pageLength: 25,
                orderCellsTop: true,
                //dom: '<"pull-left"B><"pull-right"f><"clearfix">rt<"pull-left"i><"pull-right"p><"pull-right"l><"clearfix">',
                dom: '<"d-flex justify-content-between"Bf>t<"d-flex justify-content-between"ilp>',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        filename: 'Авторизация продавцов',
                        createEmptyCells: true
                    },
                    {
                        extend: 'csv',
                        filename: 'Авторизация продавцов'
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
