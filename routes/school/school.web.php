<?php

use App\Http\Controllers\School\AttendanceController;
use App\Http\Controllers\School\ClassController;
use App\Http\Controllers\School\ComplaintController;
use App\Http\Controllers\School\ExamController;
use App\Http\Controllers\School\FeeManagementController;
use App\Http\Controllers\School\InventoryController;
use App\Http\Controllers\School\MeetingController;
use App\Http\Controllers\School\PayrollController;
use App\Http\Controllers\School\ReportController;
use App\Http\Controllers\School\SchoolDashboardController;
use App\Http\Controllers\School\SchoolStudentController;
use App\Http\Controllers\School\StaffController;
use App\Http\Controllers\School\SubjectController;
use App\Http\Controllers\School\TeacherController;
use Illuminate\Support\Facades\Route;

Route::prefix('{school}')
    ->middleware(['identify.school', 'auth'])
    ->name('school.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [SchoolDashboardController::class, 'index'])->name('dashboard');

        // =================== CLASSES & SECTIONS ===================
        Route::prefix('classes')->name('classes.')->group(function () {
            Route::get('/', [ClassController::class, 'index'])->name('index');
            Route::post('/', [ClassController::class, 'store'])->name('store');
            Route::put('/{class}', [ClassController::class, 'update'])->name('update');
            Route::delete('/{class}', [ClassController::class, 'destroy'])->name('destroy');
            Route::get('/{class}/sections', [ClassController::class, 'sections'])->name('sections');
            Route::post('/{class}/sections', [ClassController::class, 'storeSection'])->name('sections.store');
            Route::delete('/sections/{section}', [ClassController::class, 'destroySection'])->name('sections.destroy');
        });

        // =================== SUBJECTS ===================
        Route::prefix('subjects')->name('subjects.')->group(function () {
            Route::get('/', [SubjectController::class, 'index'])->name('index');
            Route::post('/', [SubjectController::class, 'store'])->name('store');
            // Static routes before dynamic
            Route::get('/assignments', [SubjectController::class, 'assignments'])->name('assignments');
            Route::post('/assign', [SubjectController::class, 'assign'])->name('assign');
            Route::get('/by-class', [SubjectController::class, 'byClass'])->name('by-class'); // AJAX
            Route::delete('/{subject}', [SubjectController::class, 'destroy'])->name('destroy');
        });

        // =================== STUDENTS ===================
        Route::prefix('students')->name('students.')->group(function () {
            Route::get('/', [SchoolStudentController::class, 'index'])->name('index');
            Route::get('/create', [SchoolStudentController::class, 'create'])->name('create');
            Route::post('/', [SchoolStudentController::class, 'store'])->name('store');
            Route::get('/{student}', [SchoolStudentController::class, 'show'])->name('show');
            Route::get('/{student}/edit', [SchoolStudentController::class, 'edit'])->name('edit');
            Route::put('/{student}', [SchoolStudentController::class, 'update'])->name('update');
            Route::delete('/{student}', [SchoolStudentController::class, 'destroy'])->name('destroy');
            Route::post('/{student}/toggle-status', [SchoolStudentController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('/{student}/promote', [SchoolStudentController::class, 'promote'])->name('promote');
            Route::post('/{student}/set-monitor', [SchoolStudentController::class, 'setClassMonitor'])->name('set-monitor');
        });

        // =================== TEACHERS ===================
        Route::prefix('teachers')->name('teachers.')->group(function () {
            Route::get('/', [TeacherController::class, 'index'])->name('index');
            Route::get('/create', [TeacherController::class, 'create'])->name('create');
            Route::post('/', [TeacherController::class, 'store'])->name('store');
            Route::get('/{teacher}/edit', [TeacherController::class, 'edit'])->name('edit');
            Route::put('/{teacher}', [TeacherController::class, 'update'])->name('update');
            Route::delete('/{teacher}', [TeacherController::class, 'destroy'])->name('destroy');
            Route::post('/{teacher}/toggle-status', [TeacherController::class, 'toggleStatus'])->name('toggle-status');
        });

        // =================== ATTENDANCE ===================
        Route::prefix('attendance')->name('attendance.')->group(function () {
            Route::get('/teachers', [AttendanceController::class, 'teacherIndex'])->name('teachers');
            Route::post('/teachers/check-in', [AttendanceController::class, 'teacherCheckIn'])->name('teachers.check-in');
            Route::post('/teachers/check-out', [AttendanceController::class, 'teacherCheckOut'])->name('teachers.check-out');
            Route::post('/teachers/absent', [AttendanceController::class, 'teacherMarkAbsent'])->name('teachers.absent');
            Route::get('/students', [AttendanceController::class, 'studentIndex'])->name('students');
            Route::post('/students', [AttendanceController::class, 'studentMark'])->name('students.mark');
        });

        // =================== PAYROLL ===================
        Route::prefix('payroll')->name('payroll.')->group(function () {
            Route::get('/', [PayrollController::class, 'index'])->name('index');
            Route::post('/generate', [PayrollController::class, 'generate'])->name('generate');
            Route::get('/{payroll}', [PayrollController::class, 'show'])->name('show');
        });

        // =================== STAFF ===================
        Route::prefix('staff')->name('staff.')->group(function () {
            Route::get('/', [StaffController::class, 'index'])->name('index');
            Route::get('/create', [StaffController::class, 'create'])->name('create');
            Route::post('/', [StaffController::class, 'store'])->name('store');
            // Static routes BEFORE dynamic {staff} routes
            Route::get('/salaries', [StaffController::class, 'salaries'])->name('salaries');
            Route::post('/salaries/generate', [StaffController::class, 'generateSalary'])->name('salaries.generate');
            Route::post('/salaries/{salary}/paid', [StaffController::class, 'markSalaryPaid'])->name('salaries.paid');
            // Dynamic routes last
            Route::get('/{staff}/edit', [StaffController::class, 'edit'])->name('edit');
            Route::put('/{staff}', [StaffController::class, 'update'])->name('update');
            Route::delete('/{staff}', [StaffController::class, 'destroy'])->name('destroy');
        });

        // =================== FEES ===================
        Route::prefix('fees')->name('fees.')->group(function () {
            Route::get('/types', [FeeManagementController::class, 'feeTypes'])->name('types');
            Route::post('/types', [FeeManagementController::class, 'storeFeeType'])->name('types.store');
            Route::get('/structures', [FeeManagementController::class, 'feeStructures'])->name('structures');
            Route::post('/structures', [FeeManagementController::class, 'storeFeeStructure'])->name('structures.store');
            Route::delete('/structures/{feeStructure}', [FeeManagementController::class, 'destroyFeeStructure'])->name('structures.destroy');
            Route::get('/vouchers', [FeeManagementController::class, 'vouchers'])->name('vouchers');
            Route::post('/vouchers/generate', [FeeManagementController::class, 'generateVouchers'])->name('vouchers.generate');
            Route::post('/vouchers/send', [FeeManagementController::class, 'sendVouchers'])->name('vouchers.send');
            Route::get('/vouchers/{feeVoucher}', [FeeManagementController::class, 'showVoucher'])->name('vouchers.show');
            Route::get('/payments', [FeeManagementController::class, 'payments'])->name('payments');
            Route::post('/payments', [FeeManagementController::class, 'collectPayment'])->name('payments.store');
        });

        // =================== EXAMS ===================
        Route::prefix('exams')->name('exams.')->group(function () {
            Route::get('/', [ExamController::class, 'index'])->name('index');
            Route::get('/create', [ExamController::class, 'create'])->name('create');
            Route::post('/', [ExamController::class, 'store'])->name('store');
            Route::get('/{exam}/schedules', [ExamController::class, 'schedules'])->name('schedules');
            Route::post('/{exam}/schedules', [ExamController::class, 'addSchedule'])->name('schedules.store');
            Route::get('/schedules/{schedule}/marks', [ExamController::class, 'enterMarks'])->name('marks');
            Route::post('/schedules/{schedule}/marks', [ExamController::class, 'saveMarks'])->name('marks.save');
            Route::post('/schedules/{schedule}/publish', [ExamController::class, 'publishMarks'])->name('marks.publish');
        });

        // =================== COMPLAINTS ===================
        Route::prefix('complaints')->name('complaints.')->group(function () {
            Route::get('/', [ComplaintController::class, 'index'])->name('index');
            Route::post('/', [ComplaintController::class, 'store'])->name('store');
            Route::post('/{complaint}/resolve', [ComplaintController::class, 'resolve'])->name('resolve');
            Route::post('/{complaint}/status', [ComplaintController::class, 'updateStatus'])->name('status');
        });

        // =================== MEETINGS ===================
        Route::prefix('meetings')->name('meetings.')->group(function () {
            Route::get('/', [MeetingController::class, 'index'])->name('index');
            Route::get('/create', [MeetingController::class, 'create'])->name('create');
            Route::post('/', [MeetingController::class, 'store'])->name('store');
            Route::post('/{meeting}/status', [MeetingController::class, 'updateStatus'])->name('status');
        });

        // =================== INVENTORY ===================
        Route::prefix('inventory')->name('inventory.')->group(function () {
            Route::get('/', [InventoryController::class, 'index'])->name('index');
            Route::post('/', [InventoryController::class, 'store'])->name('store');
            Route::post('/transaction', [InventoryController::class, 'transaction'])->name('transaction');
            Route::get('/categories', [InventoryController::class, 'categories'])->name('categories');
            Route::post('/categories', [InventoryController::class, 'storeCategory'])->name('categories.store');
        });

        // =================== QUESTION BANK & PAPER GENERATION ===================
        Route::prefix('question-bank')->name('question-bank.')->group(function () {
            Route::get('/',                    [\App\Http\Controllers\School\QuestionBankController::class, 'index'])->name('index');
            Route::get('/create',              [\App\Http\Controllers\School\QuestionBankController::class, 'create'])->name('create');
            Route::post('/process-image',      [\App\Http\Controllers\School\QuestionBankController::class, 'processImage'])->name('process-image');
            Route::get('/review',              [\App\Http\Controllers\School\QuestionBankController::class, 'review'])->name('review');
            Route::post('/save-questions',     [\App\Http\Controllers\School\QuestionBankController::class, 'saveQuestions'])->name('save-questions');
            Route::get('/{questionBank}',      [\App\Http\Controllers\School\QuestionBankController::class, 'show'])->name('show');
            Route::delete('/{questionBank}',   [\App\Http\Controllers\School\QuestionBankController::class, 'destroy'])->name('destroy');
            // Papers
            Route::get('/papers/list',         [\App\Http\Controllers\School\QuestionBankController::class, 'paperIndex'])->name('papers');
            Route::get('/papers/generate',     [\App\Http\Controllers\School\QuestionBankController::class, 'generateForm'])->name('generate');
            Route::post('/papers/preview',     [\App\Http\Controllers\School\QuestionBankController::class, 'previewPaper'])->name('preview');
            Route::post('/papers/save',        [\App\Http\Controllers\School\QuestionBankController::class, 'savePaper'])->name('save-paper');
            Route::get('/papers/{paper}/print',[\App\Http\Controllers\School\QuestionBankController::class, 'printPaper'])->name('paper.print');
        });
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/income', [ReportController::class, 'income'])->name('income');
            Route::get('/expense', [ReportController::class, 'expense'])->name('expense');
            Route::get('/income-vs-expense', [ReportController::class, 'incomeVsExpense'])->name('income-vs-expense');
            Route::get('/payroll', [ReportController::class, 'payroll'])->name('payroll');
        });
    });
