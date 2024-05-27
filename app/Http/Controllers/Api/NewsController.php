<?php

namespace App\Http\Controllers\Api;

use App\Models\News;
use App\Http\Controllers\Controller;
use App\Http\Resources\NewsResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        $news = News::latest()->paginate(5);
        return new NewsResource(true, 'List Data News', $news);
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image'     => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title'     => 'required',
            'slug'      => 'required|unique:news',
            'excerpt'   => 'required',
            'content'   => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $image = $request->file('image');
        $image->storeAs('public/news', $image->hashName());

        $news = News::create([
            'image'     => $image->hashName(),
            'title'     => $request->title,
            'slug'      => $request->slug,
            'excerpt'   => $request->excerpt,
            'content'   => $request->content,
        ]);
        return new NewsResource(true, 'Data News Berhasil Ditambahkan!', $news);
    }

    /**
     * show
     *
     * @param  mixed $id
     * @return void
     */
    public function show($id)
    {
        $news = News::find($id);
        return new NewsResource(true, 'Detail Data News!', $news);
    }

    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title'     => 'required',
            'slug'      => 'required|unique:news,slug,' . $id,
            'excerpt'   => 'required',
            'content'   => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $news = News::find($id);
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image->storeAs('public/news', $image->hashName());

            Storage::delete('public/news/' . basename($news->image));

            $news->update([
                'image'     => $image->hashName(),
                'title'     => $request->title,
                'slug'      => $request->slug,
                'excerpt'   => $request->excerpt,
                'content'   => $request->content,
            ]);
        } else {
            $news->update([
                'title'     => $request->title,
                'slug'      => $request->slug,
                'excerpt'   => $request->excerpt,
                'content'   => $request->content,
            ]);
        }
        return new NewsResource(true, 'Data News Berhasil Diubah!', $news);
    }

     /**
     * destroy
     *
     * @param  mixed $id
     * @return void
     */
    public function destroy($id)
    {
        $news = News::find($id);
        Storage::delete('public/news/'.basename($news->image));
        $news->delete();
        return new NewsResource(true, 'Data News Berhasil Dihapus!', null);
    }
}