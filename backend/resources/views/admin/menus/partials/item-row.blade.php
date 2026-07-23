{{--
  item-row.blade.php — Recursive menu item node.

  Variables:
    $item   — MenuItem (with ->_children Collection from MenuBuilderService)
    $urlMap — array<int, string>  item_id → resolved URL
    $depth  — 0 = root, 1 = child, 2 = grandchild (max)
--}}
@php
    $hasKids     = $item->_children->isNotEmpty();
    $resolvedUrl = $urlMap[$item->id] ?? $item->resolveUrl();
    $isActive    = $item->status === 1;
    $isRef       = $item->type === 'menu_reference';
    $refName     = $item->_ref_menu_name ?? null;
    $depthClass  = ['depth-0','depth-1','depth-2'][$depth] ?? 'depth-2';
@endphp

<div class="item-wrap {{ $depthClass }} {{ ! $isActive ? 'item-hidden' : '' }}"
     data-id="{{ $item->id }}"
     data-parent="{{ $item->parent_id ?? '' }}"
     data-depth="{{ $depth }}"
     data-sortable-item>

    {{-- ── Row ─────────────────────────────────────────────── --}}
    <div class="item-row {{ $isRef ? 'item-row--ref' : '' }}">

        {{-- Drag Handle --}}
        <div class="item-handle" title="Drag to reorder">
            <i class="fas fa-grip-vertical"></i>
        </div>

        {{-- Expand/Collapse toggle --}}
        <button type="button"
                class="item-toggle {{ $hasKids ? '' : 'invisible' }}"
                data-id="{{ $item->id }}"
                title="Expand / Collapse">
            <i class="fas fa-chevron-right"></i>
        </button>

        {{-- Type icon --}}
        <div class="item-type-dot" title="{{ $item->typeLabel() }}">
            <i class="fas {{ $item->typeIcon() }}"></i>
        </div>

        {{-- Title + meta --}}
        <div class="item-meta">
            <div class="item-title {{ ! $isActive ? 'text-decoration-line-through text-muted' : '' }}">
                {{ $item->title }}
                @if($isRef && $refName)
                <span class="item-ref-badge">
                    <i class="fas fa-layer-group" style="font-size:9px;"></i>
                    {{ $refName }}
                </span>
                @endif
                @if(isset($item->_ref_cycle) && $item->_ref_cycle)
                <span class="badge bg-danger ms-1" style="font-size:10px;" title="Circular reference detected">
                    ⚠ Cycle
                </span>
                @endif
            </div>
            <div class="item-sub">
                <span class="badge {{ $item->typeBadgeClass() }} {{ $item->type === 'menu_reference' ? 'bg-purple' : '' }}"
                      style="font-size:10px;">
                    {{ $item->typeLabel() }}
                </span>
                @if(! $isActive)
                <span class="badge bg-secondary ms-1 item-hidden-badge" style="font-size:10px;">Hidden</span>
                @endif
                @if(! $isRef && $resolvedUrl && $resolvedUrl !== '#')
                <span class="item-url ms-1">
                    <i class="fas fa-external-link-alt" style="font-size:9px;"></i>
                    {{ Str::limit($resolvedUrl, 60) }}
                </span>
                @endif
            </div>
        </div>

        {{-- Actions --}}
        <div class="item-actions">
            {{-- Add Child (only depth 0 and 1) --}}
            @if($depth < 2)
            <button type="button" class="ia-btn ia-add"
                    data-parent-id="{{ $item->id }}"
                    title="Add child item">
                <i class="fas fa-plus"></i>
            </button>
            @endif

            {{-- Toggle Status --}}
            <button type="button"
                    class="ia-btn ia-toggle {{ $isActive ? 'ia-active' : 'ia-inactive' }}"
                    data-id="{{ $item->id }}"
                    title="{{ $isActive ? 'Hide item' : 'Show item' }}">
                <i class="fas fa-{{ $isActive ? 'eye' : 'eye-slash' }}"></i>
            </button>

            {{-- Edit --}}
            <button type="button" class="ia-btn ia-edit"
                    data-id="{{ $item->id }}"
                    data-depth="{{ $depth }}"
                    title="Edit item">
                <i class="fas fa-pencil-alt"></i>
            </button>

            {{-- Delete --}}
            <button type="button" class="ia-btn ia-delete"
                    data-id="{{ $item->id }}"
                    data-title="{{ $item->title }}"
                    data-children="{{ $item->_children->count() }}"
                    title="Delete item">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>

    {{-- ── Children ────────────────────────────────────────── --}}
    @if($depth < 2)
    <div class="item-children {{ $hasKids ? 'open' : '' }}"
         data-parent-id="{{ $item->id }}"
         data-child-list>
        @foreach($item->_children as $child)
            @include('admin.menus.partials.item-row', [
                'item'   => $child,
                'urlMap' => $urlMap,
                'depth'  => $depth + 1,
            ])
        @endforeach
    </div>
    @endif

</div>
