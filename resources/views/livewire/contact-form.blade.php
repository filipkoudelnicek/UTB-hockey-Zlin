<div>
    @if (session()->has('message'))
        <div class="mb-6 rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit.prevent="submitForm" class="flex flex-col gap-4">

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="flex flex-col gap-1">
                <input
                    wire:model="name"
                    type="text"
                    placeholder="{{ __('Jméno') }}"
                    class="rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm placeholder-gray-400 shadow-sm focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20 outline-none transition @error('name') border-red-400 @enderror"
                >
                @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div class="flex flex-col gap-1">
                <input
                    wire:model="phone"
                    type="text"
                    placeholder="{{ __('Telefon') }}"
                    class="rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm placeholder-gray-400 shadow-sm focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20 outline-none transition @error('phone') border-red-400 @enderror"
                >
                @error('phone') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="flex flex-col gap-1">
            <input
                wire:model="email"
                type="email"
                placeholder="{{ __('E-mail') }}"
                class="rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm placeholder-gray-400 shadow-sm focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20 outline-none transition @error('email') border-red-400 @enderror"
            >
            @error('email') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
        </div>

        <div class="flex flex-col gap-1">
            <textarea
                wire:model="message"
                rows="5"
                placeholder="{{ __('S čím můžeme pomoci?') }}"
                class="rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm placeholder-gray-400 shadow-sm resize-none focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20 outline-none transition @error('message') border-red-400 @enderror"
            ></textarea>
            @error('message') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
        </div>

        <div>
            <button
                type="submit"
                class="inline-flex items-center gap-2 rounded-xl bg-violet-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-violet-500/50 transition disabled:opacity-60"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove>{{ __('Odeslat') }}</span>
                <span wire:loading class="flex items-center gap-2">
                    <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                    </svg>
                    {{ __('Odesílání...') }}
                </span>
            </button>
        </div>

    </form>
</div>