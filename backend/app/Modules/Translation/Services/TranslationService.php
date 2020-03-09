<?php
/**
 * Created by Trang Ha Viet.
 * Contact: <viettrangha@gmail.com>
 * 07/12/2018 15:33
 */

namespace App\Modules\Translation\Services;

use App\Events\SlackNotifyEvent;
use Illuminate\Support\Facades\DB;
use Stichoza\GoogleTranslate\GoogleTranslate;

class TranslationService
{
    protected $translator;

    protected $cloudService;

    public function __construct($target = 'en', $source = 'vi')
    {
        $this->translator = new GoogleTranslate($target);

        $this->translator->setSource($source);

        $this->cloudService = app(GoogleTranslateCloudService::class);
    }

    /**
     * Translate some text, use it
     *
     * @param string $message
     * @return null|string
     * @throws \Exception
     */
    public function trans($message)
    {
        try {
            return $this->translator->translate($message);
        } catch (\Exception $e) {
            event(new SlackNotifyEvent(exceptionToSlackMessage($e, "Can't use free google translate package")));

//            return $message;
            throw $e;
        }
    }

    public function ensureTrans($text)
    {
        try {
            return $this->trans($text);
        } catch (\Exception $e) {
            return $this->cloudService->trans($text);
        }
    }

    public function setSource($lang)
    {
        $this->translator->setSource($lang);
    }

    public function setTarget($lang)
    {
        $this->translator->setTarget($lang);
    }

    public function countAllCharactersForField($field)
    {
        return DB::table('locations')
            ->get([$field])
            ->reduce(function ($total, $e) use ($field) {
                return $total + strlen($e[$field]);
            }, 0);
    }
}
