@props(['id', 'name', 'previewId', 'currentSrc'])

<div x-data="logoInput">
    <div class="relative">
        <input
            type="file"
            id="{{ $id }}"
            name="{{ $name }}"
            x-on:change="change('{{ $previewId }}')"
            {{ $attributes->merge(['class' => 'defaultButton absolute top-0 left-0 w-full h-full opacity-0 cursor-pointer']) }}
        >
        <label class="btn btn-dark !static defaultButton inline-block">
            {{ __('Choose') }}
        </label>
    </div>
</div>
