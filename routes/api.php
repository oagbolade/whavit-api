<?php

use Illuminate\Http\Request;
use App\Card;
use App\Booking;
use App\User;
use App\Location;
use App\Service;
use App\ProductCategory;
use App\Extra;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('mail/disinfection', 'API\Main\DisinfectionResponderController@disinfectionResponder');
Route::post('mail/whavpremium', 'API\Main\WhavPremiumResponderController@whavPremiumResponder');

Route::group(['middleware' => 'api'], function ($router) {
    
    $router->get('test-not/{id}','API\Main\BookingController@newRequestNotfTest');
    
    $router->post('auth/2', function(){
        return response()->json([
            'auth' => true
        ],200);
    });

    // For Disinfection Invoice
    Route::get('/invoice/{name}/{company_name}/{message}/{quantity}/{amount}', 'API\Main\DisinfectionInvoice@show');
    
    //This is to test push notification 
    Route::get('test/notification/{id}', 'API\User\UserController@testNotification');

    Route::post('mailchimp/subscribe', 'API\Main\MailchimpController@create');

    Route::group(['prefix' => 'auth'], function () {
        Route::post('authenticate', 'API\Auth\AuthController@authenticate')->name('api.authenticate');
        Route::post('register', 'API\Auth\AuthController@register')->name('api.register');
    });

    //Product Category
    $router->group(['prefix'=>'product-category'], function($router){
        $router->get('all','API\Admin\ProductCategoryController@allCategories');
    });

    //Product service
    $router->group(['prefix'=>'service'], function($router){
        $router->get('all','API\Admin\ServiceController@allServices');
    });

    //extra
    $router->group(['prefix'=>'extra'], function($router){
        $router->get('all','API\Admin\ExtraController@allExtras');
    });

    //Booking
    $router->group(['prefix'=>'booking'], function($router){
        $router->get('/discount/check/{code}','API\Main\DiscountController@checkDiscount');
    });

    //User
    $router->group(['prefix'=>'user'], function($router){
        Route::post('send-reset-code/{emai}', 'API\User\UserController@checkMail');
        Route::post('verify-reset/{code}', 'API\User\UserController@validateResetCode')->name('password_rest.verify');
        Route::post('send-refferal/{email}','API\User\ReferralController@sendReferralLink');
    });
    

    // Location
    $router->group(['prefix'=> 'location'], function($router){
        $router->get('all', function(){
            return response()->json(Location::all(),200);
        });
    });

    Route::group(['middleware' => 'auth'], function ($router) {
        //Profile
        $router->group(['prefix' => 'profile'], function () {
            Route::post('change-password', 'API\Auth\AuthController@changePassword')->name('change-password');
            Route::put('update-profile', 'API\User\UserController@updateProfile');
            Route::put('device/update', 'API\User\UserController@updateDeviceDetails');
            Route::get('details', 'API\User\UserController@userDetails');
            Route::put('update/img', 'API\Auth\AuthController@updateProfilePicture');
        });

        // wallet
        $router->group(['prefix'=>'wallet'],function($router){
            $router->get('view','API\User\WalletController@show');
            $router->post('update','API\User\WalletController@edit');
            $router->put('tip/{id}','API\User\WalletController@sendTipFromWallet');
        });

        // notification
        $router->group(['prefix'=>'notification'],function($router){
            $router->get('list/{id}','API\User\NotificationController@list');
        });

        //payment
        $router->group(['prefix'=>'payment'], function($router){
            $router->post('initialize','API\User\WalletController@initializeTransaction');
            $router->get('verify','API\User\WalletController@verifyTransaction');
            $router->post('charge_card','API\User\WalletController@addToWallet');
        });

        //transactions
        $router->group(['prefix'=>'transaction'], function ($router){
            $router->post('create','API\Admin\TransactionController@createTransaction');
            $router->get('all','API\Admin\TransactionController@show');
        });

        //booking
        $router->group(['prefix'=>'booking'], function($router){

            $router->post('create','API\Main\BookingController@create');

            $router->get('all','API\Main\BookingController@showAll');
           
            $router->get('view/{id}','API\Main\BookingController@showOne');

            $router->get('show/{id}','API\Main\BookingController@showBooking');

            $router->put('update/{id}','API\Main\BookingController@updateBooking');
          
            $router->put('/{bookingId}/update/product-category/{id}','API\Main\BookingController@updateCategory');
    
            $router->put('/{bookingId}/update/schedule','API\Main\BookingController@updateSchedule');
            
            $router->put('/{bookingId}/update/reschedule','API\Main\BookingController@rescheduleBooking');
            
            $router->put('/{bookingId}/update/rooms','API\Main\BookingController@updateRooms');
            
            $router->put('/{bookingId}/update/location','API\Main\BookingController@updateLocation');
    
            $router->put('/{bookingId}/update/price','API\Main\BookingController@updatePrice');

            $router->put('/{bookingId}/update/address','API\Main\BookingController@updateAddress');
            
            $router->put('/{bookingId}/update/cancel','API\Main\BookingController@cancelRequest');
    
            $router->put('/{bookingId}/update/time','API\Main\BookingController@updateTime');
    
            $router->put('/{bookingId}/update/start_date','API\Main\BookingController@updateStartDate');
    
            $router->put('/{bookingId}/update/extra/add/{id}','API\Main\ExtraController@addExtra');
          
            $router->put('/{bookingId}/update/extra/remove/{id}','API\Main\ExtraController@removeExtra');
    
            $router->put('/{bookingId}/update/service/add/{id}','API\Main\ServiceController@addService');
          
            $router->put('/{bookingId}/update/service/remove/{id}','API\Main\ServiceController@removeService');
    
            $router->put('/{bookingId}/update/vendor/add/{id}','API\Main\VendorController@assignVendor');

            $router->put('/{bookingId}/update/vendor/remove/{id}','API\Main\VendorController@unAssignVendor');
            
            $router->put('/{bookingId}/{start_date}/update/vendor/add/assign/many','API\Main\VendorController@assignManyVendors');
            
            $router->put('/{bookingId}/update/vendor/accept','API\Main\VendorController@vendorAccept');
    
            $router->put('/{bookingId}/update/vendor/reject','API\Main\VendorController@vendorReject');

            $router->put('/{bookingId}/update/discount/add/{code}','API\Main\DiscountController@addDiscount');

            $router->put('/{bookingId}/update/discount/remove/{code}','API\Main\DiscountController@removeDiscount');
    
            $router->put('/{bookingId}/update/attribute/add/{id}','API\Main\AttributeController@addAttributeToBooking');
    
            $router->put('/{bookingId}/update/attribute/remove/{id}','API\Main\AttributeController@removeAttributeFromBooking');
      
            $router->get('/{bookingId}/debit_from_wallet','API\Main\PaymentController@payWithWallet');
    
            $router->get('/pay_with_card','API\User\WalletController@verifyTransactionForDirectPayment');
            
            $router->post('/full','API\Main\BookingController@createFullBooking');

            $router->post('/special','API\Main\BookingController@createSpecialBooking');
    
            $router->get('/task/{task_id}/done','API\Main\TaskController@taskDone');
           
            $router->get('/{booking_id}/task/all/done','API\Main\TaskController@allDone');
           
            $router->get('/{booking_id}/task/all','API\Main\TaskController@getTaskByBookingId');
           
        });

        //Cleaning Pro or Vendors 
        $router->group(['prefix' => 'vendor'], function ($router) {
            $router->get('/verify/{id}', 'API\Admin\VendorController@verifyVendor');

            $router->get('all', 'API\Vendor\VendorController@showAll');
            $router->get('unbooked/all', 'API\Vendor\VendorController@showUnbooked');
            $router->get('view/{id}', 'API\Vendor\VendorController@showOne');
            $router->get('bookings/all', 'API\Vendor\BookingController@showBookings');

            //Start: Routes for mobile
            $router->get('bookings/accepted/{id}', 'API\Vendor\BookingController@showAcceptedBookings');
            $router->get('bookings/pending/all', 'API\Vendor\BookingController@showPendingBookings');
            $router->get('bookings/rejected/all', 'API\Vendor\BookingController@showRejectedBookings');
            $router->get('bookings/completed/{id}', 'API\Vendor\BookingController@showCompletedBookings');
            //End: Routes for mobile 

            $router->get('booking/accept/{id}','API\Vendor\VendorController@acceptRequest');
            $router->get('booking/reject/{id}','API\Vendor\VendorController@rejectRequest');
            $router->post('booking/nearest', 'API\Vendor\BookingController@findNearest');
            $router->get('status/online','API\Vendor\VendorController@setOnline');
            $router->get('status/offline','API\Vendor\VendorController@setOffline');
            $router->get('all/online/{location}','API\Vendor\BookingController@getOnlineByLocation');
            $router->get('online/all', 'API\Vendor\VendorController@getOnlineVendors');

            $router->post('/bank/add','API\Vendor\BankController@addBank');
            $router->put('/bank_name/edit/{id}','API\Vendor\BankController@changeBankName');
            $router->get('/bank/all','API\Vendor\BankController@getVendorBanks');
            $router->post('/bank/disburse','API\Vendor\PayoutController@disburseFund');
        });

        //user
        $router->group(['prefix' => 'user'], function ($router) {
            $router->get('all', 'API\User\UserController@allUsers');
            $router->get('business/all', 'API\User\UserController@allBusinessReps');
            $router->get('admin/all', 'API\User\UserController@allAdmins');
            $router->get('{id}/get','API\User\UserController@getUserById');
            $router->get('booking/all','API\Main\BookingController@showByUser');
            $router->get('vendordetails/view/{vendor_id}','API\Main\BookingController@vendorDetails');
            
    
            $router->get('admin/all','API\User\UserController@getAdmins');
    
            // $router->get('business/all','API\User\UserController@getBusiness');
    
            $router->get('card/all', function(){
                return Auth()->user()->card()->get()->unique('last_4');
            });
    
            $router->delete('card/delete/{id}',function($id){
                $card = Card::findOrFail($id);
    
                if($card->delete()){
                    return response()->json([
                        'message' => 'Card removed',
                        'data' => $card
                    ],200);
                }else{
                    return response()->json([
                        'message' => 'Error ocurred, try again'
                    ],400);
                }
    
            });

            // user/delete/{userId}
            $router->delete('delete/{userId}', 'API\Main\DeleteUserController@deleteAUser');
            Route::get('transactions', 'API\User\TransactionController@getUserTransactions');
        });

        //business
        Route::group(['prefix' => 'business'], function ($router) {
            $router->get('all', 'API\Business\BusinessController@showAll');
            $router->get('view/{id}', 'API\Business\BusinessController@showOne');
        });

        //Product service
        $router->group(['prefix'=>'service'], function($router){
            $router->get('get/{id}','API\Admin\ServiceController@getService');

            $router->post('/attribute-name/add/{service_id}','API\Main\AttributeController@addAttributeName');
            $router->get('/attribute-name/get/{service_id}','API\Main\AttributeController@getAttrNameByService');
            $router->delete('/attribute-name/delete/{id}','API\Main\AttributeController@deleteAttrName');

            $router->post('/attribute/add/{attribute_name_id}','API\Main\AttributeController@addAttribute');
            $router->get('/attribute/get/{attribute_name_id}','API\Main\AttributeController@getAttrByService');
            $router->delete('/attribute/delete/{id}','API\Main\AttributeController@deleteAttr');


            $router->delete('/delete/{id}', function($id){
                Service::where('id',$id)->delete();
    
                return response()->json([
                    'message' => 'service deleted'
                ],200);
            });
        });

        //Review
        $router->group(['prefix'=>'review'], function($router){
            $router->post('create/{id}','API\User\ReviewController@createReview');
           
            $router->get('rating/{id}','API\User\ReviewController@getRating');
           
            $router->get('reviews','API\User\ReviewController@getUserReview');
           
            $router->get('user/{id}/reviews','API\User\ReviewController@getReviewByUserId');
        });

        // Location
        $router->group(['prefix'=> 'location'], function($router){
            
            $router->post('create', 'API\Admin\LocationController@create');
        });

        // Payout
        $router->group(['prefix'=>'payout'], function($router){
            $router->get('/list','API\Vendor\PayoutController@list');
            $router->get('/list/{user_id}','API\Vendor\PayoutController@listByUser');
        });

    });

    Route::group(['middleware' => ['auth','is_admin_one']], function ($router) {

        $router->group([ 'prefix' => 'admin_one'], function ($router) {
            $router->get('vendor/accept/{id}', 'API\Admin\VendorController@acceptVendor');
            $router->get('vendor/reject/{id}', 'API\Admin\VendorController@rejectVendor');

            $router->post('discount/create', 'API\Admin\DiscountController@createDiscount');
            $router->get('discount/all','API\Admin\DiscountController@getDiscounts');
            $router->get('discount/active/all','API\Admin\DiscountController@getActiveDiscounts');
            $router->delete('discount/delete/{id}','API\Admin\DiscountController@deleteDiscount');
            $router->put('discount/edit/{id}','API\Admin\DiscountController@editDiscount');
            $router->put('discount/activate/{id}','API\Admin\DiscountController@activateDiscount');
            $router->put('discount/deactivate/{id}','API\Admin\DiscountController@deactivateDiscount');
            $router->get('all', 'API\Admin\AdminController@allAdmins');

            // $router->get('all', 'API\Admin\TransactionController@show');
        });

        //user
        Route::group(['prefix' => 'user'], function ($router) {
            $router->get('{id}/account/deactivate','API\User\UserController@deactivateAccount');
            $router->get('{id}/account/activate','API\User\UserController@activateAccount');
        });

        //Product service
        $router->group(['prefix'=>'service'], function($router){
            $router->post('create','API\Admin\ServiceController@addService');
            $router->post('/attribute-name/add/{service_id}','API\Main\AttributeController@addAttributeName');
            $router->get('/attribute-name/get/{service_id}','API\Main\AttributeController@getAttrNameByService');
            $router->delete('/attribute-name/delete/{id}','API\Main\AttributeController@deleteAttrName');
        });

        //Area
        $router->group(['prefix'=>'area'], function($router){
            $router->post('create','API\Admin\AreaController@addArea');
            $router->post('{id}/clean/add','API\Admin\AreaController@addCleanToArea');
            $router->get('get/{id}', 'API\Admin\AreaController@getArea');
            $router->get('all','API\Admin\AreaController@allAreas');
        });

        //extra
        $router->group(['prefix'=>'extra'], function($router){
            $router->post('create','API\Admin\ExtraController@addExtra');
            $router->get('get/{id}','API\Admin\ExtraController@getExtra');
            $router->get('/attribute/{name}/get','API\Main\BookingController@getAttrByAttrName');

            $router->delete('/delete/{id}', function($id){
                Extra::where('id',$id)->delete();
    
                return response()->json([
                    'message' => 'extra deleted'
                ],200);
            });
        });

        //Product Category
        $router->group(['prefix'=>'product-category'], function($router){

            $router->post('create','API\Admin\ProductCategoryController@createCategory');
    
            $router->get('/{product_id}/area/{id}/add','API\Admin\AreaController@addAreaToProduct');
    
            $router->delete('/{product_id}/area/{id}/remove','API\Admin\AreaController@removeAreaFromProduct');
    
            $router->delete('/{product_id}/extra/{id}/remove','API\Admin\ExtraController@removeExtraFromProduct');
    
            $router->delete('/{product_id}/service/{id}/remove','API\Admin\ServiceController@removeServiceFromProduct');
    
            $router->get('/{product_id}/service/{id}/add','API\Admin\ServiceController@addServiceToProduct');
    
            $router->get('/{product_id}/extra/{id}/add','API\Admin\ExtraController@addExtraToProduct');
    
            $router->get('get/{id}','API\Admin\ProductCategoryController@getCategory');
    
            $router->get('disable/{id}','API\Admin\ProductCategoryController@disableCategory');
    
            $router->post('/{product_id}/update/price','API\Admin\ProductCategoryController@updatePrice');
    
            $router->delete('/delete/{id}', function($id){
                ProductCategory::where('id',$id)->delete();
    
                return response()->json([
                    'message' => 'product deleted'
                ],200);
            });
        });

    });

});
