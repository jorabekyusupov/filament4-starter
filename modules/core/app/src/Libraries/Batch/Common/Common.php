<?php

namespace Modules\App\Libraries\Batch\Common;

use Illuminate\Support\Facades\DB;

class Common
{
    public static function mysql_escape($inp)
    {
        if(is_array($inp)) return array_map(__METHOD__, $inp);

        if(!empty($inp) && is_string($inp))
        {
            return str_replace(
                ['\\', "\0", "\n", "\r", "'", '"', "\x1a"],
                ['\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'],
                $inp);
        }

        return $inp;
    }
    public static function postgres_escape($value) {
        // PostgreSQL qochish (escaping) usuli, masalan, pg_escape_literal yoki pg_escape_string
          return DB::connection()->getPdo()->quote($value); // Agar literal bo'lsa yoki pg_escape_string($value)
    }
}
