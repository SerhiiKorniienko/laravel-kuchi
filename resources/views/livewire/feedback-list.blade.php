<div class="p-8">
    <!-- Filters -->
    <div class="mb-8 grid grid-cols-1 md:grid-cols-4 gap-4">
        <input
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="SEARCH..."
            class="brutalist-input px-4 py-3 text-black font-bold uppercase placeholder-gray-500"
        >
        
        <select wire:model.live="category" class="brutalist-input px-4 py-3 text-black font-bold uppercase">
            <option value="">ALL CATEGORIES</option>
            @foreach($categories as $key => $label)
                <option value="{{ $key }}">{{ strtoupper($label) }}</option>
            @endforeach
        </select>

        <select wire:model.live="status" class="brutalist-input px-4 py-3 text-black font-bold uppercase">
            <option value="">ALL STATUS</option>
            @foreach($statuses as $key => $label)
                <option value="{{ $key }}">{{ strtoupper($label) }}</option>
            @endforeach
        </select>

        <select wire:model.live="sortBy" class="brutalist-input px-4 py-3 text-black font-bold uppercase">
            <option value="latest">LATEST</option>
            <option value="popular">POPULAR</option>
            <option value="oldest">OLDEST</option>
        </select>
    </div>

    <!-- Feedback Items -->
    <div class="space-y-6">
        @forelse($feedback as $item)
            <div class="bg-white brutalist-border brutalist-shadow-sm transform hover:rotate-1 transition-transform">
                <div class="flex">
                    <!-- Vote Section -->
                    <div class="bg-yellow p-6 flex flex-col items-center justify-center min-w-[120px] brutalist-border-thick border-t-0 border-l-0 border-b-0">
                        <button
                            wire:click="vote({{ $item->id }}, true)"
                            class="text-3xl mb-2 hover:scale-110 transition-transform {{ $item->hasUserVoted(auth()->id()) && $item->getUserVote(auth()->id())->is_upvote ? 'text-lime-green' : 'text-gray-600' }}"
                        >
                            ▲
                        </button>
                        
                        <div class="bg-black text-white px-3 py-1 brutalist-border font-black text-xl">
                            {{ $item->votes }}
                        </div>
                        
                        <button
                            wire:click="vote({{ $item->id }}, false)"
                            class="text-3xl mt-2 hover:scale-110 transition-transform {{ $item->hasUserVoted(auth()->id()) && !$item->getUserVote(auth()->id())->is_upvote ? 'text-red' : 'text-gray-600' }}"
                        >
                            ▼
                        </button>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 p-6">
                        <div class="flex flex-wrap items-start justify-between mb-4">
                            <h3 class="text-2xl font-black text-black uppercase mb-2 flex-1">{{ $item->title }}</h3>
                            <div class="flex gap-2 ml-4">
                                <span class="bg-electric-blue text-black px-3 py-1 brutalist-border font-black text-sm uppercase">
                                    {{ $item->category_label }}
                                </span>
                                <span class="px-3 py-1 brutalist-border font-black text-sm uppercase
                                    {{ $item->status === 'open' ? 'bg-lime-green text-black' : 
                                       ($item->status === 'in_progress' ? 'bg-orange text-black' : 
                                        ($item->status === 'resolved' ? 'bg-purple text-white' : 'bg-gray-400 text-black')) }}">
                                    {{ $item->status_label }}
                                </span>
                            </div>
                        </div>
                        
                        <p class="text-black font-bold text-lg mb-4 leading-relaxed">{{ Str::limit($item->description, 200) }}</p>
                        
                        <div class="bg-gray-100 px-4 py-2 brutalist-border inline-block">
                            <span class="text-black font-bold uppercase text-sm">
                                BY {{ strtoupper($item->user->name) }} • {{ strtoupper($item->created_at->diffForHumans()) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-16">
                <div class="bg-white brutalist-border brutalist-shadow-lg inline-block p-12 transform rotate-2">
                    <div class="text-6xl mb-4">🤷</div>
                    <h3 class="text-3xl font-black text-black uppercase mb-2">NO IDEAS YET!</h3>
                    <p class="text-black font-bold uppercase text-lg">BE THE FIRST TO SHARE!</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-12">
        {{ $feedback->links() }}
    </div>
</div>