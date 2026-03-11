<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PaymentPeriod\BulkDestroyPaymentPeriod;
use App\Http\Requests\Admin\PaymentPeriod\DestroyPaymentPeriod;
use App\Http\Requests\Admin\PaymentPeriod\IndexPaymentPeriod;
use App\Http\Requests\Admin\PaymentPeriod\StorePaymentPeriod;
use App\Http\Requests\Admin\PaymentPeriod\UpdatePaymentPeriod;
use App\Models\PaymentPeriod;

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

class PaymentPeriodController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexPaymentPeriod $request
     * @return array|Factory|View
     */
    public function index(IndexPaymentPeriod $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(PaymentPeriod::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'name', 'discount', 'term'],

            // set columns to searchIn
            ['id', 'name', 'term']
        );

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.payment-period.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.payment-period.create');

        return view('admin.payment-period.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StorePaymentPeriod $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StorePaymentPeriod $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the PaymentPeriod
        $paymentPeriod = PaymentPeriod::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/payment-periods'), 'message' => trans('admin.operation.succeeded')];
        }

        return redirect('admin/payment-periods');
    }

    /**
     * Display the specified resource.
     *
     * @param PaymentPeriod $paymentPeriod
     * @throws AuthorizationException
     * @return void
     */
    public function show(PaymentPeriod $paymentPeriod)
    {
        $this->authorize('admin.payment-period.show', $paymentPeriod);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param PaymentPeriod $paymentPeriod
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(PaymentPeriod $paymentPeriod)
    {
        $this->authorize('admin.payment-period.edit', $paymentPeriod);


        return view('admin.payment-period.edit', [
            'paymentPeriod' => $paymentPeriod,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdatePaymentPeriod $request
     * @param PaymentPeriod $paymentPeriod
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdatePaymentPeriod $request, PaymentPeriod $paymentPeriod)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values PaymentPeriod
        $paymentPeriod->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/payment-periods'),
                'message' => trans('admin.operation.succeeded'),
            ];
        }

        return redirect('admin/payment-periods');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyPaymentPeriod $request
     * @param PaymentPeriod $paymentPeriod
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyPaymentPeriod $request, PaymentPeriod $paymentPeriod)
    {
        $paymentPeriod->delete();

        if ($request->ajax()) {
            return response(['message' => trans('admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyPaymentPeriod $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyPaymentPeriod $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    PaymentPeriod::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('admin.operation.succeeded')]);
    }
}
