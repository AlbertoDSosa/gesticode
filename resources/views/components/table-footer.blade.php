@props(['data'])

<div class="flex flex-wrap gap-3 items-center justify-between pt-8 px-8">
    <div class="font-medium text-sm text-textColor dark:text-white flex items-center">
        <div class="border border-slate-200 dark:border-slate-700 p-2 rounded">
            <form id="perPageForm">
                <select
                    class="dark:bg-slate-800"
                    wire:model.live="rows"
                    {{-- x-on:change="" --}}
                    id="tableRow"
                    class="dropdownTableSelect"
                >
                    <option value="10" selected>
                        {{ __('10') }}
                    </option>
                    <option value="15">
                        {{ __('15') }}
                    </option>
                    <option value="25">
                        {{ __('25') }}
                    </option>
                </select>
            </form>
        </div>
    </div>
    <div>
        {{-- Pagination Links Start--}}
        {{ $data->links() }}
        {{-- Pagination Links End--}}
    </div>
</div>
