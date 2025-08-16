<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Rejestracja - Moja Biblioteka" />

        <div class="mb-6 text-center">
            <div class="flex items-center justify-center gap-3 mb-4">
                <svg class="h-8 w-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M6 2c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 2 2h10v-2H6V4h7V2H6zm13 7h-2v2h2V9zm0 4h-2v2h2v-2zm-2-8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm2 12v-2h-4v2h4z"/>
                </svg>
                <h1 class="text-2xl font-bold text-gray-800">Moja Biblioteka</h1>
            </div>
            <h2 class="text-xl font-semibold text-gray-800 mb-2">Utwórz konto</h2>
            <p class="text-gray-600">Rozpocznij organizację swojej biblioteki książek</p>
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Imię i nazwisko" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="wprowadź swoje imię i nazwisko"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Adres e-mail" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                    placeholder="wprowadź swój adres e-mail"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Hasło" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                    placeholder="minimum 8 znaków"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel
                    for="password_confirmation"
                    value="Powtórz hasło"
                />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="wprowadź hasło ponownie"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <div class="mt-6">
                <PrimaryButton
                    class="w-full justify-center"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">Tworzenie konta...</span>
                    <span v-else>Zarejestruj się</span>
                </PrimaryButton>
            </div>

            <div class="mt-6 text-center">
                <div class="text-sm text-gray-600">
                    Masz już konto w swojej bibliotece? 
                    <Link
                        :href="route('login')"
                        class="text-blue-600 underline hover:text-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded"
                    >
                        Zaloguj się tutaj
                    </Link>
                </div>
            </div>
        </form>
    </GuestLayout>
</template>
