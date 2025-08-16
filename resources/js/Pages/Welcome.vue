<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

function handleImageError() {
    document.getElementById('screenshot-container')?.classList.add('!hidden');
    document.getElementById('docs-card')?.classList.add('!row-span-1');
    document.getElementById('docs-card-content')?.classList.add('!flex-row');
    document.getElementById('background')?.classList.add('!hidden');
}
</script>

<template>
    <Head title="Moja Biblioteka - Organizuj swoje książki" />
    <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
        <div
            class="relative flex min-h-screen flex-col items-center justify-center selection:bg-blue-600 selection:text-white"
        >
            <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                <header
                    class="grid grid-cols-2 items-center gap-2 py-10 lg:grid-cols-3"
                >
                    <div class="flex lg:col-start-2 lg:justify-center">
                        <div class="flex items-center gap-3">
                            <svg 
                                class="h-12 w-auto text-blue-600 lg:h-16" 
                                fill="currentColor" 
                                viewBox="0 0 24 24"
                            >
                                <path d="M6 2c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 2 2h10v-2H6V4h7V2H6zm13 7h-2v2h2V9zm0 4h-2v2h2v-2zm-2-8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm2 12v-2h-4v2h4z"/>
                            </svg>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                                Moja Biblioteka
                            </h1>
                        </div>
                    </div>
                    <nav v-if="canLogin" class="-mx-3 flex flex-1 justify-end">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('dashboard')"
                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-blue-600 dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                        >
                            Dashboard
                        </Link>

                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-blue-600 dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                            >
                                Zaloguj się
                            </Link>

                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-blue-600 dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                            >
                                Zarejestruj się
                            </Link>
                        </template>
                    </nav>
                </header>

                <main class="mt-6">
                    <div class="text-center mb-12">
                        <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                            Zarządzaj swoją biblioteczką
                        </h2>
                        <p class="text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                            Organizuj, śledź i odkrywaj nowe książki. Twoja osobista biblioteka w jednym miejscu.
                        </p>
                    </div>

                    <div class="grid gap-6 lg:grid-cols-2 lg:gap-8">
                        <div class="flex flex-col items-start gap-6 overflow-hidden rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] md:row-span-3 lg:p-10 lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800">
                            <div class="relative flex w-full flex-1 items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-[10px] p-8">
                                <svg class="h-24 w-24 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                                </svg>
                            </div>

                            <div class="relative flex items-center gap-6 lg:items-end">
                                <div class="flex items-start gap-6 lg:flex-col">
                                    <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-blue-600/10 sm:size-16">
                                        <svg class="size-5 sm:size-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                        </svg>
                                    </div>

                                    <div class="pt-3 sm:pt-5 lg:pt-0">
                                        <h3 class="text-xl font-semibold text-black dark:text-white">
                                            Śledź postępy
                                        </h3>

                                        <p class="mt-4 text-sm/relaxed">
                                            Oznaczaj książki jako przeczytane, obecnie czytane lub do przeczytania. 
                                            Dodawaj oceny i notatki, aby pamiętać o swoich wrażeniach.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800">
                            <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-green-600/10 sm:size-16">
                                <svg class="size-5 sm:size-6 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                                </svg>
                            </div>

                            <div class="pt-3 sm:pt-5">
                                <h3 class="text-xl font-semibold text-black dark:text-white">
                                    Google Books API
                                </h3>

                                <p class="mt-4 text-sm/relaxed">
                                    Wyszukuj książki w ogromnej bazie Google Books. 
                                    Dodawaj je automatycznie do swojej biblioteki z pełnymi metadanymi i okładkami.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800">
                            <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-purple-600/10 sm:size-16">
                                <svg class="size-5 sm:size-6 text-purple-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17 3H7c-1.1 0-1.99.9-1.99 2L5 21l7-3 7 3V5c0-1.1-.9-2-2-2z"/>
                                </svg>
                            </div>

                            <div class="pt-3 sm:pt-5">
                                <h3 class="text-xl font-semibold text-black dark:text-white">
                                    Organizuj kolekcję
                                </h3>

                                <p class="mt-4 text-sm/relaxed">
                                    Twoja osobista biblioteka z intuicyjnym interfejsem. 
                                    Przeglądaj, sortuj i filtruj swoje książki według różnych kryteriów.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800">
                            <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-indigo-600/10 sm:size-16">
                                <svg class="size-5 sm:size-6 text-indigo-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 11H7v6h2v-6zm4 0h-2v6h2v-6zm4 0h-2v6h2v-6zm2.5-9H19V1h-1.5v1h-11V1H5v1H3.5C2.67 2 2 2.67 2 3.5v16C2 20.33 2.67 21 3.5 21h17c.83 0 1.5-.67 1.5-1.5v-16C22 2.67 21.33 2 20.5 2z"/>
                                </svg>
                            </div>

                            <div class="pt-3 sm:pt-5">
                                <h3 class="text-xl font-semibold text-black dark:text-white">
                                    Statystyki czytania
                                </h3>

                                <p class="mt-4 text-sm/relaxed">
                                    Śledź swoje nawyki czytelnicze z pomocą szczegółowych statystyk. 
                                    Zobacz ile książek przeczytałeś i jak rozwija się Twoja biblioteka.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div v-if="!$page.props.auth.user" class="mt-12 text-center">
                        <div class="inline-flex gap-4">
                            <Link
                                :href="route('register')"
                                class="rounded-md bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                            >
                                Rozpocznij za darmo
                            </Link>
                            <Link
                                :href="route('login')"
                                class="rounded-md border border-gray-300 px-6 py-3 text-sm font-semibold text-gray-900 shadow-sm hover:bg-gray-50 dark:border-gray-600 dark:text-white dark:hover:bg-gray-800"
                            >
                                Mam już konto
                            </Link>
                        </div>
                    </div>
                </main>

                <footer class="py-16 text-center text-sm text-black dark:text-white/70">
                    Laravel v{{ laravelVersion }} (PHP v{{ phpVersion }})
                </footer>
            </div>
        </div>
    </div>
</template>
