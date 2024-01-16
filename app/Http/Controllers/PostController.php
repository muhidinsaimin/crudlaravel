<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->paginate(5);
        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'image'   => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title'   => 'required|min:5',
            'content' => 'required|min:10',
        ]);

        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        Post::create([
            'image'   => $image->hashName(),
            'title'   => $request->title,
            'content' => $request->content,
        ]);

        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Di Tambahkan']);
    }

    public function edit(Post $post)
    {
        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $this->validate($request, [
            'image'   => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title'   => 'required|min:5',
            'content' => 'required|min:10',
        ]);
        //cek jika upload gambar
        if($request->hasFile('image')) {
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());
            //hapus gambar sebelumnya
            Storage::delete('publlic/posts/'.$post->image);
            //update post dengan gamabar baru
            $post->update([
                'image'   => $image->hashName(),
                'title'   => $request->title,
                'content' => $request->content,
            ]);
        } else {
            //update post tanpa gambar
            $post->update([
                'title'   => $request->title,
                'content' => $request->content,
            ]);
        }
        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Diupdate']);
    }

    public function destroy(Post $post)
    {
        Storage::delete('public/posts/'.$post->image);
        $post->delete();
        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Di Hapus']);
    }
}
