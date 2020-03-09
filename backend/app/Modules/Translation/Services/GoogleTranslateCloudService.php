<?php
/**
 * Created by Trang Ha Viet.
 * Contact: <viettrangha@gmail.com>
 * 07/12/2018 15:33
 */

namespace App\Modules\Translation\Services;

use App\Exceptions\BadRequestException;
use Google\Cloud\Translate\TranslateClient;

class GoogleTranslateCloudService
{
    protected $translator;

    protected $target;

    protected $source;

    /**
     * GoogleTranslateCloudService constructor.
     *
     * All supported language at https://cloud.google.com/translate/docs/languages
     *
     * @param string $target default 'en'
     * @param string $source default 'vi'
     */
    public function __construct($target = 'en', $source = 'vi')
    {
        $this->target = $target;
        $this->source = $source;

        $this->translator = new TranslateClient([
            'key' => config('services.google.cloud.key'),
        ]);
    }

    /**
     * @param string $text
     * @return string
     * @throws BadRequestException
     */
    public function trans($text)
    {
        if ($this->target === $this->source) return $text;

        $this->checkMaxLength($text);

        return $this->translator->translate($text, [
            'target' => $this->target,
        ])['text'];
    }

    public function setSource($lang)
    {
        $this->source = $lang;
    }

    public function setTarget($lang)
    {
        $this->target = $lang;
    }

    /**
     * @param string $text
     * @throws BadRequestException
     */
    protected function checkMaxLength($text)
    {
        if (strlen($text) > config('services.google.cloud.translate_max_length')) {
            throw new BadRequestException(
                null,
                'Độ dài lớn nhất cho phép là ' .
                config('services.google.cloud.translate_max_length') .
                ' ký tự. Liên hệ quản trị viên nếu bạn muốn tăng độ dài văn bản cho phép.'
            );
        }
    }
}
