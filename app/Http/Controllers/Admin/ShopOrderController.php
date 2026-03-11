<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\SiteHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ShopOrder\IndexShopOrder;
use App\Http\Requests\Admin\ShopOrder\DestroyShopOrder;

use App\Models\ShopOrder;
use App\Models\ShopOrderItems;
use App\Models\ShopOrderhipment;

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

class ShopOrderController extends Controller
{


    public function __construct() {

        $this->middleware(function ($request, $next) {

            $role = session('role', '');

            if (!in_array($role, ['admin', 'franchisee'])) {
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
     * @param IndexFranchisee $request
     * @return array|Factory|View
     */
    public function index(IndexShopOrder $request)
    {
        $this->check_role();
        //$request->merge(['per_page' => 2]);
        $data = AdminListing::create(ShopOrder::class)->processRequestAndGet(
        // pass the request with params
            $request,

            // set columns to query
            ['shop_orders.id', 'created_at', 'student_lastname', 'status', 'kwota', 'product_price', 'product_name'],

            // set columns to searchIn
            ['id', 'name'],
            function ($query) use ($request) {
                    $query->select('shop_orders.*');
                    $query->with(['items', 'shipment']);
                    $query->leftJoin('shop_order_items', 'shop_orders.id', '=', 'shop_order_items.order_id');

                    $query->addSelect('shop_order_items.product_price as product_price');
                    $query->addSelect('shop_order_items.product_name as product_name');


                    if ($request->has('status')) {
                        $query->where('status', $request->input('status'));
                    }


                    }

        );

        $data->getCollection()->transform(function ($product) {

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

        return view('admin.shoporder.index',
        [
            'data' => $data,
            'counts' => [
                    'all' => \App\Models\ShopOrder::count(),
                    'completed' => \App\Models\ShopOrder::where('status', 4)->count(),
                    'INPOST' => \App\Models\ShopOrder::where('status', 3)->count(),
                    'DHL' => \App\Models\ShopOrder::where('status', 2)->count(),
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

        return view('admin.shoporder.create', []);
    }

    public function createtest()
    {
        $result = DB::transaction(function () {

            $student = \App\Models\Student::findOrFail(4);
            $teacher = \App\Models\Teacher::findOrFail($student->teacher_id);
            $product = \App\Models\ShopProduct::findOrFail(6);
            $product_quantity = 2;
            $product_price  = $product->price * $product_quantity;
          //  echo "<pre>"; print_r ($student);exit;

            $order = ShopOrder::create([
                'student_surname'       => $student->surname,
                'student_lastname'       => $student->lastname,
                'parent_surname' => 'Леонид',
                'parent_lastname'  => 'Утесов',
                'teacher_surname'       =>  $teacher->surname,
                'teacher_lastname'       => $teacher->patronymic,
                'customer_surname' => 'Леонид',
                'customer_lastname'  => 'Утесов',
                'group_name'  => 'Śr 18:45 Starsza DKa',
                'phone'            => $student->phone,
                'email'            => $student->email,
                'student_id'       => $student->id, //
                'teacher_id'       => $student->teacher_id,
                'group_id'         => $student->group_id,  //
                'status'           => 1,
            ]);


            // 2. Добавляем товар к заказу (Таблица shop_order_items)
            // Используем связь items(), которую мы прописали в модели
            $order->items()->create([
                'product_id'       => $product->id,
                'product_name'     => $product->name,
                'product_quantity' => $product_quantity,
                'product_price'    => $product_price ,
            ]);

            // 3. Добавляем данные по доставке (Таблица shop_orders_shipments)

            $order->shipment()->create([
                'carrier'                   => 'InPost',
                'service'                   => 'International',
                'locker_code'               => 'BE041083', // Wybrany paczkomat
                'weight'                    => 0.70,       // Waga
                'length'                    => 35,         // Długość
                'width'                     => 25,         // Szerokość
                'height'                    => 12,         // Wysokość
                'insurance_amount'          => 0.00,       // Kwota ubezpieczenia
                'reference_number'          => '13276',    // Numer referencyjный
                'status'                    => 'new',
                'shipping_street'           => 'ul. Marszałkowska',
                'shipping_building_number'  => '10',
                'shipping_city'             => 'Warszawa',
                'shipping_postal_code'      => '00-001',
                'shipping_country_code'     => 'PL',
                'delivery_price'            => 15.00,
                'carrier_payload'           => [
                'template' => 'Z ustawień produktu',
                'method'   => 'Paczkomat/PaczkoPunkt/HUB'
                ],
            ]);

            return $order;
        });
    }


   /**
     * Store a newly created resource in storage.
     *
     * @param StoreShopOrder $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreShopOrder $request)
    {
        $this->check_role();

        $sanitized = $request->getSanitized();

        $ShopOrder = ShopOrder::create($sanitized);

        if ($request->ajax()) {

            return ['redirect' => url('admin/shop'), 'message' => trans('admin.operation.succeeded')];
        }

        return redirect('admin/shop');
    }

    public function edit(ShopOrder $ShopOrder)
    {
        $this->check_role();
        $rawStatuses = config('shop.order_statuses');
        $statuses = collect($rawStatuses)->map(function($key) {
                return trans($key);
            })->toArray();

        // ...existing code...
        $ShopOrder->load([
        'shipment',
        'items.product.images' => function ($query) {
            $query->orderBy('id', 'asc');
        }
        ]);

        //echo "<pre>"; print_r ($ShopOrder);exit;

        $ShopOrder->items->each(function($item) {
            $item->url = asset('images/default-product.png');
            if ($item->product && $item->product->images->isNotEmpty()) {
                $firstImg = $item->product->images->first();
                if (!empty($firstImg->path)) {
                    $item->url = Storage::disk('public_uploads')->url($firstImg->path);
                }
            }
        });

        return view('admin.shoporder.edit', [
            'ShopOrder' => $ShopOrder,
            'statuses'  => $statuses
        ]);
    }




    public function updateStatus(Request $request, $id)
    {
        $this->check_role();

        $order = ShopOrder::findOrFail((int)$id);

        // Обновляем только статус
        $order->update([
            'status' => $request->input('status')
        ]);

        if ($request->ajax()) {
            return ['message' => trans('admin.operation.succeeded')];
        }

        return back();
    }


    public function destroy(DestroyShopOrder $request, ShopOrder $ShopOrder)
    {

        $id = $ShopOrder->id;


        return DB::transaction(function () use ($request, $ShopOrder, $id) {
              echo  $id;
            // 1. Удаляем связанные данные (используйте ваши реальные имена таблиц)
            DB::table('shop_order_items')->where('order_id', $id)->delete();
            DB::table('shop_orders_shipments')->where('order_id', $id)->delete();

            // 2. Полное удаление самого заказа
            $ShopOrder->delete();

            if ($request->ajax()) {
                return [
                    'redirect' => url('admin/shop'),
                    'message' => trans('admin.operation.succeeded')
                ];
            }

            return redirect('admin/shop');
        });
    }

}
