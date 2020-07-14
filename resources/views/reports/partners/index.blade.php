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
                <tr class="nowrap">
                    <th colspan="5"></th>
                    <th>
                        <select class="form-control form-control-sm" id="statusFilter">
                            <option value="">Все</option>
                            <option value="Verified">Verified</option>
                            <option value="Not verified">Not verified</option>
                            <option value="Deleted">Deleted</option>
                        </select>
                    </th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
@stop
@push('modules')
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.20/af-2.3.4/b-1.6.1/b-colvis-1.6.1/b-flash-1.6.1/b-html5-1.6.1/b-print-1.6.1/cr-1.5.2/fc-3.3.0/fh-3.1.6/kt-2.5.1/r-2.2.3/rg-1.1.1/rr-1.2.6/sc-2.0.1/sp-1.0.1/sl-1.3.1/datatables.min.js"></script>
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
                ],
                initComplete: function () {
                    $('#statusFilter').on('change', e => {
                        const val = $(e.currentTarget).val();
                        $('#partnersTable').dataTable().api().column(5).search(val ? `^${val}$` : '', true, false).draw();
                    });
                }
            });
        } );
    </script>
@endpush
@push('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.20/af-2.3.4/b-1.6.1/b-colvis-1.6.1/b-flash-1.6.1/b-html5-1.6.1/b-print-1.6.1/cr-1.5.2/fc-3.3.0/fh-3.1.6/kt-2.5.1/r-2.2.3/rg-1.1.1/rr-1.2.6/sc-2.0.1/sp-1.0.1/sl-1.3.1/datatables.min.css"/>
    <style type="text/css">
        table.dataTable th:before, table.dataTable th:after {
            font-size: 1.5em !important;
            bottom: 0.5rem !important;
        }
    </style>
@endpush
