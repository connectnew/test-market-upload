<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class FileRule implements Rule
{
    protected $maxPostSize;
    protected $maxUploadFileSize;
    protected $message;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->message = '';
        $this->maxPostSize = $this->toBytes(ini_get('post_max_size'));
        $this->maxUploadFileSize = $this->toBytes(ini_get('upload_max_filesize'));
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $fileSize = $value->getSize();
        $fileMimeType = $value->getClientMimeType();

        if ($fileSize > $this->maxPostSize) {
            $this->message = "$attribute size must be less " . round($this->maxPostSize / 1048576, 2) . " megabytes";
            return false;
        }
        if ($fileSize > $this->maxUploadFileSize) {
            $this->message = "$attribute size must be less " . round($this->maxUploadFileSize / 1048576, 2) . " megabytes";
            return false;
        }
        if ($fileMimeType !== "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet") {
            $this->message = "$attribute type must be xlsx";
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }

    private function toBytes($configValue)
    {
        preg_match('/(?<value>\d+)(?<option>.?)/i', trim($configValue), $matches);
        $inc = array(
            'g' => 1073741824, // (1024 * 1024 * 1024)
            'm' => 1048576, // (1024 * 1024)
            'k' => 1024
        );

        $value = (int) $matches['value'];
        $key = strtolower(trim($matches['option']));
        if (isset($inc[$key])) {
            $value *= $inc[$key];
        }

        return $value;
    }
}
