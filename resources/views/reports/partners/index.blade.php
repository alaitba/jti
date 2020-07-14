@extends('layouts.master')
@section('content')
<div class="m-portlet">
    @if($notifications !== null)
        @if(session()->has('message'))
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <div class="m-portlet__head-title" >
                        <span class="m-portlet__head-icon"><i class="la la-info-circle"></i></span>
                        <h3 class="m-portlet__head-text" style="color: #34bfa3;">{{ session()->get('message') }}</h3>
                    </div>
                </div>
            </div>
        @else
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <div class="m-portlet__head-title" >
                        <span class="m-portlet__head-icon"><i class="la la-question-circle"></i></span>
                        <h3 class="m-portlet__head-text">Экспорт</h3>
                    </div>
                </div>
            </div>
        @endif
        <div class="m-portlet__body">
            <table class="table table-bordered ajax-content">
                <thead>
                <tr class="nowrap">
                    <th>Сообщение</th>
                    <th>Имя файла</th>
                    <th width="50" class="text-center"><i class="la la-download"></i></th>
                    <th width="50" class="text-center"><i class="la la-trash"></i></th>
                </tr>
                </thead>
                <tbody>
                @forelse($notifications as $notification)
                    @if($notification->type === 'App\Notifications\PartnersReportNotification' )
                        <tr>
                            <td>{{ $notification->data['message']}}</td>
                            <td>{{ $notification->data['path']}}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.reports.partners.download', ['path' => $notification->data['path']]) }}"><i class="la la-download"></i></a>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.reports.partners.delete', ['id' => $notification]) }}"><i class="la la-trash"></i></a>
                            </td>
                        </tr>
                    @endif
                @empty
                    <td>Экспорт Продавцов еще не проведен.</td>
                @endforelse
                </tbody>
            </table>
        </div>
    @endif


    <div class="m-portlet__head">
        <div class="m-portlet__head-caption">
            <div class="m-portlet__head-title">
                <span class="m-portlet__head-icon"><i class="la la-users"></i></span>
                <h3 class="m-portlet__head-text">Зарегистрированные продавцы</h3>
            </div>
        </div>
    </div>

    <div class="m-portlet__body">
        <form method="post" action="{{ route('admin.reports.partners.list') }}" class="ajax" id="filter-form">
            <div class="form-group form-group-sm form-inline">
                <div class="input-group input-group-sm ml-1">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-mobile-alt"></i></span>
                    </div>
                    <label>
                        <input class="form-control form-control-sm" type="text" name="tradepoint" placeholder="Код ТТ">
                    </label>
                </div>
                <div class="input-group input-group-sm ml-1">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-mobile-alt"></i></span>
                    </div>
                    <label>
                        <input class="form-control form-control-sm" type="tel" name="mobile_phone" placeholder="Телефон">
                    </label>
                </div>
                <div class="input-group input-group-sm ml-1">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-check"></i></span>
                    </div>
                    <label>
                        <select class="selectpicker" name="os" data-style="btn-light btn-sm" data-width="100">
                            <option value="0">All</option>
                            <option value="1">IOS</option>
                            <option value="2">Android</option>
                        </select>
                    </label>
                </div>
                <div class="input-group input-group-sm ml-1" id="drp">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-calendar-alt"></i></span>
                    </div>
                    <label>
                        <input class="form-control form-control-sm" id="period" type="text" readonly
                               value="{{ $from_date->format('d.m.Y') }} - {{ $to_date->format('d.m.Y') }}">
                    </label>
                    <input type="hidden" name="from_date" id="from_date" data-formatted="{{ $from_date->format('d.m.Y') }}"
                           value="{{ $from_date }}">
                    <input type="hidden" name="to_date" id="to_date" data-formatted="{{ $to_date->format('d.m.Y') }}" value="{{ $to_date }}">
                </div>

                <div class="input-group input-group-sm ml-1" id="last_drp">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-calendar-alt"></i></span>
                    </div>
                    <label>
                        <input class="form-control form-control-sm" id="last_period" type="text" readonly
                               value="{{ $from_date->format('d.m.Y') }} - {{ $to_date->format('d.m.Y') }}">
                    </label>
                    <input type="hidden" name="last_from_date" id="last_from_date" data-formatted="{{ $from_date->format('d.m.Y') }}"
                           value="{{ $from_date }}">
                    <input type="hidden" name="last_to_date" id="last_to_date" data-formatted="{{ $to_date->format('d.m.Y') }}" value="{{ $to_date }}">
                </div>
                <div class="input-group input-group-sm ml-1">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-check"></i></span>
                    </div>
                    <label>
                        <select class="selectpicker" name="status" data-style="btn-light btn-sm" data-width="100">
                            <option value="0">All</option>
                            <option value="1">Deleted</option>
                            <option value="2">Verified</option>
                            <option value="3">Not Verified</option>
                        </select>
                    </label>
                </div>
                <div class="input-group input-group-sm">
                    <button type="button" id="btn-filter" class="btn btn-sm btn-outline-success ml-1">Применить фильтр</button>
                    <button type="button" id="btn-export" class="btn btn-sm btn-outline-info ml-1">Экспорт</button>
                </div>
            </div>
            <input type="hidden" name="export" value="0" id="inp-exp">
            {{ csrf_field() }}
        </form>
        <table class="table table-bordered ajax-content" data-url="{{ route('admin.reports.partners.list') }}" id="salesPlanTable">
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

        <div class="pagination_placeholder"></div>

    </div>

</div>
<style>
    .btn-sm {
        padding: .45rem .8rem !important;
    }
    .daterangepicker_input {
        display: none;
    }
</style>
@stop
@push('modules')
    <script>
        var drp = $('#drp');
        var lastdrp = $('#last_drp');

        drp.daterangepicker({
            "locale": {
                "format": "DD.MM.YYYY",
                "separator": " - ",
                "applyLabel": "Применить",
                "cancelLabel": "Отмена",
                "fromLabel": "С",
                "toLabel": "по",
                "daysOfWeek": ["Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб"],
                "monthNames": ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"],
                "firstDay": 1
            },
            "opens": "left",
            "drops": "down",
            "autoApply": true,
        }).on('apply.daterangepicker', (e, picker) => {
            const start = picker.startDate;
            const end = picker.endDate;
            $('#from_date').val(start.format('YYYY-MM-DD'));
            $('#to_date').val(end.format('YYYY-MM-DD'));
            $('#period').val(`${start.format('DD.MM.YYYY')} - ${end.format('DD.MM.YYYY')}`);
        });

        lastdrp.daterangepicker({
            "locale": {
                "format": "DD.MM.YYYY",
                "separator": " - ",
                "applyLabel": "Применить",
                "cancelLabel": "Отмена",
                "fromLabel": "С",
                "toLabel": "по",
                "daysOfWeek": ["Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб"],
                "monthNames": ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"],
                "firstDay": 1
            },
            "opens": "left",
            "drops": "down",
            "autoApply": true,
        }).on('apply.daterangepicker', (e, picker) => {
            const laststart = picker.startDate;
            const lastend = picker.endDate;
            $('#last_from_date').val(laststart.format('YYYY-MM-DD'));
            $('#last_to_date').val(lastend.format('YYYY-MM-DD'));
            $('#last_period').val(`${laststart.format('DD.MM.YYYY')} - ${lastend.format('DD.MM.YYYY')}`);
        });

        var pickerData = drp.data('daterangepicker');
        var pickerDatalast = lastdrp.data('daterangepicker');

        pickerData.setStartDate($('#from_date').data('formatted'));
        pickerDatalast.setStartDate($('#last_from_date').data('formatted'));
        pickerData.setEndDate($('#to_date').data('formatted'));
        pickerDatalast.setEndDate($('#last_to_date').data('formatted'));

        $('#btn-filter').on('click', e => {
            const form = $('#filter-form');
            $('#inp-exp').val(0);
            if (!form.hasClass('ajax'))
            {
                form.addClass('ajax');
            }
            form.trigger('submit');
        });

        $('#btn-export').on('click', e => {
            $('#inp-exp').val(1);
            $('#filter-form').removeClass('ajax').trigger('submit');
        });

    </script>
@endpush
