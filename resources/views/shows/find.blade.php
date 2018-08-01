@extends('layouts.missioncontrol', ['title' => 'Join Show'])

@section('head')
    <div class="row my-3">
        <div class="col">
            <div class="d-flex flex-wrap align-items-center">
                <h1>Create New Show</h1>
                <a href="{{ route('shows.my') }}" class="btn btn-outline-secondary ml-auto"><i class="fas fa-chevron-left"></i> Back to My Shows</a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body py-5 px-4 text-center">
                    <p>Enter the ID of the show you would like to join (this is a {{ config('defaults.show_id_length', 6) }}-character string of capital letters and numbers).</p>
                    <form id="id-search-form">
                        <div class="input-group input-group-lg">
                            <input class="form-control text-center" type="text" placeholder="Show ID" id="id-search">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    Go <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback">
                                <span id="invalid-id">INVALID-ID</span> is not a valid Show ID. Please try again.
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script src="/js/pages/shows/find.js"></script>
@endpush
