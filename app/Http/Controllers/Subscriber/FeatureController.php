<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;
use App\Support\FeatureRegistry;
use Illuminate\Http\Request;

class FeatureController extends Controller
{
    /**
     * Show the "feature unavailable" page.
     *
     * Reads feature metadata from FeatureRegistry and config/features.php
     * to populate the page — nothing is hard-coded in the view.
     */
    public function unavailable(Request $request): \Illuminate\View\View
    {
        $featureKey = $request->query('feature', '');
        $featureKey = FeatureRegistry::normalize($featureKey);

        return view('subscriber.features.unavailable', [
            'featureKey' => $featureKey,
        ]);
    }
}
