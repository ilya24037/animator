<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import InputError from '@/Components/InputError.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  agree: false,
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

    <div class="max-w-md w-full mx-auto bg-white p-8 rounded-2xl shadow-lg">
      <h1 class="text-3xl font-bold text-center mb-6">Регистрация</h1>

      <form @submit.prevent="submit" class="space-y-4">
        <!-- Почта -->
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Почта</label>
          <input
            id="email"
            type="email"
            v-model="form.email"
            required
            class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
          <InputError class="mt-1" :message="form.errors.email" />
        </div>

        <!-- Логин -->
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Логин</label>
          <input
            id="name"
            type="text"
            v-model="form.name"
            required
            class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
          <InputError class="mt-1" :message="form.errors.name" />
        </div>

        <!-- Пароль -->
        <div>
          <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Пароль</label>
          <input
            id="password"
            type="password"
            v-model="form.password"
            required
            class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
          <InputError class="mt-1" :message="form.errors.password" />
        </div>

        <!-- Повторите пароль -->
        <div>
          <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Повторите пароль</label>
          <input
            id="password_confirmation"
            type="password"
            v-model="form.password_confirmation"
            required
            class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
          <InputError class="mt-1" :message="form.errors.password_confirmation" />
        </div>

        <!-- Чекбокс согласия -->
        <div class="flex items-center">
          <input
            id="agree"
            type="checkbox"
            v-model="form.agree"
            class="h-4 w-4 text-blue-600 border-gray-300 rounded"
          />
          <label for="agree" class="ml-2 block text-sm text-gray-700">
            Я согласен на обработку персональных данных
          </label>
        </div>

        <!-- Кнопка и ссылка -->
        <div class="flex items-center justify-between mt-4">
          <Link href="/login" class="text-sm text-blue-600 hover:underline">
            Уже зарегистрированы?
          </Link>

          <PrimaryButton :disabled="form.processing || !form.agree">
            Зарегистрироваться
          </PrimaryButton>
        </div>
      </form>
    </div>
  </GuestLayout>
</template>
