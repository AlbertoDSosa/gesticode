@props(['status'])

@if ($status)
    {{-- font-medium text-sm text-green-600 (old)--}}
    <div {{ $attributes->merge(['class' => 'mt-2 text-center font-medium text-sm text-green-600 dark:text-green-400']) }}>
        {{ $status }}
    </div>
@endif
