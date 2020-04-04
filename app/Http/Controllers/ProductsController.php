<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use Session;

class ProductsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::orderBy('name', 'ASC')->paginate(5);
        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
            'image' => 'required|image',
        ]);

        $product = new Product;
        $product_image = $request->image;
        $product_image_new_name = time() . $product_image->getClientOriginalName();        
        $product_image->move('uploads/products', $product_image_new_name);

        $product->name = $request->name;
        $product->price = $request->price;
        $product->description = $request->description;
        $product->image = 'uploads/products' . $product_image_new_name;

        $product->save();

        Session::flash('success', 'Product Created!');

        return redirect()->route('products.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('products.edit', [
            'product' => Product::find($id)
        ]);
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
        $this->validate($request, [
            'name' => 'required',
            'description' => 'required',
            'price' => 'required'
        ]);

        $product = Product::find($id);

        if($request->hasFile('image'))
        {
            $product_image = $request->image;
            $product_image_new_name = time() . $product_image->getClientOriginalName();        
            $product_image->move('uploads/products', $product_image_new_name);
            $product->image = 'uploads/products' . $product_image_new_name;
            $product->save();
        }
        

        $product->name = $request->name;
        $product->price = $request->price;
        $product->description = $request->description;        

        $product->save();

        Session::flash('success', 'Product Updated!');

        return redirect()->route('products.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::find($id);

        if(file_exists($product->image))
        {
            unlink($product->image);
        }

        $product->delete();

        Session::flash('success', 'Product Deleted!');

        return redirect()->back();
    }
}
