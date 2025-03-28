<?php

namespace App\Http\Controllers\API;

use App\DTO\SnippetDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUpdateSnippetRequest;
use App\Services\SnippetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SnippetController extends Controller
{
    /**
     * @var SnippetService
     */
    protected $snippetService;

    /**
     * Create a new controller instance.
     *
     * @param SnippetService $snippetService
     * @return void
     */
    public function __construct(SnippetService $snippetService)
    {
        $this->middleware('auth:api');
        $this->snippetService = $snippetService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filters = $request->all();
        $perPage = $request->has('per_page') ? (int) $request->per_page : 10;

        $snippets = $this->snippetService->getAllSnippets($filters, $perPage);

        return response()->json($snippets);
    }

    /**
     * Store a newly created resource or update an existing one.
     *
     * @param  \App\Http\Requests\StoreUpdateSnippetRequest  $request
     * @param  int|null  $id
     * @return \Illuminate\Http\Response
     */
    public function storeOrUpdate(StoreUpdateSnippetRequest $request, ?int $id = null)
    {
        $snippetDTO = SnippetDTO::fromRequest($request->validated(), $id);
        $snippet = $this->snippetService->createOrUpdateSnippet($snippetDTO);

        $isNew = $id === null;
        return response()->json($snippet, $isNew ? 201 : 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreUpdateSnippetRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUpdateSnippetRequest $request)
    {
        return $this->storeOrUpdate($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $snippet = $this->snippetService->getSnippet($id);

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
     * @param  \App\Http\Requests\StoreUpdateSnippetRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreUpdateSnippetRequest $request, $id)
    {
        return $this->storeOrUpdate($request, $id);
    }

    /**
     * Toggle the favorite status of the specified snippet.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function toggleFavorite($id)
    {
        $snippet = $this->snippetService->toggleFavorite($id);

        if (!$snippet) {
            return response()->json(['message' => 'Snippet not found or unauthorized'], 404);
        }

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
        $deleted = $this->snippetService->deleteSnippet($id);

        if (!$deleted) {
            return response()->json(['message' => 'Snippet not found or unauthorized'], 404);
        }

        return response()->json(['message' => 'Snippet deleted successfully']);
    }
}
