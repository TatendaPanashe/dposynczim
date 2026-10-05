<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class ResilientEncrypted implements CastsAttributes
{
    public function __construct(private readonly string $type = 'string') {}

    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($value === null || $value === '') {
            return $this->defaultValue();
        }

        try {
            return $this->normalize(Crypt::decrypt($value, false));
        } catch (\Throwable) {
            return $this->fallbackValue($value);
        }
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($value === null) {
            return null;
        }

        $value = $this->normalize($value);

        return Crypt::encrypt($this->type === 'array' ? json_encode($value, JSON_THROW_ON_ERROR) : $value, false);
    }

    private function normalize(mixed $value): mixed
    {
        if ($this->type === 'array') {
            if (is_array($value)) {
                return $value;
            }

            if (is_string($value)) {
                $decoded = json_decode($value, true);

                return is_array($decoded) ? $decoded : [];
            }

            return [];
        }

        if (is_array($value)) {
            return json_encode($value, JSON_THROW_ON_ERROR);
        }

        return (string) $value;
    }

    private function fallbackValue(mixed $value): mixed
    {
        if (! is_string($value)) {
            return $this->defaultValue();
        }

        if ($this->type === 'array') {
            $decoded = json_decode($value, true);

            return is_array($decoded) ? $decoded : [];
        }

        if (Str::startsWith($value, 'eyJpdiI6')) {
            report(new \RuntimeException('Unable to decrypt '.$this->type.' attribute. Check APP_KEY consistency before relying on historical encrypted data.'));

            return '';
        }

        return $value;
    }

    private function defaultValue(): mixed
    {
        return $this->type === 'array' ? [] : '';
    }
}
