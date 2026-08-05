@if($tenants->total() > 0)
    <div class="text-sm text-slate-500">
        Mostrando {{ $tenants->firstItem() ?? 0 }} - {{ $tenants->lastItem() ?? 0 }} de {{ $tenants->total() }} cliente{{ $tenants->total() === 1 ? '' : 's' }}{{ $search ? ' para "' . e($search) . '"' : '' }}.
    </div>
@endif
