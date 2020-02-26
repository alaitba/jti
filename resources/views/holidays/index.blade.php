@extends('layouts.master')
@section('content')
    <div class="m-portlet">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <span class="m-portlet__head-icon"><i class="la la-calendar"></i></span>
                    <h3 class="m-portlet__head-text">Выходные и праздники</h3>
                </div>
            </div>
        </div>
        <div class="m-portlet__body">

            <div class="calendar"></div>
            <div class="text-center">
                <button id="save" type="button" class="btn btn-outline-brand">Сохранить</button>
            </div>
        </div>
    </div>
@stop
@push('modules')
    <script src="/core/js/js-year-calendar.min.js"></script>
    <script src="/core/js/js-year-calendar.ru.js"></script>
    <script>
        $(document).ready(() => {

            const curYear = new Date().getFullYear();
            let calend = new Calendar('.calendar', {
                language: 'ru',
                minDate: new Date(curYear, 0, 1),
                maxDate: new Date(curYear + 1, 11, 31),
                dataSource: [{!! $holidays !!}],
                clickDay: e => {
                    let ds = calend.getDataSource();
                    if (calend.isThereFreeSlot(e.date)) {
                        ds.push({
                            color: '#ff0000',
                            startDate: e.date,
                            endDate: e.date
                        });
                    } else {
                        ds = ds.filter((day, idx, ds) => {
                            return day.startDate.getTime() !== e.date.getTime();
                        });
                    }
                    calend.setDataSource(ds);
                }
            });
            $('#save').click(() => {
                let dates = [];
                $.each(calend.getDataSource(), (i, date) => {
                    dates.push(date.startDate.toDateString());
                });
                let data = new FormData();
                data.append('dates', dates);
                $.ajax({
                    method: 'post',
                    url: '{{ route('admin.holidays.update') }}',
                    data: data,
                    dataType: 'json',
                    async: true,
                    cache: false,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },


                    beforeSend: () => {
                        app.functions.blockPage();
                    },

                    success: (response) => {
                        if (response.functions) {
                            $.each(response.functions, (funcName, data) => {
                                app.functions[funcName](data.params)
                            });
                        }
                    },

                    complete:  response => {
                        app.functions.unblockPage();
                    }
                });
            });
        });
    </script>
@endpush
@push('css')
    <style>
        .calendar {
            height: 500px;
        }

        .calendar-header {
            border: none !important;
        }

        .calendar-header th.prev, .calendar-header th.next {
            visibility: hidden !important;
        }

        .calendar-header th.year-title.disabled {
            display: none;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="/core/css/js-year-calendar.min.css"/>
@endpush
