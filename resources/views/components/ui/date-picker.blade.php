@props([
    'name',
    'label' => null,
    'value' => null,
    'placeholder' => null,
    'helper' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'error' => null,
    'id' => null,
    'size' => 'md',
    'lang' => 'en-GB',
])

@php
    $id ??= str($name)->replace(['[', ']'], ['_', ''])->toString();
    $error ??= $errors->first($name);
    $fieldValue = old($name, $value);
    $nativeId = $id.'_native';
    $displayValue = $fieldValue;

    if (filled($fieldValue) && preg_match('/^\d{4}-\d{2}-\d{2}/', (string) $fieldValue)) {
        $displayValue = \Illuminate\Support\Carbon::parse($fieldValue)->format('d/m/Y');
    }
@endphp

<label for="{{ $id }}" class="block space-y-2">
    @if ($label)
        <span class="font-heading text-sm font-bold text-etc-on-surface">{{ $label }} @if ($required)<span class="text-etc-magenta">*</span>@endif</span>
    @endif
    <x-filament::input.wrapper :valid="blank($error)" :disabled="$disabled" class="etc-field-size-{{ $size }}">
        <div class="relative flex w-full items-center">
            <input
                id="{{ $id }}"
                type="text"
                value="{{ $displayValue }}"
                placeholder="{{ $placeholder ?? 'dd/mm/yyyy' }}"
                inputmode="numeric"
                maxlength="10"
                autocomplete="off"
                data-date-picker-display
                data-date-picker-native="{{ $nativeId }}"
                @required($required)
                @disabled($disabled)
                @readonly($readonly)
                {{ $attributes->class('fi-input block w-full border-none bg-transparent py-1.5 pe-10 text-base text-gray-950 outline-none transition duration-75 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] sm:text-sm sm:leading-6 dark:text-white dark:placeholder:text-gray-500 dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)]') }}
            />
            <input
                id="{{ $nativeId }}"
                name="{{ $name }}"
                type="date"
                value="{{ $fieldValue }}"
                lang="{{ $lang }}"
                tabindex="-1"
                aria-hidden="true"
                data-date-picker-native-input
                @disabled($disabled)
                class="pointer-events-none absolute right-2 top-1/2 h-5 w-5 -translate-y-1/2 opacity-0"
            />
            <button
                type="button"
                class="absolute right-2 top-1/2 flex h-7 w-7 -translate-y-1/2 items-center justify-center rounded-full text-etc-on-muted transition hover:bg-etc-surface-container hover:text-etc-magenta focus:outline-none focus:ring-2 focus:ring-etc-magenta/30 disabled:pointer-events-none disabled:opacity-50"
                aria-label="Pilih tanggal"
                data-date-picker-trigger="{{ $nativeId }}"
                @disabled($disabled || $readonly)
            >
                <span class="material-symbols-outlined text-[20px]" aria-hidden="true">calendar_today</span>
            </button>
        </div>
    </x-filament::input.wrapper>
    @if ($error)<p class="text-xs font-medium text-red-600">{{ $error }}</p>@elseif ($helper)<p class="text-xs text-etc-on-muted">{{ $helper }}</p>@endif
</label>

@once
    @push('scripts')
        <script>
            (() => {
                const formatDateInput = (value) => {
                    const digits = String(value || '').replace(/\D/g, '').slice(0, 8);
                    const parts = [digits.slice(0, 2), digits.slice(2, 4), digits.slice(4, 8)].filter(Boolean);

                    return parts.join('/');
                };

                const toDisplayDate = (value) => {
                    const match = String(value || '').match(/^(\d{4})-(\d{2})-(\d{2})$/);

                    return match ? `${match[3]}/${match[2]}/${match[1]}` : '';
                };

                const toNativeDate = (value) => {
                    const match = String(value || '').trim().match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/);

                    if (!match) {
                        return '';
                    }

                    const [, day, month, year] = match;
                    const parsed = new Date(Number(year), Number(month) - 1, Number(day));
                    const validDate = parsed.getFullYear() === Number(year)
                        && parsed.getMonth() === Number(month) - 1
                        && parsed.getDate() === Number(day);

                    return validDate ? `${year.padStart(4, '0')}-${month.padStart(2, '0')}-${day.padStart(2, '0')}` : '';
                };

                const nativeFor = (display) => document.getElementById(display.dataset.datePickerNative);

                const syncNative = (display) => {
                    const native = nativeFor(display);

                    if (native) {
                        native.value = toNativeDate(display.value);
                    }
                };

                document.addEventListener('input', (event) => {
                    if (! event.target.matches('[data-date-picker-display]')) {
                        return;
                    }

                    event.target.value = formatDateInput(event.target.value);
                    syncNative(event.target);
                });

                document.addEventListener('change', (event) => {
                    if (event.target.matches('[data-date-picker-native-input]')) {
                        const display = document.querySelector(`[data-date-picker-native="${event.target.id}"]`);

                        if (display) {
                            display.value = toDisplayDate(event.target.value);
                            display.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    }

                    if (event.target.matches('[data-date-picker-display]')) {
                        syncNative(event.target);
                    }
                });

                document.addEventListener('click', (event) => {
                    const trigger = event.target.closest('[data-date-picker-trigger]');

                    if (! trigger) {
                        return;
                    }

                    const native = document.getElementById(trigger.dataset.datePickerTrigger);

                    if (native?.showPicker) {
                        native.showPicker();
                    } else {
                        native?.focus();
                        native?.click();
                    }
                });

                document.addEventListener('submit', (event) => {
                    event.target
                        .querySelectorAll('[data-date-picker-display]')
                        .forEach(syncNative);
                });
            })();
        </script>
    @endpush
@endonce
