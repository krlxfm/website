@extends('layouts.missioncontrol', ['title' => 'Priority Upgrade Certificates'])

@section('head')
    <div class="row">
        <div class="col">
            <h1>Priority Upgrade Certificates</h1>
            <div class="card my-3">
                <ul class="list-group list-group-flush">
                    @foreach($boosts as $boost)
                        <li class="list-group-item d-flex align-items-center flex-wrap">
                            <div>
                                <h5 class="head-sans-serif mb-0">
                                    <strong>{{ config('defaults.boosts.'.$boost->type) }}</strong>
                                </h5>
                                @if($boost->show)
                                    Applied to {{ $boost->show->title }}
                                @else
                                    <strong class="text-success">Available to redeem</strong>
                                @endif
                                @unless($boost->term_id)
                                    | No expiration date
                                @endunless
                            </div>
                            <div class="ml-auto btn-group">
                                @if($boost->show)
                                    <a href="{{ route('boost.redeem', $boost) }}" class="btn btn-outline-success">
                                        <i class="fas fa-exchange-alt"></i> Move
                                    </a>
                                @else
                                    <a href="{{ route('boost.redeem', $boost) }}" class="btn btn-success">
                                        <i class="fas fa-certificate"></i> Redeem
                                    </a>
                                @endif
                                @if($boost->transferable)
                                    <a href="#" class="btn btn-outline-success">
                                        <i class="fas fa-gift"></i> Transfer
                                    </a>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            <h3>Upgrade Certificate Rules</h3>
            <p>
                Priority Upgrade Certificates may be used subject to the following restrictions:
                <ul>
                    <li>Priority Upgrade Certificates may be used to request elevated priority on KRLX radio shows on eligible tracks. Tracks where upgrade certificates are permitted are indicated with "Upgrade Eligible" on the <a href="{{ route('shows.create') }}">show creation screen</a>.</li>
                    <li>With the exception of One-Zone Upgrade Certificates, only one certificate of a given type may be applied to a show. Multiple One-Zone Upgrade Certificates may be used on the same show.</li>
                    <li>Certificates are non-transferable unless a "Transfer" button appears next to a certificate above.</li>
                    <li>Certificates expire at the end of the term for which they are issued, unless "No expiration date" appears next to a certificate above. <em>Expired certificates will not be re-issued.</em></li>
                </ul>
            </p>
            <p>For more information on the technical details of Priority Upgrade Certificates, see <a href="https://github.com/krlxfm/website/wiki/Priority+Upgrade+Certificates">this wiki article</a>.</p>
        </div>
    </div>
@endsection
