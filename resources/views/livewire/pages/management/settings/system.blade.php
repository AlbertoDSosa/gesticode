<?php

use App\Concerns\HasEnvFile;
use function Livewire\Volt\{layout, state, uses, mount};

uses(HasEnvFile::class);

layout('layouts.app');

$breadcrumbItems = [
    [
        'name' => 'Settings',
        'url' => route('management.settings'),
        'active' => false
    ],
    [
        'name' => 'System',
        'url' => route('management.settings.system'),
        'active' => true
    ],
];


$pageTitle = 'System';

$envDetails = null;

state(compact('breadcrumbItems', 'pageTitle', 'envDetails'));

mount(function () {
    $this->envDetails = $this->getAllEnv()->only(['DB', 'APP']);
});


?>

<div class="space-y-8">
    <div>
        <x-breadcrumb :page-title="$pageTitle" :breadcrumb-items="$breadcrumbItems" />
    </div>

    <div class="rounded-md overflow-hidden" x-data="{open : false}" x-ref="list1">
        <div class="bg-white dark:bg-slate-800 px-5 py-7">
            <div class="grid md:grid-cols-2 xl:grid-cols-2 gap-7">
                {{-- @dd($envDetails) --}}
                @foreach($envDetails as $key => $value)

                    {{-- settings card --}}
                    <div class="generalSettings">
                        {{-- Header --}}
                        <div class="generalSettingsCardHead">
                            <h4 class="generalSettingsCardTitle">
                                {{ $key }}
                            </h4>
                            <button type="button" onclick="collapsedCard('{{$key}}')">
                                <iconify-icon icon="ic:round-keyboard-arrow-up"
                                            class="generalSettingsCardIcon{{$key}} transition-all duration-300 text-3xl dark:text-white" >
                                </iconify-icon>
                            </button>
                        </div>
                        {{-- Body --}}
                        <div class="settingBox"
                            id="settingBox{{$key}}">
                            <form class="space-y-5 sdc">
                                @foreach($value as $item)
                                    <div class="input-group">
                                        <label for="{{ $item['key'] }}"
                                            class="font-medium text-sm text-textColor mb-2 inline-block">
                                            {{ $item['key'] }}
                                        </label>
                                        <input type="{{$item['key'] === 'DB_PASSWORD'? 'password': 'text'}}"
                                            name="{{ $item['key'] }}"
                                            id="{{ $item['key'] }}"
                                            class="w-full border border-slate-300 bg-[#FBFBFB] py-[10px] px-4 outline-none focus:outline-none focus:ring-0 focus:border-[#000000] shadow-none !rounded-md text-base text-black overflow=hidden read-only:cursor-not-allowed read-only:bg-slate-200"
                                            value="{{ $item['data']['value'] }}"
                                            placeholder="{{ $item['key'] }}"
                                            disabled
                                        >
                                    </div>
                                @endforeach
                                <button class="defaultButton submitButton btn btn-dark" type="submit">
                                    {{ __('Save Changes') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</div>
@script
<script>

    // COLLPASED CARD
    function collapsedCard(key) {
        $('#settingBox' + key).toggleClass('hideContent');
        $('.generalSettingsCardIcon' + key).toggleClass('rotate-icon');
    }

</script>
@endscript
