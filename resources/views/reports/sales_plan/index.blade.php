@extends('layouts.master')
@section('content')
<div class="m-portlet">
    <div class="m-portlet__head">
        <div class="m-portlet__head-caption">
            <div class="m-portlet__head-title">
                <span class="m-portlet__head-icon"><i class="la la-bar-chart"></i></span>
                <h3 class="m-portlet__head-text">План/факт закупа.</h3>
            </div>
        </div>
        <div class="m-portlet__head-tools">
            Последнее обновление: {{ $lastUpdate }}
        </div>
    </div>
    <div class="m-portlet__body">

        <table class="table table-bordered ajax-content" data-url="{{ route('admin.reports.sales_plan.list') }}" id="salesPlanTable">
            <thead>
            <tr class="nowrap">
                <th>Account code</th>
                <th>Account name</th>
                <th>City</th>
                <th>Contact ID</th>
                <th>District</th>
                <th>Bonus Portfolio</th>
                <th>Plan Portfolio</th>
                <th>Fact Portfolio</th>
                <th>DSD till Date</th>
                <th>Brand</th>
                <th>Bonus Brand</th>
                <th>Plan Brand</th>
                <th>Fact Brand</th>
            </tr>
            </thead>
            <tbody>

            </tbody>
        </table>

        <div class="pagination_placeholder"></div>


    </div>
    <iframe width="560" height="315" src="https://www.youtube.com/embed/cIWoepRu9a8" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
</div>
@stop
