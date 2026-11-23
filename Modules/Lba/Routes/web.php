<?php

use Illuminate\Support\Facades\Route;
use Modules\Lba\Http\Controllers\LbaAssessmentController;
use Modules\Lba\Http\Controllers\LbaExaminationController;
use Modules\Lba\Http\Controllers\LbaImageController;
use Modules\Lba\Http\Controllers\LbaReportVersionController;
use Modules\Lba\Http\Controllers\LbaReviewController;

/*
|--------------------------------------------------------------------------
| Routes modul Lba
|--------------------------------------------------------------------------
| Route didaftarkan sendiri oleh modul (bukan generator menu di
| AppServiceProvider) sehingga modul bersifat mandiri. Nama route tetap
| mengikuti konvensi inti: admin.<key>.*
*/

Route::middleware(['web', 'auth', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        $examination = LbaExaminationController::class;
        Route::get('lba_examinations', [$examination, 'index'])->name('lba_examinations.index');
        Route::get('lba_examinations/data', [$examination, 'data'])->name('lba_examinations.data');
        Route::get('lba_examinations/create', [$examination, 'create'])->name('lba_examinations.create');
        Route::post('lba_examinations', [$examination, 'store'])->name('lba_examinations.store');
        Route::get('lba_examinations/{slug}/edit', [$examination, 'edit'])->name('lba_examinations.edit');
        Route::put('lba_examinations/{slug}', [$examination, 'update'])->name('lba_examinations.update');
        Route::delete('lba_examinations/{slug}', [$examination, 'destroy'])->name('lba_examinations.destroy');

        $assessment = LbaAssessmentController::class;
        Route::get('lba_assessments', [$assessment, 'index'])->name('lba_assessments.index');
        Route::get('lba_assessments/data', [$assessment, 'data'])->name('lba_assessments.data');
        Route::get('lba_assessments/create', [$assessment, 'create'])->name('lba_assessments.create');
        Route::post('lba_assessments', [$assessment, 'store'])->name('lba_assessments.store');
        Route::get('lba_assessments/{slug}/edit', [$assessment, 'edit'])->name('lba_assessments.edit');
        Route::put('lba_assessments/{slug}', [$assessment, 'update'])->name('lba_assessments.update');
        Route::delete('lba_assessments/{slug}', [$assessment, 'destroy'])->name('lba_assessments.destroy');

        $image = LbaImageController::class;
        Route::get('lba_images', [$image, 'index'])->name('lba_images.index');
        Route::get('lba_images/data', [$image, 'data'])->name('lba_images.data');
        Route::get('lba_images/create', [$image, 'create'])->name('lba_images.create');
        Route::post('lba_images', [$image, 'store'])->name('lba_images.store');
        Route::get('lba_images/{slug}/edit', [$image, 'edit'])->name('lba_images.edit');
        Route::put('lba_images/{slug}', [$image, 'update'])->name('lba_images.update');
        Route::delete('lba_images/{slug}', [$image, 'destroy'])->name('lba_images.destroy');

        $reportVersion = LbaReportVersionController::class;
        Route::get('lba_report_versions', [$reportVersion, 'index'])->name('lba_report_versions.index');
        Route::get('lba_report_versions/data', [$reportVersion, 'data'])->name('lba_report_versions.data');
        Route::get('lba_report_versions/create', [$reportVersion, 'create'])->name('lba_report_versions.create');
        Route::post('lba_report_versions', [$reportVersion, 'store'])->name('lba_report_versions.store');
        Route::get('lba_report_versions/{slug}/edit', [$reportVersion, 'edit'])->name('lba_report_versions.edit');
        Route::put('lba_report_versions/{slug}', [$reportVersion, 'update'])->name('lba_report_versions.update');
        Route::delete('lba_report_versions/{slug}', [$reportVersion, 'destroy'])->name('lba_report_versions.destroy');

        $review = LbaReviewController::class;
        Route::get('lba_reviews', [$review, 'index'])->name('lba_reviews.index');
        Route::get('lba_reviews/data', [$review, 'data'])->name('lba_reviews.data');
        Route::get('lba_reviews/create', [$review, 'create'])->name('lba_reviews.create');
        Route::post('lba_reviews', [$review, 'store'])->name('lba_reviews.store');
        Route::get('lba_reviews/{slug}/edit', [$review, 'edit'])->name('lba_reviews.edit');
        Route::put('lba_reviews/{slug}', [$review, 'update'])->name('lba_reviews.update');
        Route::delete('lba_reviews/{slug}', [$review, 'destroy'])->name('lba_reviews.destroy');
    });
