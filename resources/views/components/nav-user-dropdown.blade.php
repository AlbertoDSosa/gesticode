<div data-twe-dropdown-ref class="relative md:block hidden w-full leading-0">
    <button
        class="text-slate-800 dark:text-white focus:ring-0 focus:outline-none font-medium rounded-lg text-sm text-center
        inline-flex items-center"
        type="button"
        id="NavUserDropdown"
        data-twe-dropdown-toggle-ref
        aria-expanded="false"
        data-twe-ripple-init
        data-twe-ripple-color="light"
    >
        <div class="lg:h-8 lg:w-8 h-7 w-7 rounded-full flex-1 ltr:mr-[10px] rtl:ml-[10px]">
            <img
                class="block w-full h-full object-cover rounded-full"
                src="{{ auth()->user()->getFirstMediaUrl('profile-image', 'preview') ?:
                    Avatar::create(auth()->user()->name)->toBase64() }}"
                alt="user"
            />
        </div>
        <div class="ltr:text-left rtl:text-right">
            <span
                class="flex-none text-slate-600 dark:text-white text-sm font-normal items-center lg:flex hidden overflow-hidden text-ellipsis whitespace-nowrap">
                {{ Str::limit(Auth::user()->name, 20) }}
            </span>
            {{-- <small class="text-[9px] block">{{ auth()->user()->roles()->first()?->name }}</small> --}}
        </div>
        <svg class="w-[16px] h-[16px] dark:text-white hidden lg:inline-block text-base ml-[10px] rtl:mr-[10px]"
            aria-hidden="true" fill="none" stroke="currentColor" viewbox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>
    <!-- Dropdown menu -->
    <ul
        class="absolute text-slate-800 dark:text-slate-200 z-[1000] float-left m-0 hidden min-w-max list-none overflow-hidden rounded-lg border-none bg-white bg-clip-padding text-base shadow-lg data-[twe-dropdown-show]:block dark:bg-surface-dark"
        aria-labelledby="NavUserDropdown"
        data-twe-dropdown-menu-ref
    >
        <li>
            <a
                href="{{ route('profile') }}"
                data-twe-dropdown-item-ref
                class="flex items-center w-full px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white font-inter text-sm text-slate-600
                    dark:text-white font-normal"
                {{-- @class(['country-list', 'active' => request()->routeIs('profile')]) --}}
            >
                <iconify-icon
                    class="text-lg text-textColor dark:text-white mr-2"
                    icon="carbon:user-avatar"
                >
                </iconify-icon>
                <span class="dropdown-option">
                    @lang('Profile')
                </span>
            </a>
        </li>
        {{-- Logout --}}
        <li>
            <div class="flex items-center px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white font-inter text-sm text-slate-600
                    dark:text-white font-normal">
                <button wire:click="logout" type="submit" class="country-list flex items-start">
                    <iconify-icon
                        class="text-lg text-textColor dark:text-white mr-2"
                        icon="carbon:logout"
                    >
                    </iconify-icon>
                    <span class="dropdown-option">
                        @lang('Log Out')
                    </span>
                </button>
            </div>
        </li>
    </ul>
</div>
