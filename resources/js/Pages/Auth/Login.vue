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
    <Head title="Register" />

    <form @submit.prevent="submit" class="mt-8 space-y-6">
      <!-- ЛОГИН -->
      <div>
        <InputLabel for="name" value="Логин" />
        <TextInput
          id="name"
          type="text"
          v-model="form.name"
          required
          autofocus
          class="mt-1 block w-full"
          autocomplete="name"
        />
        <InputError class="mt-2" :message="form.errors.name" />
      </div>

      <!-- EMAIL -->
      <div>
        <InputLabel for="email" value="Email" />
        <TextInput
          id="email"
          type="email"
          v-model="form.email"
          required
          class="mt-1 block w-full"
          autocomplete="username"
        />
        <InputError class="mt-2" :message="form.errors.email" />
      </div>

      <!-- ПАРОЛЬ -->
      <div>
        <InputLabel for="password" value="Пароль" />
        <TextInput
          id="password"
          type="password"
          v-model="form.password"
          required
          class="mt-1 block w-full"
          autocomplete="new-password"
        />
        <InputError class="mt-2" :message="form.errors.password" />
      </div>

      <!-- ПОДТВЕРЖДЕНИЕ ПАРОЛЯ -->
      <div>
        <InputLabel for="password_confirmation" value="Подтвердите пароль" />
        <TextInput
          id="password_confirmation"
          type="password"
          v-model="form.password_confirmation"
          required
          class="mt-1 block w-full"
          autocomplete="new-password"
        />
        <InputError class="mt-2" :message="form.errors.password_confirmation" />
      </div>

      <!-- КНОПКА -->
      <div class="flex items-center justify-between mt-6">
        <Link href="/login" class="text-sm text-blue-600 hover:underline">
          Уже зарегистрированы?
        </Link>
        <PrimaryButton :disabled="form.processing">
          Зарегистрироваться
        </PrimaryButton>
      </div>
    </form>
  </GuestLayout>
</template>
