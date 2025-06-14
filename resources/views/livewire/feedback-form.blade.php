<div>
    <button
        wire:click="toggleForm"
        class="bg-electric-blue text-black px-8 py-4 brutalist-btn brutalist-shadow-lg text-xl transform hover:rotate-1 transition-transform"
    >
        💡 SUGGEST FEATURE
    </button>

    @if($showForm)
        <div class="fixed inset-0 bg-black bg-opacity-80 z-50 flex items-center justify-center p-4" wire:click="toggleForm">
            <div class="bg-white brutalist-border brutalist-shadow-lg max-w-2xl w-full transform rotate-1" wire:click.stop>
                <!-- Header -->
                <div class="bg-electric-blue p-6 brutalist-border-thick border-l-0 border-r-0 border-t-0">
                    <div class="flex justify-between items-center">
                        <h3 class="text-2xl font-black text-black uppercase">NEW IDEA!</h3>
                        <button wire:click="toggleForm" class="text-black hover:text-red text-3xl font-black">
                            ✕
                        </button>
                    </div>
                </div>

                <!-- Form -->
                <div class="p-8">
                    <form wire:submit="submit" class="space-y-6">
                        <div>
                            <label class="block text-black font-black uppercase text-lg mb-3">TITLE</label>
                            <input
                                type="text"
                                wire:model="title"
                                class="w-full px-4 py-3 brutalist-input text-black font-bold text-lg"
                                placeholder="WHAT'S YOUR BIG IDEA?"
                            >
                            @error('title') 
                                <div class="bg-red text-white px-3 py-2 mt-2 brutalist-border font-bold uppercase">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-black font-black uppercase text-lg mb-3">CATEGORY</label>
                            <select
                                wire:model="category"
                                class="w-full px-4 py-3 brutalist-input text-black font-bold text-lg"
                            >
                                @foreach($categories as $key => $label)
                                    <option value="{{ $key }}">{{ strtoupper($label) }}</option>
                                @endforeach
                            </select>
                            @error('category') 
                                <div class="bg-red text-white px-3 py-2 mt-2 brutalist-border font-bold uppercase">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-black font-black uppercase text-lg mb-3">DESCRIPTION</label>
                            <textarea
                                wire:model="description"
                                rows="5"
                                class="w-full px-4 py-3 brutalist-input text-black font-bold text-lg resize-none"
                                placeholder="TELL US MORE ABOUT YOUR IDEA..."
                            ></textarea>
                            @error('description') 
                                <div class="bg-red text-white px-3 py-2 mt-2 brutalist-border font-bold uppercase">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="flex gap-4 pt-4">
                            <button
                                type="button"
                                wire:click="toggleForm"
                                class="bg-gray-300 text-black px-6 py-3 brutalist-btn flex-1"
                            >
                                CANCEL
                            </button>
                            <button
                                type="submit"
                                class="bg-lime-green text-black px-6 py-3 brutalist-btn flex-1"
                            >
                                SUBMIT IDEA!
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>