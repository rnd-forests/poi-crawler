<?php

namespace App\Modules\Location\Controllers;

use App\Exceptions\BadRequestException;
use App\Modules\Location\Requests\BulkUpdateLocationRequest;
use App\Modules\Location\Requests\GetLocationsRequest;
use App\Modules\Location\Requests\SearchLocationRequest;
use App\Modules\Location\Services\GroupLocationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Exceptions\NotFoundException;
use App\Modules\Location\Models\Location;
use App\Modules\Location\Requests\CreateLocationRequest;
use App\Modules\Location\Requests\UpdateLocationRequest;
use App\Modules\Location\Services\LocationService;
use App\Modules\Shared\Controllers\Controller;

class LocationController extends Controller
{
    protected $service;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(LocationService $locationService)
    {
        $this->service = $locationService;
    }

    /**
     * Show the application dashboard.
     *
     * @return mixed
     */
    public function index(GetLocationsRequest $request)
    {
        return $this->service->getLocations($request);
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws NotFoundException
     */
    public function show($id)
    {
        $data = $this->service->firstOrFail($id)->toArray();
        unset($data['review']);

        return $this->resSuccess($data);
    }

    public function store(CreateLocationRequest $request)
    {
        return $this->resSuccess($this->service->createLocation($request));
    }

    /**
     * @param $id
     * @param UpdateLocationRequest $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws NotFoundException
     * @throws \App\Exceptions\ValidationException
     * @throws BadRequestException
     */
    public function update($id, UpdateLocationRequest $request)
    {
        if ($request->language) {
            return $this->resSuccess($this->service->createOrUpdateTranslation($id, $request));
        }

        $location = $this->service->updateLocation($id, $request);

        return $this->resSuccess($location);
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws NotFoundException
     */
    public function destroy($id)
    {
        $this->service->delete($id);

        return $this->resNoContent();
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \App\Exceptions\ApiException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function detect(Request $request)
    {
        return $this->resSuccess($this->service->detect($request));
    }

    /**
     * @param BulkUpdateLocationRequest $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws BadRequestException
     */
    public function bulkUpdate(BulkUpdateLocationRequest $request)
    {
        if ($request->action === 'delete') {
            $this->service->deleteMany($request->data);
        } else {
            throw new BadRequestException(null, 'action not exist');
        }

        return $this->resSuccess();
    }

    /**
     * @param $id
     * @param $locale
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws NotFoundException
     */
    public function getTranslation($id, $locale)
    {
        return $this->resSuccess($this->service->getTranslation($id, $locale));
    }

    public function search(SearchLocationRequest $request)
    {
        return $this->resSuccess($this->service->search($request));
    }

    public function searchByGroup(SearchLocationRequest $request, GroupLocationService $groupLocationService)
    {
        return $this->resSuccess($groupLocationService->searchByGroup($request));
    }
}
