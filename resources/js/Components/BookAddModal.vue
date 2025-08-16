<template>
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-10 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="mt-3">
               
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        Dodaj do biblioteki
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

               
                <div class="flex space-x-4 mb-6 p-3 bg-gray-50 rounded-lg">
                    <img 
                        v-if="book.cover_url"
                        :src="book.cover_url"
                        :alt="book.title"
                        class="w-12 h-16 object-cover rounded shadow-sm"
                        @error="handleImageError"
                    />
                    <div v-else class="w-12 h-16 bg-gray-300 rounded flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-semibold text-gray-900 line-clamp-2">{{ book.title }}</h4>
                        <p class="text-sm text-gray-600">{{ book.authors }}</p>
                        <div class="flex items-center space-x-2 mt-1">
                            <span v-if="book.published_year" class="text-xs text-gray-500">{{ book.published_year }}</span>
                            <span v-if="book.pages" class="text-xs text-gray-500">{{ book.pages }} str.</span>
                        </div>
                    </div>
                </div>

               
                <form @submit.prevent="addToLibrary" class="space-y-4">
                   
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                            Status w bibliotece *
                        </label>
                        <select
                            id="status"
                            v-model="form.status"
                            required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="to_read">Do przeczytania</option>
                            <option value="reading">Czytam teraz</option>
                            <option value="read">Przeczytana</option>
                        </select>
                    </div>

                   
                    <div v-if="form.status === 'read'">
                        <label for="rating" class="block text-sm font-medium text-gray-700 mb-1">
                            Ocena
                        </label>
                        <select
                            id="rating"
                            v-model="form.rating"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">Brak oceny</option>
                            <option value="1">⭐ 1/5 - Słabo</option>
                            <option value="2">⭐⭐ 2/5 - Przeciętnie</option>
                            <option value="3">⭐⭐⭐ 3/5 - Dobrze</option>
                            <option value="4">⭐⭐⭐⭐ 4/5 - Bardzo dobrze</option>
                            <option value="5">⭐⭐⭐⭐⭐ 5/5 - Świetnie</option>
                        </select>
                    </div>

                   
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                            Notatki osobiste
                        </label>
                        <textarea
                            id="notes"
                            v-model="form.notes"
                            rows="3"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Twoje przemyślenia, recenzja..."
                        ></textarea>
                    </div>

                   
                    <div v-if="error" class="bg-red-50 border border-red-200 rounded-lg p-3">
                        <div class="flex">
                            <svg class="w-5 h-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <p class="text-sm text-red-600">{{ error }}</p>
                        </div>
                    </div>

                   
                    <div class="flex justify-end space-x-3 pt-4">
                        <button
                            type="button"
                            @click="$emit('close')"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded"
                        >
                            Anuluj
                        </button>
                        <button
                            type="submit"
                            :disabled="loading"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50 inline-flex items-center"
                        >
                            <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ loading ? 'Dodawanie...' : 'Dodaj do biblioteki' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { usePage } from '@inertiajs/vue3'


const props = defineProps({
    book: {
        type: Object,
        required: true
    }
})

const emit = defineEmits(['close', 'added'])

const page = usePage()
const loading = ref(false)
const error = ref('')


const form = reactive({
    status: 'to_read',
    rating: '',
    notes: ''
})


const getCsrfToken = () => {
    return page.props.csrf_token || 
           document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
           ''
}


const addToLibrary = async () => {
    loading.value = true
    error.value = ''
    
    try {
        const response = await fetch('/api/search/add-to-library', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                google_books_id: props.book.google_books_id,
                status: form.status,
                rating: form.rating || null,
                notes: form.notes
            })
        })
        
        const data = await response.json()
        
        if (response.ok) {
           
            emit('added', data.book)
        } else {
           
            error.value = data.error || 'Wystąpił błąd podczas dodawania książki'
        }
    } catch (err) {
        console.error('Błąd dodawania książki:', err)
        error.value = 'Wystąpił błąd podczas dodawania książki'
    } finally {
        loading.value = false
    }
}


const handleImageError = (event) => {
    event.target.style.display = 'none'
}
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>