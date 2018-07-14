@extends('layouts.showapp', ['title' => 'Hosts', 'next' => 'content'])

@section('head')
    @parent
    <div class="row">
        <div class="col">
            <div class="card mb-3">
                <div class="card-body">
                    <p class="card-text">If you will be doing this show with others, you can invite your co-host(s) here. Each co-host must sign in to the website and accept the invitation in order to be listed on the show, receive experience points, count towards show priority, or be eligible to cover other shows.</p>
                </div>
            </div>
            <p>
                <button class="btn btn-block btn-lg btn-dark">
                    <i class="fas fa-user-plus"></i> Invite a host
                </button>
            </p>
            <table class="table table-responsive-sm">
                <thead>
                    <tr>
                        <th>Host</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($show->hosts->merge($show->invitees) as $dj)
                        <tr>
                            <td class="align-middle">
                                {{ $dj->name }}
                                <br>
                                <small class="text-muted">{{ $dj->email }}</small>
                            </td>
                            <td class="align-middle">
                                {{ $dj->membership->accepted ? ($dj->membership->boost ? 'Joined with Priority Boost' : 'Joined') : 'Invited' }}
                            </td>
                            <td class="align-middle"><button class="btn btn-danger" data-action="remove-dj" data-id="{{ $dj->id }}" data-name="{{ $dj->name }}"><i class="fas fa-user-minus"></i> Remove</button></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('js')
<script>
var showID = "{{ $show->id }}";
</script>
<script src="/js/pages/shows/hosts.js" defer></script>
@endpush
