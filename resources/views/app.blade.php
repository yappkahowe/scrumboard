@extends('layouts.default')

@section('app')
    @include('partials.navbar')

    <div class="container">
        <router-view :user="user"></router-view>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ css('vendors') }}">
@endpush

@push('scripts')
    <script>
        window.Laravel = { csrfToken: '{{ csrf_token() }}' }

        window.pusher = {
            key: '{{ config("broadcasting.connections.pusher.key") }}',
            cluster: '{{ config("broadcasting.connections.pusher.options.cluster") }}'
        }

        localStorage.setItem('api_token', '{{ auth()->user()->api_token }}')
    </script>
    <script src="{{ js('app') }}"></script>
    <script src="{{ js('vendors') }}"></script>
@endpush