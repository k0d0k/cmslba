@php
    $isRoot = ($level ?? 0) === 0;
@endphp
<ul class="{{ $isRoot ? 'space-y-1' : 'submenu ml-6 mt-1 space-y-1' }}"
    @if($isRoot) id="menu-tree" data-reorder-url="{{ route('admin.menus.reorder') }}" @endif>
    @foreach($items as $item)
        @php $children = $item->children_tree ?? collect(); @endphp
        <li data-id="{{ $item->id }}" class="card p-2">
            <div class="flex items-center gap-2">
                <span class="drag-handle cursor-move text-slate-400 select-none" title="{{ __('menus.drag_hint') }}">&#8942;&#8942;</span>

                <span class="badge badge-secondary">{{ $item->type }}</span>
                <span class="font-medium text-slate-700 text-sm">{{ $item->label() }}</span>
                <code class="text-[11px] text-slate-400">{{ $item->key }}</code>

                @if($item->route_name)
                    <span class="text-[11px] text-slate-400">{{ $item->route_name }}</span>
                @endif

                <div class="ml-auto flex items-center gap-1">
                    <a href="{{ route('admin.menus.index', ['edit' => $item->key]) }}" class="btn btn-secondary btn-sm"><i data-lucide="pencil"></i> {{ __('common.edit') }}</a>
                    <button type="button" class="btn btn-danger btn-sm js-delete" data-url="{{ route('admin.menus.destroy', $item->key) }}"><i data-lucide="trash-2"></i> {{ __('common.delete') }}</button>
                </div>
            </div>

            @if($children->count())
                @include('menu::menus.tree', ['items' => $children, 'level' => $level + 1])
            @endif
        </li>
    @endforeach
</ul>
