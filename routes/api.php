<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\PasswordController;

use Illuminate\Foundation\EmailVerificationRequest;
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


Route::post('/user-login', [UserController::class, 'Login'])->name('login.post'); 
Route::post('/user-registration',[UserController::class, 'Registration'])->name('register.post'); 
Route::get('/account/verify/{token}', [UserController::class, 'verifyAccount'])->name('user.verify'); 
Route::get('/profile', [UserController::class,'profile'])->name('profile');
Route::post('update_profile',[UerController::class,'updateProfile']);

Route::post('/forget-password', [PasswordController::class, 'ForgetPassword'])->name('forget.password.post'); 
Route::post('/reset-password', [PasswordController::class, 'ResetPassword'])->name('reset.password.post');

Route::post('/uploadImage',[ImageController::class,'uploadImage']);
Route::get('/viewImage',[ImageController::class,'viewImage']);
Route::delete('/deleteImage/{id}',[ImageController::class,'deleteImage']);
Route::post('searchImage',[ImageController::class,'searchImage']);
Route::post('/statusUpdate', [ImageController::class,'statusUpdate']);
Route::get('/getShareableLink', [ImageController::class,'getShareableLink']);
Route::get('/imageview/{id}', [ImageController::class,'imageview'])->name('imageview.image');





