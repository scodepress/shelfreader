<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/log', function() {

	Log::info('This is an info log');
	Log::warning('This is a warning log');
	Log::error('This is an error log');

});

Route::prefix('admin')->middleware(['auth','auth.isAdmin'])->name('admin')->group(function () { 
});
 
Auth::routes();

Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);

    $status = Password::sendResetLink(
        $request->only('email')
    );

    return $status === Password::RESET_LINK_SENT
                ? back()->with(['status' => __($status)])
                : back()->withErrors(['email' => __($status)]);
})->middleware(['guest'])->name('password.email');

Route::get('/reset-password/{token}', function ($token) {
    return view('auth.reset-password', ['token' => $token]);
})->middleware(['guest'])->name('password.reset');

Route::post('/reset-password', function (Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) use ($request) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->save();

            $user->setRememberToken(Str::random(60));

            event(new PasswordReset($user));
        }
    );

    return $status == Password::PASSWORD_RESET
                ? redirect()->route('login')->with('status', __($status))
                : back()->withErrors(['email' => __($status)]);
})->middleware(['guest'])->name('password.update');

Route::get('master_keys/export/', 'MasterKeyController@export');
Route::get('clear_masterkey_confirm/{library_id}', 'MasterKeyController@masterKeyConfirm')->name('clear_masterkey_confirm');
Route::post('clear_masterkey', 'MasterKeyController@truncate')->name('clear_masterkey');

Route::get('sort-file', 'SortFileController@index')->name('sort-file');
Route::post('store-sort-file', 'SortFileController@import')->name('store-sort-file');

Route::get('master_keys/export_shelf/', 'MasterKeyController@exportShelfList');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['middleware' => ['auth']], function() {

Route::get('/logout', '\App\Http\Controllers\Auth\LoginController@logout');
//Route::get('/logout', 'Auth\LoginController@logout')->name('logout' );
//Route::get('/login', 'Auth\LoginController@login')->name('login' );

Route::get('/shelfs/create', 'ShelfsController@create');
Route::get('/shelfs/show/{ibarcode}', 'ShelfsController@show');
Route::get('/shelfs/links', 'ShelfsController@shelf_links');

Route::post('/bookid', 'ShelfsController@showId');
Route::post('/hcurriculum', 'ShelfsController@highlight');
Route::post('/scan_in', 'ShelfsController@scan_in');

Route::get('/sorts/{barcode}', 'SortsController@show_table')->name('show_table');


Route::get('/sorts/show/{barcode}', 'SortsController@show');

Route::get('/references/show', 'ReferencesController@show');

Route::post('/bookid', 'ShelfsController@showId');

Route::post('/store_demo', 'SortsController@store');
Route::post('/clear_demo', 'SortsController@clear_demo');

Route::post('/store_sort', 'SortsController@store');

Route::get('/sorting/{item_alert?}/{barcode?}/{location?}/{title?}', 'SortsController@show')->name('sorting'); 
Route::post('/location_choice', 'SortsController@location_choice');

//Route::get('/item_alert/{barcode}/{location}/{title}', 'SortsController@item_alert')->name('item_alert'); 


Route::get('/rebound/{barcode}/{error}', 'SortsController@catch_red');
Route::post('/sorts/truncate', 'SortsController@truncate');

Route::get('/locator/{barcode}', 'SortsController@locator');

Route::post('/store_csv', 'AdminsController@store_csv');
Route::post('/dump_table', 'AdminsController@dump_table');



Route::post('/save_table', 'SortsController@save_table');

Route::get('/delete_book/{barcode}', 'SortsController@delete_book');
Route::get('/delete_full_book/{barcode}', 'FullShelvesController@delete_book');

Route::post('/book_drop', 'SortsController@book_drop');
Route::post('/book_full_drop', 'FullShelvesController@book_drop');

Route::get('/sorts/test_sort/{barcode}', 'SortsController@test_sort');


Route::post('/store_test_keys', 'SortsController@store_test_keys');
Route::post('/truncate_test', 'SortsController@truncate_test');
//Route::post('/store_dimensions', 'SortsController@store_dimensions');
Route::post('/store_shelf_size', 'SortsController@store_shelf_size');


Route::get('/search_show/{word}', 'SearchesController@show');
Route::post('/store_search', 'SearchesController@store');

Route::get('email', 'EmailsController@create');
Route::get('send_email/{title}/{content}', 'EmailsController@send');
Route::get('email_landing/{title}/{content}', 'EmailsController@landing');

Route::post('store_mail', 'EmailsController@store_mail');

Route::get('/admin/impersonate', 'Admin\ImpersonateController@index')->name('admin.impersonate');
Route::post('/admin/impersonate', 'Admin\ImpersonateController@store');
Route::get('/admin/impersonate/destroy', 'Admin\ImpersonateController@destroy');
Route::get('admin_show/{table_name}', 'AdminsController@show')->name('Admin Results');

Route::group(['prefix'=>'/admin'], function()
{
    Route::get('admin', 'AdminsController@create')->name('Admin Page');
   
    Route::get('/search', 'SearchesController@create')->name('Search');
    Route::get('/testkeys', 'SortsController@testkeys')->name('Test Call Numbers');
    Route::post('store_data', 'AdminsController@store')->name('Store Data');
    Route::post('sql_load', 'AdminsController@store_sql')->name('Store SQL');
    Route::post('delete_letters', 'AdminsController@delete_letters')->name('Delete Letters');
    Route::post('delete_duplicates', 'AdminsController@delete_duplicates')->name('Delete Duplicates');
    Route::post('analyze_table', 'AdminsController@analyze_table')->name('Analyze Table');
    Route::get('/flush_opcache', 'AdminsController@flush_opcache')->name('Flush Opcache');
    Route::get('/php_info', 'AdminsController@php_info')->name('PhpInfo');
    Route::get('/logs', 'AdminsController@php_info')->name('Logs');
    Route::get('/php_info', 'AdminsController@php_info')->name('PhpInfo');
    Route::get('/library_management', 'InstitutionsController@index')->name('Manage Libraries');
    Route::get('/show_user/{id}', 'InstitutionsController@showUser')->name('show_user');
    Route::post('/update_user', 'InstitutionsController@updateUser')->name('update_user');
    Route::get('/add_library_form', 'InstitutionsController@libraryForm')->name('add_library_form');
    Route::post('/add_library', 'InstitutionsController@storeLibrary')->name('store_library');
    Route::post('/store-file', 'InstitutionsController@storeFile')->name('store-file');
    Route::get('/upload-file-form', 'InstitutionsController@uploadFileForm')->name('upload-file-form');
    Route::post('/admin-inventory-export', 'MasterKeyController@adminExport')->name('admin-inventory-export');
    Route::get('Logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index')->name('Logs');
});

Route::get('/menu_admin', 'MenusController@admin_menu')->name('menu_admin');
Route::post('/menu_admin', 'MenusController@store_menu');


Route::get('/gulp_show', 'GulpTestcontroller@postRequest');
Route::post('/demo_test', 'FullShelvesController@demo_test');

Route::post('/add_shelf', 'FullShelvesController@add_shelf');
Route::post('/add_section', 'FullShelvesController@add_section');

Route::get('/scroll', 'SortsController@scroll')->name('scroll');

Route::get('/fullshelf/show/{item_alert?}/{barcode?}/{location?}/{title?}', 
	'FullShelvesController@show')->name('fullshow');

Route::post('/fullshelf/store', 'FullShelvesController@store');

Route::post('/demo_test', 'FullShelvesController@store');

Route::post('/ftruncate', 'FullShelvesController@ftruncate');
Route::post('/fstruncate', 'FullShelvesController@fstruncate');
Route::post('/set_width', 'SortsController@set_width');

Route::post('/new_shelf', 'FullShelvesController@new_shelf');
Route::post('/new_section', 'FullShelvesController@new_section');


Route::get('/store_corrections/{user_id}', 'FullShelvesController@corrections');
Route::get('/test_order/{callno}', 'SortsController@test_order');

Route::get('/locations_home', 'LocationsController@index')->name('locator_home');
Route::get('/locations/show/{barcode?}/{position?}', 'LocationsController@show')->name('locator');

Route::get('/prep/{barcode}', 'LocationsController@prep')->name('prep');

Route::post('/location/store', 'LocationsController@store');

Route::get('/myreports', 'ReportsController@show');
Route::get('item_alerts/{alert}/{title}', 'SortsController@item_alerts');
Route::post('/test', 'SortsController@test');
Route::get('/item_alert', 'ItemAlertsController@show');

Route::post('/receive-barcode', 'FullShelvesController@receiveBarcode');
Route::get('/stack_view/{barcode?}/{pst?}', 'FullShelvesController@stackBase')->name('stack-view');
//Route::get('/stack_error/{base}/{barcode}', 'FullShelvesController@stackErrors')->name('stack-error');


});


Route::get('/tutorial', 'SortsController@tutorial');
Route::get('/video', 'SortsController@video');
Route::get('/faq', 'SortsController@faq');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/diagnostics/{user_id?}/{date?}', [App\Http\Controllers\DiagnosticsController::class, 'show'])->name('diagnostics');
