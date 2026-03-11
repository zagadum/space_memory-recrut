<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TrainingWordsTask\BulkDestroyTrainingWordsTask;
use App\Http\Requests\Admin\TrainingWordsTask\DestroyTrainingWordsTask;
use App\Http\Requests\Admin\TrainingWordsTask\IndexTrainingWordsTask;
use App\Http\Requests\Admin\TrainingWordsTask\StoreTrainingWordsTask;
use App\Http\Requests\Admin\TrainingWordsTask\UpdateTrainingWordsTask;
use App\Models\TrainingWordsTask;

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

class TrainingWordsTaskController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexTrainingWordsTask $request
     * @return array|Factory|View
     */
    public function index(IndexTrainingWordsTask $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(TrainingWordsTask::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id','name','part_word','lang'],

            // set columns to searchIn
            ['name','part_word'],
        );

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.training-words-task.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.training-words-task.create');

        return view('admin.training-words-task.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreTrainingWordsTask $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreTrainingWordsTask $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the TrainingWordsTask
        $trainingWordsTask = TrainingWordsTask::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/training-words-tasks'), 'message' => trans('admin.operation.succeeded')];
        }

        return redirect('admin/training-words-tasks');
    }

    /**
     * Display the specified resource.
     *
     * @param TrainingWordsTask $trainingWordsTask
     * @throws AuthorizationException
     * @return void
     */
    public function show(TrainingWordsTask $trainingWordsTask)
    {
        $this->authorize('admin.training-words-task.show', $trainingWordsTask);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param TrainingWordsTask $trainingWordsTask
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(int $trainingWordsTask)
    {
        $trainingWordsTask = TrainingWordsTask::where('id', $trainingWordsTask)->first();

        $this->authorize('admin.training-words-task.edit', $trainingWordsTask);

        return view('admin.training-words-task.edit', [
            'trainingWordsTask' => $trainingWordsTask,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateTrainingWordsTask $request
     * @param TrainingWordsTask $trainingWordsTask
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateTrainingWordsTask $request, int $trainingWordsTask)
    {


        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values TrainingWordsTask
//        $trainingWordsTask->update($sanitized);

        TrainingWordsTask::where('id', $trainingWordsTask)->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/training-words-tasks'),
                'message' => trans('admin.operation.succeeded'),
            ];
        }

        return redirect('admin/training-words-tasks');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyTrainingWordsTask $request
     * @param TrainingWordsTask $trainingWordsTask
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyTrainingWordsTask $request, int $trainingWordsTask)
    {
        $trainingWordsTask = TrainingWordsTask::where('id', $trainingWordsTask)->delete();

        if ($request->ajax()) {
            return response(['message' => trans('admin.operation.succeeded')]);
        }
    }


    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyTrainingWordsTask $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyTrainingWordsTask $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    TrainingWordsTask::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('admin.operation.succeeded')]);
    }
}
