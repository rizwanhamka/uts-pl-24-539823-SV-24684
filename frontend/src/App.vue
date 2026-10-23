<script setup>
import { useRoute, useRouter } from 'vue-router'
import http from './api/http'

const route = useRoute()
const router = useRouter()

async function logout() {
  try { await http.post('/logout') } catch (e) { /* token mungkin sudah tidak valid */ }
  localStorage.removeItem('token')
  router.push({ name: 'login' })
}
</script>

<template>
  <header v-if="route.name !== 'login'" class="bar">
    <strong>Perpustakaan Mini</strong>
    <button @click="logout">Logout</button>
  </header>
  <main><RouterView /></main>
</template>
