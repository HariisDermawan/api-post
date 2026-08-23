<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\PaginatedResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ProductRequest $request): JsonResponse
    {
        $products = Product::search($request->search)
            ->latest()
            ->paginate($request->limit ?? 10);

        return ApiResponse::success(
            new PaginatedResource($products, ProductResource::class),
            'Products list'
        );
    }

    public function options(ProductRequest $request)
    {
        $products = Product::select('id', 'name')
            ->search($request->search)
            ->orderBy('name')->get();

        return ApiResponse::success(
            ProductResource::collection($products),
            'Product  list'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('product_image', 'public');
        }

        $product = Product::create($data);

        return ApiResponse::success(
            new ProductResource($product),
            'Product created successfully',
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
{
    $product = Product::find($id);

    if (!$product) {
        return ApiResponse::error(
            'Product not found',
            Response::HTTP_NOT_FOUND
        );
    }

    return ApiResponse::success(
        new ProductResource($product),
        'Product detail'
    );
}

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, string $id): JsonResponse
    {
        $product = Product::find($id);

        if (! $product) {
            return ApiResponse::error('Product not found', Response::HTTP_NOT_FOUND);
        }

        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $data['image'] = $request->file('image')->store('product_image', 'public');
        }

        $product->update($data);

        return ApiResponse::success(
            new ProductResource($product->fresh()),
            'Product updated successfully'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $product = Product::find($id);

        if (! $product) {
            return ApiResponse::error('Product not found', Response::HTTP_NOT_FOUND);
        }

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return ApiResponse::success(null, 'Product deleted successfully');
    }
}
