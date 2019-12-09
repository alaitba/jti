@extends('backend.layouts.master')

@section('title')
    {{ $title }}
@endsection

@section('content')

    <div class="m-portlet m-portlet--tab">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
						<span class="m-portlet__head-icon m--hide">
						<i class="la la-gear"></i>
						</span>
                    <h3 class="m-portlet__head-text">
                        {{ $title }}
                    </h3>
                </div>
            </div>
            <div class="m-portlet__head-tools">
                <ul class="m-portlet__nav">
                    <li class="m-portlet__nav-item">
                        <a href="{{ route('admin.users.admins') }}" class="m-portlet__nav-link m-portlet__nav-link--icon">
                            <div class="m-demo-icon__preview">
                                <i class="la la-fast-backward"></i>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <form action="{{ $formAction }}" method="post" class="ajax-submit m-form m-form--fit m-form--label-align-right"
              data-block-element=".box-body" id="adminUsersForm">
            <div class="m-portlet__body">
                <div class="form-group m-form__group">
                    <label for="name">Имя администратора</label>
                    <input type="text" class="form-control m-input m-input--square" id="name" name="name"
                           value="{{ $userAuth->name }}">
                    <p class="help-block"></p>
                </div>
                <div class="form-group m-form__group">
                    <label for="email">Электронный адрес админстратора</label>
                    <input type="text" class="form-control m-input m-input--square" id="email" name="email"
                           value="{{ $userAuth->email }}">
                    <p class="help-block"></p>
                </div>
                <div class="form-group m-form__group">
                    <label for="password">Пароль администратора</label>
                    <input type="password" class="form-control m-input m-input--square" id="password" name="password"
                           placeholder="Введите новый пароль или смените пароль">
                    <p class="help-block"></p>
                </div>

                <div class="m-portlet__foot m-portlet__foot--fit">
                    <div class="m-form__actions">
                        <button type="submit" class="btn btn-brand">{{  $buttonText }} </button>
                    </div>
                </div>
            </div>
        </form>
    </div>


@endsection


