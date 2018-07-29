<term-selector root="{{ $root }}" term="{{ $term->id }}"></term-selector>

@push('js')
<script>
var terms = {!! json_encode(KRLX\Term::orderByDesc('on_air')->get()) !!};
</script>
@endpush
