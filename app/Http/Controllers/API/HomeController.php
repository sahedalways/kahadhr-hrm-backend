<?php

namespace App\Http\Controllers\API;

use App\Models\SiteSetting;
use App\Models\SocialInfoSettings;
use Illuminate\Http\Request;



class HomeController extends BaseController
{

    public function getHomeData(Request $request)
    {
        try {
            $siteInfo = SiteSetting::first();
            $socialInfo = SocialInfoSettings::first();


            if ($siteInfo) {
                unset(
                    $siteInfo->created_at,
                    $siteInfo->updated_at,
                    $siteInfo->id,
                    $siteInfo->site_title,
                    $siteInfo->favicon_url,
                    $siteInfo->logo_url,
                    $siteInfo->company_id
                );
            }

            if ($socialInfo) {
                unset($socialInfo->created_at, $socialInfo->updated_at, $socialInfo->id, $socialInfo->company_id);
            }

            return response()->json([
                'success' => true,
                'message' => 'Home data fetched successfully',
                'data' => [
                    'site_info' => $siteInfo,
                    'social_info' => $socialInfo,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch home data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
