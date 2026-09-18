<div>
    @if ($isOpen)
        <div class="fixed inset-0 z-50 flex items-start justify-center pt-20 p-4 bg-black/60 backdrop-blur-sm transition-opacity"
             x-data
             @click.self="$wire.close()"
             @keydown.escape.window="$wire.close()">
            
            <div class="w-full max-w-2xl bg-base-100 rounded-2xl shadow-2xl border border-base-300 overflow-hidden flex flex-col max-h-[80vh] animate-in fade-in zoom-in-95 duration-150">
                
                {{-- Search Header --}}
                <div class="flex items-center px-4 py-3 border-b border-base-200 gap-3 bg-base-200/40">
                    <x-mary-icon name="o-magnifying-glass" class="w-6 h-6 text-primary" />
                    <input
                        type="text"
                        wire:model.live.debounce.150ms="query"
                        placeholder="Type a command, employee, project, or inventory item..."
                        class="w-full bg-transparent border-0 focus:outline-none text-base sm:text-lg placeholder:text-base-content/40"
                        autofocus
                    />
                    <kbd class="kbd kbd-sm bg-base-300 text-xs font-semibold">ESC</kbd>
                </div>

                {{-- Results Body --}}
                <div class="overflow-y-auto p-2 divide-y divide-base-200/50 flex-1">
                    @php
                        $grouped = collect($results)->groupBy('category');
                    @endphp

                    @forelse ($grouped as $category => $items)
                        <div class="py-2">
                            <div class="px-3 py-1 text-xs font-semibold uppercase tracking-wider text-base-content/50">
                                {{ $category }}
                            </div>
                            <div class="space-y-1 mt-1">
                                @foreach ($items as $item)
                                    <a href="{{ $item['url'] }}"
                                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-primary/10 hover:text-primary transition-colors group">
                                        <div class="p-2 rounded-lg bg-base-200 group-hover:bg-primary group-hover:text-primary-content transition-colors">
                                            <x-mary-icon :name="$item['icon']" class="w-5 h-5" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="font-medium text-sm truncate group-hover:text-primary">
                                                {{ $item['title'] }}
                                            </div>
                                            <div class="text-xs text-base-content/60 truncate">
                                                {{ $item['subtitle'] }}
                                            </div>
                                        </div>
                                        <x-mary-icon name="o-chevron-right" class="w-4 h-4 text-base-content/30 group-hover:text-primary transition-transform group-hover:translate-x-1" />
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center text-base-content/50">
                            <x-mary-icon name="o-magnifying-glass" class="w-12 h-12 mx-auto mb-2 opacity-30" />
                            <p class="text-sm font-medium">No results found for "{{ $query }}"</p>
                            <p class="text-xs mt-1">Try searching for an employee name, project, item ID, or system action.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Footer Hints --}}
                <div class="flex items-center justify-between px-4 py-2 bg-base-200/50 border-t border-base-200 text-[11px] text-base-content/60">
                    <div class="flex items-center gap-2">
                        <span>Navigate</span>
                        <kbd class="kbd kbd-xs">↑</kbd>
                        <kbd class="kbd kbd-xs">↓</kbd>
                        <span class="ml-2">Select</span>
                        <kbd class="kbd kbd-xs">↵</kbd>
                    </div>
                    <div class="flex items-center gap-1">
                        <span>Shortcut</span>
                        <kbd class="kbd kbd-xs">Cmd</kbd> + <kbd class="kbd kbd-xs">K</kbd>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
