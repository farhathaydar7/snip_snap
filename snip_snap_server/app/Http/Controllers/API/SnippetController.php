<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Snippet;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SnippetController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Snippet::with('tags');

        // Only show authenticated user's snippets
        $query->where('user_id', Auth::id());

        // Filter by title, description, or tags
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhereHas('tags', function($q) use ($search) {
                      $q->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Filter by language
        if ($request->has('language')) {
            $query->where('language', $request->language);
        }

        // Filter by favorite status
        if ($request->has('is_favorite')) {
            $query->where('is_favorite', filter_var($request->is_favorite, FILTER_VALIDATE_BOOLEAN));
        }

        // Filter by tag
        if ($request->has('tag')) {
            $query->whereHas('tags', function($q) use ($request) {
                $q->where('name', $request->tag);
            });
        }

        // Sort by a specific field
        if ($request->has('sort')) {
            $direction = $request->has('direction') && strtolower($request->direction) === 'asc' ? 'asc' : 'desc';
            $query->orderBy($request->sort, $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $perPage = $request->has('per_page') ? (int) $request->per_page : 10;
        return response()->json($query->paginate($perPage));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'code' => 'required|string',
            'language' => 'required|string|max:50',
            'is_favorite' => 'boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $snippet = Snippet::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'code' => $request->code,
            'language' => $request->language,
            'is_favorite' => $request->is_favorite ?? false,
        ]);

        // Handle tags
        if ($request->has('tags') && is_array($request->tags)) {
            $tagIds = [];

            foreach ($request->tags as $tagName) {
                $tag = Tag::firstOrCreate(['name' => $tagName]);
                $tagIds[] = $tag->id;
            }

            $snippet->tags()->sync($tagIds);
        }

        return response()->json($snippet->load('tags'), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $snippet = Snippet::with('tags')->find($id);

        if (!$snippet) {
            return response()->json(['message' => 'Snippet not found'], 404);
        }

        // Check if user is authorized to view this snippet
        if ($snippet->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($snippet);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $snippet = Snippet::find($id);

        if (!$snippet) {
            return response()->json(['message' => 'Snippet not found'], 404);
        }

        // Check if user is authorized to update this snippet
        if ($snippet->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'code' => 'sometimes|required|string',
            'language' => 'sometimes|required|string|max:50',
            'is_favorite' => 'boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $snippet->update($request->only([
            'title', 'description', 'code', 'language', 'is_favorite'
        ]));

        // Handle tags
        if ($request->has('tags') && is_array($request->tags)) {
            $tagIds = [];

            foreach ($request->tags as $tagName) {
                $tag = Tag::firstOrCreate(['name' => $tagName]);
                $tagIds[] = $tag->id;
            }

            $snippet->tags()->sync($tagIds);
        }

        return response()->json($snippet->load('tags'));
    }

    /**
     * Toggle the favorite status of the specified snippet.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function toggleFavorite($id)
    {
        $snippet = Snippet::find($id);

        if (!$snippet) {
            return response()->json(['message' => 'Snippet not found'], 404);
        }

        // Check if user is authorized to update this snippet
        if ($snippet->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $snippet->is_favorite = !$snippet->is_favorite;
        $snippet->save();

        return response()->json([
            'message' => $snippet->is_favorite ? 'Snippet marked as favorite' : 'Snippet removed from favorites',
            'is_favorite' => $snippet->is_favorite
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $snippet = Snippet::find($id);

        if (!$snippet) {
            return response()->json(['message' => 'Snippet not found'], 404);
        }

        // Check if user is authorized to delete this snippet
        if ($snippet->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $snippet->delete();

        return response()->json(['message' => 'Snippet deleted successfully']);
    }
}
