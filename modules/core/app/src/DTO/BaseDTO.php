<?php

namespace Modules\App\DTO;

use Illuminate\Foundation\Http\FormRequest;

class BaseDTO
{
    public static function fromArray(array $data): self
    {
        $object = new self();
        return self::array_to_obj($data, $object);
    }

    protected static function array_to_obj($array, &$obj)
    {
        foreach ($array as $key => $value) {
            if (property_exists($obj, $key)) {
                $obj->$key = $value;
            }

        }
        return $obj;
    }

    public static function fromRequest(FormRequest $request): self
    {
        return self::fromArray($request->all());
    }
}