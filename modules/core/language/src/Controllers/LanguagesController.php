<?php

namespace Modules\Language\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Modules\Language\Repositories\LanguageReadRepositoryInterface;
use Modules\Language\Repositories\LanguageWriteRepositoryInterface;

class LanguagesController extends Controller
{
    public function __construct(
        protected LanguageReadRepositoryInterface  $languageReadRepository,
        protected LanguageWriteRepositoryInterface $languageWriteRepository
    )
    {
    }

    public function switchLang($old, $code)
    {

        $previousUrl = url()->previous();
        $lang = $this->languageReadRepository->findWithCode($code);
        if ($lang) {
            $this->languageWriteRepository->updateIsDefault($code);
        }else{
            $newUrl = '/'.$old.'/'.ltrim($previousUrl, url('/'));
        }

        $newUrl = str_replace('/' . $old, '/' . $code, $previousUrl);

        return redirect($newUrl);


    }

    public function translate()
    {
        $json = base_path('lang');
        //get all json files in lang directory
        $files = glob($json . '/*.json');
        //get all json files get name and data
        $data = [];
        $storagePath = storage_path('app/public/languages');
        $storageFiles = glob($storagePath . '/*.json');

        foreach (array_merge($storageFiles, $files) as $file) {
            $name = basename($file, '.json');
            $data[$name] = json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);
        }

        //get all keys in ru.json
        $keys = array_keys($data['ru']);
        $langKeys = array_keys($data);

        return view('languages::translate', compact(['data', 'keys', 'langKeys']));
    }


}
