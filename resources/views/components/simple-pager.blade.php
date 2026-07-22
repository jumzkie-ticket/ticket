@props(['paginator'])

@if ($paginator->hasPages())
    <nav aria-label="Pagination" style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:14px;color:var(--muted);font-size:11px;font-weight:750">
        <span>Showing {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} of {{ $paginator->total() }}</span>
        <span style="display:flex;gap:8px">
            <a href="{{ $paginator->previousPageUrl() ?? '#' }}" @if ($paginator->onFirstPage()) aria-disabled="true" @endif style="min-height:34px;display:inline-flex;align-items:center;padding:0 12px;border:1px solid var(--line);border-radius:6px;background:var(--panel);color:var(--ink);font-weight:850;text-decoration:none;{{ $paginator->onFirstPage() ? 'opacity:.45;pointer-events:none' : '' }}">Previous</a>
            <a href="{{ $paginator->nextPageUrl() ?? '#' }}" @if (! $paginator->hasMorePages()) aria-disabled="true" @endif style="min-height:34px;display:inline-flex;align-items:center;padding:0 12px;border:1px solid var(--line);border-radius:6px;background:var(--panel);color:var(--ink);font-weight:850;text-decoration:none;{{ ! $paginator->hasMorePages() ? 'opacity:.45;pointer-events:none' : '' }}">Next</a>
        </span>
    </nav>
@endif
