<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/inertia-vue3';
import { ref } from 'vue';

const nameInput = ref(null);
const addressInput = ref(null);

const form = useForm({
    name: '',
    address: '',
});

const storeMonitor = () => {
    form.post(route('monitor.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
        onError: () => {
            if (form.errors.name) {
                form.reset('password', 'password_confirmation');
                nameInput.value.focus();
            }
            if (form.errors.address) {
                form.reset('current_password');
                addressInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-slate-400">Add monitor</h2>
        </header>

        <form @submit.prevent="storeMonitor" class="grid grid-cols-2 gap-4">
            <div>
                <InputLabel for="name" value="Name" />

                <TextInput
                    id="name"
                    type="text"
                    ref="nameInput"
                    class="mt-1 block w-full"
                    placeholder="Website Name"
                    v-model="form.name"
                    required
                    autofocus
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="address" value="Address" />

                <TextInput
                    id="address"
                    type="text"
                    ref="addressInput"
                    class="mt-1 block w-full"
                    placeholder="example.com"
                    v-model="form.address"
                    required
                />

                <InputError class="mt-2" :message="form.errors.address" />
            </div>

            <div class="flex items-center gap-4 col-start-1 col-end-2">
                <PrimaryButton :disabled="form.processing">Create</PrimaryButton>

                <Transition enter-from-class="opacity-0" leave-to-class="opacity-0" class="transition ease-in-out">
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Saved.</p>
                </Transition>
            </div>
        </form>
    </section>
</template>