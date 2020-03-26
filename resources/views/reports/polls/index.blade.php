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
            <form method="post" action="{{ route('admin.reports.polls.list') }}" class="ajax" id="filter-form">
                <div class="form-group form-group-sm form-inline">
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-users"></i></span>
                        </div>
                        <label>
                            <input class="form-control form-control-sm" type="text" name="name" placeholder="Ф.И.О">
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
                            <span class="input-group-text"><i class="fa fa-question"></i></span>
                        </div>
                        <label>
                            <select class="selectpicker" data-live-search="true" name="poll_id"  data-style="btn-light btn-sm" data-width="200">
                                <option value="0">Все опросы</option>
                                @foreach($polls as $poll)
                                    <option value="{{ $poll->id }}" @if($poll->trashed()) data-subtext="удален" @endif>{{ $poll->title }}</option>
                                @endforeach
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
                    <div class="input-group input-group-sm">
                        <button type="button" id="btn-filter" class="btn btn-sm btn-outline-success ml-1">Применить фильтр</button>
                        <button type="button" id="btn-export" class="btn btn-sm btn-outline-info ml-1">Экспорт</button>
                    </div>
                </div>
                <input type="hidden" name="export" value="0" id="inp-exp">
                {{ csrf_field() }}
            </form>
            <table class="table table-bordered ajax-content" data-url="{{ route('admin.reports.polls.list') }}" id="pollsTable">
                <thead>
                <tr class="nowrap">
                    <th width="100" class="text-center">ID</th>
                    <th>Ф.И.О.</th>
                    <th>Телефон</th>
                    <th>Опрос</th>
                    <th>Дата</th>
                    <th>Бонус</th>
                    <th width="50" class="text-center"><i class="la la-eye"></i></th>
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
@endsection
@push('modules')
    <script>
        var drp = $('#drp');
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
        var pickerData = drp.data('daterangepicker');
        pickerData.setStartDate($('#from_date').data('formatted'));
        pickerData.setEndDate($('#to_date').data('formatted'));

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
