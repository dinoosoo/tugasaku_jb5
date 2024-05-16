<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{    
    public function index(): View
    {
        $posts = Post::latest()->paginate(5);
        return view('posts.index', compact('posts'));
    }

    public function create(): View
    {
        return view('posts.create');
    }
 
    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'nama_obat' => 'required|min:3',
            'gambar' => 'required|image|mimes:jpeg,jpg,png|max:2048',
            'harga' => 'required|numeric',
            'keluhan' => 'required',
            'stok_obat' => 'required|integer',
        ]);

        $gambar = $request->file('gambar');
        $gambar->storeAs('public/posts', $gambar->hashName());

        Post::create([
            'nama_obat' => $request->nama_obat,
            'gambar' => $gambar->hashName(),
            'harga' => $request->harga,
            'keluhan' => $request->keluhan,
            'stok_obat' => $request->stok_obat,
        ]);

        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }
    
    public function show(string $id): View
    {
        $post = Post::findOrFail($id);
        return view('posts.show', compact('post'));
    }

    public function edit(string $id): View
    {
        $post = Post::findOrFail($id);
        return view('posts.edit', compact('post'));
    }
        
    public function update(Request $request, string $id): RedirectResponse
    {
        $this->validate($request, [
            'nama_obat' => 'required|min:3',
            'harga' => 'required|numeric',
            'keluhan' => 'required',
            'stok_obat' => 'required|integer',
            'gambar' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $post = Post::findOrFail($id);

        if ($request->hasFile('gambar')) {
            $gambar = $request->file('gambar');
            $gambar->storeAs('public/posts', $gambar->hashName());

            Storage::delete('public/posts/'.$post->gambar);

            $post->update([
                'gambar' => $gambar->hashName(),
            ]);
        }

        $post->update([
            'nama_obat' => $request->nama_obat,
            'harga' => $request->harga,
            'keluhan' => $request->keluhan,
            'stok_obat' => $request->stok_obat,
        ]);

        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Diubah!']);
    }

    public function destroy(string $id): RedirectResponse
    {
        $post = Post::findOrFail($id);

        Storage::delete('public/posts/'. $post->gambar);

        $post->delete();

        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }
   
}
