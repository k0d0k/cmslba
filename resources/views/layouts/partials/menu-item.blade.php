@foreach($items as $item)
    @php
        $children = $item->children_tree ?? collect();
        $url = $item->resolveUrl();
        $isActive = $item->route_name && request()->route()?->getName() === $item->route_name;
        $hasChildren = $children->count() > 0;
        $icon = $item->icon ?: 'circle';
    @endphp

    @if($item->type === 'header')
        <div class="sidebar-group-label">{{ $item->label() }}</div>
    @elseif($item->type === 'group')
        <div class="mt-2 mb-1">
            <div class="sidebar-group-label">
                <i data-lucide="{{ $icon }}"></i>
                <span>{{ $item->label() }}</span>
            </div>
            @if($hasChildren)
                @include('layouts.partials.menu-item', ['items' => $children, 'level' => $level + 1])
            @endif
        </div>
    @else
        @if($url)
            <a href="{{ $url }}" target="{{ $item->target ?: '_self' }}"
               class="sidebar-link {{ $isActive ? 'active' : '' }}">
                <i data-lucide="{{ $icon }}"></i>
                <span>{{ $item->label() }}</span>
            </a>
        @else
            <span class="sidebar-link opacity-60">
                <i data-lucide="{{ $icon }}"></i>
                <span>{{ $item->label() }}</span>
            </span>
        @endif

        @if($hasChildren)
            <div class="sidebar-sub">
                @include('layouts.partials.menu-item', ['items' => $children, 'level' => $level + 1])
            </div>
        @endif
    @endif
@endforeach
