<div class="modal fade" id="{{ $id }}" dusk="{{ $dusk ?? $id.'-modal' }}" tabindex="-1" role="dialog" aria-labelledby="{{ $id }}-label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="{{ $id }}-label">{{ $title }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" name="{{ $id }}-form" action="{{ $action ?? '#' }}">
                @csrf
                <div class="modal-body">
                    {!! $slot !!}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ $close ?? 'Cancel' }}</button>
                    {!! $footer ?? '' !!}
                </div>
            </form>
        </div>
    </div>
</div>
