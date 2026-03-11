<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\SiteHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Franchisee\BulkDestroyFranchisee;
use App\Http\Requests\Admin\Franchisee\DestroyFranchisee;
use App\Http\Requests\Admin\Franchisee\IndexFranchisee;
use App\Http\Requests\Admin\Franchisee\StoreFranchisee;
use App\Http\Requests\Admin\Franchisee\UpdateFranchisee;
use App\Models\Country;
use App\Models\Franchisee;
use App\Models\City;
use App\Models\Region;
use App\Models\Currency;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TeacherGroup;

use App\AdminModule\AdminListing;
use Exception;
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

class FranchiseeController extends Controller
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
    /**
     * Display a listing of the resource.
     *
     * @param IndexFranchisee $request
     * @return array|Factory|View
     */
    public function index(IndexFranchisee $request)
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
        $data = AdminListing::create(Franchisee::class)->processRequestAndGet(
        // pass the request with params
            $request,

            // set columns to query
            ['id', 'surname', 'first_name', 'patronymic', 'region.name as region_name', 'city.name as city_name', 'teacher_group_count', 'student_count','teacher_count', 'country.name as city_name', 'country_id'],

            // set columns to searchIn
            ['id', 'surname', 'first_name', 'patronymic', 'phone', 'email', 'fin_legal', 'fin_address', 'fin_regno', 'fin_price_aboniment', 'fin_currency', 'passport', 'iin', 'subscibe_email'],
            function ($query) use ($request) {
                $query->with(['Country', 'City', 'Region']);

                $query->withCount(['TeacherGroup' => function ($query2) {
                    $query2->where('teacher_groups.deleted', 0);
                }]);
                $query->withCount(['Teacher' => function ($query2) {
                    $query2->where('teacher.deleted', 0);
                }]);

                $query->withCount(['Student'=>function ($query2) {$query2->where('student.deleted',  0)->where('student.blocked',  0); }]);
                //$query->withCount(['StudentBlock'=>function ($query3) {$query3->where('student.deleted',  0)->where('student.blocked',  1); }]);


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

        return view('admin.franchisee.index', ['data' => $data]);
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
        //$this->authorize('admin.franchisee.create');
        $CityEmpty = collect([]);
        $ipCountryCode=SiteHelper::GetIPInfo();



        return view('admin.franchisee.create', ['Country' => Country::all(), 'Currency' => Currency::all(), 'City' => $CityEmpty, 'Region' => $CityEmpty,'phone_country'=>$ipCountryCode]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreFranchisee $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreFranchisee $request)
    {
        $role = session('role','');
        if (!in_array($role,['admin' ,'franchisee'])){
            return redirect('/logout');
        }

        $sanitized = $request->getSanitized();

        $sanitized['phone'] = $request->getPhone('phoneSave');
        $sanitized['phone_country'] = $request->getPhoneCountry('phoneSave');

        $sanitized['country_id'] =(int) $request->getCountryId();
        $sanitized['region_id'] =(int) $request->getRegionId();
        $sanitized['city_id'] =(int) $request->getCityId();
        $sanitized['fin_currency'] = $request->getCurrencyCode();

        $sanitized['enabled'] = 1;

        $sanitized['password'] = Hash::make($sanitized['password']);

        $franchisee = Franchisee::create($sanitized);

        if ($request->ajax()) {

            return ['redirect' => url('admin/franchisees'), 'message' => trans('admin.operation.succeeded')];
        }

        return redirect('admin/franchisees');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param Franchisee $franchisee
     * @return Factory|View
     * @throws AuthorizationException
     */
    public function edit($id){


        $role = session('role','');
        if (!in_array($role,['admin' ,'franchisee'])){
            return redirect('/logout');
        }

        $id=(int)$id;
        $franchisee = Franchisee::find($id);
        if (!empty($franchisee->id)) {
            $franchisee->load('City');
            $franchisee->load('Region');
            $franchisee->load('Country');
            $franchisee->load('Currency');
        }

        $canBlock = self::enableBlock($id);
        if (empty($franchisee->phone_country)){
            $ipCountryCode=SiteHelper::GetIPInfo();
            if (empty($franchisee->phone_country)){
                $franchisee->phone_country=$ipCountryCode;
            }
        }


        return view('admin.franchisee.edit', ['franchisee' => $franchisee, 'Currency' => Currency::all(), 'Country' => Country::all(), 'block' => $canBlock,'locales'=>SiteHelper::GetLang()]);
    }

    private static function enableBlock($id)
    {
        $role = session('role','');
        if (!in_array($role,['admin' ,'franchisee'])){
            return redirect('/logout');
        }

        $id=(int)$id;
        $canBlock = true;
        $teacher = Teacher::where('franchisee_id', $id)
            ->where('deleted', 0)
            ->first();
        $student = Student::where('franchisee_id', $id)
            ->where('deleted', 0)
            ->first();
        $teacherGroup = TeacherGroup::where('franchisee_id', $id)
            ->where('deleted', 0)
            ->first();
        if ($teacher || $student || $teacherGroup) $canBlock = false;
        return $canBlock;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateFranchisee $request
     * @param Franchisee $franchisee
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateFranchisee $request, Franchisee $franchisee)
    {
        $role = session('role','');
        if (!in_array($role,['admin' ,'franchisee'])){
            return redirect('/logout');
        }

        $sanitized = $request->getSanitized();

        $sanitized['country_id'] =(int) $request->getCountryId();
        $sanitized['region_id'] =(int)  $request->getRegionId();
        $sanitized['city_id'] = (int) $request->getCityId();
        $sanitized['fin_currency'] = $request->getCurrencyCode();

        $sanitized['phone'] = $request->getPhone('phoneSave');
        $sanitized['phone_country'] = $request->getPhoneCountry('phoneSave');



        if (empty($sanitized['password'])) {
            unset($sanitized['password']);
        } else {
            $sanitized['password'] = Hash::make($sanitized['password']);
        }

        // Update changed values Franchisee - используем $franchisee из route model binding
        $franchisee->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/franchisees'),
                'message' => trans('admin.operation.succeeded'),
            ];
        }

        return redirect('admin/franchisees');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyFranchisee $request
     * @param Franchisee $franchisee
     * @return ResponseFactory|RedirectResponse|Response
     * @throws Exception
     */
    public function destroy(DestroyFranchisee $request, Franchisee $franchisee)
    {
        $role = session('role','');
        if (!in_array($role,['admin' ,'franchisee'])){
            return redirect('/logout');
        }

        if (isset($franchisee) && !empty($franchisee->id)) {
            $franchisee->deleted = 1;
            $franchisee->save();
        }
        if ($request->ajax()) {
            return response(['message' => trans('admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    public function lock(DestroyFranchisee $request, $id)
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
                $franchisee = Franchisee::find($id);
                if (isset($franchisee)) {
                    if (empty($franchisee->enabled)) {
                        $franchisee->enabled = 1;
                        $isLock = 'lock';
                    } else {
                        $franchisee->enabled = 0;
                        $isLock = 'unlock';
                    }
                    $franchisee->save();
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
