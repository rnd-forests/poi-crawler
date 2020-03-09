<?php

namespace App\Modules\Location\Controllers;

use App\Exceptions\ApiException;
use App\Exceptions\ValidationException;
use App\Modules\Location\Requests\UpdateLocationTypeTranslationRequest;
use App\Modules\Location\Services\LocationTypeService;
use Illuminate\Http\Request;
use App\Exceptions\NotFoundException;
use App\Modules\Location\Models\LocationType;
use App\Modules\Location\Requests\CreateLocationTypeRequest;
use App\Modules\Shared\Controllers\Controller;

class LocationTypeController extends Controller
{
    protected $service;

    protected $creationFields = ['name', 'slug', 'description'];

    public function __construct(LocationTypeService $service)
    {
        $this->service = $service;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (! $request->per_page) {
            return $this->resSuccess(LocationType::all());
        }

        return LocationType::paginate((int) $request->query('per_page', 15));
    }

    public function store(CreateLocationTypeRequest $request)
    {
        return $this->resSuccess(LocationType::create($request->all($this->creationFields)));
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws NotFoundException
     */
    public function show($id)
    {
        $type = LocationType::where('_id', '=', $id)->first();

        if ($type) {
            return $this->resSuccess($type);
        }

        throw new NotFoundException();
    }

    /**
     * @param $id
     * @param CreateLocationTypeRequest $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws NotFoundException
     * @throws ValidationException
     * @throws ApiException
     */
    public function update($id, CreateLocationTypeRequest $request)
    {
        if ($request->language) {
            return $this->resSuccess($this->service->createOrUpdateTranslation($id, $request));
        }

        $type = LocationType::where('_id', '=', $id)
            ->orWhere('slug', '=', $request->slug)
            ->get();

        $quantity = $type->count();

        if (!$quantity) {
            throw new NotFoundException();
        }

        if ($quantity === 2) {
            throw new ValidationException(null, __('validation.unique', ['attribute' => 'slug']));
        }

        $type = $type[0];

        if ($type->update($request->all($this->creationFields))) {
            return $this->resSuccess();
        }

        throw new ApiException();
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws NotFoundException
     */
    public function delete($id)
    {
        $isSuccess = LocationType::where('_id', '=', $id)->delete();

        if ($isSuccess) {
            return $this->resNoContent();
        }

        throw new NotFoundException();
    }

    public function getTranslation($id, $locale)
    {
        return $this->resSuccess($this->service->getTranslation($id, $locale));
    }

//    public function createTranslation($id, UpdateLocationTypeTranslationRequest $request)
//    {
//        $this->service->createTranslation($id, $request);
//
//        return $this->resNoContent();
//    }
//
//    public function updateTranslation($id, UpdateLocationTypeTranslationRequest $request)
//    {
//        $this->service->updateTranslation($id, $request);
//
//        return $this->resNoContent();
//    }
}
