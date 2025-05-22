<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCycleRequest;
use App\Http\Resources\CycleResource;
use App\Models\Cycle;
use Illuminate\Http\Request;


class CycleController extends Controller
{
    use ApiResponseTrait;


    public function index()
    {
        $cycles = Cycle::all();
        return $this->apiResponse(
            CycleResource::collection($cycles),
            'Cycles retrieved successfully',
            true,
            200
        );
    }


    public function store(StoreCycleRequest $request)
    {

        $created = Cycle::create([
            'week_days' => $request->week_days,
            'delivers_times' => $request->delivers_times,
        ]);

        return $this->apiResponse(

            new CycleResource($created),
            'Cycle created successfully',
            true,
            200
        );

    }


    public function show($id)
    {
        $cycle = Cycle::find($id);
        if (!$cycle) {
            return $this->apiResponse(
                null,
                'Cycle not found',
                false,
                404
            );
        }
        return $this->apiResponse(
            new CycleResource($cycle),
            'Cycle retrieved successfully',
            true,
            200
        );


    }


    public function update(StoreCycleRequest $request, $id)
    {
        $cycle = Cycle::find($id);
        if (!$cycle) {
            return $this->apiResponse(
                null,
                'Cycle not found',
                false,
                404
            );
        }

        $cycle->update([
            'week_days' => $request->week_days,
            'delivers_times' => $request->delivers_times,
        ]);

        return $this->apiResponse(
            new CycleResource($cycle),
            'Cycle updated successfully',
            true,
            200
        );

    }

    public function destroy($id)
    {
        $cycle = Cycle::find($id);
        if (!$cycle) {
            return $this->apiResponse(
                null,
                'Cycle not found',
                false,
                404
            );
        }
        $cycle->delete();
        return $this->apiResponse(
            null,
            'Cycle deleted successfully',
            true,
            200
        );

    }


    public function generate(Request $request)
    {
        $request->validate([
            'week_days' => 'required|array',
            'week_days.*' => 'in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday'
        ]);

        $selectedDays = $request->selected_days;
        $options = $this->generateDeliveryOptions(count($selectedDays));

        return $this->apiResponse(
            [
                'week_days' => $selectedDays,
                'delivers_times' => $options
            ],
            'Delivery options generated successfully',
            true,
            200
        );
    }

    protected function generateDeliveryOptions($selectedDaysCount)
    {
        $maxPerDay = 4; // الحد الأقصى لكل يوم في الشهر
        $maxTotal = $selectedDaysCount * $maxPerDay;

        // توليد الخيارات بناءً على عدد الأيام المختارة
        $options = [];

        if ($selectedDaysCount === 1) {
            // يوم واحد: من 1 إلى 4 مرات
            $options = range(1, $maxPerDay);
        } elseif ($selectedDaysCount === 2) {
            // يومين: من 2 إلى 8 مرات
            $options = range(2, $maxTotal, 2);
        } else {
            // ثلاثة أيام: من 3 إلى 12 مرة
            $options = range(3, $maxTotal, 3);
        }

        return $options;
    }

    protected function generateDeliveryPattern($selectedDays, $totalDeliveries)
    {
        $pattern = [];
        $daysCount = count($selectedDays);

        // التأكد من أن العدد المطلوب ضمن الحدود المقبولة
        $maxPossible = $daysCount * 4;
        $totalDeliveries = min($totalDeliveries, $maxPossible);

        // توزيع العدد على الأيام بالتساوي قدر المستطاع
        $baseDeliveries = floor($totalDeliveries / $daysCount);
        $remaining = $totalDeliveries % $daysCount;

        foreach ($selectedDays as $day) {
            $count = $baseDeliveries;
            if ($remaining > 0) {
                $count++;
                $remaining--;
            }
            $pattern[$day] = min($count, 4); // لا يتجاوز 4 مرات لكل يوم
        }

        return $pattern;
    }


}
