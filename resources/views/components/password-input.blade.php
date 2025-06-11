@props(['id', 'name', 'value' => '', 'placeholder' => '', 'required' => false])

<div x-data="passwordToggle">
    <div class="relative">
        <input
            x-ref="passwordField"
            type="password"
            id="{{ $id }}"
            name="{{ $name }}"
            value="{{ $value }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->merge(['class' => 'form-control passwordfield']) }}
        >
        <button
            type="button"
            @click="toggleVisibility"
            class="absolute right-0 top-1/2 -translate-y-1/2 w-9 h-full flex items-center justify-center text-xl"
        >
            <span x-show="!showPassword" id="hidePassword">
                <iconify-icon icon="heroicons-outline:eye"></iconify-icon>
            </span>
            <span x-show="showPassword" id="showPassword" style="display: none;">
                <iconify-icon icon="heroicons-outline:eye-off"></iconify-icon>
            </span>
        </button>
    </div>
</div>
