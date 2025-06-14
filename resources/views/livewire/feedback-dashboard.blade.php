<div>
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-12">
        <div class="bg-electric-blue brutalist-border brutalist-shadow-lg p-6 transform rotate-1">
            <div class="text-center">
                <div class="text-4xl font-black text-black mb-2">{{ $stats['total_feedback'] }}</div>
                <div class="text-black font-bold uppercase text-sm">TOTAL IDEAS</div>
            </div>
        </div>

        <div class="bg-lime-green brutalist-border brutalist-shadow-lg p-6 transform -rotate-1">
            <div class="text-center">
                <div class="text-4xl font-black text-black mb-2">{{ $stats['open_feedback'] }}</div>
                <div class="text-black font-bold uppercase text-sm">OPEN IDEAS</div>
            </div>
        </div>

        <div class="bg-hot-pink brutalist-border brutalist-shadow-lg p-6 transform rotate-2">
            <div class="text-center">
                <div class="text-4xl font-black text-white mb-2">{{ $stats['total_bugs'] }}</div>
                <div class="text-white font-bold uppercase text-sm">TOTAL BUGS</div>
            </div>
        </div>

        <div class="bg-yellow brutalist-border brutalist-shadow-lg p-6 transform -rotate-2">
            <div class="text-center">
                <div class="text-4xl font-black text-black mb-2">{{ $stats['open_bugs'] }}</div>
                <div class="text-black font-bold uppercase text-sm">OPEN BUGS</div>
            </div>
        </div>

        <div class="bg-red brutalist-border brutalist-shadow-lg p-6 transform rotate-1">
            <div class="text-center">
                <div class="text-4xl font-black text-white mb-2">{{ $stats['critical_bugs'] }}</div>
                <div class="text-white font-bold uppercase text-sm">CRITICAL</div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="mb-8">
            <div class="bg-lime-green brutalist-border brutalist-shadow-lg p-6 transform rotate-1">
                <p class="text-black font-black text-lg uppercase text-center">
                    {{ session('message') }}
                </p>
            </div>
        </div>
    @endif

    <!-- Tabs -->
    <div class="bg-white brutalist-border brutalist-shadow-lg">
        <div class="bg-purple p-6 brutalist-border-thick border-l-0 border-r-0 border-t-0">
            <div class="flex space-x-4 justify-center">
                <button
                    wire:click="setTab('feedback')"
                    class="px-6 py-3 font-black uppercase text-lg brutalist-btn {{ $activeTab === 'feedback' ? 'bg-yellow text-black' : 'bg-white text-black' }}"
                >
                    IDEAS ({{ $stats['total_feedback'] }})
                </button>
                <button
                    wire:click="setTab('bugs')"
                    class="px-6 py-3 font-black uppercase text-lg brutalist-btn {{ $activeTab === 'bugs' ? 'bg-yellow text-black' : 'bg-white text-black' }}"
                >
                    BUGS ({{ $stats['total_bugs'] }})
                </button>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="p-8">
            @if($activeTab === 'feedback')
                <!-- Feedback Filters -->
                <div class="mb-8 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <select wire:model.live="feedbackStatus" class="brutalist-input px-4 py-3 text-black font-bold uppercase">
                        <option value="">ALL STATUS</option>
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}">{{ strtoupper($label) }}</option>
                        @endforeach
                    </select>
                    
                    <select wire:model.live="feedbackCategory" class="brutalist-input px-4 py-3 text-black font-bold uppercase">
                        <option value="">ALL CATEGORIES</option>
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}">{{ strtoupper($label) }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Feedback List -->
                <div class="space-y-6">
                    @forelse($feedback as $item)
                        <div class="bg-gray-50 brutalist-border p-6">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-xl font-black text-black uppercase">{{ $item->title }}</h3>
                                <div class="flex items-center space-x-3">
                                    <span class="bg-electric-blue text-black px-3 py-1 brutalist-border font-black text-sm uppercase">
                                        {{ $item->category_label }}
                                    </span>
                                    <select
                                        wire:change="updateFeedbackStatus({{ $item->id }}, $event.target.value)"
                                        class="brutalist-input px-3 py-1 text-black font-bold text-sm"
                                    >
                                        @foreach($statuses as $key => $label)
                                            <option value="{{ $key }}" {{ $item->status === $key ? 'selected' : '' }}>
                                                {{ strtoupper($label) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <p class="text-black font-bold mb-4">{{ Str::limit($item->description, 200) }}</p>
                            <div class="flex items-center justify-between">
                                <div class="bg-yellow px-3 py-1 brutalist-border">
                                    <span class="text-black font-black uppercase text-sm">
                                        BY {{ strtoupper($item->user->name) }} • {{ strtoupper($item->created_at->diffForHumans()) }}
                                    </span>
                                </div>
                                <div class="bg-black text-white px-3 py-1 brutalist-border font-black">
                                    {{ $item->votes }} VOTES
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="bg-gray-100 brutalist-border brutalist-shadow-lg inline-block p-8">
                                <p class="text-black font-black uppercase text-xl">NO FEEDBACK FOUND</p>
                            </div>
                        </div>
                    @endforelse
                </div>

                <div class="mt-8">
                    {{ $feedback->links() }}
                </div>
            @else
                <!-- Bug Report Filters -->
                <div class="mb-8 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <select wire:model.live="bugStatus" class="brutalist-input px-4 py-3 text-black font-bold uppercase">
                        <option value="">ALL STATUS</option>
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}">{{ strtoupper($label) }}</option>
                        @endforeach
                    </select>
                    
                    <select wire:model.live="bugPriority" class="brutalist-input px-4 py-3 text-black font-bold uppercase">
                        <option value="">ALL PRIORITIES</option>
                        @foreach($priorities as $key => $label)
                            <option value="{{ $key }}">{{ strtoupper($label) }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Bug Reports List -->
                <div class="space-y-6">
                    @forelse($bugs as $bug)
                        <div class="bg-gray-50 brutalist-border p-6">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-xl font-black text-black uppercase">{{ $bug->title }}</h3>
                                <div class="flex items-center space-x-3">
                                    <span class="px-3 py-1 brutalist-border font-black text-sm uppercase
                                        {{ $bug->priority === 'low' ? 'bg-lime-green text-black' : 
                                           ($bug->priority === 'medium' ? 'bg-yellow text-black' : 
                                            ($bug->priority === 'high' ? 'bg-orange text-black' : 'bg-red text-white')) }}">
                                        {{ $bug->priority_label }}
                                    </span>
                                    <select
                                        wire:change="updateBugStatus({{ $bug->id }}, $event.target.value)"
                                        class="brutalist-input px-3 py-1 text-black font-bold text-sm"
                                    >
                                        @foreach($statuses as $key => $label)
                                            <option value="{{ $key }}" {{ $bug->status === $key ? 'selected' : '' }}>
                                                {{ strtoupper($label) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <p class="text-black font-bold mb-4">{{ Str::limit($bug->description, 200) }}</p>
                            @if($bug->url)
                                <div class="bg-electric-blue px-3 py-1 brutalist-border mb-4 inline-block">
                                    <a href="{{ $bug->url }}" target="_blank" class="text-black font-black uppercase text-sm">
                                        VIEW URL →
                                    </a>
                                </div>
                            @endif
                            <div class="bg-yellow px-3 py-1 brutalist-border">
                                <span class="text-black font-black uppercase text-sm">
                                    BY {{ strtoupper($bug->user->name) }} • {{ strtoupper($bug->created_at->diffForHumans()) }}
                                    @if($bug->browser || $bug->operating_system)
                                        • {{ strtoupper($bug->browser) }} ON {{ strtoupper($bug->operating_system) }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="bg-gray-100 brutalist-border brutalist-shadow-lg inline-block p-8">
                                <p class="text-black font-black uppercase text-xl">NO BUGS FOUND</p>
                            </div>
                        </div>
                    @endforelse
                </div>

                <div class="mt-8">
                    {{ $bugs->links() }}
                </div>
            @endif
        </div>
    </div>
</div>