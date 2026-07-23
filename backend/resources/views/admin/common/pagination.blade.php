@if ($paginator->lastPage() > 1)
    @php
        $current = $paginator->currentPage();
        $last = $paginator->lastPage();
        $start = max(1, $current - 5);
        $end = min($last, $current + 5);

        // Many admin list pages render this partial as part of an ajax=1 JSON
        // response (live search/filter refresh). That request's query string —
        // including `ajax=1` — gets baked into $paginator->url() via
        // withQueryString(). Left as-is, clicking a page number does a normal
        // browser navigation to a URL that still carries `ajax=1`, so the
        // controller's `$request->ajax() || $request->boolean('ajax')` (or
        // ->has('ajax')) check returns true and it responds with raw JSON
        // instead of the rendered page — the "code showing" bug. Strip it here
        // so every generated link always lands on the real HTML page.
        $stripAjaxParam = function (?string $url) {
            if (!$url) return $url;
            $parts = parse_url($url);
            if (empty($parts['query'])) return $url;
            parse_str($parts['query'], $q);
            if (!array_key_exists('ajax', $q)) return $url;
            unset($q['ajax']);
            $scheme = isset($parts['scheme']) ? $parts['scheme'] . '://' : '';
            $host   = $parts['host'] ?? '';
            $port   = isset($parts['port']) ? ':' . $parts['port'] : '';
            $qs     = http_build_query($q);
            return $scheme . $host . $port . ($parts['path'] ?? '') . ($qs ? '?' . $qs : '');
        };
    @endphp

    <ul class="pagination justify-content-center">

        {{-- Previous --}}
        <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
            <a class="page-link" href="{{ $stripAjaxParam($paginator->previousPageUrl()) }}">&lt;</a>
        </li>

        {{-- First Page --}}
        @if ($start > 1)
            <li class="page-item">
                <a class="page-link" href="{{ $stripAjaxParam($paginator->url(1)) }}">1</a>
            </li>
            @if ($start > 2)
                <li class="page-item disabled"><span class="page-link">…</span></li>
            @endif
        @endif

        {{-- Middle Pages --}}
        @for ($i = $start; $i <= $end; $i++)
            <li class="page-item {{ $current == $i ? 'active' : '' }}">
                <a class="page-link" href="{{ $stripAjaxParam($paginator->url($i)) }}">{{ $i }}</a>
            </li>
        @endfor

        {{-- Last Page --}}
        @if ($end < $last)
            @if ($end < $last - 1)
                <li class="page-item disabled"><span class="page-link">…</span></li>
            @endif
            <li class="page-item">
                <a class="page-link" href="{{ $stripAjaxParam($paginator->url($last)) }}">{{ $last }}</a>
            </li>
        @endif

        {{-- Next --}}
        <li class="page-item {{ !$paginator->hasMorePages() ? 'disabled' : '' }}">
            <a class="page-link" href="{{ $stripAjaxParam($paginator->nextPageUrl()) }}">&gt;</a>
        </li>
    </ul>
@endif
