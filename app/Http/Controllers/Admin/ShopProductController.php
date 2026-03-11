<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\SiteHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Shop\IndexShopProduct;
use App\Http\Requests\Admin\Shop\StoreShopProduct;
use App\Http\Requests\Admin\Shop\UpdateShopProduct;
use App\Http\Requests\Admin\Shop\DestroyShopProduct;

use App\Models\ShopProduct;
use App\Models\ShopProductImage;
use App\AdminModule\AdminListing;
use Exception;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use phpDocumentor\Reflection\Types\Collection;

class ShopProductController extends Controller
{


    public function __construct() {

        $this->middleware(function ($request, $next) {

            $role = session('role', '');
            if (!in_array($role, ['admin', 'shop'])) {
                abort(403, 'Доступ запрещен');
            }

            return $next($request);
        });


    }

    private function check_role()
    {

        $role = session('role');

        if (empty($role))
        {
            die('Error role');
        }

        if (!in_array($role,['admin' ,'shop'])){
            abort(403, 'Доступ запрещен');
            return redirect('/logout');
        }

    }
    /**
     * Display a listing of the resource.
     *
     * @param IndexShopProduct $request
     * @return array|Factory|View
     */
    public function index(IndexShopProduct $request)
    {
        $this->check_role();
        //$request->merge(['per_page' => 2]);
        $data = AdminListing::create(ShopProduct::class)->processRequestAndGet(
        // pass the request with params
            $request,

            // set columns to query
            ['id', 'name', 'price', 'status', 'deleted', 'sold'],

            // set columns to searchIn
            ['id', 'name'],
            function ($query) use ($request) {
                $query->select('shop_products.*')
                    ->addSelect(\DB::raw('0 as sold'))
                    ->with(['images' => function($q) {
                        $q->orderBy('position', 'asc');
                    }]);

                if ($request->input('deleted') == 1) {
                    $query->where('deleted', 1);
                } else {
                    $query->where('deleted', 0);
                }

                if ($request->has('status')) {
                    $query->where('status', $request->input('status'));
                }
            }
        );

        $data->getCollection()->transform(function ($product) {
            // Берем первую картинку по позиции
            $firstImage = $product->images->first();


            $product->thumb_url = $firstImage
                ? asset( $firstImage->path)
                : asset('/images/shop/default.png');


            return $product;
        });

        $data->appends($request->all());

        if ($request->ajax()) {

            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.shop.index',
        [
            'data' => $data,
            'counts' => [
                    'all' => \App\Models\ShopProduct::where('deleted', 0)->count(),
                    'active' => \App\Models\ShopProduct::where('deleted', 0)->where('status', 1)->count(),
                    'inactive' => \App\Models\ShopProduct::where('deleted', 0)->where('status', 0)->count(),
                    'deleted' => \App\Models\ShopProduct::where('deleted', 1)->count(),
                ]
        ]);


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Factory|View
     * @throws AuthorizationException
     */
    public function create()
    {
        $this->check_role();

        return view('admin.shop.create', []);
    }

   /**
     * Store a newly created resource in storage.
     *
     * @param StoreShopProduct $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreShopProduct $request)
    {
        $this->check_role();

        $sanitized = $request->getSanitized();

        $shopproduct = ShopProduct::create($sanitized);
        $this->processImages($shopproduct, $request);

        if ($request->ajax()) {

            return ['redirect' => url('admin/shop'), 'message' => trans('admin.operation.succeeded')];
        }

        return redirect('admin/shop');
    }

    public function edit(ShopProduct $shopProduct)
    {
        $this->check_role();

        $shopProduct->load('images');
        $images = [];


        foreach ($shopProduct->images as $img) {

                $images[$img->position] = [
                    'url' => Storage::disk('public_uploads')->url($img->path),
                    'path' => $img->path,
                    'position' => $img->position,
                    'isDefault' => false
                ];
        }

        return view('admin.shop.edit', [
            'shopProduct' => $shopProduct,
            'images' => array_values($images)
        ]);
    }


    public function update(UpdateShopProduct $request, ShopProduct $shopProduct)
    {
        $this->check_role();


        $sanitized = $request->getSanitized();


        if ($request->input('status') != 2) {
            $shopProduct->deleted = 0;
        } else {
            unset($sanitized['status']);
            $shopProduct->deleted = 1;
        }

        $shopProduct->update($sanitized);

        $incomingPaths = array_filter([$request->image1, $request->image2, $request->image3]);

        $imagesToDelete = $shopProduct->images()
            ->whereNotIn('path', $incomingPaths)
            ->get();
        //echo "<pre>"; print_r ($imagesToDelete);echo "</pre>";
        foreach ($imagesToDelete as $image) {
            // Удаляем файл с диска public_uploads
            if (Storage::disk('public_uploads')->exists($image->path)) {
                Storage::disk('public_uploads')->delete($image->path);
            }
            $image->delete();
        }

        $this->processImages($shopProduct, $request);

        if ($request->ajax()) {

            return ['redirect' => url('admin/shop'), 'message' => trans('admin.operation.succeeded')];
        }
        return redirect('admin/shop');

    }

    protected function processImages($shopProduct, $request)
    {
        $disk = Storage::disk('public_uploads');
        $tempDir = config('filesystems.shop_paths.temp');
        $productDir = config('filesystems.shop_paths.products');

        for ($i = 1; $i <= 3; $i++) {
            $path = $request->input('image' . $i); //

            if ($path && str_contains($path, $tempDir)) {
                if ($disk->exists($path)) {
                    $fileName = basename($path);
                    $finalPath = "{$productDir}/{$shopProduct->id}/{$fileName}";

                    $disk->move($path, $finalPath);
                    $path = $finalPath;
                }
            }

            if ($path) {
                $shopProduct->images()->updateOrCreate(
                    ['position' => $i],
                    ['path' => $path]
                );
            }
        }
    }

    public function upload(Request $request)
    {
        $this->check_role();

        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');

            $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $tempDir = config('filesystems.shop_paths.temp');
            $path = $file->storeAs($tempDir, $fileName, 'public_uploads');

            return response()->json([
                'path' => $path,
                'url' => Storage::disk('public_uploads')->url($path),
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
            ], 200);
        }

        return response()->json(['error' => 'Файл не найден'], 400);
    }

    public function destroy(DestroyShopProduct $request, shopProduct $shopProduct)
    {

        if ($shopProduct->deleted){
            $productDir = config('filesystems.shop_paths.products');
            $directoryPath = "{$productDir}/{$shopProduct->id}";

            $disk = Storage::disk('public_uploads');
            if ($disk->exists($directoryPath)) {
                $disk->deleteDirectory($directoryPath);
            }
            \DB::table('shop_product_images')->where('product_id', $shopProduct->id)->delete();
            $shopProduct->delete();

        }else{
            $shopProduct->update([
                'deleted' => 1
            ]);

        }


        if ($request->ajax()) {
            return ['redirect' => url('admin/shop?deleted=1'), 'message' => trans('admin.operation.succeeded')];
        }
        return redirect('admin/shop?deleted=1');
    }

    public function deleteTempFile(Request $request)
    {

        $path = $request->input('path');
        $tempDir = config('filesystems.shop_paths.temp');

        if ($path && str_contains($path, $tempDir)) {
            if (Storage::disk('public_uploads')->exists($path)) {
                Storage::disk('public_uploads')->delete($path);
                return response()->json(['message' => 'Временный файл удален']);
            }
        }

        return response()->json(['error' => 'Файл не найден'], 400);
    }
}
