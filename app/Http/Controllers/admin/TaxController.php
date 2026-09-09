<?php
namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tax;
use App\Models\PricingPlan;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use DB;
class TaxController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $gettax = Tax::where('vendor_id', $vendor_id)->where('is_deleted',2)->orderBy('reorder_id')->get();
        return view('admin.tax.index', compact("gettax"));
    }
    public function add(Request $request)
    {
        return view('admin.tax.add');
    }

    /**
     * Shared write for save + update.
     *
     * A subscription rule is created INACTIVE with a value of 0 by default — nothing is charged
     * until the company's registered rate is confirmed and the rule is switched on deliberately.
     */
    private function applyFields(Tax $tax, Request $request, bool $isNew): void
    {
        $tax->name       = $request->name;
        $tax->type       = in_array((int) $request->type, [Tax::TYPE_FIXED, Tax::TYPE_PERCENTAGE], true)
            ? (int) $request->type : Tax::TYPE_PERCENTAGE;
        $tax->tax        = is_numeric($request->tax) ? $request->tax : 0;
        $tax->applies_to = array_key_exists($request->applies_to, Tax::appliesToOptions())
            ? $request->applies_to : Tax::APPLIES_VENDOR_SALES;
        $tax->systems    = array_key_exists((string) $request->systems, Tax::systemOptions())
            ? $request->systems : 'all';
        $tax->price_type = array_key_exists((string) $request->price_type, Tax::priceTypeOptions())
            ? $request->price_type : 'exclusive';

        // A rule with no rate can never be active — that is the safety net behind the "do not
        // charge tax until confirmed" requirement.
        $status = (int) $request->is_available === 1 ? 1 : 2;
        $tax->is_available = ((float) $tax->tax) > 0 ? $status : 2;

        if ($isNew) {
            $tax->is_deleted = 2;
        }
    }
    public function save(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $tax = new Tax();
        $tax->vendor_id = $vendor_id;
        $this->applyFields($tax, $request, true);
        $tax->save();
        return redirect('admin/tax/')->with('success', trans('messages.success'));
    }
    public function edit(Request $request)
    {
        $edittax = Tax::where('id', $request->id)->first();
        return view('admin.tax.edit', compact("edittax"));
    }
    public function update(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $tax = Tax::where('id', $request->id)->first();
        $tax->vendor_id = $vendor_id;
        $this->applyFields($tax, $request, false);
        $tax->update();
        return redirect('admin/tax')->with('success', trans('messages.success'));
    }
    public function change_status(Request $request)
    {
        $tax = Tax::where('id', $request->id)->first();
        // A 0% rule stays off: activating it would charge nothing and only be misleading.
        if ($tax && (int) $request->status === 1 && (float) $tax->tax <= 0) {
            return redirect('admin/tax')->with('error', trans('messages.tax_rate_required_to_activate'));
        }
        Tax::where('id', $request->id)->update(['is_available' => $request->status]);
        return redirect('admin/tax')->with('success', trans('messages.success'));
    }
    public function delete(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $checktax = Tax::where('id', $request->id)->first();
        if(Auth::user()->type == 1)
        {
            $getplan = PricingPlan::where(DB::Raw("FIND_IN_SET($checktax->id, replace(tax, '|', ','))"), '>', 0)->get();
            foreach($getplan as $plan)
            {
                $tax_id = explode('|', $plan->tax);
                $key = array_search($checktax->id, $tax_id);
                if ($key !== false) {
                    unset($tax_id[$key]);
                    PricingPlan::where('id',$plan->id)->update(array('tax' => implode('|', $tax_id)));
                }
            }
        }
        else{
            $getproduct = Item::where(DB::Raw("FIND_IN_SET($checktax->id, replace(tax, '|', ','))"), '>', 0)->get();
            foreach($getproduct as $product)
            {
                $tax = explode('|', $product->tax);
                $key = array_search($checktax->id, $tax);
                if ($key !== false) {
                    unset($tax[$key]);
                    Item::where('vendor_id', $vendor_id)->update(array('tax' => implode('|', $tax)));
                }
            }
        }
        $checktax->delete();
        return redirect('admin/tax')->with('success', trans('messages.success'));
    }
    public function reorder_tax(Request $request)
    {
        if (Auth::user()->type == 4) {
            $vendor_id = Auth::user()->vendor_id;
        } else {
            $vendor_id = Auth::user()->id;
        }
        $gettax = Tax::where('vendor_id', $vendor_id)->get();
        foreach ($gettax as $tax) {
            foreach ($request->order as $order) {
                $tax = Tax::where('id', $order['id'])->first();
                $tax->reorder_id = $order['position'];
                $tax->save();
            }
        }
        return response()->json(['status' => 1, 'msg' => trans('messages.success')], 200);
    }
  
}
