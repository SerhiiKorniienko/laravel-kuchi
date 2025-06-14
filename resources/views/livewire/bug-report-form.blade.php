<div>
    <button
        wire:click="toggleForm"
        class="bg-red text-white px-8 py-4 brutalist-btn brutalist-shadow-lg text-xl transform hover:-rotate-1 transition-transform"
    >
        🐛 REPORT BUG
    </button>

    @if($showForm)
        <div class="fixed inset-0 bg-black bg-opacity-80 z-50 flex items-center justify-center p-4" wire:click="toggleForm">
            <div class="bg-white brutalist-border brutalist-shadow-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto transform -rotate-1" wire:click.stop>
                <!-- Header -->
                <div class="bg-red p-6 brutalist-border-thick border-l-0 border-r-0 border-t-0">
                    <div class="flex justify-between items-center">
                        <h3 class="text-2xl font-black text-white uppercase">BUG ALERT!</h3>
                        <button wire:click="toggleForm" class="text-white hover:text-yellow text-3xl font-black">
                            ✕
                        </button>
                    </div>
                </div>

                <!-- Form -->
                <div class="p-8">
                    <form wire:submit="submit" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-black font-black uppercase text-lg mb-3">BUG TITLE</label>
                                <input
                                    type="text"
                                    wire:model="title"
                                    class="w-full px-4 py-3 brutalist-input text-black font-bold"
                                    placeholder="WHAT BROKE?"
                                >
                                @error('title') 
                                    <div class="bg-red text-white px-3 py-2 mt-2 brutalist-border font-bold uppercase text-sm">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-black font-black uppercase text-lg mb-3">PRIORITY</label>
                                <select
                                    wire:model="priority"
                                    class="w-full px-4 py-3 brutalist-input text-black font-bold"
                                >
                                    @foreach($priorities as $key => $label)
                                        <option value="{{ $key }}">{{ strtoupper($label) }}</option>
                                    @endforeach
                                </select>
                                @error('priority') 
                                    <div class="bg-red text-white px-3 py-2 mt-2 brutalist-border font-bold uppercase text-sm">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-black font-black uppercase text-lg mb-3">WHAT HAPPENED?</label>
                            <textarea
                                wire:model="description"
                                rows="3"
                                class="w-full px-4 py-3 brutalist-input text-black font-bold resize-none"
                                placeholder="DESCRIBE THE BUG..."
                            ></textarea>
                            @error('description') 
                                <div class="bg-red text-white px-3 py-2 mt-2 brutalist-border font-bold uppercase text-sm">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-black font-black uppercase text-lg mb-3">HOW TO REPRODUCE</label>
                            <textarea
                                wire:model="steps_to_reproduce"
                                rows="3"
                                class="w-full px-4 py-3 brutalist-input text-black font-bold resize-none"
                                placeholder="1. DO THIS&#10;2. THEN THIS&#10;3. BUG APPEARS"
                            ></textarea>
                            @error('steps_to_reproduce') 
                                <div class="bg-red text-white px-3 py-2 mt-2 brutalist-border font-bold uppercase text-sm">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-black font-black uppercase text-lg mb-3">EXPECTED</label>
                                <textarea
                                    wire:model="expected_behavior"
                                    rows="2"
                                    class="w-full px-4 py-3 brutalist-input text-black font-bold resize-none"
                                    placeholder="WHAT SHOULD HAPPEN?"
                                ></textarea>
                                @error('expected_behavior') 
                                    <div class="bg-red text-white px-3 py-2 mt-2 brutalist-border font-bold uppercase text-sm">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-black font-black uppercase text-lg mb-3">ACTUAL</label>
                                <textarea
                                    wire:model="actual_behavior"
                                    rows="2"
                                    class="w-full px-4 py-3 brutalist-input text-black font-bold resize-none"
                                    placeholder="WHAT ACTUALLY HAPPENED?"
                                ></textarea>
                                @error('actual_behavior') 
                                    <div class="bg-red text-white px-3 py-2 mt-2 brutalist-border font-bold uppercase text-sm">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-black font-black uppercase mb-3">BROWSER</label>
                                <input
                                    type="text"
                                    wire:model="browser"
                                    class="w-full px-4 py-3 brutalist-input text-black font-bold"
                                    placeholder="CHROME"
                                >
                            </div>

                            <div>
                                <label class="block text-black font-black uppercase mb-3">OS</label>
                                <input
                                    type="text"
                                    wire:model="operating_system"
                                    class="w-full px-4 py-3 brutalist-input text-black font-bold"
                                    placeholder="WINDOWS"
                                >
                            </div>

                            <div>
                                <label class="block text-black font-black uppercase mb-3">URL</label>
                                <input
                                    type="url"
                                    wire:model="url"
                                    class="w-full px-4 py-3 brutalist-input text-black font-bold"
                                    placeholder="WHERE?"
                                >
                            </div>
                        </div>

                        <div class="flex gap-4 pt-6">
                            <button
                                type="button"
                                wire:click="toggleForm"
                                class="bg-gray-300 text-black px-6 py-3 brutalist-btn flex-1"
                            >
                                CANCEL
                            </button>
                            <button
                                type="submit"
                                class="bg-hot-pink text-white px-6 py-3 brutalist-btn flex-1"
                            >
                                REPORT BUG!
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>