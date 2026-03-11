<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TrainingStatistic\BulkDestroyTrainingStatistic;
use App\Http\Requests\Admin\TrainingStatistic\DestroyTrainingStatistic;
use App\Http\Requests\Admin\TrainingStatistic\IndexTrainingStatistic;
use App\Http\Requests\Admin\TrainingStatistic\StoreTrainingStatistic;
use App\Http\Requests\Admin\TrainingStatistic\UpdateTrainingStatistic;
use App\Models\TrainingStatistic;
//use Brackets\AdminListing\Facades\AdminListing;
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

class TrainingStatisticsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexTrainingStatistic $request
     * @return array|Factory|View
     */
    public function index(IndexTrainingStatistic $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(TrainingStatistic::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'training_id', 'name', 'dates', 'tolal_good', 'tolal_bad', 'total_today'],

            // set columns to searchIn
            ['id', 'name']
        );

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.training-statistic.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.training-statistic.create');

        return view('admin.training-statistic.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreTrainingStatistic $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreTrainingStatistic $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the TrainingStatistic
        $trainingStatistic = TrainingStatistic::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/training-statistics'), 'message' => trans('admin.operation.succeeded')];
        }

        return redirect('admin/training-statistics');
    }

    /**
     * Display the specified resource.
     *
     * @param TrainingStatistic $trainingStatistic
     * @throws AuthorizationException
     * @return void
     */
    public function show(TrainingStatistic $trainingStatistic)
    {
        $this->authorize('admin.training-statistic.show', $trainingStatistic);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param TrainingStatistic $trainingStatistic
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(TrainingStatistic $trainingStatistic)
    {
        $this->authorize('admin.training-statistic.edit', $trainingStatistic);


        return view('admin.training-statistic.edit', [
            'trainingStatistic' => $trainingStatistic,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateTrainingStatistic $request
     * @param TrainingStatistic $trainingStatistic
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateTrainingStatistic $request, TrainingStatistic $trainingStatistic)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values TrainingStatistic
        $trainingStatistic->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/training-statistics'),
                'message' => trans('admin.operation.succeeded'),
            ];
        }

        return redirect('admin/training-statistics');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyTrainingStatistic $request
     * @param TrainingStatistic $trainingStatistic
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyTrainingStatistic $request, TrainingStatistic $trainingStatistic)
    {
        $trainingStatistic->delete();

        if ($request->ajax()) {
            return response(['message' => trans('admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyTrainingStatistic $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyTrainingStatistic $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    TrainingStatistic::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('admin.operation.succeeded')]);
    }
}
