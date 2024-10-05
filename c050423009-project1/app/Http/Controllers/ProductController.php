<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class ProductController extends Controller
{
    /**
     * index
     * 
     * @return void
     */
    public function index()
    {
        $products = Products::latest()->paginate(10);
        return view('products.index', compact('products'));

    }
    /**
     * create
     * @return
     */
    public function create()
    {
        return view('products.create');
    }
    /**
     * store
     * 
     * @param mixed $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        //validate form
        $request->validate([
            'image'        => 'required|image|mimes:jpeg,jpg,png|max:2048',
            'title'        => 'required|min:5',
            'description'  => 'required|min:10',
            'price'        => 'required|numeric',
            'stock'        => 'required|numeric'
        ]);

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/products', $image->hashName());

        //create product
        Products::create([
            'image'        => $image->hashName(),
            'title'        => $request->title,
            'description'  => $request->description,
            'price'        => $request->price,
            'stock'        => $request->stock 
        ]);

       // Redirect ke index
       return redirect()->route('products.index')->with('success', 'Data berhasil ditambahkan!');
    }
    public function show(string $id)
    {
        //get product by ID
        $product = Products::findOrFail($id);

        //render view with product
        return view('products.show', compact('product'));
    }

    public function edit(string $id)
    {
        //get product by ID
        $product = Products::findOrFail($id);

        //render view with product
        return view('products.edit', compact('product'));
    }

    /**
     * update
     * 
     * @param mixed $request
     * @param mixed $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id)
    {
        //validate form
        $request->validate([
            'image'         => 'image|mimes:jpeg,jpg,png|max:2048',
            'title'         => 'required|min:5',
            'description'   => 'required|min:10',
            'price'         => 'required|numeric',
            'stock'         => 'required|numeric'
        ]);

        //get product by ID
        $product = Products::findOrFail($id);

        //check if image is uploaded
        if ($request->hasFile('image')) {
            
            //upload new image
            $image = $request->file('image');
            $image->storeAs('public/products', $image->hashName());

            //delete old image
            Storage::delete('public/products'.$product->image);

            //update product with new image
            $product->update([
            'image'        => $image->hashName(),
            'title'        => $request->title,
            'description'  => $request->description,
            'price'        => $request->price,
            'stock'        => $request->stock  
            ]);
        } else {

            //update product without image
            $product->update([
            'title'        => $request->title,
            'description'  => $request->description,
            'price'        => $request->price,
            'stock'        => $request->stock  
            ]);
        }

        //redirect to index
        return redirect()->route('products.index')->with(['success' => 'Data Berhasil Diubah']);
    }

    public function destroy(string $id)
    {
        //get product by ID
        $product = Products::findOrFail($id);

        //delete image
        Storage::delete('public/products/', $product->image);

        //delete product
        $product->delete();

        //redirect to index
        return redirect()->route('products.index')->with('success', 'Data berhasil dihapus');
    }
}
