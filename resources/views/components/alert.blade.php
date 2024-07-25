@props(['message','type'])

<div x-show="open" x-data="message" class="alert alert-success light-mode" {{ $attributes->merge(['class' => 'mb-3 alert ' . $type]) }}>
    <div class="flex items-center space-x-3 rtl:space-x-reverse">
        <iconify-icon class="text-2xl flex-0" icon="system-uicons:target"></iconify-icon>
        <p class="flex-1 font-Inter">{{ $message }}</p>
        <div class="flex-0 text-xl cursor-pointer">
            <button x-on:click="trigger">
                <iconify-icon icon="line-md:close" class="relative top-[2px] ">
                </iconify-icon>
            </button>
        </div>
    </div>
</div>

@script
<script>
    Alpine.data('message', () => {
        return {
            trigger() {
                this.open = false;
                $wire.resetStatus();
            },
            open: $wire.status,
            // init() {
            //     setTimeout(() => {
            //         this.open = false;
            //         $wire.resetStatus();
            //     }, 5000);
            // }
        }
    });
</script>
@endscript
