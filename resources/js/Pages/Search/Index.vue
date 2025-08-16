<template>
    <Head title="Wyszukaj książki" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Wyszukaj książki w Google Books
                </h2>
                <Link 
                    :href="route('books.index')" 
                    class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg inline-flex items-center"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Wróć do biblioteki
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Info o API -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="text-sm text-blue-800">
                                <strong>Google Books API:</strong>
                                {{ apiInfo.has_api_key ? 'Konfiguracja OK' : 'Bezpłatna wersja' }} 
                                ({{ apiInfo.daily_limit }})
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            
                            <div class="lg:col-span-2">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Wyszukiwanie ogólne</h3>
                                <form @submit.prevent="searchBooks" class="space-y-4">
                                    <div>
                                        <label for="general-search" class="block text-sm font-medium text-gray-700 mb-1">
                                            Tytuł, autor, słowa kluczowe
                                        </label>
                                        <div class="flex space-x-2">
                                            <input
                                                id="general-search"
                                                v-model="searchForm.query"
                                                type="text"
                                                required
                                                class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                placeholder="np. Wiedźmin Sapkowski, Harry Potter, programowanie"
                                            />
                                            <button
                                                type="submit"
                                                :disabled="searching || !searchForm.query.trim()"
                                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg disabled:opacity-50 inline-flex items-center"
                                            >
                                                <svg v-if="searching" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                {{ searching ? 'Szukam...' : 'Szukaj' }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            
                            <div class="border-l border-gray-200 pl-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Wyszukiwanie precyzyjne</h3>
                                
                                
                                <form @submit.prevent="searchByTitleAuthor" class="space-y-3 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Tytuł lub Autor</label>
                                        <input
                                            v-model="titleAuthorForm.title"
                                            type="text"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                            placeholder="Tytuł książki"
                                        />
                                        <input
                                            v-model="titleAuthorForm.author"
                                            type="text"
                                            class="w-full mt-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                            placeholder="Autor"
                                        />
                                        <button
                                            type="submit"
                                            :disabled="searching || (!titleAuthorForm.title.trim() && !titleAuthorForm.author.trim())"
                                            class="w-full mt-2 bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-3 rounded text-sm disabled:opacity-50"
                                        >
                                            Szukaj po tytule/autorze
                                        </button>
                                    </div>
                                </form>

                                
                                <form @submit.prevent="searchByIsbn" class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">ISBN</label>
                                        <input
                                            v-model="isbnForm.isbn"
                                            type="text"
                                            required
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                            placeholder="978-83-240-2077-5"
                                        />
                                        <button
                                            type="submit"
                                            :disabled="searching || !isbnForm.isbn.trim()"
                                            class="w-full mt-2 bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-3 rounded text-sm disabled:opacity-50"
                                        >
                                            Szukaj po ISBN
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

               
                <div v-if="searchResults.length > 0 || searchPerformed" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                       
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">
                                    Wyniki wyszukiwania
                                </h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    {{ searchResults.length > 0 ? `Znaleziono ${searchResults.length} książek` : 'Brak wyników' }}
                                    <span v-if="lastQuery" class="font-medium">dla: "{{ lastQuery }}"</span>
                                </p>
                            </div>
                            <button 
                                v-if="searchResults.length > 0"
                                @click="clearResults"
                                class="text-sm text-gray-500 hover:text-gray-700"
                            >
                                Wyczyść wyniki
                            </button>
                        </div>

                       
                        <div v-if="searchResults.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div 
                                v-for="book in searchResults" 
                                :key="book.google_books_id"
                                class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow duration-200"
                            >
                           
                                <div class="flex space-x-4 mb-4">
                                    <div class="flex-shrink-0">
                                        <img 
                                            v-if="book.cover_url"
                                            :src="book.cover_url"
                                            :alt="book.title"
                                            class="w-16 h-24 object-cover rounded shadow-sm"
                                            @error="handleImageError"
                                        />
                                        <div v-else class="w-16 h-24 bg-gray-300 rounded flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-semibold text-gray-900 line-clamp-2">{{ book.title }}</h4>
                                        <p class="text-sm text-gray-600 mt-1">{{ book.authors }}</p>
                                        <div class="mt-2 flex items-center space-x-2">
                                            <span v-if="book.published_year" class="text-xs text-gray-500">{{ book.published_year }}</span>
                                            <span v-if="book.pages" class="text-xs text-gray-500">{{ book.pages }} str.</span>
                                        </div>
                                    </div>
                                </div>

                               
                                <div v-if="book.description" class="mb-4">
                                    <p class="text-xs text-gray-600 line-clamp-3">{{ book.description }}</p>
                                </div>

                               
                                <div v-if="book.in_library" class="mb-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        W bibliotece
                                    </span>
                                </div>

                               
                                <div class="flex space-x-2">
                                    <button
                                        @click="showAddModal(book)"
                                        :disabled="book.in_library"
                                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-2 px-3 rounded disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        {{ book.in_library ? 'W bibliotece' : 'Dodaj do biblioteki' }}
                                    </button>
                                    <button
                                        @click="showDetailsModal(book)"
                                        class="bg-gray-600 hover:bg-gray-700 text-white text-xs font-bold py-2 px-3 rounded"
                                    >
                                        Szczegóły
                                    </button>
                                </div>
                            </div>
                        </div>

                       
                        <div v-else-if="searchPerformed" class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-lg font-medium text-gray-900">Brak wyników</h3>
                            <p class="mt-1 text-gray-500">Spróbuj wyszukać używając innych słów kluczowych.</p>
                        </div>
                    </div>
                </div>

               
                <BookAddModal 
                    v-if="showModal && selectedBook"
                    :book="selectedBook"
                    @close="closeModal"
                    @added="handleBookAdded"
                />

               
                <BookDetailsModal
                    v-if="showDetailsModalFlag && selectedBook"
                    :book="selectedBook"
                    @close="closeDetailsModal"
                    @add-to-library="showAddModal"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Head, Link, usePage, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import BookAddModal from '@/Components/BookAddModal.vue'
import BookDetailsModal from '@/Components/BookDetailsModal.vue'


const props = defineProps({
    apiInfo: Object
})


const page = usePage()


const getCsrfToken = () => {
    return page.props.csrf_token || 
           document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
           ''
}


const searching = ref(false)
const searchResults = ref([])
const searchPerformed = ref(false)
const lastQuery = ref('')
const showModal = ref(false)
const showDetailsModalFlag = ref(false)
const selectedBook = ref(null)


const searchForm = reactive({
    query: '',
    maxResults: 20
})

const titleAuthorForm = reactive({
    title: '',
    author: ''
})

const isbnForm = reactive({
    isbn: ''
})


const searchBooks = async () => {
    if (!searchForm.query.trim()) return
    
    searching.value = true
    searchPerformed.value = true
    lastQuery.value = searchForm.query
    
    try {
        const response = await fetch('/api/search/books', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                query: searchForm.query,
                max_results: searchForm.maxResults
            })
        })
        
        const data = await response.json()
        
        if (response.ok) {
            searchResults.value = data.books || []
        } else {
            console.error('API Error:', data)
            searchResults.value = []
        }
    } catch (error) {
        console.error('Błąd wyszukiwania:', error)
        searchResults.value = []
    } finally {
        searching.value = false
    }
}


const searchByTitleAuthor = async () => {
    if (!titleAuthorForm.title.trim() && !titleAuthorForm.author.trim()) return
    
    searching.value = true
    searchPerformed.value = true
    lastQuery.value = `${titleAuthorForm.title} ${titleAuthorForm.author}`.trim()
    
    try {
        const response = await fetch('/api/search/title-author', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json'
            },
            body: JSON.stringify(titleAuthorForm)
        })
        
        const data = await response.json()
        
        if (response.ok) {
            searchResults.value = data.books || []
        } else {
            console.error('API Error:', data)
            searchResults.value = []
        }
    } catch (error) {
        console.error('Błąd wyszukiwania:', error)
        searchResults.value = []
    } finally {
        searching.value = false
    }
}


const searchByIsbn = async () => {
    if (!isbnForm.isbn.trim()) return
    
    searching.value = true
    searchPerformed.value = true
    lastQuery.value = isbnForm.isbn
    
    try {
        const response = await fetch('/api/search/isbn', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json'
            },
            body: JSON.stringify(isbnForm)
        })
        
        const data = await response.json()
        
        if (response.ok) {
            searchResults.value = data.books || []
        } else {
            console.error('API Error:', data)
            searchResults.value = []
        }
    } catch (error) {
        console.error('Błąd wyszukiwania:', error)
        searchResults.value = []
    } finally {
        searching.value = false
    }
}


const showAddModal = (book) => {
    if (showDetailsModalFlag.value) {
        showDetailsModalFlag.value = false
    }
    selectedBook.value = book
    showModal.value = true
}

const showDetailsModal = (book) => {
    selectedBook.value = book
    showDetailsModalFlag.value = true
}

const closeModal = () => {
    showModal.value = false
    selectedBook.value = null
}

const closeDetailsModal = () => {
    showDetailsModalFlag.value = false
    selectedBook.value = null
}

const handleBookAdded = () => {

    if (selectedBook.value) {
        selectedBook.value.in_library = true
    }
    closeModal()
}


const clearResults = () => {
    searchResults.value = []
    searchPerformed.value = false
    lastQuery.value = ''
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

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>