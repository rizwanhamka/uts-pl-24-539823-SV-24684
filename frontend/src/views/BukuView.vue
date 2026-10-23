<script setup>
import { ref, onMounted } from 'vue'
import http from '../api/http'

const fields = [
  { key: 'judul', label: 'Judul', type: 'text' },
  { key: 'penulis', label: 'Penulis', type: 'text' },
  { key: 'tahun_terbit', label: 'Tahun Terbit', type: 'number' },
  { key: 'stok', label: 'Stok', type: 'number' },
]
const kosong = () => ({ judul: '', penulis: '', tahun_terbit: '', stok: '' })

const daftar = ref([])
const form = ref(kosong())
const editId = ref(null)
const errors = ref({})
const pesan = ref('')

async function muat() {
  const { data } = await http.get('/buku')
  daftar.value = data.data
}

function reset() {
  form.value = kosong()
  editId.value = null
  errors.value = {}
}

async function simpan() {
  errors.value = {}
  try {
    if (editId.value) {
      await http.put(`/buku/${editId.value}`, form.value)
      pesan.value = 'Buku berhasil diubah.'
    } else {
      await http.post('/buku', form.value)
      pesan.value = 'Buku berhasil ditambahkan.'
    }
    reset()
    await muat()
  } catch (e) {
    if (e.response?.status === 422) errors.value = e.response.data.errors
    else pesan.value = 'Terjadi kesalahan pada server.'
  }
}

function ubah(b) {
  editId.value = b.id
  form.value = { judul: b.judul, penulis: b.penulis, tahun_terbit: b.tahun_terbit, stok: b.stok }
}

async function hapus(b) {
  if (!confirm(`Hapus "${b.judul}"?`)) return
  await http.delete(`/buku/${b.id}`)
  pesan.value = 'Buku berhasil dihapus.'
  await muat()
}

onMounted(muat)
</script>

<template>
  <section>
    <h2>Daftar Buku</h2>
    <p v-if="pesan" class="ok">{{ pesan }}</p>

    <form @submit.prevent="simpan">
      <h3>{{ editId ? 'Ubah Buku' : 'Tambah Buku' }}</h3>
      <div v-for="f in fields" :key="f.key">
        <input v-model="form[f.key]" :type="f.type" :placeholder="f.label" />
        <div v-if="errors[f.key]" class="error">{{ errors[f.key][0] }}</div>
      </div>
      <button type="submit">{{ editId ? 'Simpan Perubahan' : 'Tambah' }}</button>
      <button v-if="editId" type="button" @click="reset">Batal</button>
    </form>

    <table>
      <thead>
        <tr><th>Judul</th><th>Penulis</th><th>Tahun</th><th>Stok</th><th>Aksi</th></tr>
      </thead>
      <tbody>
        <tr v-for="b in daftar" :key="b.id">
          <td>{{ b.judul }}</td><td>{{ b.penulis }}</td><td>{{ b.tahun_terbit }}</td><td>{{ b.stok }}</td>
          <td><button @click="ubah(b)">Ubah</button> <button @click="hapus(b)">Hapus</button></td>
        </tr>
        <tr v-if="daftar.length === 0"><td colspan="5">Belum ada data.</td></tr>
      </tbody>
    </table>
  </section>
</template>
