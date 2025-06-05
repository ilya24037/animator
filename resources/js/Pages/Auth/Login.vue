<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import InputError from '@/Components/InputError.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

const submit = () => {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  })
}
</script>

<template>
  <GuestLayout>
    <Head title="Вход" />
    <h1 class="text-2xl font-bold mb-8 text-center">Вход в аккаунт</h1>
    <form @submit.prevent="submit" class="space-y-6">
      <div>
        <label for="email" class="block mb-1 text-gray-700 font-medium">Email</label>
        <TextInput
          id="email"
          type="email"
          v-model="form.email"
          required
          autofocus
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
          autocomplete="current-password"
          class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring focus:border-blue-500"
        />
        <InputError :message="form.errors.password" class="mt-2" />
      </div>

      <div class="flex items-center justify-between text-sm">
        <label class="flex items-center select-none">
          <input type="checkbox" v-model="form.remember" class="mr-2 rounded" />Запомнить меня
        </label>
        <Link
          :href="route('password.request')"
          class="text-blue-600 hover:underline"
        >
          Забыли пароль?
        </Link>
      </div>

      <PrimaryButton
        class="w-full py-3 mt-4 rounded-lg font-bold bg-blue-600 text-white hover:bg-blue-700 transition"
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
      >
        Войти
      </PrimaryButton>
    </form>
    <div class="mt-8 text-center text-sm text-gray-500">
      Нет аккаунта?
      <Link :href="route('register')" class="text-blue-600 hover:underline">Зарегистрироваться</Link>
    </div>
  </GuestLayout>
</template>
