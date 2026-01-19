{{-- Partial compartido para scripts de propiedades --}}
<script>
    const csrfToken = "{{ csrf_token() }}";
</script>
<script src="{{ asset('js/admin/properties-table.js') }}?v={{ time() }}"></script>
<link rel="stylesheet" href="{{ asset('css/admin/properties-table.css') }}?v={{ time() }}">
