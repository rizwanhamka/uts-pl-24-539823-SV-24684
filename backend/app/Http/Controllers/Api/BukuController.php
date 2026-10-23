<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BukuController extends Controller
{
    private function rules(): array
    {
        return [
            'judul' => ['required', 'string', 'max:255'],
            'penulis' => ['required', 'string', 'max:255'],
            'tahun_terbit' => ['required', 'integer', 'between:1900,2100'],
            'stok' => ['required', 'integer', 'min:0'],
        ];
    }

    public function index(): JsonResponse
    {
        return response()->json(['data' => Buku::latest('id')->get()]);
    }

    public function show(Buku $buku): JsonResponse
    {
        return response()->json(['data' => $buku]);
    }

    public function store(Request $request): JsonResponse
    {
        $buku = Buku::create($request->validate($this->rules()));

        return response()->json(['message' => 'Buku berhasil ditambahkan.', 'data' => $buku], 201);
    }

    public function update(Request $request, Buku $buku): JsonResponse
    {
        $buku->update($request->validate($this->rules()));

        return response()->json(['message' => 'Buku berhasil diubah.', 'data' => $buku]);
    }

    public function destroy(Buku $buku): JsonResponse
    {
        $buku->delete();

        return response()->json(['message' => 'Buku berhasil dihapus.']);
    }
}
