<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('user_id', auth()->id());

        if ($request->has('search') && $request->search != '') {
            $term = trim($request->search);
            if (DB::getDriverName() === 'pgsql') {
                $query->whereRaw('name ILIKE ?', ['%' . $term . '%']);
            } else {
                $query->where('name', 'like', '%' . $term . '%');
            }
        }

        $products = $query->paginate(20);
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120'
        ]);

        $data = $request->only(['name', 'price', 'stock']);
        $data['user_id'] = auth()->id();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'products/' . uniqid() . '.' . $file->getClientOriginalExtension();
            $hasSupabase = env('SUPABASE_URL') && env('SUPABASE_STORAGE_KEY') && env('SUPABASE_STORAGE_SECRET');
            if ($hasSupabase) {
                Storage::disk('supabase')->put($filename, file_get_contents($file->getRealPath()));
            } else {
                Storage::disk('public')->put($filename, file_get_contents($file->getRealPath()));
            }
            $data['image'] = $filename;
        }

        $product = Product::create($data);
        // Clear dashboard cache for this user
        $userId = auth()->id();
        \Illuminate\Support\Facades\Cache::forget("dashboard_stats_{$userId}");
        \Illuminate\Support\Facades\Cache::forget("dashboard_low_stock_{$userId}");
        \Illuminate\Support\Facades\Cache::forget("low_stock_{$userId}_" . (auth()->user()->default_stock_threshold ?? 5));
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'stock' => $product->stock,
                    'image_url' => $product->image_url,
                ]
            ]);
        }
        return redirect()->route('products.index', ['_fresh' => 1])->with('success', 'Product added!');
    }

    public function edit(Product $product)
    {
        // Check if product belongs to current user
        if ($product->user_id !== auth()->id()) {
            abort(403);
        }
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        // Check if product belongs to current user
        if ($product->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120'
        ]);

        $data = $request->only(['name', 'price', 'stock']);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'products/' . uniqid() . '.' . $file->getClientOriginalExtension();
            $hasSupabase = env('SUPABASE_URL') && env('SUPABASE_STORAGE_KEY') && env('SUPABASE_STORAGE_SECRET');
            if ($hasSupabase) {
                Storage::disk('supabase')->put($filename, file_get_contents($file->getRealPath()));
                if ($product->image && Storage::disk('supabase')->exists($product->image)) {
                    Storage::disk('supabase')->delete($product->image);
                }
            } else {
                Storage::disk('public')->put($filename, file_get_contents($file->getRealPath()));
                if ($product->image && Storage::disk('public')->exists($product->image)) {
                    Storage::disk('public')->delete($product->image);
                }
            }
            $data['image'] = $filename;
        }

        $product->update($data);
        // Clear dashboard cache for this user
        $userId = auth()->id();
        \Illuminate\Support\Facades\Cache::forget("dashboard_low_stock_{$userId}");
        \Illuminate\Support\Facades\Cache::forget("low_stock_{$userId}_" . (auth()->user()->default_stock_threshold ?? 5));
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'stock' => $product->stock,
                    'image_url' => $product->image_url,
                ]
            ]);
        }
        return redirect()->route('products.index', ['_fresh' => 1])->with('success', 'Product updated!');
    }

    public function destroy(Product $product)
    {
        // Check if product belongs to current user
        if ($product->user_id !== auth()->id()) {
            abort(403);
        }

        if ($product->image && Storage::disk('supabase')->exists($product->image)) {
            Storage::disk('supabase')->delete($product->image);
        }

        $product->delete();
        // Clear dashboard cache for this user
        $userId = auth()->id();
        \Illuminate\Support\Facades\Cache::forget("dashboard_stats_{$userId}");
        \Illuminate\Support\Facades\Cache::forget("dashboard_low_stock_{$userId}");
        \Illuminate\Support\Facades\Cache::forget("low_stock_{$userId}_" . (auth()->user()->default_stock_threshold ?? 5));
        return redirect()->route('products.index', ['_fresh' => 1])->with('success', 'Product deleted!');
    }

    public function syncOfflineProduct(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0'
            ]);

            Product::create([
                'user_id' => auth()->id(),
                'name' => $validated['name'],
                'price' => $validated['price'],
                'stock' => $validated['stock'],
                'image' => null // No image from offline creation
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Product synced successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
