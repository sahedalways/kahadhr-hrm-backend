<?php

namespace App\Http\Controllers\API;

use App\Models\CompanyChargeRate;
use App\Models\SiteSetting;
use App\Models\SocialInfoSettings;
use Illuminate\Http\Request;



class HomeController extends BaseController
{

    public function getHomeData()
    {
        try {
            $siteInfo = SiteSetting::first();
            $socialInfo = SocialInfoSettings::first();
            $chargeRate = CompanyChargeRate::value('rate');


            if ($siteInfo) {
                unset(
                    $siteInfo->created_at,
                    $siteInfo->updated_at,
                    $siteInfo->id,
                    $siteInfo->favicon_url,
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
                    'company_charge_rate' => $chargeRate,
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





    public function getChargeRate()
    {
        try {
            $data = CompanyChargeRate::first();


            if ($data) {
                unset(
                    $data->created_at,
                    $data->updated_at,
                    $data->id,
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Rate data fetched successfully',
                'data' => [
                    'rate_info' => $data->rate,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch rate data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
