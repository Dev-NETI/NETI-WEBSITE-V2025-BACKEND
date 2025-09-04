<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $news = News::with('creator:id,name')
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $news
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'excerpt' => 'required|string',
            'content' => 'required|string',
            'author' => 'required|string|max:255',
            'author_title' => 'required|string|max:255',
            'date' => 'required|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'in:published,archived',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $data['slug'] = Str::slug($data['title']);
        $data['created_by'] = auth()->id();

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            // $imageName = time() . '_' . Str::slug($data['title']) . '.' . $image->getClientOriginalExtension();
            // $image->storeAs('public/news_images', $imageName);
            $image->store('news_images', 'public');
            $data['image'] = $image->hashName();
        }

        $news = News::create($data);
        $news->load('creator:id,name');

        return response()->json([
            'success' => true,
            'message' => 'News article created successfully',
            'data' => $news
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $news = News::with('creator:id,name')->findOrFail($id);
        $news->incrementViews();

        return response()->json([
            'success' => true,
            'data' => $news
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $news = News::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'excerpt' => 'required|string',
            'content' => 'required|string',
            'author' => 'required|string|max:255',
            'author_title' => 'required|string|max:255',
            'date' => 'required|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'in:published,archived',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $data['slug'] = Str::slug($data['title']);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($news->image && Storage::exists('public/news_images/' . $news->image)) {
                Storage::delete('public/news_images/' . $news->image);
            }

            $image = $request->file('image');
            // $imageName = time() . '_' . Str::slug($data['title']) . '.' . $image->getClientOriginalExtension();
            // $image->storeAs('public/news_images', $imageName);
            $image->store('news_images', 'public');
            $data['image'] = $image->hashName();
        }

        $news->update($data);
        $news->load('creator:id,name');

        return response()->json([
            'success' => true,
            'message' => 'News article updated successfully',
            'data' => $news
        ]);
    }

    /**
     * Archive the specified resource (soft delete).
     */
    public function destroy(string $id): JsonResponse
    {
        $news = News::findOrFail($id);

        // Archive the news article by setting status to 'archived'
        $news->update(['status' => 'archived']);

        return response()->json([
            'success' => true,
            'message' => 'News article archived successfully'
        ]);
    }

    /**
     * Reactivate an archived news article.
     */
    public function reactivate(string $id): JsonResponse
    {
        $news = News::findOrFail($id);

        // Reactivate the news article by setting status to 'published'
        $news->update(['status' => 'published']);

        return response()->json([
            'success' => true,
            'message' => 'News article reactivated successfully',
            'data' => $news->load('creator:id,name')
        ]);
    }
}
