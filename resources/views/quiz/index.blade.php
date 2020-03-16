@extends('layouts.master')

@section('content')
    <div class="m-portlet">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <span class="m-portlet__head-icon"><i class="la la-question-circle"></i></span>
                    <h3 class="m-portlet__head-text">
                        Викторины и опросы
                    </h3>
                </div>
            </div>

            <div class="m-portlet__head-tools">
                <ul class="m-portlet__nav">
                    <li class="m-portlet__nav-item">
                        <a href="#" data-url="{{ route('admin.quizzes.create') }}"
                           data-type="modal" data-modal="regularModal"
                           class="m-portlet__nav-link m-portlet__nav-link--icon handle-click"
                           data-container="body"
                           data-toggle="m-tooltip"
                           data-placement="top"
                           title="Добавить викторину или опрос">
                            <i class="la la-plus-circle"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>


        <!--begin::Section-->
        <div class="m-section">
            <div class="m-section__content">
                <table class="table table-bordered ajax-content"
                       data-url="{{ route('admin.quizzes.list') }}">
                    <thead>
                    <tr>
                        <th class="text-center" width="50">#</th>
                        <th>Тип</th>
                        <th>Заголовок</th>
                        <th>Период</th>
                        <th>Цена</th>
                        <th>ЦА</th>
                        <th class="text-center" width="100"><i class="fa fa-bars" aria-hidden="true"></i></th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>

                <div class="pagination_placeholder"></div>
            </div>
        </div>
        <!--end::Section-->
    </div>
@stop



