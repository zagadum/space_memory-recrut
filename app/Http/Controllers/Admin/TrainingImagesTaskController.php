<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TrainingImagesTask\BulkDestroyTrainingImagesTask;
use App\Http\Requests\Admin\TrainingImagesTask\DestroyTrainingImagesTask;
use App\Http\Requests\Admin\TrainingImagesTask\IndexTrainingImagesTask;
use App\Http\Requests\Admin\TrainingImagesTask\StoreTrainingImagesTask;
use App\Http\Requests\Admin\TrainingImagesTask\UpdateTrainingImagesTask;
use App\Models\TrainingImagesTask;

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

class TrainingImagesTaskController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexTrainingImagesTask $request
     * @return array|Factory|View
     */
    public function index(IndexTrainingImagesTask $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(TrainingImagesTask::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'name', 'number', 'file_path', 'lang'],

            // set columns to searchIn
            ['id', 'name', 'file_path', 'lang']
        );

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.training-images-task.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.training-images-task.create');

        return view('admin.training-images-task.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreTrainingImagesTask $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreTrainingImagesTask $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the TrainingImagesTask
        $trainingImagesTask = TrainingImagesTask::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/training-images-tasks'), 'message' => trans('admin.operation.succeeded')];
        }

        return redirect('admin/training-images-tasks');
    }

    /**
     * Display the specified resource.
     *
     * @param TrainingImagesTask $trainingImagesTask
     * @throws AuthorizationException
     * @return void
     */
    public function show(TrainingImagesTask $trainingImagesTask)
    {
        $this->authorize('admin.training-images-task.show', $trainingImagesTask);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param TrainingImagesTask $trainingImagesTask
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(int $trainingImagesTask)
    {
        $trainingImagesTask = TrainingImagesTask::where('id', $trainingImagesTask)->first();

        $this->authorize('admin.training-images-task.edit', $trainingImagesTask);

        return view('admin.training-images-task.edit', [
            'trainingImagesTask' => $trainingImagesTask,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateTrainingImagesTask $request
     * @param TrainingImagesTask $trainingImagesTask
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateTrainingImagesTask $request, int $trainingImagesTask)
    {

        // Sanitize input
        $sanitized = $request->getSanitized();
        // Update changed values TrainingImagesTask
        TrainingImagesTask::where('id', $trainingImagesTask)->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/training-images-tasks'),
                'message' => trans('admin.operation.succeeded'),
            ];
        }

        return redirect('admin/training-images-tasks');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyTrainingImagesTask $request
     * @param TrainingImagesTask $trainingImagesTask
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyTrainingImagesTask $request, int $trainingImagesTask)
    {
        $trainingImagesTask = TrainingImagesTask::where('id', $trainingImagesTask)->delete();

        if ($request->ajax()) {
            return response(['message' => trans('admin.operation.succeeded')]);
        }
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyTrainingImagesTask $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyTrainingImagesTask $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    TrainingImagesTask::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('admin.operation.succeeded')]);
    }
}
