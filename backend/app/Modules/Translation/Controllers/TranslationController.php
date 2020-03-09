<?php
/**
 * Created by Trang Ha Viet.
 * Contact: <viettrangha@gmail.com>
 * 08/12/2018 09:12
 */

namespace App\Modules\Translation\Controllers;

use App\Modules\Translation\Services\GoogleTranslateCloudService;
use Illuminate\Http\Request;
use App\Modules\Shared\Controllers\Controller;
use App\Modules\Translation\Services\TranslationService;

class TranslationController extends Controller
{
    protected $service;
    protected $cloudService;

    public function __construct(TranslationService $service, GoogleTranslateCloudService $cloudService)
    {
        $this->service = $service;

        $this->cloudService = $cloudService;
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \App\Exceptions\BadRequestException
     */
    public function trans(Request $request)
    {
        $this->setTargetLanguage();

        return $this->resSuccess($this->service->ensureTrans($request->query('text')));
    }

    public function countAllNameCharacters()
    {
        return $this->resSuccess(
            $this->service->countAllCharactersForField('name')
        );
    }

    protected function setTargetLanguage()
    {
        $target = request()->query('target');

        if ($target) {
            $this->service->setTarget($target);

            $this->cloudService->setTarget($target);
        }
    }
}
