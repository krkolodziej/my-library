<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Moja Biblioteka
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Powitanie użytkownika -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900 mb-2">
                                    Witaj, {{ $page.props.auth.user.name }}! 👋
                                </h1>
                                <p class="text-gray-600">
                                    Zarządzaj swoją osobistą biblioteką książek
                                </p>
                            </div>
                            <div class="hidden sm:block">
            
                                <Link
                                        :href="route('books.create')"
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg inline-flex items-center"
                                    >
                                        <svg class="mr-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Dodaj książkę
                                    </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statystyki -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            Wszystkie książki
                                        </dt>
                                        <dd class="text-lg font-medium text-gray-900">
                                            {{ stats.totalBooks }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            Przeczytane
                                        </dt>
                                        <dd class="text-lg font-medium text-gray-900">
                                            {{ stats.readBooks }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            Czytam teraz
                                        </dt>
                                        <dd class="text-lg font-medium text-gray-900">
                                            {{ stats.currentlyReading }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            Do przeczytania
                                        </dt>
                                        <dd class="text-lg font-medium text-gray-900">
                                            {{ stats.toRead }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Główna zawartość -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Ostatnio dodane książki -->
                    <div class="lg:col-span-2">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-medium text-gray-900">
                                        Ostatnio dodane książki
                                    </h3>
                                    <Link 
                                        href="/books" 
                                        class="text-sm text-blue-600 hover:text-blue-500"
                                    >
                                        Zobacz wszystkie
                                    </Link>
                                </div>
                                
                                <div v-if="recentBooks.length > 0" class="space-y-4">
                                    <div 
                                        v-for="book in recentBooks" 
                                        :key="book.id"
                                        class="flex items-center space-x-4 p-3 bg-gray-50 rounded-lg"
                                    >
                                        <div class="flex-shrink-0">
                                            <div class="w-12 h-16 bg-gray-300 rounded flex items-center justify-center">
                                                <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                {{ book.title }}
                                            </p>
                                            <p class="text-sm text-gray-500 truncate">
                                                {{ book.author }}
                                            </p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <span 
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                :class="getStatusClass(book.status)"
                                            >
                                                {{ getStatusText(book.status) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div v-else class="text-center py-8">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">Brak książek</h3>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Zacznij budować swoją bibliotekę dodając pierwszą książkę.
                                    </p>
                                    <div class="mt-6">
                                        <Link
                                            :href="route('books.create')"
                                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
                                        >
                                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            Dodaj pierwszą książkę
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Boczny panel -->
                    <div class="space-y-6">
                        <!-- Szybkie akcje -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">
                                    Szybkie akcje
                                </h3>
                                <div class="space-y-3">
                                    <Link
                                        :href="route('books.create')"
                                        class="w-full flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-md"
                                    >
                                        <svg class="mr-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Dodaj książkę
                                    </Link>
                                    <Link 
                                        href="/books"
                                        class="w-full flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-md"
                                    >
                                        <svg class="mr-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                        Przeglądaj bibliotekę
                                    </Link>
                                    <Link 
                                        href="/search"
                                        class="w-full flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-md"
                                    >
                                        <svg class="mr-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16l2.879-2.879m0 0a3 3 0 104.243-4.242 3 3 0 00-4.243 4.242zM21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Wyszukaj książki
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <!-- Cel czytania -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">
                                    Cel na 2025 rok
                                </h3>
                                <div class="text-center">
                                    <div class="text-3xl font-bold text-blue-600 mb-2">
                                        {{ stats.readBooks }}/{{ readingGoal }}
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                                        <div 
                                            class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                            :style="{ width: `${Math.min(100, (stats.readBooks / readingGoal) * 100)}%` }"
                                        ></div>
                                    </div>
                                    <p class="text-sm text-gray-500">
                                        {{ Math.round((stats.readBooks / readingGoal) * 100) }}% ukończone
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

// Dane z API
const stats = ref({
    totalBooks: 0,
    readBooks: 0,
    currentlyReading: 0,
    toRead: 0
})

const recentBooks = ref([])

// Pobierz dane przy załadowaniu komponentu
const fetchLibraryStats = async () => {
    try {
        const response = await fetch('/api/books/stats')
        const data = await response.json()
        stats.value = data.stats
        recentBooks.value = data.recentBooks
    } catch (error) {
        console.error('Błąd podczas pobierania statystyk:', error)
    }
}

// Załaduj dane przy pierwszym wyświetleniu
onMounted(() => {
    fetchLibraryStats()
})

const readingGoal = ref(12) // 12 książek na rok

// Funkcje pomocnicze dla statusów
const getStatusClass = (status) => {
    const classes = {
        'read': 'bg-green-100 text-green-800',
        'reading': 'bg-yellow-100 text-yellow-800',
        'to_read': 'bg-purple-100 text-purple-800'
    }
    return classes[status] || 'bg-gray-100 text-gray-800'
}

const getStatusText = (status) => {
    const texts = {
        'read': 'Przeczytana',
        'reading': 'Czytam',
        'to_read': 'Do przeczytania'
    }
    return texts[status] || 'Nieznany status'
}
</script>