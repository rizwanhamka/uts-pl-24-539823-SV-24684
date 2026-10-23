<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import http from '../api/http'

const router = useRouter()
const email = ref('')
const password = ref('')
const error = ref('')

async function login() {
  error.value = ''
  try {
    const { data } = await http.post('/login', { email: email.value, password: password.value })
    localStorage.setItem('token', data.token)
    router.push({ name: 'buku' })
  } catch (e) {
    error.value = e.response?.data?.message ?? 'Gagal terhubung ke server.'
  }
}
</script>

<template>
  <section>
    <h2>Login</h2>
    <form @submit.prevent="login">
      <input v-model="email" type="email" placeholder="Email" /><br />
      <input v-model="password" type="password" placeholder="Password" /><br />
      <button type="submit">Masuk</button>
    </form>
    <p v-if="error" class="error">{{ error }}</p>
  </section>
</template>
