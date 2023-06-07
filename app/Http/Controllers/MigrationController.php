<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PersonalData;
use App\Models\SpouseRecords;
use App\Models\ChildrenRecords;
use App\Models\FamilyProfile;
use App\Models\EducationalAttainment;
use App\Models\ExaminationsTaken;
use App\Models\LicenseDetails;
use App\Models\LanguagesDialects;
use App\Models\CesWe;
use App\Models\AssessmentCenter;
use App\Models\ValidationHr;
use App\Models\BoardInterview;
use App\Models\CesStatus;
use App\Models\RecordOfCespesRatings;
use App\Models\WorkExperience;
use App\Models\DatabaseMigrations;
use Illuminate\Support\Facades\Auth;

class MigrationController extends Controller
{

    public function getMigrationSystemPage(){

        if(RolesController::validateUserCesWebAppGeneralPageAccess('System Utility') == 'true'){

            $DatabaseMigrations = DatabaseMigrations::get();

            return view('admin.system_utility.database_migration', compact('DatabaseMigrations'))->render();

        }
        else{

            return view('restricted');
        }
    }

    public function getDatabaseMigrations($updated_category){

        if(RolesController::validateUserCesWebAppGeneralPageAccess('System Utility') == 'true'){

            $DatabaseMigrations = DatabaseMigrations::where('updated_category','=',$updated_category)->select('updated_category')->get();

            if($DatabaseMigrations == '[]'){

                $status = 'false';
            }
            else{

                $status = 'true';
            }
            
            return $status;

        }
        else{

            return 'Restricted';
        }
    }

    public function migratePersonalData(Request $request){

        if(RolesController::validateUserCesWebAppGeneralPageAccess('System Utility') == 'true'){

            // Reset database_migrations table
            DatabaseMigrations::truncate();

            // Reset personal_data table
            PersonalData::truncate();

            $start = new \DateTime();

            $vw_profile_main = DB::connection('sqlsrv-2')->table('vw_profile_main')->get();

            foreach($vw_profile_main as $item){

                $profile_tblAddress = DB::connection('sqlsrv-2')->table('profile_tblAddress')->where('cesno','=',$item->cesno)->where('catid','=','Home')->get();

                PersonalData::create([

                    'cesno' => $item->cesno,
                    'sp' => null,
                    'moig' => null,
                    'pwd' => null,
                    'title' => $item->title,
                    'picture' => $item->picture,
                    'gsis' => null,
                    'pagibig' => null,
                    'philhealt' => null,
                    'sss_no' => null,
                    'tin' => null,
                    'status' => $item->status,
                    'citizenship' => $item->civilstatus,
                    'd_citizenship' => null,
                    'lastname' => $item->lastname,
                    'firstname' => $item->firstname,
                    'middlename' => $item->middlename,
                    'mi' => $item->middleinitial,
                    'ne' => null,
                    'nickname' => $item->nickname,
                    'birthdate' => $item->birthdate,
                    'age' => null,
                    'birth_place' => $item->birthplace,
                    'gender' => $item->gender,
                    'civil_status' => $item->civilstatus,
                    'religion' => $item->religion,
                    'height' => $item->height,
                    'weight' => $item->weight,
                    'fb_pa' => ($profile_tblAddress == '[]' ? '' : $profile_tblAddress[0]->house_bldg),
                    'ns_pa' => ($profile_tblAddress == '[]' ? '' : $profile_tblAddress[0]->st_road),
                    'bd_pa' => ($profile_tblAddress == '[]' ? '' : $profile_tblAddress[0]->brgy_vill),
                    'cm_pa' => ($profile_tblAddress == '[]' ? '' : $profile_tblAddress[0]->city_code),
                    'zc_pa' => null,
                    'fb_ma' => null,
                    'ns_ma' => null,
                    'bd_ma' => null,
                    'cm_ma' => null,
                    'zc_ma' => null,
                    'oea_ma' => $item->emailadd,
                    'telno1_ma' => $item->telno,
                    'mobileno1_ma' => $item->mobileno,
                    'mobileno2_ma' => $item->mobileno2,
                    'acno' => $item->acno,
                    'remarks' => $item->remarks,
                    'cesstat_code' => $item->CESStat_Code,
                    'mailingaddr' => $item->mailingaddr,
                    'last_updated_by' => $item->lastupd_encoder,
                    'created_at' => $item->e_date,
                    'updated_at' => $item->lastupd_dt,
                    'encoder' => $item->encoder,

                ]);

            }

            $finish = new \DateTime();

            DatabaseMigrations::create([

                'updated_category' => 'Personal Data',
                'table_source' => 'vw_profile_main,profile_tblAddress',
                'start' => $start,
                'finish' => $finish,
                'duration_in_minutes' => $start->diff($finish)->format('%I'),
                'migration_status' => 'Success',
                'last_updated_by' => Auth::user()->role.' - '.Auth::user()->role_name_no,

            ]);

            return 'Successfully Migrated';
        }
        else{

            return 'Restricted';
        }
    }

    public function migrateFamilyProfileSpouse(Request $request){

        if(RolesController::validateUserCesWebAppGeneralPageAccess('System Utility') == 'true'){

            // Reset spouse_records table
            SpouseRecords::truncate();

            // Reset family_profiles table
            FamilyProfile::truncate();

            $start = new \DateTime();

            $vw_profile_main = DB::connection('sqlsrv-2')->table('vw_profile_main')->get();

            foreach($vw_profile_main as $item){

                SpouseRecords::create([
                    'cesno' => $item->cesno,
                    'lastname_sn_fp' => $item->spouse_lname,
                    'first_sn_fp' => $item->spouse_fname,
                    'middlename_sn_fp' => $item->spouse_mname,
                    'ne_sn_fp' => null,
                    'occu_sn_fp' => null,
                    'ebn_sn_fp' => null,
                    'eba_sn_fp' => null,
                    'etn_sn_fp' => null,
                    'civil_status_sn_fp' => null,
                    'gender_sn_fp' => null,
                    'birthdate_sn_fp' => null,
                    'age_sn_fp' => null,
                    'last_updated_by' => $item->lastupd_encoder,
                    'created_at' => $item->e_date,
                    'updated_at' => $item->lastupd_dt,
                    'encoder' => $item->encoder,              
                ]);

            }

            $finish = new \DateTime();

            DatabaseMigrations::create([

                'updated_category' => 'Family Profile Spouse',
                'table_source' => 'vw_profile_main,',
                'start' => $start,
                'finish' => $finish,
                'duration_in_minutes' => $start->diff($finish)->format('%I'),
                'migration_status' => 'Success',
                'last_updated_by' => Auth::user()->role.' - '.Auth::user()->role_name_no,

            ]);

            return 'Successfully Migrated';
        }
        else{

            return 'Restricted';
        }
    }

    public function migrateFamilyProfileChildren(Request $request){

        if(RolesController::validateUserCesWebAppGeneralPageAccess('System Utility') == 'true'){

            // Reset children_records table
            ChildrenRecords::truncate();

            // Reset family_profiles table
            FamilyProfile::truncate();

            $start = new \DateTime();

            $profile_tblChildren = DB::connection('sqlsrv-2')->table('profile_tblChildren')->get();

            foreach($profile_tblChildren as $item){

                ChildrenRecords::create([
                    'cesno' => $item->cesno,
                    'ch_lastname_fp' => $item->lname,
                    'ch_first_fp' => $item->fname,
                    'ch_middlename_fp' => $item->mname,
                    'ch_ne_fp' => null,
                    'ch_gender_fp' => $item->gender,
                    'ch_birthdate_fp' => $item->bdate,
                    'ch_birthplace_fp' => null,
                    'last_updated_by' => $item->lastupd_enc,
                    'created_at' => $item->encdate,
                    'updated_at' => $item->lastupd_dt,
                    'encoder' => $item->encoder,           
                ]);

            }

            $finish = new \DateTime();

            DatabaseMigrations::create([

                'updated_category' => 'Family Profile Children',
                'table_source' => 'profile_tblChildren,',
                'start' => $start,
                'finish' => $finish,
                'duration_in_minutes' => $start->diff($finish)->format('%I'),
                'migration_status' => 'Success',
                'last_updated_by' => Auth::user()->role.' - '.Auth::user()->role_name_no,

            ]);

            return 'Successfully Migrated';
        }
        else{

            return 'Restricted';
        }
    }

    public function migrateEducationalBackgroundOrAttainment(Request $request){

        if(RolesController::validateUserCesWebAppGeneralPageAccess('System Utility') == 'true'){

            // Reset educational_attainments table
            EducationalAttainment::truncate();

            $start = new \DateTime();

            $profile_tblEducation = DB::connection('sqlsrv-2')->table('profile_tblEducation')->get();

            foreach($profile_tblEducation as $item){

                EducationalAttainment::create([
                    'cesno' => $item->cesno,
                    'level_ea' => null,
                    'school_ea' => $item->school_code,
                    'degree_ea' => $item->degree_code,
                    'date_grad_ea' => $item->year_grad,
                    'ms_ea' => $item->major_code,
                    'school_type_ea' => $item->school_status,
                    'date_f_ea' => null,
                    'date_t_ea' => null,
                    'hlu_ea' => null,
                    'ahr_ea' => $item->honors,
                    'last_updated_by' => $item->lastupd_enc,
                    'created_at' => $item->encdate,
                    'updated_at' => $item->lastupd_dt,
                    'encoder' => $item->encoder,         
                ]);

            }

            $finish = new \DateTime();

            DatabaseMigrations::create([

                'updated_category' => 'Educational Background or Attainment',
                'table_source' => 'profile_tblEducation,',
                'start' => $start,
                'finish' => $finish,
                'duration_in_minutes' => $start->diff($finish)->format('%I'),
                'migration_status' => 'Success',
                'last_updated_by' => Auth::user()->role.' - '.Auth::user()->role_name_no,

            ]);

            return 'Successfully Migrated';
        }
        else{

            return 'Restricted';
        }
    }

    public function migrateExaminationsTaken(Request $request){

        if(RolesController::validateUserCesWebAppGeneralPageAccess('System Utility') == 'true'){

            // Reset examinations_takens table
            ExaminationsTaken::truncate();

            // Reset license_details table
            LicenseDetails::truncate();

            $start = new \DateTime();

            $profile_tblExaminations = DB::connection('sqlsrv-2')->table('profile_tblExaminations')->get();

            foreach($profile_tblExaminations as $item){

                ExaminationsTaken::create([
                    'cesno' => $item->cesno,
                    'tox_et' => $item->exam_code,
                    'rating_et' => $item->rate,
                    'doe_et' => null,
                    'poe_et' => $item->exam_place,
                    'last_updated_by' => $item->lastupd_enc,
                    'created_at' => $item->encdate,
                    'updated_at' => $item->lastupd_dt,
                    'encoder' => $item->encoder,       
                ]);

            }

            $finish = new \DateTime();

            DatabaseMigrations::create([

                'updated_category' => 'Examinations Taken',
                'table_source' => 'profile_tblExaminations,',
                'start' => $start,
                'finish' => $finish,
                'duration_in_minutes' => $start->diff($finish)->format('%I'),
                'migration_status' => 'Success',
                'last_updated_by' => Auth::user()->role.' - '.Auth::user()->role_name_no,

            ]);

            return 'Successfully Migrated';
        }
        else{

            return 'Restricted';
        }
    }

    public function migrateLanguageDialects(Request $request){

        if(RolesController::validateUserCesWebAppGeneralPageAccess('System Utility') == 'true'){

            // Reset languages_dialects table
            LanguagesDialects::truncate();

            $start = new \DateTime();

            $profile_tblLanguages = DB::connection('sqlsrv-2')->table('profile_tblLanguages')->get();

            foreach($profile_tblLanguages as $item){

                LanguagesDialects::create([
                    'cesno' => $item->cesno,
                    'lang_languages_dialects' => $item->lang_code,
                    'last_updated_by' => $item->lastupd_enc,
                    'created_at' => $item->encdate,
                    'updated_at' => $item->lastupd_dt,
                    'encoder' => $item->encoder,      
                ]);

            }

            $finish = new \DateTime();

            DatabaseMigrations::create([

                'updated_category' => 'Language Dialects',
                'table_source' => 'profile_tblLanguages,',
                'start' => $start,
                'finish' => $finish,
                'duration_in_minutes' => $start->diff($finish)->format('%I'),
                'migration_status' => 'Success',
                'last_updated_by' => Auth::user()->role.' - '.Auth::user()->role_name_no,

            ]);

            return 'Successfully Migrated';
        }
        else{

            return 'Restricted';
        }
    }

    public function migrateERISCESWE(Request $request){

        if(RolesController::validateUserCesWebAppGeneralPageAccess('System Utility') == 'true'){

            // Reset ces_wes table
            CesWe::truncate();

            $start = new \DateTime();

            $erad_tblWExam = DB::connection('sqlsrv-2')->table('erad_tblWExam')->get();

            foreach($erad_tblWExam as $item){

                CesWe::create([
                    'cesno' => null,
                    'ed_ces_we' => $item->we_date,
                    'r_ces_we' => $item->we_rating,
                    'rd_ces_we' => null,
                    'poe_ces_we' => $item->we_location,
                    'tn_ces_we' => null,
                    'acno' => $item->acno,
                    'last_updated_by' => null,
                    'created_at' => $item->encdate,
                    'updated_at' => null,
                    'encoder' => $item->encoder,   
                ]);

            }

            $finish = new \DateTime();

            DatabaseMigrations::create([

                'updated_category' => 'ERIS CES WE',
                'table_source' => 'erad_tblWExam,',
                'start' => $start,
                'finish' => $finish,
                'duration_in_minutes' => $start->diff($finish)->format('%I'),
                'migration_status' => 'Success',
                'last_updated_by' => Auth::user()->role.' - '.Auth::user()->role_name_no,

            ]);

            return 'Successfully Migrated';
        }
        else{

            return 'Restricted';
        }
    }

    public function migrateERISAssessmentCenter(Request $request){

        if(RolesController::validateUserCesWebAppGeneralPageAccess('System Utility') == 'true'){

            // Reset assessment_centers table
            AssessmentCenter::truncate();

            $start = new \DateTime();

            $erad_tblAC = DB::connection('sqlsrv-2')->table('erad_tblAC')->get();

            foreach($erad_tblAC as $item){

                AssessmentCenter::create([
                    'cesno' => null,
                    'an_achr_ces_we' => $item->acno,
                    'ad_achr_ces_we' => $item->acdate,
                    'r_achr_ces_we' => null,
                    'cfd_achr_ces_we' => null,
                    'last_updated_by' => null,
                    'created_at' => $item->encdate,
                    'updated_at' => null,
                    'encoder' => $item->encoder,
                ]);

            }

            $finish = new \DateTime();

            DatabaseMigrations::create([

                'updated_category' => 'ERIS Assessment Center',
                'table_source' => 'erad_tblAC,',
                'start' => $start,
                'finish' => $finish,
                'duration_in_minutes' => $start->diff($finish)->format('%I'),
                'migration_status' => 'Success',
                'last_updated_by' => Auth::user()->role.' - '.Auth::user()->role_name_no,

            ]);

            return 'Successfully Migrated';
        }
        else{

            return 'Restricted';
        }
    }

    public function migrateERISValidationInDepthAndRapid(Request $request){

        if(RolesController::validateUserCesWebAppGeneralPageAccess('System Utility') == 'true'){

            // Reset validation_hrs table
            ValidationHr::truncate();

            $start = new \DateTime();

            $erad_tblRVP = DB::connection('sqlsrv-2')->table('erad_tblRVP')->get();
            $erad_tblIVP = DB::connection('sqlsrv-2')->table('erad_tblIVP')->get();

            foreach($erad_tblRVP as $item){

                ValidationHr::create([
                    'cesno' => null,
                    'vd_vhr_ces_we' => $item->dteassign,
                    'tov_vhr_ces_we' => 'Rapid',
                    'r_vhr_ces_we' => null,
                    'acno' => $item->acno,
                    'last_updated_by' => null,
                    'created_at' => $item->encdate,
                    'updated_at' => null,
                    'encoder' => $item->encoder,
                ]);

            }

            foreach($erad_tblIVP as $item){

                ValidationHr::create([
                    'cesno' => null,
                    'vd_vhr_ces_we' => $item->dteassign,
                    'tov_vhr_ces_we' => 'In-Dept',
                    'r_vhr_ces_we' => null,
                    'acno' => $item->acno,
                    'last_updated_by' => null,
                    'created_at' => $item->encdate,
                    'updated_at' => null,
                    'encoder' => $item->encoder,
                ]);

            }

            $finish = new \DateTime();

            DatabaseMigrations::create([

                'updated_category' => 'ERIS Validation In-depth and Rapid',
                'table_source' => 'erad_tblRVP,erad_tblIVP',
                'start' => $start,
                'finish' => $finish,
                'duration_in_minutes' => $start->diff($finish)->format('%I'),
                'migration_status' => 'Success',
                'last_updated_by' => Auth::user()->role.' - '.Auth::user()->role_name_no,

            ]);

            return 'Successfully Migrated';
        }
        else{

            return 'Restricted';
        }
    }

    public function migrateERISBoardInterview(Request $request){

        if(RolesController::validateUserCesWebAppGeneralPageAccess('System Utility') == 'true'){

            // Reset board_interviews table
            BoardInterview::truncate();

            $start = new \DateTime();

            $erad_tblBOARD = DB::connection('sqlsrv-2')->table('erad_tblBOARD')->get();
            $erad_tblPBOARD = DB::connection('sqlsrv-2')->table('erad_tblPBOARD')->get();

            foreach($erad_tblBOARD as $item){

                BoardInterview::create([
                    'cesno' => null,
                    'bid_bi_ces_we' => $item->dteassign,
                    'r_bi_ces_we' => null,
                    'type_of_interview' => 'Board',
                    'acno' => $item->acno,
                    'last_updated_by' => null,
                    'created_at' => $item->encdate,
                    'updated_at' => null,
                    'encoder' => $item->encoder,
                ]);

            }

            foreach($erad_tblPBOARD as $item){

                BoardInterview::create([
                    'cesno' => null,
                    'bid_bi_ces_we' => $item->dteassign,
                    'r_bi_ces_we' => null,
                    'type_of_interview' => 'Panel Board',
                    'acno' => $item->acno,
                    'last_updated_by' => null,
                    'created_at' => $item->encdate,
                    'updated_at' => null,
                    'encoder' => $item->encoder,
                ]);

            }

            $finish = new \DateTime();

            DatabaseMigrations::create([

                'updated_category' => 'ERIS Board Interview',
                'table_source' => 'erad_tblBOARD,erad_tblPBOARD',
                'start' => $start,
                'finish' => $finish,
                'duration_in_minutes' => $start->diff($finish)->format('%I'),
                'migration_status' => 'Success',
                'last_updated_by' => Auth::user()->role.' - '.Auth::user()->role_name_no,

            ]);

            return 'Successfully Migrated';
        }
        else{

            return 'Restricted';
        }
    }

    public function migrateCESStatus(Request $request){

        if(RolesController::validateUserCesWebAppGeneralPageAccess('System Utility') == 'true'){

            // Reset ces_statuses table
            CesStatus::truncate();

            $start = new \DateTime();

            $profile_tblCESstatus = DB::connection('sqlsrv-2')->table('profile_tblCESstatus')->get();

            foreach($profile_tblCESstatus as $item){

                CesStatus::create([
                    'cesno' => $item->cesno,
                    'cs_cs_ces_we' => $item->cesstat_code,
                    'at_cs_ces_we' => $item->acc_code,
                    'st_cs_ces_we' => $item->type_code,
                    'aa_cs_ces_we' => $item->official_code,
                    'rn_cs_ces_we' => $item->resolution_no,
                    'da_cs_ces_we' => $item->appointed_dt,
                    'last_updated_by' => $item->lastupd_enc,
                    'created_at' => $item->encdate,
                    'updated_at' => $item->lastupd_dt,
                    'encoder' => $item->encoder,
                ]);

            }

            $finish = new \DateTime();

            DatabaseMigrations::create([

                'updated_category' => 'CES Status',
                'table_source' => 'profile_tblCESstatus,',
                'start' => $start,
                'finish' => $finish,
                'duration_in_minutes' => $start->diff($finish)->format('%I'),
                'migration_status' => 'Success',
                'last_updated_by' => Auth::user()->role.' - '.Auth::user()->role_name_no,

            ]);

            return 'Successfully Migrated';
        }
        else{

            return 'Restricted';
        }
    }

    public function migrateRecordOfCespesRatings(Request $request){

        if(RolesController::validateUserCesWebAppGeneralPageAccess('System Utility') == 'true'){

            // Reset record_of_cespes_ratings table
            RecordOfCespesRatings::truncate();

            $start = new \DateTime();

            $cespes_tblRatingPeriod = DB::connection('sqlsrv-2')->table('cespes_tblRatingPeriod')->get();

            foreach($cespes_tblRatingPeriod as $item){

                RecordOfCespesRatings::create([
                    'cesno' => $item->cesno,
                    'date_from_rocr' => $item->from_dt,
                    'date_to_rocr' => $item->to_dt,
                    'rating_rocr' => null,
                    'status_rocr' => null,
                    'remarks_rocr' => null,
                    'pdf_rating_certificate_rocr' => null,
                    'last_updated_by' => $item->lastupd_enc,
                    'created_at' => $item->encdate,
                    'updated_at' => $item->lastupd_dt,
                    'encoder' => $item->encoder,
                ]);

            }

            $finish = new \DateTime();

            DatabaseMigrations::create([

                'updated_category' => 'Record of Cespes Ratings',
                'table_source' => 'cespes_tblRatingPeriod,',
                'start' => $start,
                'finish' => $finish,
                'duration_in_minutes' => $start->diff($finish)->format('%I'),
                'migration_status' => 'Success',
                'last_updated_by' => Auth::user()->role.' - '.Auth::user()->role_name_no,

            ]);

            return 'Successfully Migrated';
        }
        else{

            return 'Restricted';
        }
    }

    public function migrateWorkExperience(Request $request){

        if(RolesController::validateUserCesWebAppGeneralPageAccess('System Utility') == 'true'){

            // Reset work_experiences table
            WorkExperience::truncate();

            $start = new \DateTime();

            $profile_tblWorkExperience = DB::connection('sqlsrv-2')->table('profile_tblWorkExperience')->get();

            foreach($profile_tblWorkExperience as $item){

                WorkExperience::create([
                    'cesno' => $item->cesno,
                    'date_from_work_experience' => $item->from_dt,
                    'date_to_work_experience' => $item->to_dt,
                    'destination_from_work_experience' => $item->designation,
                    'status_from_work_experience' => $item->status,
                    'salary_from_work_experience' => $item->salary,
                    'salary_job_pay_grade_work_experience' => null,
                    'status_of_appointment_work_experience' => null,
                    'government_service_work_experience' => null,
                    'department_from_work_experience' => $item->department,
                    'remarks_from_work_experience' => $item->remarks,
                    'last_updated_by' => $item->lastupd_enc,
                    'created_at' => $item->encdate,
                    'updated_at' => $item->lastupd_dt,
                    'encoder' => $item->encoder,
                ]);

            }

            $finish = new \DateTime();

            DatabaseMigrations::create([

                'updated_category' => 'Work Experience',
                'table_source' => 'profile_tblWorkExperience,',
                'start' => $start,
                'finish' => $finish,
                'duration_in_minutes' => $start->diff($finish)->format('%I'),
                'migration_status' => 'Success',
                'last_updated_by' => Auth::user()->role.' - '.Auth::user()->role_name_no,

            ]);

            return 'Successfully Migrated';
        }
        else{

            return 'Restricted';
        }
    }
}
