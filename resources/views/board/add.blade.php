<h2>Add a position</h2>
<p>To learn more about each of the positions and their associated responsibilities and desired skills, please <a href="{{ route('board.positions') }}">click here</a>.</p>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Position</th>
            <th style="width: 280px;">Add</th>
        </tr>
    </thead>
    <tbody>
        @forelse($positions as $position)
            <form method="post" action="{{ route('points.store') }}">
                @csrf
                <tr>
                    <td class="align-middle">
                        {{ $position->title }} <span class="badge badge-{{ $position->dark ? 'dark' : 'light' }}" style="background: {{ $position->color }}">{{ $position->abbr }}</span>
                    </td>
                    <input type="hidden" name="board_app_id" value="{{ $app->id }}">
                    <input type="hidden" name="position_id" value="{{ $position->id }}">
                    <td class="align-middle"><button type="submit" class="btn btn-success btn-block"><i class="fas fa-plus"></i> Add {{ $position->title }}</button></td>
                </tr>
            </form>
        @empty
            <tr>
                <td colspan="2">You are currently not eligible to apply for any additional positions.</td>
            </tr>
        @endforelse
    </tbody>
</table>
