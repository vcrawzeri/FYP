<?php

namespace App\Http\Controllers\Api;


use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductListResource;
use App\Http\Resources\ProductResource;
use App\Models\Api\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = request('search', false);
        $perPage = request('per_page', 10);
        $sortField = request('sort_field', 'updated_at');
        $sortDirection = request('sort_direction', 'desc');
        $query = Product::query();
        $query->orderBy($sortField, $sortDirection);
        if ($search) {
            $query->where('title', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%");
        }
        return ProductListResource::collection($query->paginate($perPage));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {

        $data = $request->validated();
        $data['created_by'] = $request->user()->id;
        $data['updated_by'] = $request->user()->id;
        /** @var \Illuminate\Http\UploadedFile $image */
        $image = $data['image'] ?? null;
        //check if the image was given and we save on the local file system
        if ($image) {
            $relativePath = $this->saveImage($image);
            $data['image'] = URL::to(Storage::url($relativePath));
            $data['image_mime'] = $image->getClientMimeType();
        }

        $product = Product::create($data);
        return new ProductResource($product);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, Product $product)
    {
        $data = $request->validated();
        $data['updated_by'] = $request->user()->id;

        // Handle new image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $relativePath = $this->saveImage($image);

            // Delete old image if exists
            if ($product->image) {
                $oldPath = str_replace(URL::to('/storage') . '/', '', $product->image);
                Storage::disk('public')->delete($oldPath);

                // Optional: delete old folder if empty
                $folder = dirname($oldPath);
                if (empty(Storage::disk('public')->files($folder))) {
                    Storage::disk('public')->deleteDirectory($folder);
                }
            }

            // Save new image info
            $data['image'] = URL::to(Storage::url($relativePath));
            $data['image_mime'] = $image->getClientMimeType();
            $data['image_size'] = $image->getSize();
        }

        $product->update($data);

        return new ProductResource($product);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return response()->noContent();
    }

    private function saveImage(\Illuminate\Http\UploadedFile $image)
    {
        $folder = 'images/' . Str::random(12); // random folder
        $filePath = Storage::disk('public')->putFileAs($folder, $image, $image->getClientOriginalName());

        if (!$filePath) {
            throw new \Exception("Unable to save the file \"{$image->getClientOriginalName()}\"");
        }

        return $filePath; // e.g., 'images/abc123/file.png'
    }
}
