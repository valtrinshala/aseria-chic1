<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;

class LanguageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('languages/language-index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('languages/language-create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'locale' => 'required|unique:languages',
                'image' => 'required|extensions:jpg,jpeg,png,svg,webp'
            ]);
            $data = $request->all();
            $isoLang = $this->isoLanguages()[$data['locale']];
            $id = Uuid::uuid4()->toString();
            $data['id'] = $id;
            $data['locale'] = $isoLang['locale'];
            $data['set2'] = $isoLang['set2'];
            $data['name'] = $isoLang['name'];
            if ($request->file('image')) {
                $data['image'] = $this->itemImageValidated($request);
            }
            Language::create($data);
            if (!file_exists(lang_path('en.json'))) {
                $this->createDirectoryForLanguage();
                File::put(base_path() . '/resources/lang/en.json', json_encode(["key" => "value"], JSON_THROW_ON_ERROR));
            }
            return response()->json(['success' => 'You have added language', 'id' => $id]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Language $language)
    {
        if (!file_exists(lang_path('en.json'))) {
            $this->createDirectoryForLanguage();
            $filePath = base_path('resources/lang/' . $language->locale . '.json');
            File::put(base_path() . '/resources/lang/' . 'en.json', json_encode(["key" => "value"], JSON_THROW_ON_ERROR));
            File::put($filePath, json_encode(["key" => "value"], JSON_THROW_ON_ERROR));
        }
        $keys = array_keys($this->getFile('en'));
        $words = [];
        if (file_exists(lang_path($language->locale . '.json'))) {
            $words = $this->getFile($language->locale);
        }

        if (!file_exists(lang_path('android/en.json'))) {
            $directoryPath = base_path('resources/lang/android/');
            if (!File::exists($directoryPath)) {
                File::makeDirectory($directoryPath, 0755, true, true);
            }
            $filePath = base_path('resources/lang/android/' . $language->locale . '.json');
            File::put(base_path() . '/resources/lang/android/' . 'en.json', json_encode([["key" => "key_1", "value" => "value1"], ["key" => "key_2", "value" => "value2"]], JSON_THROW_ON_ERROR));
            File::put($filePath, json_encode([["key" => "value1"], ["key" => "value12"]], JSON_THROW_ON_ERROR));
        }
        $androidKeys = json_decode(File::get(base_path() . '/resources/lang/android/en.json'), true, 512,JSON_THROW_ON_ERROR);
        $androidWords = [];
        if (file_exists(lang_path('android/'.$language->locale . '.json'))) {
            $androidWords = json_decode(File::get(base_path() . '/resources/lang/android/' . $language->locale . '.json'), true, 512, JSON_THROW_ON_ERROR);;
        }
        return view('languages/language-edit', compact('language', 'keys', 'words', 'androidKeys', 'androidWords'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Language $language)
    {
        try {
            $data = $request->all();
            if ($request->image) {
                $data['image'] = $this->itemImageValidated($request);
                if (!empty($language->image)) {
                    Storage::disk('public')->delete($language->image);
                }
            }
            $language->update($data);
            $data = $request->except('_method', 'name', 'locale', 'direction', 'image', 'image_remove');
            $this->createDirectoryForLanguage();

            $newData = array_map(function($key, $value) {
                return [
                    "key" => $key,
                    "value" => $value
                ];
            }, array_keys($data['android']), $data['android']);
            $systemFilePath = base_path('resources/lang/' . $language->locale . '.json');
            $androidFilePath = base_path('resources/lang/android/' . $language->locale . '.json');
            File::put($systemFilePath, json_encode($data['keys'], JSON_PRETTY_PRINT));
            File::put($androidFilePath, json_encode($newData, JSON_PRETTY_PRINT));
            return response()->json(['success' => 'You have updated language']);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 422);
        }
    }

    public function destroy(Language $language)
    {
        if ($language->isPrime() || $language->locale == Setting::first()->default_language) {
            return response()->json(['error' => "You can't delete the main language, or the language is default language for the system"], 422);
        }
        $dirWeb = base_path('resources/lang/' . $language->locale . '.json');
        $dirAndroid = base_path('resources/lang/android/' . $language->locale . '.json');
        if (file_exists($dirWeb)){
            File::delete($dirWeb);
        }
        if (file_exists($dirAndroid)){
            File::delete($dirAndroid);
        }
        $language->forceDelete();
//        $language->delete();
        return response()->json(['success' => 'The language is trashed']);
    }

    /**
     * Remove selected resource from storage.
     */
    public function deleteSelectedItems(Request $request)
    {
        $languageIds = $request->ids;
        $error = false;
        $languages = [];
        foreach ($languageIds as $languageId) {
            $language = Language::find($languageId);
            if ($language?->isPrime() || $language->locale == Setting::first()->default_language) {
                $error = true;
            } elseif ($language) {
                $languages[] = $language->id;
                $dirWeb = base_path('resources/lang/' . $language->locale . '.json');
                $dirAndroid = base_path('resources/lang/android/' . $language->locale . '.json');
                if (file_exists($dirWeb)){
                    File::delete($dirWeb);
                }
                if (file_exists($dirAndroid)){
                    File::delete($dirAndroid);
                }
            }
        }

        $statusCode = $error ? 422 : 200;
        //        Language::whereIn('id', $languages)->delete();
        Language::whereIn('id', $languages)->forceDelete();
        return response()->json(!$error ? (['success' => 'The records are trashed']) : ['error' => 'Languages are deleted except the main language, or language is default language for system, which you cannot delete'], $statusCode);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function restore(Language $language)
    {
        $language->restore();
        return response()->json(['success' => 'The record is restored']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function forceDelete(Language $language)
    {
        $language->forceDelete();
        return response()->json(['success' => 'The record is deleted']);
    }

    private function getFile($locale)
    {
        return json_decode(
            File::get(base_path() . '/resources/lang/' . $locale . '.json'),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    }

    private function createDirectoryForLanguage(): void
    {
        $directoryPath = base_path('resources/lang/');
        if (!File::exists($directoryPath)) {
            File::makeDirectory($directoryPath, 0755, true, true);
        }
    }

    public function setLanguageInStorage(Language $language)
    {
        auth()->user()->update(['language' => $language->locale]);
        session()->put('locale', $language->locale);
        session()->put('language_id', $language->id);
        return redirect()->back();
    }


    protected function itemImageValidated($request): string
    {
        return $request->file('image')
            ->store('languages', 'public');
    }

    private function isoLanguages(){
        $data = [
            'cs' => [
                'name' => 'Czech',
                'locale' => 'cs',
                'set2' => 'ces'
            ],
            'da' => [
                'name' => 'Danish',
                'locale' => 'da',
                'set2' => 'dan'
            ],
            'de' => [
                'name' => 'German',
                'locale' => 'de',
                'set2' => 'deu'
            ],
            'el' => [
                'name' => 'Greek',
                'locale' => 'el',
                'set2' => 'ell'
            ],
            'en' => [
                'name' => 'English',
                'locale' => 'en',
                'set2' => 'eng'
            ],
            'es' => [
                'name' => 'Spanish',
                'locale' => 'es',
                'set2' => 'spa'
            ],
            'fi' => [
                'name' => 'Finnish',
                'locale' => 'fi',
                'set2' => 'fin'
            ],
            'fr' => [
                'name' => 'French',
                'locale' => 'fr',
                'set2' => 'fra'
            ],
            'hu' => [
                'name' => 'Hungarian',
                'locale' => 'hu',
                'set2' => 'hun'
            ],
            'it' => [
                'name' => 'Italian',
                'locale' => 'it',
                'set2' => 'ita'
            ],
            'nl' => [
                'name' => 'Dutch, Flemish',
                'locale' => 'nl',
                'set2' => 'nld'
            ],
            'no' => [
                'name' => 'Norwegian',
                'locale' => 'no',
                'set2' => 'nor'
            ],
            'pl' => [
                'name' => 'Polish',
                'locale' => 'pl',
                'set2' => 'pol'
            ],
            'pt' => [
                'name' => 'Portuguese',
                'locale' => 'pt',
                'set2' => 'por'
            ],
            'sk' => [
                'name' => 'Slovak',
                'locale' => 'sk',
                'set2' => 'slk'
            ],
            'sl' => [
                'name' => 'Slovenian',
                'locale' => 'sl',
                'set2' => 'slv'
            ],
            'sv' => [
                'name' => 'Swedish',
                'locale' => 'sv',
                'set2' => 'swe'
            ],
            'tr' => [
                'name' => 'Turkish',
                'locale' => 'tr',
                'set2' => 'tur'
            ]
        ];
        return $data;
    }
}
