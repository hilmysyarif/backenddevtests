<?php

namespace App\Http\Controllers;

use JWTAuth;
use App\Product;
use App\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * ProductController constructor.
     */
    public function __construct()
    {

    }

    /**
     * @return mixed
     */
    public function index()
    {
        $products = Product::all()->toArray();

        return response()->json([
            'error'   => false,
            'data' => $products
        ], 200);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'error' => true,
                'message' => 'Sorry, product with id ' . $id . ' cannot be found.'
            ], 400);
        }else{
            return response()->json([
                'error'   => false,
                'data' => $product
            ], 200);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'product_name'  => 'required',
            'price'         => 'required',
            'stock'         => 'required',
            'category_id'   => 'required',
        ]);

        $product                = new Product();
        $product->_product_id   = $request->_product_id;
        $product->product_name  = $request->product_name;
        $product->price         = $request->price;
        $product->stock         = $request->stock;

        // Check existing category from DB's
        //
        $categoryChecks         = Category::find($request->category_id);
        if(empty($categoryChecks)){
            // Create new category
            // and assign the id to product
            //
            $category                = new Category();
            $category->_category_id  = $request->category_id;
            $category->category_name = $request->category_id;
            $category->save();

            $product->category_id    = $category->_category_id;

        }else{
            // existing category
            // assign the id to product
            $product->category_id    = $categoryChecks->_category_id;
        }


        if ($product->save())
            return response()->json([
                'error'     => false,
                'success'   => true,
                'product'   => $product
            ]);
        else
            return response()->json([
                'error'   => true,
                'success' => false,
                'message' => 'Sorry, product could not be added.'
            ], 500);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'product_name'  => 'required',
            'price'         => 'required',
            'stock'         => 'required',
            'category_id'   => 'required',
        ]);

        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'error' => true,
                'message' => 'Sorry, product with id ' . $id . ' cannot be found.'
            ], 400);
        }else{
            $updated = $product->fill($request->all())->save();

            if ($updated) {
                return response()->json([
                    'error'   => false,
                    'success' => true
                ]);
            } else {
                return response()->json([
                    'error'   => true,
                    'success' => false,
                    'message' => 'Sorry, product could not be updated.'
                ], 500);
            }
        }

    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $product = Product::where($id);

        if (!$product) {
            return response()->json([
                'error' => true,
                'message' => 'Sorry, product with id ' . $id . ' cannot be found.'
            ], 400);
        }else{
            if ($product->delete()) {
                return response()->json([
                    'error'   => false,
                    'success' => true
                ]);
            } else {
                return response()->json([
                    'error'   => true,
                    'success' => false,
                    'message' => 'Product could not be deleted.'
                ], 500);
            }
        }
    }
}
