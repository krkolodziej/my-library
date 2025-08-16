<template>
    <Head :title="`Edytuj: ${book.title}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <Link 
                        :href="route('books.show', book.id)"
                        class="text-gray-600 hover:text-gray-900"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </Link>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Edytuj książkę
                    </h2>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Podstawowe informacje -->
                            <div class="border-b border-gray-200 pb-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Podstawowe informacje</h3>
                                
                                <div class="grid grid-cols-1 gap-6">
                                    <!-- Tytuł -->
                                    <div>
                                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                                            Tytuł książki *
                                        </label>
                                        <input
                                            id="title"
                                            v-model="form.title"
                                            type="text"
                                            required
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            :class="{ 'border-red-500': form.errors.title }"
                                            placeholder="Wpisz tytuł książki"
                                        />
                                        <div v-if="form.errors.title" class="mt-1 text-sm text-red-600">
                                            {{ form.errors.title }}
                                        </div>
                                    </div>

                                    <!-- Autor -->
                                    <div>
                                        <label for="author" class="block text-sm font-medium text-gray-700 mb-1">
                                            Autor *
                                        </label>
                                        <input
                                            id="author"
                                            v-model="form.author"
                                            type="text"
                                            required
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            :class="{ 'border-red-500': form.errors.author }"
                                            placeholder="Wpisz imię i nazwisko autora"
                                        />
                                        <div v-if="form.errors.author" class="mt-1 text-sm text-red-600">
                                            {{ form.errors.author }}
                                        </div>
                                    </div>

                                    <!-- ISBN -->
                                    <div>
                                        <label for="isbn" class="block text-sm font-medium text-gray-700 mb-1">
                                            ISBN
                                        </label>
                                        <input
                                            id="isbn"
                                            v-model="form.isbn"
                                            type="text"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            :class="{ 'border-red-500': form.errors.isbn }"
                                            placeholder="np. 978-83-240-2077-5"
                                        />
                                        <div v-if="form.errors.isbn" class="mt-1 text-sm text-red-600">
                                            {{ form.errors.isbn }}
                                        </div>
                                    </div>

                                    <!-- Opis -->
                                    <div>
                                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                            Opis
                                        </label>
                                        <textarea
                                            id="description"
                                            v-model="form.description"
                                            rows="4"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            :class="{ 'border-red-500': form.errors.description }"
                                            placeholder="Krótki opis książki..."
                                        ></textarea>
                                        <div v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                                            {{ form.errors.description }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Szczegóły techniczne -->
                            <div class="border-b border-gray-200 pb-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Szczegóły</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Liczba stron -->
                                    <div>
                                        <label for="pages" class="block text-sm font-medium text-gray-700 mb-1">
                                            Liczba stron
                                        </label>
                                        <input
                                            id="pages"
                                            v-model="form.pages"
                                            type="number"
                                            min="1"
                                            max="10000"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            :class="{ 'border-red-500': form.errors.pages }"
                                            placeholder="np. 320"
                                        />
                                        <div v-if="form.errors.pages" class="mt-1 text-sm text-red-600">
                                            {{ form.errors.pages }}
                                        </div>
                                    </div>

                                    <!-- Rok wydania -->
                                    <div>
                                        <label for="published_year" class="block text-sm font-medium text-gray-700 mb-1">
                                            Rok wydania
                                        </label>
                                        <input
                                            id="published_year"
                                            v-model="form.published_year"
                                            type="number"
                                            :min="1000"
                                            :max="new Date().getFullYear() + 1"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            :class="{ 'border-red-500': form.errors.published_year }"
                                            placeholder="np. 2023"
                                        />
                                        <div v-if="form.errors.published_year" class="mt-1 text-sm text-red-600">
                                            {{ form.errors.published_year }}
                                        </div>
                                    </div>
                                </div>

                                <!-- URL okładki -->
                                <div class="mt-6">
                                    <label for="cover_image" class="block text-sm font-medium text-gray-700 mb-1">
                                        Link do okładki
                                    </label>
                                    <input
                                        id="cover_image"
                                        v-model="form.cover_image"
                                        type="url"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :class="{ 'border-red-500': form.errors.cover_image }"
                                        placeholder="https://example.com/cover.jpg"
                                    />
                                    <div v-if="form.errors.cover_image" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.cover_image }}
                                    </div>
                                </div>
                            </div>

                            <!-- Status i historia czytania -->
                            <div class="border-b border-gray-200 pb-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Status i historia czytania</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Status -->
                                    <div>
                                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                                            Status *
                                        </label>
                                        <select
                                            id="status"
                                            v-model="form.status"
                                            required
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            :class="{ 'border-red-500': form.errors.status }"
                                        >
                                            <option value="">Wybierz status</option>
                                            <option 
                                                v-for="(label, value) in statuses" 
                                                :key="value" 
                                                :value="value"
                                            >
                                                {{ label }}
                                            </option>
                                        </select>
                                        <div v-if="form.errors.status" class="mt-1 text-sm text-red-600">
                                            {{ form.errors.status }}
                                        </div>
                                    </div>

                                    <!-- Ocena (tylko dla przeczytanych) -->
                                    <div v-if="form.status === 'read'">
                                        <label for="rating" class="block text-sm font-medium text-gray-700 mb-1">
                                            Ocena
                                        </label>
                                        <select
                                            id="rating"
                                            v-model="form.rating"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            :class="{ 'border-red-500': form.errors.rating }"
                                        >
                                            <option value="">Brak oceny</option>
                                            <option value="1">⭐ 1/5 - Słabo</option>
                                            <option value="2">⭐⭐ 2/5 - Przeciętnie</option>
                                            <option value="3">⭐⭐⭐ 3/5 - Dobrze</option>
                                            <option value="4">⭐⭐⭐⭐ 4/5 - Bardzo dobrze</option>
                                            <option value="5">⭐⭐⭐⭐⭐ 5/5 - Świetnie</option>
                                        </select>
                                        <div v-if="form.errors.rating" class="mt-1 text-sm text-red-600">
                                            {{ form.errors.rating }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Daty czytania -->
                                <div v-if="form.status === 'reading' || form.status === 'read'" class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                                    <!-- Data rozpoczęcia -->
                                    <div>
                                        <label for="started_reading_at" class="block text-sm font-medium text-gray-700 mb-1">
                                            Data rozpoczęcia czytania
                                        </label>
                                        <input
                                            id="started_reading_at"
                                            v-model="form.started_reading_at"
                                            type="date"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            :class="{ 'border-red-500': form.errors.started_reading_at }"
                                        />
                                        <div v-if="form.errors.started_reading_at" class="mt-1 text-sm text-red-600">
                                            {{ form.errors.started_reading_at }}
                                        </div>
                                    </div>

                                    <!-- Data ukończenia -->
                                    <div v-if="form.status === 'read'">
                                        <label for="finished_reading_at" class="block text-sm font-medium text-gray-700 mb-1">
                                            Data ukończenia czytania
                                        </label>
                                        <input
                                            id="finished_reading_at"
                                            v-model="form.finished_reading_at"
                                            type="date"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            :class="{ 'border-red-500': form.errors.finished_reading_at }"
                                        />
                                        <div v-if="form.errors.finished_reading_at" class="mt-1 text-sm text-red-600">
                                            {{ form.errors.finished_reading_at }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Notatki -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Moje notatki</h3>
                                
                                <div>
                                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                                        Notatki osobiste
                                    </label>
                                    <textarea
                                        id="notes"
                                        v-model="form.notes"
                                        rows="4"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :class="{ 'border-red-500': form.errors.notes }"
                                        placeholder="Twoje przemyślenia, recenzja, ciekawostki..."
                                    ></textarea>
                                    <div v-if="form.errors.notes" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.notes }}
                                    </div>
                                </div>
                            </div>

                            <!-- Przyciski -->
                            <div class="flex justify-end space-x-4 pt-6">
                                <Link 
                                    :href="route('books.show', book.id)"
                                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg"
                                >
                                    Anuluj
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg disabled:opacity-50 inline-flex items-center"
                                >
                                    <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    {{ form.processing ? 'Zapisywanie...' : 'Zapisz zmiany' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'


const props = defineProps({
    book: Object,
    statuses: Object
})


const formatDateForInput = (dateString) => {
    if (!dateString) return ''
    const date = new Date(dateString)
    return date.toISOString().split('T')[0]
}


const form = useForm({
    title: props.book.title || '',
    author: props.book.author || '',
    isbn: props.book.isbn || '',
    description: props.book.description || '',
    pages: props.book.pages || '',
    published_year: props.book.published_year || '',
    status: props.book.status || 'to_read',
    rating: props.book.rating || '',
    notes: props.book.notes || '',
    cover_image: props.book.cover_image || '',
    started_reading_at: formatDateForInput(props.book.started_reading_at),
    finished_reading_at: formatDateForInput(props.book.finished_reading_at)
})


const submit = () => {
    form.patch(route('books.update', props.book.id), {
        onSuccess: () => {
           
        }
    })
}
</script>