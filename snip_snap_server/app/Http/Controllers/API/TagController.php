<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TagController extends Controller
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
        $query = Tag::withCount(['snippets' => function($query) {
            $query->where('user_id', Auth::id());
        }]);

        // Filter by tags with snippets from the authenticated user
        $query->whereHas('snippets', function($q) {
            $q->where('user_id', Auth::id());
        });

        // Search by tag name
        if ($request->has('search')) {
            $query->where('name', 'LIKE', "%{$request->search}%");
        }

        // Sort by name or snippet count
        if ($request->has('sort')) {
            $direction = $request->has('direction') && strtolower($request->direction) === 'asc' ? 'asc' : 'desc';

            if ($request->sort === 'snippets_count') {
                $query->orderBy('snippets_count', $direction);
            } else {
                $query->orderBy($request->sort, $direction);
            }
        } else {
            $query->orderBy('name', 'asc');
        }

        return response()->json($query->get());
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
            'name' => 'required|string|max:50|unique:tags,name,NULL,id,user_id,' . Auth::id(),
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $tag = Tag::create([
            'name' => $request->name,
            'user_id' => Auth::id()
        ]);

        return response()->json($tag, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $tag = Tag::with(['snippets' => function($query) {
            $query->where('user_id', Auth::id());
        }])->find($id);

        if (!$tag) {
            return response()->json(['message' => 'Tag not found'], 404);
        }

        // Only return the tag if it has snippets from the authenticated user
        if ($tag->snippets->isEmpty()) {
            return response()->json(['message' => 'Tag not found'], 404);
        }

        return response()->json($tag);
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
        $tag = Tag::find($id);

        if (!$tag) {
            return response()->json(['message' => 'Tag not found'], 404);
        }

        // Check if user is authorized to update this tag
        // by checking if the user has snippets with this tag
        $hasSnippetsWithTag = $tag->snippets()->where('user_id', Auth::id())->exists();
        if (!$hasSnippetsWithTag) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:tags,name,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $tag->update([
            'name' => $request->name,
        ]);

        return response()->json($tag);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $tag = Tag::find($id);

        if (!$tag) {
            return response()->json(['message' => 'Tag not found'], 404);
        }

        // Check if user is authorized to delete this tag
        // Only allow deleting if this tag is only used by the authenticated user's snippets
        $hasOtherUsersSnippets = $tag->snippets()->where('user_id', '!=', Auth::id())->exists();
        if ($hasOtherUsersSnippets) {
            return response()->json(['message' => 'Cannot delete tag used by other users'], 403);
        }

        // Detach tag from all of the user's snippets before deletion
        $tag->snippets()->where('user_id', Auth::id())->detach();

        // If tag is not used by any other snippets, delete it
        if ($tag->snippets()->count() === 0) {
            $tag->delete();
            return response()->json(['message' => 'Tag deleted successfully']);
        }

        return response()->json(['message' => 'Tag removed from your snippets']);
    }
}
