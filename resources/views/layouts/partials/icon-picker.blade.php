@php
    // Partial icon picker (lucide). Parameter:
    //   $name  : nama input (default 'icon')
    //   $value : nilai ikon terpilih (default 'circle')
    $pickerName = $name ?? 'icon';
    $pickerValue = old($pickerName, $value ?? 'circle') ?: 'circle';
@endphp
<div class="icon-picker" data-icon-picker>
    <input type="hidden" name="{{ $pickerName }}" value="{{ $pickerValue }}" data-icon-picker-input>

    <button type="button" class="icon-picker-toggle" data-icon-picker-toggle>
        <span data-icon-picker-preview><i data-lucide="{{ $pickerValue }}"></i></span>
        <span class="icon-picker-label" data-icon-picker-label>{{ $pickerValue }}</span>
        <i data-lucide="chevron-down" class="icon-picker-caret"></i>
    </button>

    <div class="icon-picker-panel hidden" data-icon-picker-panel>
        <input type="text" class="form-control icon-picker-search" data-icon-picker-search
               placeholder="{{ __('common.search_icon') }}"
               data-empty-text="{{ __('common.icon_not_found') }}">
        <div class="icon-picker-grid" data-icon-picker-grid></div>
    </div>
</div>
