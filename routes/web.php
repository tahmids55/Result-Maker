<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\MarksheetController;
use App\Http\Controllers\MarksheetTemplateController;
use App\Http\Controllers\OcrController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use Illuminate\Support\Facades\Route;

// ── Authentication ──────────────────────────────────────────────────────────
Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login')->middleware('prevent-back-history');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register')->middleware('prevent-back-history');
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ── Landing Page ────────────────────────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// ── Authenticated Routes ────────────────────────────────────────────────────
// ONLYOFFICE Webhooks & Plugin (Must be outside auth/CSRF for Docker container access)
Route::get('onlyoffice/download/{id}/{token}', [\App\Http\Controllers\EditorController::class, 'download'])->name('onlyoffice.download');
Route::post('onlyoffice/callback', [\App\Http\Controllers\EditorController::class, 'callback'])->name('onlyoffice.callback')->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
Route::get('onlyoffice/placeholders', [\App\Http\Controllers\EditorController::class, 'placeholders'])->name('onlyoffice.placeholders');
Route::get('onlyoffice-plugin/panel/config.json', [\App\Http\Controllers\PluginController::class, 'config'])->name('onlyoffice.config');
Route::get('onlyoffice-plugin/panel/index.html', [\App\Http\Controllers\PluginController::class, 'index'])->withoutMiddleware([\Illuminate\Http\Middleware\FrameGuard::class])->name('onlyoffice.index');
Route::get('onlyoffice-plugin/panel/icon.png', fn() => response()->file(public_path('onlyoffice-plugin/icon.png'), ['Access-Control-Allow-Origin' => '*']));
Route::get('onlyoffice-plugin/panel/icon@2x.png', fn() => response()->file(public_path('onlyoffice-plugin/icon@2x.png'), ['Access-Control-Allow-Origin' => '*']));

Route::middleware(['auth', 'prevent-back-history'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Classes
    Route::resource('classes', ClassController::class)->except(['show']);
    Route::get('classes/{class}/details', [ClassController::class, 'show'])->name('classes.show');

    // Sections
    Route::resource('sections', SectionController::class);
    Route::post('sections/bulk-import', [SectionController::class, 'bulkImport'])->name('sections.bulk-import');

    // Students
    Route::resource('students', StudentController::class);
    Route::post('students/bulk-import', [StudentController::class, 'bulkImport'])->name('students.bulk-import');
    Route::post('students/bulk-delete', [StudentController::class, 'bulkDelete'])->name('students.bulk-delete');
    Route::get('api/sections-by-class', [StudentController::class, 'getSectionsByClass'])->name('api.sections-by-class');

    // Subjects
    Route::resource('subjects', SubjectController::class);
    Route::post('subjects/{subject}/copy', [SubjectController::class, 'copy'])->name('subjects.copy');
    Route::post('subjects/copy-to-class', [SubjectController::class, 'copyToClass'])->name('subjects.copy-to-class');

    // Exams
    Route::resource('exams', ExamController::class);
    Route::post('exams/{exam}/toggle-active', [ExamController::class, 'toggleActive'])->name('exams.toggle-active');

    // Marks Entry (Livewire)
    Route::get('marks', fn() => view('marks.index'))->name('marks.index');

    // Results
    Route::get('results', [ResultController::class, 'index'])->name('results.index');
    Route::post('results/class', [ResultController::class, 'classResult'])->name('results.class');
    Route::post('results/recalculate', [ResultController::class, 'recalculate'])->name('results.recalculate');
    Route::post('results/merit', [ResultController::class, 'meritList'])->name('results.merit');
    Route::post('results/export', [ResultController::class, 'exportExcel'])->name('results.export');
    Route::get('results/{student}/{exam}', [ResultController::class, 'studentResult'])->name('results.student');

    // Marksheet Templates
    Route::get('templates', [MarksheetTemplateController::class, 'index'])->name('templates.index');
    Route::get('templates/create', [MarksheetTemplateController::class, 'create'])->name('templates.create');
    Route::post('templates', [MarksheetTemplateController::class, 'store'])->name('templates.store');
    Route::get('templates/{template}/map', [MarksheetTemplateController::class, 'showMapping'])->name('templates.map');
    Route::post('templates/{template}/map', [MarksheetTemplateController::class, 'saveMapping'])->name('templates.save-map');
    Route::post('templates/{template}/set-default', [MarksheetTemplateController::class, 'setDefault'])->name('templates.set-default');
    Route::put('templates/{template}', [MarksheetTemplateController::class, 'update'])->name('templates.update');
    Route::delete('templates/{template}', [MarksheetTemplateController::class, 'destroy'])->name('templates.destroy');
    Route::get('editor/{id}', [\App\Http\Controllers\EditorController::class, 'edit'])->name('onlyoffice.edit');

    // Marksheet Generation
    Route::get('marksheets', [MarksheetController::class, 'index'])->name('marksheets.index');
    Route::post('marksheets/generate', [MarksheetController::class, 'generate'])->name('marksheets.generate');
    Route::post('marksheets/generate-sync', [MarksheetController::class, 'generateSync'])->name('marksheets.generate-sync');
    Route::get('marksheets/batch-progress', [MarksheetController::class, 'batchProgress'])->name('marksheets.batch-progress');
    Route::get('marksheets/batch-dismiss', [MarksheetController::class, 'batchDismiss'])->name('marksheets.batch-dismiss');
    Route::get('marksheets/download/{marksheet}', [MarksheetController::class, 'download'])->name('marksheets.download');
    Route::get('marksheets/download-zip', [MarksheetController::class, 'downloadZip'])->name('marksheets.download-zip');
    Route::get('marksheets/history', [MarksheetController::class, 'history'])->name('marksheets.history');
    Route::post('marksheets/bulk-delete', [MarksheetController::class, 'bulkDelete'])->name('marksheets.bulk-delete');

    // OCR
    Route::get('ocr', [OcrController::class, 'index'])->name('ocr.index');
    Route::post('ocr/upload', [OcrController::class, 'upload'])->name('ocr.upload');
    Route::post('ocr/bulk-upload', [OcrController::class, 'bulkUpload'])->name('ocr.bulk-upload');
    Route::get('ocr/{import}', [OcrController::class, 'show'])->name('ocr.show');
    Route::post('ocr/{import}/save-marks', [OcrController::class, 'saveMarks'])->name('ocr.save-marks');

    // SMS / WhatsApp
    Route::get('sms', [SmsController::class, 'index'])->name('sms.index');
    Route::post('sms/send-bulk', [SmsController::class, 'sendBulk'])->name('sms.send-bulk');
    Route::post('sms/send-single', [SmsController::class, 'sendSingle'])->name('sms.send-single');
    Route::get('sms/logs', [SmsController::class, 'logs'])->name('sms.logs');

    // Settings
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings/school', [SettingsController::class, 'updateSchool'])->name('settings.school');
    Route::post('settings/general', [SettingsController::class, 'updateGeneral'])->name('settings.general');
    Route::post('settings/grades', [SettingsController::class, 'updateGrades'])->name('settings.grades');
    Route::get('settings/backup', [SettingsController::class, 'backup'])->name('settings.backup');
    Route::post('settings/restore', [SettingsController::class, 'restore'])->name('settings.restore');
});
