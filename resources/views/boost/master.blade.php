@extends('layouts.missioncontrol', ['title' => 'All Priority Upgrade Certificates'])

@section('head')
    <div class="row">
        <div class="col">
            <h1>All Priority Upgrade Certificates</h1>
            <table class="table">
                <thead>
                    <tr>
                        <th>Certificate Type</th>
                        <th>Issued To</th>
                        <th>Attached Show</th>
                        <th>Expiration</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($boosts as $boost)
                        <tr>
                            <td>{{ config('defaults.boosts.'.$boost->type) }}</td>
                            <td>{{ $boost->user ? $boost->user->full_name : 'No user' }}</td>
                            <td>{{ $boost->show ? $boost->show->title : 'No show' }}</td>
                            <td>{{ $boost->term ? $boost->term->name : 'No expiry date' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <p>For more information on the technical details of Priority Upgrade Certificates, see <a href="https://github.com/krlxfm/website/wiki/Priority+Upgrade+Certificates">this wiki article</a>.</p>
        </div>
    </div>
@endsection
