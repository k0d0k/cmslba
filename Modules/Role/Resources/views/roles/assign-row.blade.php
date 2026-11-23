@foreach($items as $item)
    @php
        $assignedAbilities = $assigned[$item->id] ?? [];
        $pad = ($level ?? 0) * 1.5;
    @endphp
    <tr class="border-b border-slate-100">
        <td class="px-4 py-2" style="padding-left: {{ $pad + 1 }}rem">
            <span class="font-medium text-slate-700 text-sm">{{ $item->label() }}</span>
            @if($item->type !== 'link')
                <span class="badge badge-secondary ml-1">{{ $item->type }}</span>
            @endif
            <code class="text-[11px] text-slate-400 ml-1">{{ $item->key }}</code>
        </td>
        @foreach($abilities as $ability)
            <td class="px-4 py-2 text-center">
                <input type="checkbox" class="form-check" name="menus[{{ $item->id }}][]" value="{{ $ability }}"
                       @checked(in_array($ability, $assignedAbilities))>
            </td>
        @endforeach
    </tr>
    @if(($item->children_tree ?? collect())->count())
        @include('role::roles.assign-row', ['items' => $item->children_tree, 'level' => $level + 1])
    @endif
@endforeach
