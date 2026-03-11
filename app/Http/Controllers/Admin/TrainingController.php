<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\SiteHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Training\BulkDestroyTraining;
use App\Http\Requests\Admin\Training\DestroyTraining;
use App\Http\Requests\Admin\Training\IndexTraining;
use App\Http\Requests\Admin\Training\StoreTraining;
use App\Http\Requests\Admin\Training\UpdateTraining;
use App\Models\Student;
use App\Models\Training;
use App\Models\TrainingType;

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

class TrainingController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexTraining $request
     * @return array|Factory|View
     */
    public function index(IndexTraining $request)
    {

        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(Training::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'training_id', 'type_training', 't_digital', 't_bitness', 't_repetitions', 't_interlval', 'enabled'],

            // set columns to searchIn
            ['id']
        );

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.training.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.training.create');

        return view('admin.training.create');
    }

    public function startTraining($student_id=0)
    {
        $student =Student::find($student_id);

        if($student) {
            $trainingTypes = TrainingType::all();
            $capacity = SiteHelper::getCapacityArray();
            $digit_number = SiteHelper::getDigitNumberArray();
            $digitIntervals = SiteHelper::getIntervalsArray();
            $digitIncrements = SiteHelper::getDigitIncrementsArray();
            $intervals = SiteHelper::getIntervalsArray();
            $repeat_number = SiteHelper::getRepeatNumberArray();
            //  dd($trainingTypes);

            return view('admin.training.start_training', ['trainingTypes' => $trainingTypes, 'capacity' => $capacity,
                'digit_number' => $digit_number, 'digitIntervals' => $digitIntervals, 'intervals' => $intervals,
                'repeat_number' => $repeat_number, 'digitIncrements' => $digitIncrements, 'student_id' => $student_id]);
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param StoreTraining $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreTraining $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the Training
        $training = Training::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/trainings'), 'message' => trans('admin.operation.succeeded')];
        }

        return redirect('admin/trainings');
    }

    /**
     * Display the specified resource.
     *
     * @param Training $training
     * @throws AuthorizationException
     * @return void
     */
    public function show(Training $training)
    {
        $this->authorize('admin.training.show', $training);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Training $training
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(Training $training)
    {
        $this->authorize('admin.training.edit', $training);


        return view('admin.training.edit', [
            'training' => $training,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateTraining $request
     * @param Training $training
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateTraining $request, Training $training)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values Training
        $training->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/trainings'),
                'message' => trans('admin.operation.succeeded'),
            ];
        }

        return redirect('admin/trainings');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyTraining $request
     * @param Training $training
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyTraining $request, Training $training)
    {
        $training->delete();

        if ($request->ajax()) {
            return response(['message' => trans('admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyTraining $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyTraining $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    Training::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('admin.operation.succeeded')]);
    }
}
