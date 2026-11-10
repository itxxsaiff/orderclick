<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Services\AiAssistant;
use App\Services\StoreTranslator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * "Languages" page: the store's services, products, categories, FAQs and description in every
 * website language. The AI translates them all; the owner can correct any translation.
 */
class TranslationController extends Controller
{
    private function vendorId(): int
    {
        return (int) (Auth::user()->type == 4 ? Auth::user()->vendor_id : Auth::user()->id);
    }

    public function index()
    {
        $vid = $this->vendorId();
        $groups = [];
        foreach (array_keys(StoreTranslator::TYPES) as $type) {
            $records = StoreTranslator::records($type, $vid);
            if ($records->count()) {
                $groups[$type] = $records;
            }
        }

        return view('admin.translations.index', [
            'groups'  => $groups,
            'pending' => StoreTranslator::pending($vid),
            'langs'   => StoreTranslator::LANGS,
            'aiReady' => AiAssistant::enabled(),
        ]);
    }

    /** One batch of AI translation; the page calls this until nothing is left. */
    public function step()
    {
        return response()->json(StoreTranslator::step($this->vendorId()));
    }

    /** Translate everything again from scratch (the owner's corrections are replaced). */
    public function reset()
    {
        StoreTranslator::reset($this->vendorId());

        return response()->json(['success' => true, 'remaining' => StoreTranslator::pending($this->vendorId())['total']]);
    }

    /** The owner's own corrections for one record. */
    public function save(Request $request)
    {
        $request->validate([
            'type' => 'required|in:' . implode(',', array_keys(StoreTranslator::TYPES)),
            'id'   => 'required|integer',
            't'    => 'nullable|array',
        ]);
        $cls = StoreTranslator::TYPES[$request->type];
        // Only this store's own records.
        $record = $cls::where('vendor_id', $this->vendorId())->where('id', $request->id)->firstOrFail();
        StoreTranslator::saveManual($record, (array) $request->input('t', []));

        return response()->json(['success' => true, 'message' => trans('messages.success')]);
    }
}
