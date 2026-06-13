<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index()
    {
        abort_if(Auth::user()->role !== 'admin', 403, 'Akses Ditolak: Hanya Admin yang dapat mengakses menu Produk.');

        $products = Product::orderBy('name', 'asc')->paginate(10);
        return view('products.index', compact('products'));
    }

    public function create()
    {
        abort_if(Auth::user()->role !== 'admin', 403, 'Akses Ditolak: Hanya Admin yang dapat mengakses menu Produk.');

        return view('products.create');
    }

    public function store(Request $request)
    {
        abort_if(Auth::user()->role !== 'admin', 403);

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        Product::create([
            'name' => $request->name,
            'price' => $request->price,
        ]);

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        abort_if(Auth::user()->role !== 'admin', 403, 'Akses Ditolak: Hanya Admin yang dapat mengakses menu Produk.');

        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        abort_if(Auth::user()->role !== 'admin', 403);

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        $product->update([
            'name' => $request->name,
            'price' => $request->price,
        ]);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        abort_if(Auth::user()->role !== 'admin', 403);

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }
}
