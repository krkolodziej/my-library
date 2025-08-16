<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Resetowanie hasła - Moja Biblioteka" />

        <div class="mb-6 text-center">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Zapomniałeś hasła?</h1>
            <p class="text-gray-600">Nie ma problemu, pomożemy Ci je odzyskać</p>
        </div>

        <div class="mb-4 text-sm text-gray-600">
            Podaj swój adres e-mail, a wyślemy Ci link do resetowania hasła,
            który pozwoli Ci ustawić nowe hasło.
        </div>

        <div
            v-if="status"
            class="mb-4 text-sm font-medium text-green-600"
        >
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Adres e-mail" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="wprowadź swój adres e-mail"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-6">
                <PrimaryButton
                    class="w-full justify-center"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">Wysyłanie...</span>
                    <span v-else>Wyślij link resetujący hasło</span>
                </PrimaryButton>
            </div>

            <div class="mt-4 text-center">
                <div class="text-sm text-gray-600">
                    Pamiętasz hasło? 
                    <Link
                        :href="route('login')"
                        class="text-blue-600 underline hover:text-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded"
                    >
                        Zaloguj się
                    </Link>
                </div>
            </div>
        </form>
    </GuestLayout>
</template>
