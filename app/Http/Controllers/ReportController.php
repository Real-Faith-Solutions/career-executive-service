<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Definitions\AppDefinitions;
use App\Models\PersonalData;

class ReportController extends Controller
{
    public function getGeneralReportsPage(){

        if(RolesController::validateUserCesWebAppGeneralPageAccess('Reports') == 'true'){
            $CESIDS = AppDefinitions::DEFAULT_CESOS_ID;
            $CESIDS = array_merge($CESIDS, AppDefinitions::DEFAULT_CSEE_ID);
            $CESIDS = array_merge($CESIDS, AppDefinitions::DEFAULT_ELIGIBLE_ID);
            $recordProfiles = PersonalData::all();

            $activeProfiles = $recordProfiles
                ->where('status', "Active")
                ->whereIN('cesstat_code', $CESIDS);

            $retiredProfiles = $recordProfiles
                ->where('status', "Retired")
                ->whereIN('cesstat_code', $CESIDS);

            $deceasedProfiles = $recordProfiles
                ->where('status', "Deceased")
                ->whereIN('cesstat_code', $CESIDS);

            $candidateRetireProfiles = $recordProfiles
                ->where('status', "Active")
                ->whereIN('cesstat_code', $CESIDS)
                ->where('age_now', '>=', 60);
            // $candidateRetireProfiles = $candidateRetireProfiles->filter(function($model){
            //     return $model->age_now >= 60;
            // });

            return view('admin.reports_management.general_reports', compact('recordProfiles', 'activeProfiles', 'retiredProfiles', 'deceasedProfiles', 'candidateRetireProfiles'))->render();
        } else {

            return view('restricted');
        }
    }
}
