<?php

namespace App\Http\Controllers\addons\included;

use App\Helpers\helper;
use App\Http\Controllers\Controller;
use App\Models\Languages;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class LanguageController extends Controller
{
    public function index(Request $request)
    {
        $getlanguages = Languages::get();
        if ($request->code == "") {
            foreach ($getlanguages as $firstlang) {
                $currantLang = Languages::where('code', $firstlang->code)->first();
                break;
            }
        } else {
            $currantLang = Languages::where('code', $request->code)->first();
        }
        if ($request->has('lang')) {
            if ($request->lang != "" && $request->lang != null) {
                $settingdata = Settings::where('vendor_id', 1)->first();
                $settingdata->default_language = $request->lang;
                $settingdata->update();
            }
        }
        if (empty($currantLang)) {
            $dir = base_path() . '/resources/lang/' . 'en';
        } else {
            $dir = base_path() . '/resources/lang/' . $currantLang->code;
        }
        if (!is_dir($dir)) {
            $dir = base_path() . '/resources/lang/en';
        }
        // Read the .php files: they are what Laravel serves at runtime. The editor used to read
        // the .json siblings, which had drifted behind by hundreds of keys - those keys could
        // never be translated from the panel, so whole pages stayed in English.
        $code = $currantLang->code ?? 'en';
        $arrLabel   = (object) self::readGroup($code, 'labels');
        $arrMessage = (object) self::readGroup($code, 'messages');
        $arrLanding = (object) self::readGroup($code, 'landing');
        return view('admin.included.language.index', compact('getlanguages', 'currantLang', 'arrLabel', 'arrMessage', 'arrLanding'));
    }
    /** Keys for one language file, English keys first so nothing is ever missing from the list. */
    public static function readGroup(string $code, string $file): array
    {
        $base = base_path('resources/lang/');
        $english = is_file($base . 'en/' . $file . '.php') ? (array) include($base . 'en/' . $file . '.php') : [];
        $own = is_file($base . $code . '/' . $file . '.php') ? (array) include($base . $code . '/' . $file . '.php') : [];

        // Untranslated keys fall back to the English wording so the translator sees real text.
        return array_merge($english, array_filter($own, fn($v) => $v !== null && $v !== ''));
    }

    /**
     * Write a language file as both .php (used at runtime) and .json (used by tooling).
     * Built with real encoders: the previous string concatenation broke on any translation
     * containing a quote or a backslash.
     */
    public static function writeGroup(string $code, string $file, array $values): void
    {
        $dir = base_path('resources/lang/' . $code);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $php = "<?php\n\nreturn " . var_export($values, true) . ";\n";
        file_put_contents($dir . '/' . $file . '.php', $php);
        file_put_contents(
            $dir . '/' . $file . '.json',
            json_encode($values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );
    }

    /** The three files a translator needs. */
    private static function groups(): array
    {
        return ['labels', 'messages', 'landing'];
    }

    /**
     * Download every key of one language as a single JSON file, ready to hand to a translator.
     * Shape: {"labels": {...}, "messages": {...}, "landing": {...}}
     */
    public function export($code)
    {
        $language = Languages::where('code', $code)->first();
        $payload = [];
        foreach (self::groups() as $file) {
            $payload[$file] = self::readGroup($code, $file);
        }

        $json = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return response($json, 200, [
            'Content-Type'        => 'application/json; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . ($language->code ?? $code) . '.json"',
        ]);
    }

    /**
     * Upload a translated JSON and apply it to a language. Accepts the grouped shape produced by
     * export(), and also a flat {"labels.save": "Speichern"} map. Unknown keys are ignored and
     * missing ones keep their current wording, so a partial translation is safe to import.
     */
    public function import(Request $request)
    {
        $code = $request->get('code');
        $language = Languages::where('code', $code)->first();
        if (empty($language) || !$request->hasFile('file')) {
            return redirect()->back()->with('error', trans('messages.wrong'));
        }

        $data = json_decode(file_get_contents($request->file('file')->getRealPath()), true);
        if (!is_array($data)) {
            return redirect()->back()->with('error', app()->getLocale() === 'ar'
                ? 'الملف ليس بصيغة JSON صالحة.'
                : 'That file is not valid JSON.');
        }

        // Flat "labels.key" form -> grouped.
        if (!array_intersect(self::groups(), array_keys($data))) {
            $grouped = [];
            foreach ($data as $key => $value) {
                if (!is_string($value) || !str_contains($key, '.')) {
                    continue;
                }
                [$group, $realKey] = explode('.', $key, 2);
                if (in_array($group, self::groups(), true)) {
                    $grouped[$group][$realKey] = $value;
                }
            }
            $data = $grouped;
        }

        $applied = 0;
        foreach (self::groups() as $file) {
            $incoming = array_filter((array) ($data[$file] ?? []), fn($v) => is_string($v) && $v !== '');
            if (empty($incoming)) {
                continue;
            }
            $current = self::readGroup($code, $file);
            $merged = array_merge($current, array_intersect_key($incoming, $current));
            $applied += count(array_intersect_key($incoming, $current));
            self::writeGroup($code, $file, $merged);
        }

        return redirect()->back()->with('success', (app()->getLocale() === 'ar'
            ? 'تم استيراد الترجمات: '
            : 'Translations imported: ') . $applied);
    }

    public function add()
    {
        return view('admin.included.language.add');
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'layout' => 'required',
            'name' => 'required_with:code',
            'image.*' => 'mimes:jpeg,png,jpg',
        ], [
            "code.required" => trans('messages.language_required'),
            "layout.required" => trans('messages.layout_required'),
            "name.required_with" => trans('messages.wrong'),
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        } else {
            $path = base_path('resources/lang/' . $request->code);
            if (!File::isDirectory($path)) {
                File::makeDirectory($path, 0777, true, true);
            }
            File::copyDirectory(base_path() . '/resources/lang/en', base_path() . '/resources/lang/' . $request->code);
            $language = new Languages();
            $language->code = $request->code;
            $language->name = $request->name;
            $language->layout = $request->layout;
            if ($request->has('image')) {
                $flagimage = 'flag-' . uniqid() . "." . $request->file('image')->getClientOriginalExtension();
                $request->file('image')->move(storage_path('app/public/admin-assets/images/language/'), $flagimage);
                $language->image = $flagimage;
            }
            $language->is_available = 1;
            $language->save();
            return redirect('admin/language-settings')->with('success', trans('messages.success'));
        }
    }

    public function storeLanguageData(Request $request)
    {
        $langFolder = base_path() . '/resources/lang/' . $request->currantLang;
        if (!is_dir($langFolder)) {
            mkdir($langFolder);
            chmod($langFolder, 0777);
        }
        if (isset($request->file) == "label") {
            if (isset($request->label) && !empty($request->label)) {
                $values = array_merge(self::readGroup($request->currantLang, 'labels'), (array) $request->label);
                self::writeGroup($request->currantLang, 'labels', $values);
            }
        }
        if (isset($request->file) == "message") {
            if (isset($request->message) && !empty($request->message)) {
                $values = array_merge(self::readGroup($request->currantLang, 'messages'), (array) $request->message);
                self::writeGroup($request->currantLang, 'messages', $values);
            }
        }
        if (isset($request->file) == "landing") {
            if (isset($request->landing) && !empty($request->landing)) {
                $values = array_merge(self::readGroup($request->currantLang, 'landing'), (array) $request->landing);
                self::writeGroup($request->currantLang, 'landing', $values);
            }
        }

        return redirect()->back()->with('success', trans('messages.success'));
    }
    public function delete(Request $request)
    {
        try {
            $language = Languages::find($request->id);
            $getdefault = Settings::get();
            $setactive = Languages::where('code', 'en')->first();
            $setactive->is_available = 1;
            $setactive->update();
            foreach ($getdefault as $default) {
                $code = explode('|', $default->languages);
                $key = array_search($language->code, $code);
                if ($key !== false) {
                    unset($code[$key]);
                }
                Settings::where('vendor_id', $default->vendor_id)->update(array('languages' => implode('|', $code)));
                Settings::where('default_language', $language->code)->update(array('default_language' => "en"));
            }
            $path = base_path('resources/lang/' . $language->code);
            if (File::exists($path)) {
                File::deleteDirectory($path);
            }
            if (file_exists(env('ASSETSPATHURL') . 'admin-assets/images/language/' . $language->image)) {
                unlink(env('ASSETSPATHURL') . 'admin-assets/images/language/' . $language->image);
            }
            $language->delete();
            return redirect('admin/language-settings')->with('success', trans('messages.success'));
        } catch (\Throwable $th) {
            return redirect('admin/language-settings')->with('error', trans('messages.wrong'));
        }
    }
    public function edit($id)
    {
        $getlanguage = Languages::where('id', $id)->first();
        return view('admin.included.language.edit', compact('getlanguage'));
    }
    public function update(Request $request, $id)
    {
        try {
            $default = 2;
            if ($request->default == 1) {
                Languages::where('is_default', '1')->update(array('is_default' => 2));
                $default = $request->default;
            }
            $language = Languages::where('id', $id)->first();
            $language->layout = $request->layout;
            $language->is_default = @$default;
            if ($request->has('image')) {
                $validator = Validator::make($request->all(), [
                    'image' => 'image|max:' . helper::imagesize() . '|' . helper::imageext(),
                ], [
                    'image.max' => trans('messages.image_size_message'),
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->with('error', trans('messages.image_size_message') . ' ' . helper::appdata('')->image_size . ' ' . 'MB');
                }
                if (file_exists(env('ASSETSPATHURL') . 'admin-assets/images/language/' . $language->image)) {
                    unlink(env('ASSETSPATHURL') . 'admin-assets/images/language/' . $language->image);
                }
                $flagimage = 'flag-' . uniqid() . "." . $request->file('image')->getClientOriginalExtension();
                $request->file('image')->move(storage_path('app/public/admin-assets/images/language/'), $flagimage);
                $language->image = $flagimage;
            }
            $language->update();
            return redirect('admin/language-settings')->with('success', trans('messages.success'));
        } catch (\Throwable $th) {
            return redirect('admin/language-settings')->with('error', trans('messages.wrong'));
        }
    }

    public function status(Request $request)
    {
        $language = Languages::find($request->id);
        if ($language->code == helper::appdata('')->default_language) {
            return redirect()->back()->with('error', trans('messages.remove_default'));
        }
        $language->is_available = $request->status;
        $language->save();
        if ($language) {
            return redirect()->back()->with('success', trans('messages.success'));
        } else {
            return redirect()->back()->with('error', trans('messages.wrong'));
        }
    }
    public function languagestatus(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }

        $settingdata = Settings::where('vendor_id', $vendor_id)->first();
        if ($request->status == 2) {
            $code = explode('|', helper::appdata($vendor_id)->languages);
            if (count($code) == 1) {
                return redirect()->back()->with('error', trans('messages.language_required_msg'));
            }
            if ($request->code == helper::appdata($vendor_id)->default_language) {
                return redirect()->back()->with('error', trans('messages.remove_default'));
            }
            $key = array_search($request->code, $code);
            if ($key !== false) {
                unset($code[$key]);
                $settingdata->languages = implode('|', $code);
            }
        }
        if ($request->status == 1) {
            if (helper::appdata($vendor_id)->languages != "") {
                $code = explode('|', helper::appdata($vendor_id)->languages);
                array_push($code, $request->code);
                $settingdata->languages = implode('|', $code);
            } else {
                $settingdata->languages = $request->code;
            }
        }
        $settingdata->update();
        return redirect()->back()->with('success', trans('messages.success'));
    }
    public function setdefault(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $settingdata = Settings::where('vendor_id', $vendor_id)->first();
        if (in_array($request->code, explode('|', $settingdata->languages))) {
            $settingdata->default_language = $request->code;
            $settingdata->update();
            return redirect()->back()->with('success', trans('messages.success'));
        } else {
            return redirect()->back()->with('error', trans('messages.not_available'));
        }
    }

    public function change(Request $request)
    {
        $layout = Languages::select('name', 'layout', 'image')->where('code', $request->lang)->first();
        App::setLocale($request->lang);
        session()->put('locale', $request->lang);
        session()->put('language', $layout->name);
        session()->put('flag', $layout->image);
        session()->put('direction', $layout->layout);
        return redirect()->back();
    }
}
