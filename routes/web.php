<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\AdminController;

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

Route::get('/', [TopicController::class, 'index']);
Route::get('/topics/new', [TopicController::class, 'showCreate']);
Route::get('/topics/{tag}', [TopicController::class, 'paginator']);
Route::get('/topics/{tag}/{page}', [TopicController::class, 'paginator']);
Route::get('/topic/{id}', [TopicController::class, 'show']);
Route::get('/search', [TopicController::class, 'search']);


Route::post("/topic/like", [TopicController::class, "like"]);
Route::post("/topic", [TopicController::class, "store"]);
Route::post("/createtopic", [TopicController::class, "newStore"]);

Route::get('/admin', [AdminController::class, 'index'])->middleware('auth');
Route::get('/admin/users', [AdminController::class, 'usersindex'])->middleware('auth');
Route::get('/admin/users/search', [AdminController::class, 'search'])->middleware('auth');
Route::get('/admin/users/new', [AdminController::class, 'view'])->middleware('auth');
Route::post('/admin/users/create', [AdminController::class, 'store'])->middleware('auth');
Route::post('/admin/users/delete', [AdminController::class, 'delete'])->middleware('auth');

Route::get('/admin/topics', [AdminController::class, 'topics'])->middleware('auth');
Route::post('/admin/topics/delete', [AdminController::class, 'deleteTopic'])->middleware('auth');
Route::post('/admin/topics/hide', [AdminController::class, 'hideTopic'])->middleware('auth');
Route::post('/admin/topics/sticky', [AdminController::class, 'stickyTopic'])->middleware('auth');
Route::get('/admin/topics/search', [AdminController::class, 'searchTopics'])->middleware('auth');


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/logout', [App\Http\Controllers\HomeController::class, 'logout'])->middleware('auth');
Route::post("/upload", [App\Http\Controllers\HomeController::class, 'store'])->middleware('auth');