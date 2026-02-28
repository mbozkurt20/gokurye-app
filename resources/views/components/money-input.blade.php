@props([
    'name',
    'value' => '',
    'label' => null,
    'required' => false,
    'placeholder' => '0,00'
])

@php
    $displayValue = $value !== '' ? number_format((float)$value, 2, ',', '.') : '';
    $hiddenValue = $value !== '' ? number_format((float)$value, 2, '.', '') : '';
@endphp

<div class="mb-4 money-input-container">
    @if($label)
        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block pl-1">
            {{ $label }}
            @if($required) <span class="text-rose-500">*</span> @endif
        </label>
    @endif

    <div class="relative group">
        <input
            {{ $attributes->merge([
                'type' => 'text',
                'class' => 'form-control !rounded-2xl border-slate-100 bg-slate-50/50 p-4 pr-16 font-black text-slate-700 focus:ring-4 focus:ring-indigo-50/50 focus:border-indigo-200 transition-all shadow-sm w-full',
                'placeholder' => $placeholder,
                'inputmode' => 'decimal',
                'autocomplete' => 'off',
            ]) }}
            name="{{ $name }}_display"
            value="{{ $displayValue }}"
            oninput="formatMoneyForComponent(this)"
            {{ $required ? 'required' : '' }}
        >

        <div class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center text-slate-500 font-black text-sm transition-all group-focus-within:bg-indigo-600 group-focus-within:text-white z-10">
            ₺
        </div>

        <input type="hidden" name="{{ $name }}" value="{{ $hiddenValue }}">
    </div>
</div>

@once
    <script>
        function formatMoneyForComponent(el) {
            let cursorPosition = el.selectionStart;
            let oldLength = el.value.length;

            let value = el.value.replace(/\D/g, '');

            if (value === '') {
                el.value = '';
                const hiddenInput = el.closest('.money-input-container').querySelector('input[type="hidden"]');
                if(hiddenInput) hiddenInput.value = '';
                return;
            }

            let decimalValue = (parseInt(value) / 100).toFixed(2);
            let parts = decimalValue.split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');

            el.value = parts.join(',');

            let newLength = el.value.length;
            cursorPosition = cursorPosition + (newLength - oldLength);
            el.setSelectionRange(cursorPosition, cursorPosition);

            const container = el.closest('.money-input-container');
            if (container) {
                const hiddenInput = container.querySelector('input[type="hidden"]');
                if (hiddenInput) {
                    hiddenInput.value = decimalValue;
                }
            }
        }
    </script>
@endonce
