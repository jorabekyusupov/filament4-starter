<?php

namespace Modules\Language\Livewire;

use Livewire\Component;

class Translate extends Component

{
    public $search = '';
    public $key = '';
    public $data = [
    ];

    public function render()
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
            $langData = json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

            $data[$name] = array_merge($langData, $data[$name] ?? []);
        }

        //get all keys in ru.json
        $keys = array_keys($data['ru']);

        $langKeys = array_keys($data);
        // search keys
        if ($this->search) {
            $keys = array_filter($keys, function ($key) {
                return str_contains($key, $this->search);
            });
        }
        $this->data = $data;

        return view('languages::livewire.translate', compact(['data', 'keys', 'langKeys']));
    }

    /**
     * @throws \JsonException
     */
    public function addkey()
    {
        $languages = cache()->get('languages');

        $key = $this->key;

        foreach ($languages as $language) {
            //exist file lang code
            if (file_exists(storage_path('app/public/languages/' . $language->code . '.json'))) {
                $data = json_decode(file_get_contents(storage_path('app/public/languages/' . $language->code . '.json')), true, 512, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                $data[$key] = '';
                file_put_contents(storage_path('app/public/languages/' . $language->code . '.json'), json_encode($data, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            } else {
                $data = [];
                $data[$key] = '';
                file_put_contents(storage_path('app/public/languages/' . $language->code . '.json'), json_encode($data, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            }
        }
        $this->search = $key;
        $this->key = '';
    }

    public function updateValue($value, $key, $lang)

    {

        $getStorageLang = glob(base_path('lang') . '/*.json');

        //add Value this language

        foreach ($getStorageLang as $file) {
            if (basename($file, '.json') == $lang) {
                $data = json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                $data[$key] = $value;
                file_put_contents($file, json_encode($data, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            }
        }
    }

    public function downloadJsonFileInZIP()
    {
        $zip = new \ZipArchive();
        $fileName = 'languages.zip';
        if (file_exists(storage_path('app/public/' . $fileName))) {
            unlink(storage_path('app/public/' . $fileName));
        }
        if ($zip->open(storage_path('app/public/' . $fileName), \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            $files = glob(base_path('lang') . '/*.json');
            foreach ($files as $file) {
                $zip->addFile($file, basename($file));
            }
            $zip->close();
        }
        return response()->download(storage_path('app/public/' . $fileName));

    }

}
