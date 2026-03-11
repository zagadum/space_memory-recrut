<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Region\BulkDestroyRegion;
use App\Http\Requests\Admin\Region\DestroyRegion;
use App\Http\Requests\Admin\Region\IndexRegion;
use App\Http\Requests\Admin\Region\StoreRegion;
use App\Http\Requests\Admin\Region\UpdateRegion;
use App\Models\Region;

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

class RegionController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexRegion $request
     * @return array|Factory|View
     */
    public function index(IndexRegion $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(Region::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'name', 'country_id', 'enabled'],

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

        return view('admin.region.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.region.create');

        return view('admin.region.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRegion $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreRegion $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the Region
        $region = Region::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/regions'), 'message' => trans('admin.operation.succeeded')];
        }

        return redirect('admin/regions');
    }

    /**
     * Display the specified resource.
     *
     * @param Region $region
     * @throws AuthorizationException
     * @return void
     */
    public function show(Region $region)
    {
        $this->authorize('admin.region.show', $region);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Region $region
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(Region $region)
    {
        $this->authorize('admin.region.edit', $region);


        return view('admin.region.edit', [
            'region' => $region,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRegion $request
     * @param Region $region
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateRegion $request, Region $region)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values Region
        $region->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/regions'),
                'message' => trans('admin.operation.succeeded'),
            ];
        }

        return redirect('admin/regions');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyRegion $request
     * @param Region $region
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyRegion $request, Region $region)
    {
        $region->delete();

        if ($request->ajax()) {
            return response(['message' => trans('admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyRegion $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyRegion $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    Region::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('admin.operation.succeeded')]);
    }
}
