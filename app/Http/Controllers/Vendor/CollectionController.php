<?php

namespace App\Http\Controllers\Vendor;

use App\Contracts\CollectionContract;
use App\Http\Controllers\Controller;
use App\Http\Requests\CollectionRequest;
use App\Http\Resources\CollectionResource;
use App\Models\Collection;
use App\Repositories\FilterForCollectionContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class CollectionController extends Controller
{
    public function __construct(private CollectionContract $filterForCollectionContract)
    {
    }

    public function index(): JsonResponse
    {
        $collections = $this->filterForCollectionContract
            ->buildQuery(Collection::query())
            ->select(['id', 'name'])
            ->where('vendor_id' , \request()->user('api-vendor')->id)
            ->jsonPaginate(10);

        return $this->apiResponse(
            data: $collections
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CollectionRequest $request): JsonResponse
    {
        $collection = Collection::create([
            'name' => $request->name,
            'vendor_id' => $request->user('api-vendor')->id
        ]);

        return $this->apiResponse(
            data: $collection,
            code: JsonResponse::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Collection $collection): JsonResponse
    {
        return $this->apiResponse(
            data: CollectionResource::make($collection)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CollectionRequest $request, Collection $collection): JsonResponse
    {

        $collection->update($request->validated());

        return $this->apiResponse(
            data: CollectionResource::make($collection->fresh()),
            code: JsonResponse::HTTP_CREATED
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Collection $collection): JsonResponse
    {
        abort_if(\request()->user('api-vendor')->id != $collection->id,403);
        $collection->forceDelete();

        return $this->apiResponse(
            msg:"The Resource got deleted successfully."
        );
    }
}
