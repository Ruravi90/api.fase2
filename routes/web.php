<?php
use fase2\Events\ActionTasks;
use fase2\User;
use fase2\Task;
use fase2\Http\Controllers\RolController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
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


Auth::routes();

Route::group(['middleware' => 'guest'], function () {
    /*Mailables*/
    Route::get('/mailable/new_user', function() {
        $user = new User;
        return new fase2\Mail\NewUser($user);
    });

    Route::get('/mailable/job_cron', function() {
        Mail::to("eaguilar.arrezola@gmail.com")->send(new fase2\Mail\ExecJobCron(Carbon::now()));
        return new fase2\Mail\ExecJobCron();
    });
    //Mail::to("eaguilar.arrezola@gmail.com")->send(new ExecJobCron(Carbon::now()));

    Route::get('/docs', function(){
        return View::make('docs.api.index');
    });

    Route::get('/clear-cache', function() {
        $exitCode = Artisan::call('config:clear');
        $exitCode = Artisan::call('cache:clear');
        $exitCode = Artisan::call('config:cache');
        return 'DONE'; //Return anything
    });

    Route::get('/logout', function(Request $request) {
        Auth::logout();
        Session::flush();
        
        $exitCode = Artisan::call('config:clear');
        $exitCode = Artisan::call('cache:clear');
        $exitCode = Artisan::call('config:cache');

        $data = Array ( '_token' => 'FZAfP7NmTIYfBa9gg39ZdBN2vXXqcvYmIuGM8VN8', 'email' => 'demo1006@gmail.com', 'password' => '123456' ); 
        Session::push('user', $data);  
        Session::regenerate(true);
        
        return 'DONE'; //Return anything
    });
});
