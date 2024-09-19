<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\WebsiteTestimonialController;


Route::middleware(['statusCheck'])->group(function () {
    /******************************* website panel route start **********************************/

    Route::prefix('website/advertisement')->group(function () {
        Route::get('/lists', [WebsiteController::class, 'getAdvertisement'])->name('AdvertisementLists');
        Route::post('/store', [WebsiteController::class, 'storeAdvertisement'])->name('storeAdvertisement');
        Route::post('update-detail', [WebsiteController::class, 'updateAdvertisement'])->name('updateAdvertisement');
        Route::delete('{id}/destroy', [WebsiteController::class, 'destroyAdvertisement'])->name('destroyAdvertisement');
    });

    Route::prefix('website/slider')->group(function () {
        Route::get('/lists', [WebsiteController::class, 'getSlider'])->name('sliderLists');
        Route::post('/get-website-product', [WebsiteController::class, 'getWebsite_prod'])->name('getWebsiteProd');
        Route::post('/store', [WebsiteController::class, 'store_slider'])->name('sliderStore');
        Route::post('update-slide', [WebsiteController::class, 'update_slide'])->name('updateSliderImage');
        Route::delete('{id}/destroy-slide', [WebsiteController::class, 'destroy_slide'])->name('destroySliderImage');
    });

    Route::prefix('website/social-link')->group(function () {
        Route::get('/lists', [WebsiteController::class, 'getSocialLink'])->name('socialList');
        Route::post('/store', [WebsiteController::class, 'store_SocialLink'])->name('socialinkStore');
        Route::patch('/{id}/update', [WebsiteController::class, 'update_socialLink'])->name('socialinkUpdate');
        Route::delete('/{id}/destroy', [WebsiteController::class, 'destroy_socialLink'])->name('socialinkDestroy');
    });

    Route::prefix('delivery')->group(function () {
        Route::get('/lists', [WebsiteController::class, 'getDeliveryArea'])->name('deliveryAreasList');
        Route::post('/get-website-branches', [WebsiteController::class, 'getWebsiteBranches'])->name('getWebsiteBranches');
        Route::post('/get-area-values', [WebsiteController::class, 'getDeliveryAreaValues'])->name('getdeliveryAreasValues');
        Route::post('/store', [WebsiteController::class, 'store_deliveryArea'])->name('deliveryAreaStore');
        Route::post('/add-area', [WebsiteController::class, 'single_deliveryAreaName_store'])->name('deliveryAreaNameStore');
        Route::post('/update-area-detail', [WebsiteController::class, 'update_deliveryArea'])->name('deliveryAreaNameUpdate');
        // Route::patch('/{id}/',[WebsiteController::class,' update_deliveryArea'])->name('deliveryAreaNameUpdate');
        Route::patch('/{branchid}/update', [WebsiteController::class, 'update_deliveryAreaSpecificField'])->name('deliveryAreaUpdate');
        Route::delete('/{branchid}/destroy', [WebsiteController::class, 'destroy_deliveryArea'])->name('deliveryAreaDestroy');
        Route::delete('/{id}/{branchid}/destroy-area-value', [WebsiteController::class, 'destroy_deliveryAreaValue'])->name('deliveryAreaValueDestroy');
    });

    Route::prefix('website/terminal-assign')->group(function () {
        Route::get('/view', [WebsiteController::class, 'getTerminalAssign'])->name('terminalAssignList');
        Route::post('/get-terminal-branches', [WebsiteController::class, 'getTerminalsFromBranches'])->name('getTerminalBranches');
        Route::post('/store', [WebsiteController::class, 'storeterminalBind'])->name('terminalAssignStore');
        Route::post('/update', [WebsiteController::class, 'updateTerminalBind'])->name('terminalAssignUpdate');
        Route::post('/delete', [WebsiteController::class, 'deleteTerminalBind'])->name('deleteWebsiteTerminal');
    });


    Route::prefix('website/branch-timings')->group(function () {
        Route::get('/view/{id?}', [WebsiteController::class, 'viewBranchTiming'])->name('branchTimingList');
        Route::post('/get-branch-timing', [WebsiteController::class, 'getBranchTiming'])->name('getBranchTiming');
        Route::post('/branch-timing-store', [WebsiteController::class, 'storeBranchTiming'])->name('branchTimingStore');
        Route::post('/branch-timing-delete', [WebsiteController::class, 'deleteBranchTiming'])->name('deleteBranchTiming');
    });

    Route::prefix('website/')->group(function () {
        Route::get('theme-setting/{id?}', [WebsiteController::class, 'website_setting'])->name('getWebSetting');
        Route::post('save-changes', [WebsiteController::class, 'webSetting_saveChanges'])->name('webSetSaveChanges');
        Route::post('get-website-branch-schedule', [WebsiteController::class, 'get_websiteBranches_schedule'])->name('getWebsiteBrancheSchedule');
        Route::post('website-branch-isopen', [WebsiteController::class, 'websiteBranches_isOpen'])->name('websiteBranchesIsOpen');

        Route::post('website-isopen', [WebsiteController::class, 'websiteIsOpen'])->name('websiteIsOpen');

        Route::post('/get-depart-n-subdepart', [WebsiteController::class, 'getDepart_n_subDepart_website_product'])->name('getDepart_n_subDepart_wb');
    });

    Route::resource('website/testimonials', WebsiteTestimonialController::class);
    Route::get('website/testimonials/{id}/filter', [WebsiteTestimonialController::class,'index'])->name('filterTestimonial');

    Route::get('website/customer-reviews/lists', [WebsiteController::class,'getCustomer_reviews']);
    Route::get('website/customer-reviews/{id}/filter', [WebsiteController::class, 'getCustomer_reviews'])->name('filterCustomerReviews');


    Route::resource('website', WebsiteController::class);
    /******************************* website panel route closing **********************************/
});
