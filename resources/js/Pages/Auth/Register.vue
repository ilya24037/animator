<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const submit = () => {
  form.post(route('register'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
  })
}
</script>

<template>
  <GuestLayout>
    <Head title="Регистрация" />

    <div class="w-full max-w-md mx-auto mt-16 bg-white rounded-2xl shadow-xl p-8">
      <h1 class="text-2xl font-bold mb-8 text-center">Регистрация</h1>
      <form @submit.prevent="submit" class="space-y-6">
        <div>
          <label for="name" class="block mb-1 text-gray-700 font-medium">Имя</label>
          <TextInput
            id="name"
            type="text"
            v-model="form.name"
            required
            autocomplete="name"
            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring focus:border-blue-500"
          />
          <InputError :message="form.errors.name" class="mt-2" />
        </div>

        <div>
          <label for="email" class="block mb-1 text-gray-700 font-medium">Email</label>
          <TextInput
            id="email"
            type="email"
            v-model="form.email"
            required
            autocomplete="username"
            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring focus:border-blue-500"
          />
          <InputError :message="form.errors.email" class="mt-2" />
        </div>

        <div>
          <label for="password" class="block mb-1 text-gray-700 font-medium">Пароль</label>
          <TextInput
            id="password"
            type="password"
            v-model="form.password"
            required
            autocomplete="new-password"
            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring focus:border-blue-500"
          />
          <InputError :message="form.errors.password" class="mt-2" />
        </div>

        <div>
          <label for="password_confirmation" class="block mb-1 text-gray-700 font-medium">Подтвердите пароль</label>
          <TextInput
            id="password_confirmation"
            type="password"
            v-model="form.password_confirmation"
            required
            autocomplete="new-password"
            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring focus:border-blue-500"
          />
        </div>

        <PrimaryButton
          class="w-full py-3 mt-4 rounded-lg font-bold bg-blue-600 text-white hover:bg-blue-700 transition"
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
        >
          Зарегистрироваться
        </PrimaryButton>
      </form>
      <div class="mt-8 text-center text-sm text-gray-500">
        Уже есть аккаунт?
        <Link :href="route('login')" class="text-blue-600 hover:underline">Войти</Link>
      </div>
    </div>
  </GuestLayout>
</template>
