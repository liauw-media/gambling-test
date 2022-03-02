<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadJsonFileRequest;
use App\Models\Affiliate;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;

class AffiliateController extends Controller
{
    /** @var array|float[] */
    private $dublin_office = [53.3340285, -6.2535495];

    public function index(): View
    {
        $dublin_office = $this->dublin_office;
        $affiliates = Affiliate::orderBy('affiliate_id')->get();
        $affiliates = $affiliates->filter(function ($affiliate) {
            if ($affiliate->calculateDistance() < 100) {
                return true;
            }
        });

        return view('welcome', compact('affiliates'));
    }

    public function store(UploadJsonFileRequest $request): RedirectResponse
    {
        if ($request->hasFile('file')) {
            /** @var UploadedFile $file */
            $file = $request->file('file');
            foreach (explode("\n", $file->getContent()) as $line) {
                $affiliate = json_decode($line, true);
                if (is_array($affiliate)) {
                    $validator = Validator::make($affiliate, [
                        'affiliate_id'=> 'required|integer',
                        'name'=> 'required|string',
                        'latitude' => 'required|numeric',
                        'longitude' => 'required|numeric',
                    ]);
                    if ($validator->valid()) {
                        (new Affiliate())->updateOrCreate($affiliate);
                    }
                } else {
                    return redirect()->to('/')->withErrors(['Input file not valid']);
                }
            }
        }

        return redirect()->to('/');
    }

    public function showValidAffiliates(int $distance = 100): JsonResponse
    {
        $dublin_office = $this->dublin_office;
        $affiliates = Affiliate::orderBy('affiliate_id')->get();
        $affiliates = $affiliates->filter(function ($affiliate) use ($dublin_office, $distance) {
            if ($affiliate->calculateDistance($dublin_office) < $distance) {
                return true;
            }
        });

        return response()->json($affiliates);
    }
}
