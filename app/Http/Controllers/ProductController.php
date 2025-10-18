<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

/**
 * @OA\Tag(
 * name="Productos",
 * description="Operaciones para gestionar productos"
 * )
 *
 * @OA\Schema(
 * schema="Product",
 * type="object",
 * title="Product",
 * description="Modelo de producto",
 * @OA\Property(property="id", type="integer", readOnly="true", example="1"),
 * @OA\Property(property="name", type="string", example="Smartphone"),
 * @OA\Property(property="description", type="string", example="Un dispositivo móvil inteligente."),
 * @OA\Property(property="price", type="number", format="float", example="599.99"),
 * @OA\Property(property="stock", type="integer", example="100"),
 * @OA\Property(property="is_active", type="boolean", example="true"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Timestamp de creación", readOnly="true"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Timestamp de la última actualización", readOnly="true")
 * )
 */
class ProductController extends Controller
{
    /**
     * @OA\Get(
     * path="/products",
     * operationId="getProductsList",
     * tags={"Productos"},
     * summary="Obtener una lista de productos",
     * description="Devuelve una lista de todos los productos.",
     * security={{"sanctum": {}}},
     * @OA\Response(
     * response=200,
     * description="Operación exitosa",
     * @OA\JsonContent(
     * type="array",
     * @OA\Items(ref="#/components/schemas/Product")
     * )
     * )
     * )
     */
    public function index()
    {
        $product = Product::get();
        return response()->json($product);
    }

    /**
     * @OA\Post(
     * path="/products",
     * operationId="storeProduct",
     * tags={"Productos"},
     * summary="Crear un nuevo producto",
     * description="Crea un nuevo producto y lo almacena en la base de datos.",
     * security={{"sanctum": {}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"name", "price"},
     * @OA\Property(property="name", type="string", example="Smartphone"),
     * @OA\Property(property="description", type="string", example="Un dispositivo móvil inteligente."),
     * @OA\Property(property="price", type="number", format="float", example="599.99"),
     * @OA\Property(property="stock", type="integer", example="100"),
     * @OA\Property(property="is_active", type="boolean", example="true")
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Producto creado exitosamente",
     * @OA\JsonContent(ref="#/components/schemas/Product")
     * )
     * )
     */
    public function store(Request $request)
    {
        // Validar los datos de entrada para los campos requeridos
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'stock' => 'nullable|integer',
            'is_active' => 'nullable|boolean'
        ]);

        // Crear el producto usando los datos validados
        $product = Product::create($validatedData);

        return response()->json($product, 201);
    }

    /**
     * @OA\Get(
     * path="/products/{id}",
     * operationId="showProduct",
     * tags={"Productos"},
     * summary="Obtener un producto por ID",
     * description="Devuelve un solo producto.",
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="ID del producto a obtener",
     * @OA\Schema(
     * type="integer"
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Operación exitosa",
     * @OA\JsonContent(ref="#/components/schemas/Product")
     * ),
     * @OA\Response(
     * response=404,
     * description="Producto no encontrado"
     * )
     * )
     */
    public function show(string $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        return response()->json($product);
    }

    /**
     * @OA\Put(
     * path="/products/{id}",
     * operationId="updateProduct",
     * tags={"Productos"},
     * summary="Actualizar un producto existente",
     * description="Actualiza un producto con la información proporcionada.",
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="ID del producto a actualizar",
     * @OA\Schema(
     * type="integer"
     * )
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * @OA\Property(property="name", type="string", example="Smartphone Pro"),
     * @OA\Property(property="description", type="string", example="Un dispositivo móvil mejorado."),
     * @OA\Property(property="price", type="number", format="float", example="699.99"),
     * @OA\Property(property="stock", type="integer", example="150"),
     * @OA\Property(property="is_active", type="boolean", example="false")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Producto actualizado exitosamente",
     * @OA\JsonContent(ref="#/components/schemas/Product")
     * ),
     * @OA\Response(
     * response=404,
     * description="Producto no encontrado"
     * )
     * )
     */
    public function update(Request $request, string $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->stock = $request->stock;
        $product->is_active = $request->is_active;
        $product->updated_at = now();
        $product->save();
        return response()->json($product);
    }

    /**
     * @OA\Delete(
     * path="/products/{id}",
     * operationId="deleteProduct",
     * tags={"Productos"},
     * summary="Eliminar un producto",
     * description="Elimina un producto por su ID.",
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="ID del producto a eliminar",
     * @OA\Schema(
     * type="integer"
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Producto eliminado exitosamente"
     * ),
     * @OA\Response(
     * response=404,
     * description="Producto no encontrado"
     * )
     * )
     */
    public function destroy(string $id)
    {
        if (!Product::find($id)){
            return response()->json(['message' => 'Product not found'],404);
        }
        Product::destroy($id);
        return response()->json(['message' => 'Deleted'], 200);
    }
}
