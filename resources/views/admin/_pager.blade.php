@if ($paginator->hasPages())
    <nav class="row between small">
        <span class="muted">{{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} of {{ $paginator->total() }}</span>
        <span class="row">
            @if ($paginator->onFirstPage())<span class="btn btn-sm" style="opacity:.4">← Prev</span>@else<a class="btn btn-sm" href="{{ $paginator->previousPageUrl() }}">← Prev</a>@endif
            <span class="muted">Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}</span>
            @if ($paginator->hasMorePages())<a class="btn btn-sm" href="{{ $paginator->nextPageUrl() }}">Next →</a>@else<span class="btn btn-sm" style="opacity:.4">Next →</span>@endif
        </span>
    </nav>
@endif
