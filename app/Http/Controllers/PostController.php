<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\{Post, Category};
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    // Tampilkan daftar artikel, urut dari yang terbaru, 10 per halaman
    public function index(Post $post)
    {
        $posts = $post->with('category')->orderBy('created_at', 'desc')->latest()->paginate(10);

        return view('admin.posts.index', compact('posts'));
    }

    // Tampilkan halaman detail artikel
    public function show(Post $post)
    {
        $categories = Category::with('posts')->get(); // ambil semua kategori dan artikel terkait
        // ambil 5 artikel terbaru selain artikel yang sedang dibuka
        $posts = Post::where('slug', '!=', $post->slug)->orderBy('created_at', 'desc')->limit(5)->get();

        return view('blog.show', compact('post', 'posts', 'categories'));
    }

    // Cari artikel berdasarkan kata kunci judul
    public function search(Request $request)
    {
        $request->validate(['keyword' => 'required|string']);

        $keyword = $request->keyword;

        $categories = Category::all(); // ambil semua kategori
        $posts = Post::where('title', 'like', '%'.$keyword.'%')->latest()->paginate(10);

        return view('blog.search', compact('keyword', 'categories', 'posts'));
    }

    // Tampilkan artikel berdasarkan penulis
    public function author($author)
    {
        $categories = Category::all();
        $posts = Post::where('author', $author)->latest()->paginate(10);
        
        return view('blog.author', compact('author', 'categories', 'posts'));
    }

    // Tampilkan artikel berdasarkan kategori
    public function category(Category $category)
    {
        $category_name = $category->name;
        $categories = Category::all();
        $posts = $category->posts;
        
        return view('blog.category', compact('category_name', 'categories', 'posts'));
    }

    // Simpan kategori baru lewat AJAX (json response)
    public function category_save(Request $request)
    {
        $isExists = Category::where('name', $request->name)->exists();

        // jika nama kosong atau kategori sudah ada, gagal
        if(!$request->name || $isExists) {
            return response()->json(['status' => 'failed']);
        }

        // buat kategori baru dengan slug dari nama
        $result = Category::create([
            'name' => $request->name,
            'slug' => \Str::slug($request->name)
        ]);

        return response()->json(['status' => 'success', 'id' => $result->id]);
    }

    // Simpan artikel baru
    public function store(Request $request)
    {
        // validasi input wajib
        $request->validate([
            'thumbnail'  => 'required|file|image|mimes:jpg,png,svg',
            'title' => 'required',
            'body' => 'required',
            'category_id' => 'required',
            'author' => 'required'
        ]);

        $data = $request->all();

        // cek apakah slug sudah ada, kalau iya buat slug unik dengan tambahan hash
        $isExists = Post::where('slug', \Str::slug($request->title))->exists();
        $data['slug'] = ($isExists) ? \Str::slug($request->title.'-'.substr(md5(time()), 0, 8)) : \Str::slug($request->title);

        // proses upload thumbnail ke folder posts/tanggal sekarang
        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $folder = Carbon::now()->format('m-d-Y');
            $fileName = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension(); // buat nama file unik
            $path = $file->storeAs('posts/' . $folder, $fileName, 'public');
            $data['thumbnail'] = $path;
        }

        // simpan artikel ke database, jika error tampil pesan gagal
        try {
            Post::create($data);
            return redirect()->route('admin.dashboard')->with('success', 'Artikel berhasil disimpan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menyimpan: ' . $e->getMessage());
        }
    }

    // Tampilkan form edit artikel
    public function edit(Post $post)
    {
        return view('admin.posts.edit', compact('post'));
    }

    // Update artikel yang sudah ada
    public function update(Post $post, Request $request)
    {
        $request->validate([
            'title' => 'required',
            'body' => 'required',
            'category_id' => 'required',
            'author' => 'required'
        ]);

        $data = $request->all();

        // buat slug baru dengan tambahan timestamp supaya unik
        $data['slug'] = \Str::slug($request->title.'-'.now());

        // jika upload thumbnail baru, proses upload
        if($request->hasFile('thumbnail')){
            $file = $request->file('thumbnail');

            $fileName = $file->getClientOriginalName();
            $folder = Carbon::now()->format('m-d-Y');

            $file->storeAs('posts/'.$folder, $fileName, 'public');

            $data['thumbnail'] = 'posts/'.$folder.'/'.$fileName;
        } else {
            // jika tidak upload, pakai thumbnail lama
            $data['thumbnail'] = $post->thumbnail;
        }

        // update data artikel di database
        $post->update($data);

        return redirect()->to(route('admin.dashboard'))->with('success', 'Artikel berhasil diupdate!');
    }

    // Hapus artikel
    public function destroy(Post $post)
    {
        $post->delete();

        return redirect(route('admin.dashboard'))->with('success', 'Artikel berhasil dihapus!');
    }
}
