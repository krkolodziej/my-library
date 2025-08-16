<template>
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-5 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-medium text-gray-900">
                        Szczegóły książki
                    </h3>
                    <button 
                        @click="$emit('close')"
                        class="text-gray-400 hover:text-gray-600"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Główne informacje -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <!-- Okładka -->
                    <div class="md:col-span-1">
                        <img 
                            v-if="book.cover_url"
                            :src="book.cover_url"
                            :alt="book.title"
                            class="w-full max-w-48 mx-auto object-cover rounded shadow-lg"
                            @error="handleImageError"
                        />
                        <div v-else class="w-full max-w-48 mx-auto aspect-[3/4] bg-gray-300 rounded flex items-center justify-center">
                            <svg class="w-16 h-16 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Informacje tekstowe -->
                    <div class="md:col-span-2 space-y-4">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ book.title }}</h1>
                            <p v-if="book.subtitle" class="text-lg text-gray-700 mb-2">{{ book.subtitle }}</p>
                            <p class="text-lg text-gray-600">{{ book.authors }}</p>
                        </div>

                        <!-- Status w bibliotece -->
                        <div v-if="book.in_library" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Już w Twojej bibliotece
                        </div>

                        <!-- Ocena Google -->
                        <div v-if="book.average_rating" class="flex items-center">
                            <span class="text-sm text-gray-600 mr-2">Ocena Google Books:</span>
                            <div class="flex items-center">
                                <svg 
                                    v-for="i in 5" 
                                    :key="i"
                                    class="w-4 h-4"
                                    :class="i <= Math.round(book.average_rating) ? 'text-yellow-400' : 'text-gray-300'"
                                    fill="currentColor" 
                                    viewBox="0 0 20 20"
                                >
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                <span class="ml-1 text-sm text-gray-600">
                                    {{ book.average_rating }}/5 
                                    <span v-if="book.ratings_count">({{ book.ratings_count }} ocen)</span>
                                </span>
                            </div>
                        </div>

                        <!-- Szczegóły techniczne -->
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div v-if="book.isbn">
                                <span class="font-medium text-gray-700">ISBN:</span>
                                <span class="text-gray-600 ml-2">{{ book.isbn }}</span>
                            </div>
                            <div v-if="book.pages">
                                <span class="font-medium text-gray-700">Strony:</span>
                                <span class="text-gray-600 ml-2">{{ book.pages }}</span>
                            </div>
                            <div v-if="book.published_year">
                                <span class="font-medium text-gray-700">Rok wydania:</span>
                                <span class="text-gray-600 ml-2">{{ book.published_year }}</span>
                            </div>
                            <div v-if="book.publisher">
                                <span class="font-medium text-gray-700">Wydawca:</span>
                                <span class="text-gray-600 ml-2">{{ book.publisher }}</span>
                            </div>
                            <div v-if="book.language">
                                <span class="font-medium text-gray-700">Język:</span>
                                <span class="text-gray-600 ml-2">{{ getLanguageName(book.language) }}</span>
                            </div>
                        </div>

                        <!-- Kategorie -->
                        <div v-if="book.categories && book.categories.length > 0">
                            <span class="font-medium text-gray-700 text-sm">Kategorie:</span>
                            <div class="flex flex-wrap gap-2 mt-1">
                                <span 
                                    v-for="category in book.categories" 
                                    :key="category"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                                >
                                    {{ category }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Opis -->
                <div v-if="book.description" class="mb-6">
                    <h4 class="text-lg font-medium text-gray-900 mb-3">Opis</h4>
                    <div class="text-gray-700 leading-relaxed">
                        <p>{{ book.description }}</p>
                    </div>
                </div>

                <!-- Linki -->
                <div v-if="book.preview_link || book.info_link" class="mb-6">
                    <h4 class="text-lg font-medium text-gray-900 mb-3">Linki</h4>
                    <div class="flex space-x-4">
                        <a 
                            v-if="book.preview_link"
                            :href="book.preview_link"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Podgląd w Google Books
                        </a>
                        <a 
                            v-if="book.info_link"
                            :href="book.info_link"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Więcej informacji
                        </a>
                    </div>
                </div>

                <!-- Przyciski akcji -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <button
                        @click="$emit('close')"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded"
                    >
                        Zamknij
                    </button>
                    <button
                        v-if="!book.in_library"
                        @click="$emit('add-to-library', book)"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Dodaj do biblioteki
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
// Props i emity
const props = defineProps({
    book: {
        type: Object,
        required: true
    }
})

const emit = defineEmits(['close', 'add-to-library'])

// Pomocnicze funkcje
const handleImageError = (event) => {
    event.target.style.display = 'none'
}

const getLanguageName = (code) => {
    const languages = {
        'pl': 'Polski',
        'en': 'Angielski',
        'de': 'Niemiecki',
        'fr': 'Francuski',
        'es': 'Hiszpański',
        'it': 'Włoski',
        'ru': 'Rosyjski'
    }
    return languages[code] || code.toUpperCase()
}
</script>