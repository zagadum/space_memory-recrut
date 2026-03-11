<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StudentPayment\BulkDestroyStudentPayment;
use App\Http\Requests\Admin\StudentPayment\DestroyStudentPayment;
use App\Http\Requests\Admin\StudentPayment\IndexStudentPayment;
use App\Http\Requests\Admin\StudentPayment\StoreStudentPayment;
use App\Http\Requests\Admin\StudentPayment\UpdateStudentPayment;
use App\Models\StudentPayment;

use App\AdminModule\AdminListing;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StudentPaymentController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexStudentPayment $request
     * @return array|Factory|View
     */
    public function index(IndexStudentPayment $request)
    {

        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(StudentPayment::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query  'surname', 'lastname', 'patronymic',  'teacher_groups.name as teacher',
            ['id',  'date_pay', 'date_finish', 'sum_aboniment', 'payment_period.name as type_aboniment', 'type_pay', 'enabled'],

            // set columns to searchIn
            ['id', 'type_aboniment', 'type_pay'], function ($query) use ($request) {
            $query->leftjoin('payment_period', 'payment_period.id', '=', 'student_payment.aboniment_id');

            }
        );

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.student-payment.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        //$this->authorize('admin.student-payment.create');

        return view('admin.student-payment.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreStudentPayment $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreStudentPayment $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the StudentPayment
        $studentPayment = StudentPayment::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/student-payments'), 'message' => trans('admin.operation.succeeded')];
        }

        return redirect('admin/student-payments');
    }

    /**
     * Display the specified resource.
     *
     * @param StudentPayment $studentPayment
     * @throws AuthorizationException
     * @return void
     */
    public function show(StudentPayment $studentPayment)
    {
        $this->authorize('admin.student-payment.show', $studentPayment);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param StudentPayment $studentPayment
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(StudentPayment $studentPayment)
    {
        $this->authorize('admin.student-payment.edit', $studentPayment);


        return view('admin.student-payment.edit', [
            'studentPayment' => $studentPayment,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateStudentPayment $request
     * @param StudentPayment $studentPayment
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateStudentPayment $request, StudentPayment $studentPayment)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values StudentPayment
        $studentPayment->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/student-payments'),
                'message' => trans('admin.operation.succeeded'),
            ];
        }

        return redirect('admin/student-payments');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyStudentPayment $request
     * @param StudentPayment $studentPayment
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyStudentPayment $request, StudentPayment $studentPayment)
    {
        $studentPayment->delete();

        if ($request->ajax()) {
            return response(['message' => trans('admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyStudentPayment $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyStudentPayment $request) : Response
    {
//        DB::transaction(static function () use ($request) {
//            collect($request->data['ids'])
//                ->chunk(1000)
//                ->each(static function ($bulkChunk) {
//                    StudentPayment::whereIn('id', $bulkChunk)->delete();
//
//                    // TODO your code goes here
//                });
//        });

        return response(['message' => trans('admin.operation.succeeded')]);
    }
}
