@extends('layouts.medical-record')

@section('content')
    @include('medical-records.partials.header')
    @include('medical-records.partials.patient-info')
    @include('medical-records.partials.contact-info')
    @include('medical-records.partials.medical-history')
    @include('medical-records.partials.medical-tests')
    @include('medical-records.partials.footer')
@endsection
