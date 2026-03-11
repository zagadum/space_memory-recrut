<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\SiteHelper;
use App\Http\Controllers\Controller;


use App\Models\Franchisee;
use App\Models\Managers;

use App\Http\Requests\Admin\Managers\IndexManagers;
use App\Http\Requests\Admin\Managers\StoreManagers;
use App\Http\Requests\Admin\Managers\UpdateManagers;
use App\Http\Requests\Admin\Managers\DestroyManagers;

use App\Models\Teacher;

use App\AdminModule\AdminListing;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;


class ManagersController extends Controller
{


    public function __construct() {

//        $this->middleware(function ($request, $next) {
//            $role = session('role', '');
//
//            if (!in_array($role, ['admin', 'franchisee'])) {
//                abort(403, 'Доступ запрещен');
//            }
//            return $next($request);
//        });
    }
    /**
     * Display a listing of the resource.
     *
     * @param IndexManagers $request
     * @return array|Factory|View
     */
    public function index(IndexManagers $request)
    {

        setcookie("per_page", 50, time() + 3600000000, "/"); //При входе добавить
        $role = session('role');


        if (empty($role))
        {
            die('Error role');
        }
        if (!in_array($role,['admin' ,'franchisee'])){
            return redirect('/logout');
        }
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(Managers::class)->processRequestAndGet(
        // pass the request with params
            $request,

            // set columns to query
            ['id', 'surname', 'first_name', 'patronymic','enabled','email'],
            // set columns to searchIn
            ['id', 'surname', 'first_name', 'patronymic',   'email'  ],
            function ($query) use ($request) {


                $query->where('deleted', 0);
            }
        );

        if (!empty($data)) {
            foreach ($data as &$items) {
                $items->city_name = @$items->Country->name . ' / ' . @$items->Region->name . ' / ' . @$items->City->name;
            }
        }

        if ($request->ajax()) {

            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.managers.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Factory|View
     * @throws AuthorizationException
     */
    public function create()
    {
        $role = session('role','');
        if (!in_array($role,['admin' ,'franchisee'])){
            return redirect('/logout');
        }

        return view('admin.managers.create', []);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreFranchisee $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreManagers $request)
    {
        $role = session('role','');
        if (!in_array($role,['admin' ,'franchisee'])){
            return redirect('/logout');
        }

        // Валидация
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'string',
                function ($attribute, $value, $fail) {
                    if (
                        Managers::where('email', $value)->exists() ||
                        Franchisee::where('email', $value)->exists() ||
                        Teacher::where('email', $value)->exists()
                    ) {
                        $fail('Этот email уже используется в другой таблице.');
                    }
                },
            ],
        ]);
        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $sanitized = $request->getSanitized();

      //  $sanitized['phone'] = $request->getPhone('phoneSave');
       // $sanitized['phone_country'] = $request->getPhoneCountry('phoneSave');

//        $sanitized['country_id'] =(int) $request->getCountryId();
//        $sanitized['region_id'] =(int) $request->getRegionId();
//        $sanitized['city_id'] =(int) $request->getCityId();


        $sanitized['enabled'] = 1;

        $sanitized['password'] = Hash::make($sanitized['password']);

        Managers::create($sanitized);

        if ($request->ajax()) {

            return ['redirect' => url('admin/managers'), 'message' => trans('admin.operation.succeeded')];
        }

        return redirect('admin/managers');
    }



    public function edit($id){

        $role = session('role','');
        if (!in_array($role,['admin' ,'franchisee'])){
            return redirect('/logout');
        }

        $id=(int)$id;
        $managers = Managers::find($id);

        $canBlock = self::enableBlock($id);


        return view('admin.managers.edit', ['managers' => $managers]);
    }

    private static function enableBlock($id){
        $role = session('role','');
        if (!in_array($role,['admin' ,'franchisee'])){
            return  false;
        }
        return  true;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateManagers $request
     * @param Franchisee $franchisee
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateManagers $request, Managers $managers)
    {
        $role = session('role','');
        if (!in_array($role,['admin' ,'franchisee'])){
            return redirect('/logout');
        }

        // Валидация
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'string',
                function ($attribute, $value, $fail) use ($managers) {
                    if (
                        Managers::where('email', $value)->where('id', '!=', $managers->id)->exists() ||
                        Franchisee::where('email', $value)->exists() ||
                        Teacher::where('email', $value)->exists()
                    ) {
                        $fail('Этот email уже используется в другой таблице.');
                    }
                },
            ],
        ]);
        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }


        $sanitized = $request->getSanitized();

        if (empty($sanitized['password'])) {
            unset($sanitized['password']);
        } else {
            $sanitized['password'] = Hash::make($sanitized['password']);
        }

        // Используем $managers напрямую из route model binding
        $managers->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/managers'),
                'message' => trans('admin.operation.succeeded'),
            ];
        }

        return redirect('admin/managers');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyManagers $request
     * @param Managers $managers
     * @return ResponseFactory|RedirectResponse|Response
     * @throws Exception
     */
    public function destroy(DestroyManagers $request, $id)
    {
        $id=(int)$id;
        $role = session('role','');
        if (!in_array($role,['admin' ,'franchisee'])){
            return redirect('/logout');
        }
        $managers = Managers::find($id);

        if (isset($managers) && !empty($managers->id)) {
            $managers->deleted = 1;
            $managers->save();
        }
        if ($request->ajax()) {
            return response(['message' => trans('admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    public function lock(DestroyManagers $request, $id)
    {

        $id=(int)$id;
        $role = session('role','');
        if (!in_array($role,['admin' ,'franchisee'])){
            return redirect('/logout');
        }

        $canBlock = self::enableBlock($id);

        if ($canBlock) {
            $isLock = 'lock';
            if ($id > 0) {
                $managers = Managers::find($id);
                if (isset($managers)) {
                    if (empty($managers->enabled)) {
                        $managers->enabled = 1;
                        $isLock = 'lock';
                    } else {
                        $managers->enabled = 0;
                        $isLock = 'unlock';
                    }
                    $managers->save();
                }

                if ($request->ajax()) {
                    return response(['message' => trans('admin.operation.' . $isLock)]);
                }

            } else {
                return response(['message' => 'error']);
            }
            return redirect()->back();
        } else return redirect()->back();
    }

}
