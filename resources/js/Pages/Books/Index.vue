<template>
    <Head title="Moja Biblioteka" />
    
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Moja Biblioteka
                </h2>
                <Link 
                    :href="route('books.create')" 
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg inline-flex items-center"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Dodaj książkę
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8"> 
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="md:col-span-2">
                                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">
                                    Wyszukaj książkę
                                </label>
                                <input
                                    id="search"
                                    v-model="searchForm.search"
                                    type="text"
                                    placeholder="Tytuł lub autor..."
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    @input="debouncedSearch"
                                />
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                                    Status
                                </label>
                                <select
                                    id="status"
                                    v-model="searchForm.status"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    @change="applyFilters"
                                >
                                    <option value="">Wszystkie</option>
                                    <option v-for="(label, value) in statuses" :key="value" :value="value">
                                        {{ label }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">
                                    Sortuj według
                                </label>
                                <select
                                    id="sort"
                                    v-model="searchForm.sort"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    @change="applyFilters"
                                >
                                    <option value="created_at">Data dodania</option>
                                    <option value="title">Tytuł</option>
                                    <option value="author">Autor</option>
                                    <option value="rating">Ocena</option>
                                    <option value="published_year">Rok wydania</option>
                                </select>
                            </div>
                        </div>

                        <div v-if="hasActiveFilters" class="mt-4 flex flex-wrap gap-2">
                            <span class="text-sm text-gray-600">Aktywne filtry:</span>
                            <span 
                                v-if="filters.search"
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                            >
                                Wyszukiwanie: {{ filters.search }}
                                <button @click="clearSearch" class="ml-1 text-blue-600 hover:text-blue-800">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </span>
                            <span 
                                v-if="filters.status"
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"
                            >
                                Status: {{ statuses[filters.status] }}
                                <button @click="clearStatus" class="ml-1 text-green-600 hover:text-green-800">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </span>
                            <button 
                                @click="clearAllFilters"
                                class="text-xs text-gray-500 hover:text-gray-700 underline"
                            >
                                Wyczyść wszystkie
                            </button>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div v-if="books.data.length > 0">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                                <div 
                                    v-for="book in books.data" 
                                    :key="book.id"
                                    class="bg-white border border-gray-200 rounded-lg shadow hover:shadow-md transition-shadow duration-200"
                                >
                                    <div class="p-6">
                                        <div class="flex items-start space-x-4">
                                            <div class="flex-shrink-0">
                                                <div class="w-16 h-24 bg-gray-300 rounded flex items-center justify-center">
                                                    <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                    </svg>
                                                </div>
                                            </div>

                                            <div class="flex-1 min-w-0">
                                                <Link 
                                                    :href="route('books.show', book.id)"
                                                    class="text-lg font-semibold text-gray-900 hover:text-blue-600 block truncate"
                                                >
                                                    {{ book.title }}
                                                </Link>
                                                <p class="text-sm text-gray-600 truncate">{{ book.author }}</p>
                                                
                                                <div class="mt-2">
                                                    <span 
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                        :class="getStatusClass(book.status)"
                                                    >
                                                        {{ book.status_label }}
                                                    </span>
                                                </div>

                                                <div v-if="book.rating" class="mt-2 flex items-center">
                                                    <div class="flex items-center">
                                                        <svg 
                                                            v-for="i in 5" 
                                                            :key="i"
                                                            class="w-4 h-4"
                                                            :class="i <= book.rating ? 'text-yellow-400' : 'text-gray-300'"
                                                            fill="currentColor" 
                                                            viewBox="0 0 20 20"
                                                        >
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                        </svg>
                                                    </div>
                                                    <span class="ml-1 text-sm text-gray-600">{{ book.rating }}/5</span>
                                                </div>

                                                <div class="mt-3 flex space-x-2">
                                                    <Link 
                                                        :href="route('books.show', book.id)"
                                                        class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-800 px-2 py-1 rounded"
                                                    >
                                                        Zobacz
                                                    </Link>
                                                    <Link 
                                                        :href="route('books.edit', book.id)"
                                                        class="text-xs bg-blue-100 hover:bg-blue-200 text-blue-800 px-2 py-1 rounded"
                                                    >
                                                        Edytuj
                                                    </Link>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-if="books.links.length > 3" class="flex justify-center">
                                <nav class="flex items-center space-x-1">
                                    <component
                                        v-for="(link, index) in books.links"
                                        :key="index"
                                        :is="link.url ? 'Link' : 'span'"
                                        :href="link.url"
                                        v-html="link.label"
                                        class="px-3 py-2 text-sm leading-tight text-gray-500 bg-white border border-gray-300"
                                        :class="{
                                            'hover:bg-gray-100 hover:text-gray-700': link.url,
                                            'bg-blue-50 border-blue-500 text-blue-600': link.active,
                                            'cursor-not-allowed opacity-50': !link.url,
                                        }"
                                    />
                                </nav>
                            </div>
                        </div>

                        <div v-else class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <h3 class="mt-2 text-lg font-medium text-gray-900">
                                {{ hasActiveFilters ? 'Brak wyników' : 'Brak książek' }}
                            </h3>
                            <p class="mt-1 text-gray-500">
                                {{ hasActiveFilters ? 'Spróbuj zmienić filtry wyszukiwania.' : 'Zacznij budować swoją bibliotekę dodając pierwszą książkę.' }}
                            </p>
                            <div class="mt-6">
                                <Link 
                                    v-if="!hasActiveFilters"
                                    :href="route('books.create')"
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
                                >
                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Dodaj pierwszą książkę
                                </Link>
                                <button 
                                    v-else
                                    @click="clearAllFilters"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    Wyczyść filtry
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { debounce } from 'lodash'

const props = defineProps({
    books: Object,
    filters: Object,
    statuses: Object,
})

const searchForm = ref({
    search: props.filters.search || '',
    status: props.filters.status || '',
    sort: props.filters.sort || 'created_at',
    direction: props.filters.direction || 'desc',
})

const hasActiveFilters = computed(() => {
    return props.filters.search || props.filters.status
})

const debouncedSearch = debounce(() => {
    applyFilters()
}, 300)

const applyFilters = () => {
    router.get(route('books.index'), searchForm.value, {
        preserveState: true,
        preserveScroll: true,
    })
}

const clearSearch = () => {
    searchForm.value.search = ''
    applyFilters()
}

const clearStatus = () => {
    searchForm.value.status = ''
    applyFilters()
}

const clearAllFilters = () => {
    searchForm.value = {
        search: '',
        status: '',
        sort: 'created_at',
        direction: 'desc',
    }
    applyFilters()
}

const getStatusClass = (status) => {
    const classes = {
        'read': 'bg-green-100 text-green-800',
        'reading': 'bg-yellow-100 text-yellow-800',
        'to_read': 'bg-purple-100 text-purple-800'
    }
    return classes[status] || 'bg-gray-100 text-gray-800'
}

watch(() => props.filters, (newFilters) => {
    searchForm.value = {
        search: newFilters.search || '',
        status: newFilters.status || '',
        sort: newFilters.sort || 'created_at',
        direction: newFilters.direction || 'desc',
    }
}, { immediate: true })
</script>