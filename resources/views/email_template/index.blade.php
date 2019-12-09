@extends('layouts.master')

@section('title')
    {{ $title }}
@endsection

@section('content')

    <div class="m-portlet">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        {{ $title }}
                    </h3>
                </div>
            </div>

            @if(Auth::guard('admins')->user()->develop)
                <div class="m-portlet__head-tools">
                    <ul class="m-portlet__nav">
                        <li class="m-portlet__nav-item">
                            <a href="#" data-url="{{ route('admin.email_templates.create') }}"
                               data-type="modal" data-modal="largeModal"
                               class="m-portlet__nav-link m-portlet__nav-link--icon handle-click"
                               data-container="body"
                               data-toggle="m-tooltip" data-placement="top" title="Создать шаблон">
                                <i class="la la-check-square"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            @endif

        </div>

        <table class="table table-bordered m-table">

            <thead>
            <tr>
                <th class="text-center" width="50">#</th>
                <th>Название</th>
                <th class="text-center" width="100"><i class="fa fa-bars" aria-hidden="true"></i></th>
            </tr>
            </thead>
            <tbody>
            @foreach($templates as $template)
                <tr>
                    <td class="text-center">{{$template->id}}</td>
                    <td>{{$template->display_name}}</td>
                    <td class="text-center">
                        <a href="#" data-url="{{route('admin.email_templates.edit', ['id' => $template->id])}}"
                           class="handle-click" data-type="modal" data-modal="largeModal">
                            <i class="la la-edit"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection

@push('modules')
    <script>


        $(document.body).on('click', '.variable', function (e) {
            e.preventDefault();
            var variable = $(this).data('variable');
            var target = $(".tab-content").find('.tab-pane.active textarea');
            target.insertAtCaret(' ' + variable + ' ');
            $(".tab-content").find('.tab-pane.active textarea').focus();

        });

        jQuery.fn.extend({
            insertAtCaret: function(myValue){
                return this.each(function(i) {

                    console.log(document.selection)
                    if (document.selection) {
                        //For browsers like Internet Explorer
                        this.focus();
                        var sel = document.selection.createRange();
                        sel.text = myValue;
                        this.focus();
                    }
                    else if (this.selectionStart || this.selectionStart == '0') {
                        //For browsers like Firefox and Webkit based
                        var startPos = this.selectionStart;
                        var endPos = this.selectionEnd;
                        var scrollTop = this.scrollTop;
                        this.value = this.value.substring(0, startPos)+myValue+this.value.substring(endPos,this.value.length);
                        this.focus();
                        this.selectionStart = startPos + myValue.length;
                        this.selectionEnd = startPos + myValue.length;
                        this.scrollTop = scrollTop;
                    } else {
                        this.value += myValue;
                        this.focus();
                    }
                });
            }
        });

    </script>
@endpush



