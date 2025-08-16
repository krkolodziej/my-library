<template>
    <Head :title="book.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <Link 
                        :href="route('books.index')"
                        class="text-gray-600 hover:text-gray-900"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </Link>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ book.title }}
                    </h2>
                </div>
                <div class="flex space-x-2">
                    <Link 
                        :href="route('books.edit', book.id)"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg"
                    >
                        Edytuj
                    </Link>
                    <button
                        @click="showDeleteModal = true"
                        class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg"
                    >
                        Usuń
                    </button>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                            <div class="lg:col-span-1">
                                <div class="w-full max-w-sm mx-auto">
                                    <div class="aspect-[3/4] bg-gray-300 rounded-lg flex items-center justify-center mb-4">
                                        <svg class="w-16 h-16 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                    </div>
                                </div>

                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h3 class="text-sm font-medium text-gray-900 mb-3">Status książki</h3>
                                    <form @submit.prevent="updateStatus">
                                        <select 
                                            v-model="statusForm.status"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 mb-3"
                                        >
                                            <option 
                                                v-for="(label, value) in statuses" 
                                                :key="value" 
                                                :value="value"
                                            >
                                                {{ label }}
                                            </option>
                                        </select>
                                        
                                        <div v-if="statusForm.status === 'read'" class="mb-3">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                Ocena
                                            </label>
                                            <select 
                                                v-model="statusForm.rating"
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            >
                                                <option value="">Brak oceny</option>
                                                <option value="1">⭐ 1/5</option>
                                                <option value="2">⭐⭐ 2/5</option>
                                                <option value="3">⭐⭐⭐ 3/5</option>
                                                <option value="4">⭐⭐⭐⭐ 4/5</option>
                                                <option value="5">⭐⭐⭐⭐⭐ 5/5</option>
                                            </select>
                                        </div>
                                        
                                        <button 
                                            type="submit"
                                            :disabled="statusForm.processing"
                                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg disabled:opacity-50"
                                        >
                                            {{ statusForm.processing ? 'Zapisywanie...' : 'Zaktualizuj status' }}
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="lg:col-span-2">
                                <div class="space-y-6">     
                                    <div>
                                        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ book.title }}</h1>
                                        <p class="text-xl text-gray-600 mb-4">{{ book.author }}</p>
                                        
                                        <div class="flex items-center space-x-4 mb-4">
                                            <span 
                                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                                                :class="getStatusClass(book.status)"
                                            >
                                                {{ book.status_label }}
                                            </span>
                                            
                                            <div v-if="book.rating" class="flex items-center">
                                                <div class="flex items-center mr-2">
                                                    <svg 
                                                        v-for="i in 5" 
                                                        :key="i"
                                                        class="w-5 h-5"
                                                        :class="i <= book.rating ? 'text-yellow-400' : 'text-gray-300'"
                                                        fill="currentColor" 
                                                        viewBox="0 0 20 20"
                                                    >
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                </div>
                                                <span class="text-gray-600">{{ book.rating }}/5</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-if="book.description">
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">Opis</h3>
                                        <p class="text-gray-700 leading-relaxed">{{ book.description }}</p>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div class="bg-gray-50 p-4 rounded-lg">
                                            <h3 class="text-lg font-medium text-gray-900 mb-3">Szczegóły</h3>
                                            <dl class="space-y-2">
                                                <div v-if="book.isbn">
                                                    <dt class="text-sm font-medium text-gray-500">ISBN</dt>
                                                    <dd class="text-sm text-gray-900">{{ book.isbn }}</dd>
                                                </div>
                                                <div v-if="book.pages">
                                                    <dt class="text-sm font-medium text-gray-500">Liczba stron</dt>
                                                    <dd class="text-sm text-gray-900">{{ book.pages }}</dd>
                                                </div>
                                                <div v-if="book.published_year">
                                                    <dt class="text-sm font-medium text-gray-500">Rok wydania</dt>
                                                    <dd class="text-sm text-gray-900">{{ book.published_year }}</dd>
                                                </div>
                                                <div>
                                                    <dt class="text-sm font-medium text-gray-500">Dodano do biblioteki</dt>
                                                    <dd class="text-sm text-gray-900">{{ formatDate(book.created_at) }}</dd>
                                                </div>
                                            </dl>
                                        </div>

                                        <div class="bg-gray-50 p-4 rounded-lg">
                                            <h3 class="text-lg font-medium text-gray-900 mb-3">Historia czytania</h3>
                                            <dl class="space-y-2">
                                                <div v-if="book.started_reading_at">
                                                    <dt class="text-sm font-medium text-gray-500">Rozpoczęto czytanie</dt>
                                                    <dd class="text-sm text-gray-900">{{ formatDate(book.started_reading_at) }}</dd>
                                                </div>
                                                <div v-if="book.finished_reading_at">
                                                    <dt class="text-sm font-medium text-gray-500">Ukończono czytanie</dt>
                                                    <dd class="text-sm text-gray-900">{{ formatDate(book.finished_reading_at) }}</dd>
                                                </div>
                                                <div v-if="book.reading_duration">
                                                    <dt class="text-sm font-medium text-gray-500">Czas czytania</dt>
                                                    <dd class="text-sm text-gray-900">{{ book.reading_duration }} dni</dd>
                                                </div>
                                            </dl>
                                        </div>
                                    </div>

                                    <div v-if="book.notes">
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">Moje notatki</h3>
                                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                            <p class="text-gray-700 whitespace-pre-wrap">{{ book.notes }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal usuwania -->
        <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <svg class="mx-auto h-12 w-12 text-red-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Usuń książkę</h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Czy na pewno chcesz usunąć książkę "{{ book.title }}"? Ta akcja jest nieodwracalna.
                    </p>
                    <div class="flex justify-center space-x-4">
                        <button 
                            @click="showDeleteModal = false"
                            class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400"
                        >
                            Anuluj
                        </button>
                        <button 
                            @click="deleteBook"
                            :disabled="deleteForm.processing"
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 disabled:opacity-50"
                        >
                            {{ deleteForm.processing ? 'Usuwanie...' : 'Usuń' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    book: Object,
    statuses: {
        type: Object,
        default: () => ({
            'to_read': 'Do przeczytania',
            'reading': 'Czytam teraz',
            'read': 'Przeczytana'
        })
    }
})

const showDeleteModal = ref(false)

const statusForm = useForm({
    status: props.book.status,
    rating: props.book.rating
})

const deleteForm = useForm({})

const updateStatus = () => {
    statusForm.patch(route('books.update-status', props.book.id), {
        onSuccess: () => {
        }
    })
}

const deleteBook = () => {
    deleteForm.delete(route('books.destroy', props.book.id), {
        onSuccess: () => {
            showDeleteModal.value = false
        }
    })
}

const getStatusClass = (status) => {
    const classes = {
        'read': 'bg-green-100 text-green-800',
        'reading': 'bg-yellow-100 text-yellow-800',
        'to_read': 'bg-purple-100 text-purple-800'
    }
    return classes[status] || 'bg-gray-100 text-gray-800'
}

const formatDate = (dateString) => {
    if (!dateString) return ''
    const date = new Date(dateString)
    return date.toLocaleDateString('pl-PL', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}
</script>