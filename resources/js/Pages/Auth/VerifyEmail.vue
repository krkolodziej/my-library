<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <GuestLayout>
        <Head title="Weryfikacja e-mail - Moja Biblioteka" />

        <div class="mb-6 text-center">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Potwierdź swój adres e-mail</h1>
            <p class="text-gray-600">Ostatni krok do aktywacji Twojego konta</p>
        </div>

        <div class="mb-4 text-sm text-gray-600">
            Dziękujemy za rejestrację! Przed rozpoczęciem korzystania z aplikacji,
            prosimy o weryfikację adresu e-mail poprzez kliknięcie w link, który
            właśnie do Ciebie wysłaliśmy. Jeśli nie otrzymałeś wiadomości, chętnie
            wyślemy Ci kolejną.
        </div>

        <div
            class="mb-4 text-sm font-medium text-green-600"
            v-if="verificationLinkSent"
        >
            Nowy link weryfikacyjny został wysłany na adres e-mail podany podczas
            rejestracji.
        </div>

        <form @submit.prevent="submit">
            <div class="mt-6 space-y-4">
                <PrimaryButton
                    class="w-full justify-center"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">Wysyłanie...</span>
                    <span v-else>Wyślij ponownie link weryfikacyjny</span>
                </PrimaryButton>

                <div class="text-center">
                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded"
                    >
                        Wyloguj się
                    </Link>
                </div>
            </div>
        </form>
    </GuestLayout>
</template>
