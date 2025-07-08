<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

use App\Http\Controllers\ConnectionController as Connection;

Route::GET('/connection', [Connection::class, 'index'])->name('connection');

use App\Http\Controllers\ApprovalController as ApprovalMail;

Route::POST('/mailApproval', [ApprovalMail::class, 'sendApprovalMail']);
Route::GET('/approvestatus/{status}/{entity_cd}/{doc_no}/{level_no}', [ApprovalMail::class, 'changestatus']);

use App\Http\Controllers\LandApprovalController as LandApproval;

Route::POST('/SendMailLand', [LandApproval::class, 'LandApprovalMail']);
Route::POST('/SendMailLand/update', [LandApproval::class, 'update']);
Route::GET('/approvestatusLand/{status}/{entity_cd}/{doc_no}/{level_no}', [LandApproval::class, 'changestatus']);

use App\Http\Controllers\LandFphApprovalController as LandFphApproval;

Route::POST('/SendMailLandfph', [LandFphApproval::class, 'LandFphApprovalMail']);
Route::POST('/SendMailLandfph/update', [LandFphApproval::class, 'update']);
Route::GET('/approvestatusLandFph/{status}/{entity_cd}/{doc_no}/{level_no}', [LandFphApproval::class, 'changestatus']);

use App\Http\Controllers\LandChangeEntityController as LandChangeEntity;

Route::POST('/SendMailchangeentity', [LandChangeEntity::class, 'LandChangeEntityMail']);
Route::POST('/SendMailchangeentity/update', [LandChangeEntity::class, 'update']);
Route::GET('/SendMailchangeentity/{status}/{entity_cd}/{doc_no}/{level_no}', [LandChangeEntity::class, 'changestatus']);

use App\Http\Controllers\LandMasterApprovalController as LandMasterApproval;

Route::POST('/SendMailLandmaster', [LandMasterApproval::class, 'LandMasterApprovalMail']);
Route::POST('/SendMailLandmaster/update', [LandMasterApproval::class, 'update']);
Route::GET('/approvestatusLandMaster/{status}/{entity_cd}/{doc_no}/{level_no}', [LandMasterApproval::class, 'changestatus']);

use App\Http\Controllers\LandVerificationApprovalController as LandVerificationApproval;

Route::POST('/SendMailLandverififcation', [LandVerificationApproval::class, 'LandVerificationApprovalMail']);
Route::POST('/SendMailLandverififcation/update', [LandVerificationApproval::class, 'update']);
Route::GET('/approvestatusLandverififcation/{status}/{entity_cd}/{doc_no}/{level_no}', [LandVerificationApproval::class, 'changestatus']);

use App\Http\Controllers\LandMeasuringController as LandMeasuring;

Route::POST('/landmeasuring', [LandMeasuring::class, 'LandMeasuringMail']);
Route::POST('/landmeasuring/update', [LandMeasuring::class, 'update']);
Route::GET('/landmeasuring/{status}/{entity_cd}/{doc_no}/{level_no}', [LandMeasuring::class, 'changestatus']);

use App\Http\Controllers\LandSphController as LandSph;

Route::POST('/landsph', [LandSph::class, 'LandSphMail']);
Route::POST('/landsph/update', [LandSph::class, 'update']);
Route::GET('/landsph/{status}/{entity_cd}/{doc_no}/{level_no}', [LandSph::class, 'changestatus']);

use App\Http\Controllers\LandSertifikatController as LandSertifikat;

Route::POST('/landsertifikat', [LandSertifikat::class, 'LandSertifikatMail']);
Route::GET('/landsertifikat/{status}/{entity_cd}/{doc_no}/{level_no}', [LandSertifikat::class, 'changestatus']);

use App\Http\Controllers\LandRequestController as LandRequest;

Route::POST('/landrequest', [LandRequest::class, 'LandRequestMail']);
Route::POST('/landrequest/update', [LandRequest::class, 'update']);
Route::GET('/landrequest/{status}/{entity_cd}/{doc_no}/{level_no}', [LandRequest::class, 'changestatus']);


use App\Http\Controllers\LandSubmissionController as LandSubmission;

Route::POST('/landsubmission', [LandSubmission::class, 'mail']);
Route::POST('/landsubmission/update', [LandSubmission::class, 'update']);
Route::GET('/landsubmission/{status}/{entity_cd}/{doc_no}/{level_no}', [LandSubmission::class, 'changestatus']);

use App\Http\Controllers\AgentDeactiveController as AgentDeactive;

Route::POST('/agentdeactive', [AgentDeactive::class, 'AgentDeactiveMail']);
Route::POST('/agentdeactive/update', [AgentDeactive::class, 'update']);
Route::GET('/agentdeactive/{status}/{entity_cd}/{doc_no}/{level_no}/{code}', [AgentDeactive::class, 'changestatus']);

use App\Http\Controllers\SalesDeactiveController as SalesDeactive;

Route::POST('/salesdeactive', [SalesDeactive::class, 'SalesDeactiveMail']);
Route::POST('/salesdeactive/update', [SalesDeactive::class, 'update']);
Route::GET('/salesdeactive/{status}/{entity_cd}/{doc_no}/{level_no}/{code}', [SalesDeactive::class, 'changestatus']);

use App\Http\Controllers\LotPriceDeactiveController as LotPriceDeactive;

Route::POST('/lotpricedeactive', [LotPriceDeactive::class, 'Mail']);
Route::POST('/lotpricedeactive/update', [LotPriceDeactive::class, 'update']);
Route::GET('/lotpricedeactive/{status}/{entity_cd}/{doc_no}/{level_no}/{code}/{lot_no}', [LotPriceDeactive::class, 'changestatus']);

use App\Http\Controllers\LotTempController as LotTemp;

Route::POST('/lottemp', [LotTemp::class, 'Mail']);
Route::GET('/lottemp/{entity_cd}/{project_no}/{doc_no}/{status}/{level_no}/{grp_name}/{userid}', [LotTemp::class, 'changestatus']);
Route::POST('/lottemp/update', [LotTemp::class, 'update']);

use App\Http\Controllers\SalesLotController as SalesLot;

Route::POST('/saleslot', [SalesLot::class, 'Mail']);
Route::GET('/saleslot/{entity_cd}/{project_no}/{doc_no}/{status}/{level_no}/{rt_grp_name}/{userid}', [SalesLot::class, 'changestatus']);
Route::POST('/saleslot/update', [SalesLot::class, 'update']);

use App\Http\Controllers\SalesChangeNameController as SalesChangeName;

Route::POST('/saleschangename', [SalesChangeName::class, 'Mail']);
Route::POST('/saleschangename/update', [SalesChangeName::class, 'update']);
Route::GET('/saleschangename/{entity_cd}/{project_no}/{doc_no}/{lot_no}/{status}/{level_no}/{grp}/{userid}', [SalesChangeName::class, 'changestatus']);

use App\Http\Controllers\MeasuringSftController as MeasuringSft;

Route::POST('/measuringsft', [MeasuringSft::class, 'Mail']);
Route::POST('/measuringsft/update', [MeasuringSft::class, 'update']);
Route::GET('/measuringsft/{status}/{entity_cd}/{doc_no}/{level_no}', [MeasuringSft::class, 'changestatus']);

use App\Http\Controllers\LandApprovalSftController as LandApprovalSft;

Route::POST('/landapprovalsft', [LandApprovalSft::class, 'Mail']);
Route::POST('/landapprovalsft/update', [LandApprovalSft::class, 'update']);
Route::GET('/landapprovalsft/{status}/{entity_cd}/{doc_no}/{level_no}', [LandApprovalSft::class, 'changestatus']);

use App\Http\Controllers\LandApprovalSftBphtbController as LandApprovalSftBphtb;

Route::POST('/landapprovalsftbphtb', [LandApprovalSftBphtb::class, 'Mail']);
Route::POST('/landapprovalsftbphtb/update', [LandApprovalSftBphtb::class, 'update']);
Route::GET('/landapprovalsftbphtb/{status}/{entity_cd}/{doc_no}/{level_no}', [LandApprovalSftBphtb::class, 'changestatus']);

use App\Http\Controllers\LandApprovalSftShgbController as LandApprovalSftShgb;

Route::POST('/landapprovalsftshgb', [LandApprovalSftShgb::class, 'Mail']);
Route::POST('/landapprovalsftshgb/update', [LandApprovalSftShgb::class, 'update']);
Route::GET('/landapprovalsftshgb/{status}/{entity_cd}/{doc_no}/{level_no}', [LandApprovalSftShgb::class, 'changestatus']);

use App\Http\Controllers\PlBudgetLymanController as PlBudgetLyman;

Route::POST('/plbudgetlyman', [PlBudgetLyman::class, 'Mail']);
Route::GET('/plbudgetlyman/{entity_cd}/{project_no}/{doc_no}/{status}/{level_no}/{user_id}', [PlBudgetLyman::class, 'changestatus']);

use App\Http\Controllers\PlBudgetRevisionController as PlBudgetRevision;

Route::POST('/plbudgetrevision', [PlBudgetRevision::class, 'Mail']);
Route::GET('/plbudgetrevision/{entity_cd}/{project_no}/{doc_no}/{trx_type}/{status}/{level_no}/{userid}', [PlBudgetRevision::class, 'changestatus']);

use App\Http\Controllers\PlCogsActController as PlCogsAct;

Route::POST('/plcogsact', [PlCogsAct::class, 'Mail']);
Route::GET('/plcogsact/{entity_cd}/{project_no}/{doc_no}/{status}/{level_no}/{userid}', [PlCogsAct::class, 'changestatus']);

use App\Http\Controllers\PlRecMaintenanceController as PlRecMaintenance;

Route::POST('/plrecmaintenance', [PlRecMaintenance::class, 'Mail']);
Route::GET('/plrecmaintenance/{entity_cd}/{project_no}/{doc_no}/{status}/{level_no}/{userid}', [PlRecMaintenance::class, 'changestatus']);

use App\Http\Controllers\SalesCancelController as SalesCancel;

Route::POST('/salescancel', [SalesCancel::class, 'Mail']);
Route::POST('/salescancel/update', [SalesCancel::class, 'update']);
Route::GET('/salescancel/{entity_cd}/{project_no}/{doc_no}/{status}/{level_no}/{user_id}', [SalesCancel::class, 'changestatus']);

use App\Http\Controllers\CmProgressController as CmProgress;

Route::POST('/cmprogress', [CmProgress::class, 'Mail']);
Route::GET('/cmprogress/{entity_cd}/{project_no}/{doc_no}/{ref_no}/{status}/{level_no}/{usergroup}/{user_id}/{supervisor}', [CmProgress::class, 'changestatus']);

use App\Http\Controllers\ProspectCancelController as ProspectCancel;

Route::POST('/prospectcancel', [ProspectCancel::class, 'Mail']);
Route::POST('/prospectcancel/update', [ProspectCancel::class, 'update']);
Route::GET('/prospectcancel/{entity_cd}/{project_no}/{doc_no}/{ref_no}/{status}/{level_no}/{user_id}/', [ProspectCancel::class, 'changestatus']);

use App\Http\Controllers\CmContractDoneController as CmContractDone;

Route::POST('/cmcontractdone', [CmContractDone::class, 'Mail']);
Route::GET('/cmcontractdone/{entity_cd}/{project_no}/{doc_no}/{ref_no}/{status}/{level_no}/{usergroup}/{user_id}/{supervisor}', [CmContractDone::class, 'changestatus']);

use App\Http\Controllers\CmContractCloseController as CmContractClose;

Route::POST('/cmcontractclose', [CmContractClose::class, 'Mail']);
Route::GET('/cmcontractclose/{entity_cd}/{project_no}/{doc_no}/{ref_no}/{status}/{level_no}/{usergroup}/{user_id}/{supervisor}', [CmContractClose::class, 'changestatus']);

use App\Http\Controllers\RsRevenueShareController as RsRevenueShare;

Route::POST('/revenueshare', [RsRevenueShare::class, 'Mail']);
Route::POST('/revenueshare/update', [RsRevenueShare::class, 'update']);
Route::GET('/revenueshare/{entity_cd}/{project_no}/{doc_no}/{trx_type}/{doc_date}/{ref_no}/{status}/{level_no}/{usergroup}/{user_id}/{supervisor}', [RsRevenueShare::class, 'changestatus']);

use App\Http\Controllers\SalesLotActivityController as SalesLotActivity;

Route::POST('/saleslotactivity', [SalesLotActivity::class, 'Mail']);
Route::POST('/saleslotactivity/update', [SalesLotActivity::class, 'update']);
Route::GET('/saleslotactivity/{entity_cd}/{project_no}/{doc_no}/{status}/{level_no}/{user_id}', [SalesLotActivity::class, 'changestatus']);

use App\Http\Controllers\SalesLotActivityProspController as SalesLotActivityProsp;

Route::POST('/saleslotactivityprosp', [SalesLotActivityProsp::class, 'Mail']);
Route::POST('/saleslotactivityprosp/update', [SalesLotActivityProsp::class, 'update']);
Route::GET('/saleslotactivityprosp/{entity_cd}/{project_no}/{doc_no}/{prospect_no}/{status}/{level_no}/{user_id}', [SalesLotActivityProsp::class, 'changestatus']);

use App\Http\Controllers\SalesBookingPackageController as SalesBookingPackage;

Route::POST('/salesbookingpackage', [SalesBookingPackage::class, 'Mail']);
Route::GET('/salesbookingpackage/{entity_cd}/{project_no}/{doc_no}/{lot_no}/{status}/{level_no}/{user_id}', [SalesBookingPackage::class, 'changestatus']);

use App\Http\Controllers\SalesBookingDiscController as SalesBookingDisc;

Route::POST('/salesbookingdisc', [SalesBookingDisc::class, 'Mail']);
Route::GET('/salesbookingdisc/{entity_cd}/{project_no}/{doc_no}/{lot_no}/{status}/{level_no}/{user_id}', [SalesBookingDisc::class, 'changestatus']);

use App\Http\Controllers\SalesTransferController as SalesTransfer;

Route::POST('/salestransfer', [SalesTransfer::class, 'Mail']);
Route::GET('/salestransfer/{entity_cd}/{project_no}/{doc_no}/{status}/{level_no}/{user_id}', [SalesTransfer::class, 'changestatus']);

use App\Http\Controllers\CmVoController as CmVo;

Route::POST('/cmvo', [CmVo::class, 'Mail']);
Route::GET('/cmvo/{entity_cd}/{project_no}/{doc_no}/{ref_no}/{status}/{level_no}/{usergroup}/{user_id}/{supervisor}', [CmVo::class, 'changestatus']);

use App\Http\Controllers\LandChangeNopApprovalController as LandChangeNopApproval;

Route::POST('/approvestatusLandChangeNop', [LandChangeNopApproval::class, 'Mail']);
Route::POST('/approvestatusLandChangeNop/update', [LandChangeNopApproval::class, 'update']);
Route::GET('/approvestatusLandChangeNop/{entity_cd}/{doc_no}/{status}/{level_no}/{user_id}', [LandChangeNopApproval::class, 'changestatus']);

use App\Http\Controllers\CoordinateController as Coordinate;

Route::GET('/getCoordinate', [Coordinate::class, 'getCoordinate']);

use App\Http\Controllers\LandBoundaryController as LandBoundary;

Route::POST('/landboundary', [LandBoundary::class, 'Mail']);
Route::POST('/landboundary/update', [LandBoundary::class, 'update']);
Route::GET('/landboundary/{status}/{entity_cd}/{doc_no}/{level_no}', [LandBoundary::class, 'changestatus']);

use App\Http\Controllers\LandMasterRenewController as LandMasterRenew;

Route::POST('/landmasterrenew', [LandMasterRenew::class, 'Mail']);
Route::POST('/landmasterrenew/update', [LandMasterRenew::class, 'update']);
Route::GET('/landmasterrenew/{status}/{entity_cd}/{ref_no}/{doc_no}/{level_no}', [LandMasterRenew::class, 'changestatus']);

use App\Http\Controllers\CmProgressMockupController as CmProgressMockup;

Route::POST('/cmprogressmockup', [CmProgressMockup::class, 'Mail']);
Route::GET('/cmprogressmockup/{entity_cd}/{project_no}/{doc_no}/{ref_no}/{status}/{level_no}/{usergroup}/{user_id}/{supervisor}', [CmProgressMockup::class, 'changestatus']);
Route::POST('/cmprogressmockup/update', [CmProgressMockup::class, 'update']);

use App\Http\Controllers\SalesLotActivityProspnewController as SalesLotActivityProspnew;

Route::POST('/saleslotactivityprospnew', [SalesLotActivityProspnew::class, 'Mail']);
Route::GET('/saleslotactivityprospnew/{entity_cd}/{project_no}/{doc_no}/{status}/{level_no}/{user_id}', [SalesLotActivityProspnew::class, 'changestatus']);

use App\Http\Controllers\ContractTerminateController as ContractTerminate;

Route::POST('/contractterminate', [ContractTerminate::class, 'Mail']);
Route::POST('/contractterminate/update', [ContractTerminate::class, 'update']);
Route::GET('/contractterminate/{entity_cd}/{project_no}/{doc_no}/{status}/{level_no}/{user_id}/{doc_date}', [ContractTerminate::class, 'changestatus']);

use App\Http\Controllers\ContractRenewController as ContractRenew;

Route::POST('/contractrenew', [ContractRenew::class, 'Mail']);
Route::POST('/contractrenew/update', [ContractRenew::class, 'update']);
Route::GET('/contractrenew/{entity_cd}/{project_no}/{doc_no}/{ref_no}/{status}/{level_no}/{user_id}/{grp_name}/{renew_no}', [ContractRenew::class, 'changestatus']);

use App\Http\Controllers\SendController as Send;
Route::POST('/Sendattach', [Send::class, 'Mail']);

use App\Http\Controllers\InvoiceIfcaController as InvoiceIfca;
Route::POST('/invoice_send', [InvoiceIfca::class, 'index']);

use App\Http\Controllers\ShgbMergerController as ShgbMerger;
Route::POST('/shgbmerger', [ShgbMerger::class, 'mail']);
Route::GET('/shgbmerger/{entity_cd}/{doc_no}/{status}/{level_no}', [ShgbMerger::class, 'changestatus']);
Route::POST('/shgbmerger/update', [ShgbMerger::class, 'update']);

use App\Http\Controllers\LandCancelNopController as LandCancelNop;
Route::POST('/landcancelnop', [LandCancelNop::class, 'mail']);
Route::GET('/landcancelnop/{entity_cd}/{doc_no}/{status}/{level_no}', [LandCancelNop::class, 'changestatus']);
Route::POST('/landcancelnop/update', [LandCancelNop::class, 'update']);

use App\Http\Controllers\LandApprovalHandoverShgbController as LandApprovalHandoverShgb;
Route::POST('/landapprovalhandovershgb', [LandApprovalHandoverShgb::class, 'Mail']);
Route::POST('/landapprovalhandovershgb/update', [LandApprovalHandoverShgb::class, 'update']);
Route::GET('/landapprovalhandovershgb/{status}/{entity_cd}/{doc_no}/{level_no}', [LandApprovalHandoverShgb::class, 'changestatus']);

use App\Http\Controllers\LandApprovalHandoverLegalController as LandApprovalHandoverLegal;
Route::POST('/landapprovalhandoverlegal', [LandApprovalHandoverLegal::class, 'Mail']);
Route::POST('/landapprovalhandoverlegal/update', [LandApprovalHandoverLegal::class, 'update']);
Route::GET('/landapprovalhandoverlegal/{status}/{entity_cd}/{doc_no}/{level_no}', [LandApprovalHandoverLegal::class, 'changestatus']);


use App\Http\Controllers\LandApprovalSplitShgbController as LandApprovalSplitShgb;

Route::POST('/landapprovalsplitshgb', [LandApprovalSplitShgb::class, 'Mail']);
Route::POST('/landapprovalsplitshgb/update', [LandApprovalSplitShgb::class, 'update']);
Route::GET('/landapprovalsplitshgb/{status}/{entity_cd}/{doc_no}/{level_no}', [LandApprovalSplitShgb::class, 'changestatus']);

use App\Http\Controllers\LandApprovalExtShgbController as LandApprovalExtShgb;

Route::POST('/landapprovalextshgb', [LandApprovalExtShgb::class, 'Mail']);
Route::POST('/landapprovalextshgb/update', [LandApprovalExtShgb::class, 'update']);
Route::GET('/landapprovalextshgb/{status}/{entity_cd}/{doc_no}/{level_no}', [LandApprovalExtShgb::class, 'changestatus']);


use App\Http\Controllers\AutoSendController as AutoSend;
Route::GET('/autosend', [AutoSend::class, 'index']);

use App\Http\Controllers\LandRequestLegalController as LandRequestLegal;

Route::POST('/landrequestlegal', [LandRequestLegal::class, 'index']);
Route::POST('/landrequestlegal/update', [LandRequestLegal::class, 'update']);
Route::GET('/landrequestlegal/{status}/{entity_cd}/{doc_no}/{level_no}', [LandRequestLegal::class, 'changestatus']);


use App\Http\Controllers\FeedbackLandSubmissionController as FeedbackSubmission;
Route::POST('/feedbacksubmission', [FeedbackSubmission::class, 'Mail']);

use App\Http\Controllers\FeedbackLandSphController as FeedbackSph;
Route::POST('/feedbackSph', [FeedbackSph::class, 'Mail']);

use App\Http\Controllers\FeedbackLandRequestController as FeedbackLandRequest;
Route::POST('/feedbacklandrequest', [FeedbackLandRequest::class, 'Mail']);

use App\Http\Controllers\ContractTerminateLotController as ContractTerminateLot;

Route::POST('/contractterminatelot', [ContractTerminateLot::class, 'Mail']);
Route::POST('/contractterminatelot/update', [ContractTerminateLot::class, 'update']);
Route::GET('/contractterminatelot/{entity_cd}/{project_no}/{doc_no}/{status}/{level_no}/{user_id}/{doc_date}', [ContractTerminateLot::class, 'changestatus']);

