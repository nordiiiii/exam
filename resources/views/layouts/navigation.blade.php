<nav class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @if(Auth::user()?->isAdmin())
                        <x-nav-link :href="route('admin')" :active="request()->routeIs('admin')">
                            {{ __('Admin') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <div class="relative" data-dropdown>
                    <button
                        type="button"
                        data-dropdown-toggle
                        aria-expanded="false"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none"
                    >
                        <span>{{ Auth::user()->name }}</span>
                        <svg class="ms-2 h-4 w-4 pointer-events-none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div data-dropdown-menu class="hidden absolute right-0 z-50 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 py-1">
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            {{ __('Profile') }}
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <div class="relative" data-dropdown>
                    <button
                        type="button"
                        data-dropdown-toggle
                        aria-expanded="false"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none"
                    >
                        <svg class="h-6 w-6 pointer-events-none" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <div data-dropdown-menu class="hidden absolute right-0 z-50 mt-2 w-64 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 py-2">
                        <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-base font-medium text-gray-700 hover:bg-gray-100">
                            {{ __('Dashboard') }}
                        </a>

                        @if(Auth::user()?->isAdmin())
                            <a href="{{ route('admin') }}" class="block px-4 py-2 text-base font-medium text-gray-700 hover:bg-gray-100">
                                {{ __('Admin') }}
                            </a>
                        @endif

                        <div class="border-t border-gray-200 mt-2 pt-2">
                            <div class="px-4 text-base font-medium text-gray-800">{{ Auth::user()->name }}</div>
                            <div class="px-4 text-sm font-medium text-gray-500">{{ Auth::user()->email }}</div>

                            <a href="{{ route('profile.edit') }}" class="mt-2 block px-4 py-2 text-base font-medium text-gray-700 hover:bg-gray-100">
                                {{ __('Profile') }}
                            </a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-base font-medium text-gray-700 hover:bg-gray-100">
                                    {{ __('Log Out') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
    (() => {
        if (window.__laracraftDropdownsInitialized) return;
        window.__laracraftDropdownsInitialized = true;

        function closeDropdown(dropdown) {
            const button = dropdown.querySelector('[data-dropdown-toggle]');
            const menu = dropdown.querySelector('[data-dropdown-menu]');
            if (!button || !menu) return;
            menu.classList.add('hidden');
            button.setAttribute('aria-expanded', 'false');
        }

        function closeAllDropdowns(except = null) {
            document.querySelectorAll('[data-dropdown]').forEach((dropdown) => {
                if (dropdown !== except) closeDropdown(dropdown);
            });
        }

        document.addEventListener('click', (event) => {
            const button = event.target.closest('[data-dropdown-toggle]');

            if (button) {
                event.preventDefault();
                event.stopPropagation();

                const dropdown = button.closest('[data-dropdown]');
                const menu = dropdown?.querySelector('[data-dropdown-menu]');
                if (!dropdown || !menu) return;

                const willOpen = menu.classList.contains('hidden');
                closeAllDropdowns(dropdown);
                menu.classList.toggle('hidden', !willOpen);
                button.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
                return;
            }

            if (!event.target.closest('[data-dropdown]')) {
                closeAllDropdowns();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') closeAllDropdowns();
        });
    })();
</script>
