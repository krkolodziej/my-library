<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Potwierdź hasło - Moja Biblioteka" />

        <div class="mb-6 text-center">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Potwierdzenie hasła</h1>
            <p class="text-gray-600">Strefa bezpieczna aplikacji</p>
        </div>

        <div class="mb-4 text-sm text-gray-600">
            To jest bezpieczna strefa aplikacji. Proszę potwierdź swoje hasło,
            aby kontynuować.
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="password" value="Hasło" />
                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    autofocus
                    placeholder="wprowadź swoje hasło"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-6">
                <PrimaryButton
                    class="w-full justify-center"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">Potwierdzanie...</span>
                    <span v-else>Potwierdź</span>
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
