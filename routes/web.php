<?php

use App\Http\Controllers\allmigrationController;
use App\Http\Controllers\ConfirmOrderController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\IssuingbookupdateController;
use App\Http\Controllers\IssuingController;
use App\Http\Controllers\IssuingLoanController;
use App\Http\Controllers\ItemaddbillController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CancelorderController;
use App\Http\Controllers\ItemreceivedController;
use App\Http\Controllers\ItemreceivededitController;
use App\Http\Controllers\ModifyorderController;
use App\Http\Controllers\OrderaController;
use App\Http\Controllers\OrdermController;
use App\Http\Controllers\OrderPrintController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SubGroupController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::get('/home', function () {
        return view('pages.home');
    })->name('home');

    // Supplier Routes
    Route::get('/suplier', [SupplierController::class, 'showData'])->name('supplier.showData');
    Route::post('/suplier', [SupplierController::class, 'saveData'])->name('supplier.saveData');
    Route::get('/suplier/edit/{id}', [SupplierController::class, 'editData'])->name('supplier.editData');
    Route::post('/suplier/update/{id}', [SupplierController::class, 'updateData'])->name('supplier.updateData');
    Route::get('/suplier/{id}', [SupplierController::class, 'deleteData'])->name('supplier.deleteData');

    Route::get('/ajax/supliers', [SupplierController::class, 'ajaxSearch'])->name('ajax.supliers');

    // Customer Routes
    Route::get('/customer', [CustomerController::class, 'showData'])->name('customer.showData');
    Route::post('/customer', [CustomerController::class, 'saveData'])->name('customer.saveData');
    Route::get('/customer/edit/{id}', [CustomerController::class, 'editData'])->name('customer.editData');
    Route::post('/customer/update/{id}', [CustomerController::class, 'updateData'])->name('customer.updateData');
    Route::get('/customer/{id}', [CustomerController::class, 'deleteData'])->name('customer.deleteData');

    Route::get('/ajax/customers', [CustomerController::class, 'ajaxSearch'])->name('ajax.customers');

    // Product Routes
    // Route::get('/item', [ItemController::class, 'index'])->name('product.index');

    Route::get('/item', [ItemController::class, 'showData'])->name('item.showData');
    Route::post('/item', [ItemController::class, 'saveData'])->name('item.saveData');
    Route::get('/item/edit/{id}', [ItemController::class, 'editData'])->name('item.editData');
    Route::post('/item/update/{id}', [ItemController::class, 'updateData'])->name('item.updateData');
    Route::get('/item/{id}', [ItemController::class, 'deleteData'])->name('item.deleteData');
    Route::get('/ajax/items', [ItemController::class, 'ajaxSearch'])->name('ajax.items');
    Route::delete('/items/delete-all', [ItemController::class, 'deleteAll'])->name('items.deleteAll');
    Route::delete('/items/delete-all-ForeignKey', [ItemController::class, 'deleteAllForeignKey'])->name('items.deleteAllForeignKey');

    // Group Routes
    Route::get('/ajax/groups', [GroupController::class, 'ajaxSearch'])->name('ajax.groups');

    Route::post('/group', [GroupController::class, 'saveData'])->name('group.saveData');
    Route::get('/group', [GroupController::class, 'showData'])->name('group.showData');
    Route::get('/group/edit/{id}', [GroupController::class, 'editData'])->name('group.editData');
    Route::post('/group/update/{id}', [GroupController::class, 'updateData'])->name('group.updateData');
    Route::get('/group/{id}', [GroupController::class, 'deleteData'])->name('group.deleteData');

    // Sub Group Routes
    Route::get('/ajax/subgroups', [SubGroupController::class, 'ajaxSearch'])->name('ajax.subgroups');

    Route::post('/subgroup', [SubGroupController::class, 'saveData'])->name('subgroup.saveData');
    Route::get('/subgroup', [SubGroupController::class, 'showData'])->name('subgroup.showData');
    Route::get('/subgroup/edit/{id}', [SubGroupController::class, 'editData'])->name('subgroup.editData');
    Route::post('/subgroup/update/{id}', [SubGroupController::class, 'updateData'])->name('subgroup.updateData');
    Route::get('/subgroup/{id}', [SubGroupController::class, 'deleteData'])->name('subgroup.deleteData');

    // Orderm Routes
    Route::get('/orderm', [OrdermController::class, 'showData'])->name('orderm.showData');
    Route::post('/orderm', [OrdermController::class, 'tempsaveData'])->name('orderm.tempsaveData');
    Route::post('/orderm/update/{id}', [OrdermController::class, 'updateData'])->name('orderm.updateData');
    Route::get('/orderm/delete/{id}', [OrdermController::class, 'deleteData'])->name('orderm.deleteData');
    Route::post('/orderm/finish-order', [OrdermController::class, 'finishOrder'])->name('orderm.finishOrder');
    Route::post('/order/mark-pending', [OrdermController::class, 'markPending'])->name('orderm.markPending');
    Route::get('/orderprint/{order_id}', [OrdermController::class, 'printView'])->where('order_id', '.*')->name('orderm.orderprint');

    Route::post('/order/loadPendingOrder', [OrdermController::class, 'loadPendingOrder'])->name('orderm.loadPendingOrder');
    Route::post('/orderm/loadreorderlevelorder', [OrdermController::class, 'loadreorderlevelorder'])->name('orderm.loadreorderlevelorder');

    //Ordera Routes
    Route::get('/ordera', [OrderaController::class, 'showData'])->name('ordera.showData');

    Route::post('/ordera/process-book-code', [OrderaController::class, 'processBookCode'])->name('ordera.processBookCode');

    // Route::get('/ordera/delete/{order_id}', [OrderaController::class, 'alldeleteData'])->name('ordera.alldeleteData');

    // In web.php - Update the route
    Route::delete('/ordera/delete/{order_id}', [OrderaController::class, 'alldeleteData'])
        ->name('ordera.alldeleteData')
        ->where('order_id', '.*'); // This allows slashes in order_id

    Route::post('/ordera/update/{id}', [OrderaController::class, 'updateData'])->name('ordera.updateData');
    Route::get('/ordera/delete/{id}', [OrderaController::class, 'deleteData'])->name('ordera.deleteData');

    Route::post('/ordera/finish-order', [OrderaController::class, 'finishOrder'])->name('ordera.finishOrder');
    Route::get('/orderaprint/{order_id}', [OrderaController::class, 'printView'])
        ->where('order_id', '.*')
        ->name('ordera.orderprint');

    //    Route::get('/orderprint', [OrderPrintController::class, 'showData'])->name('orderprint.showData');

    // Route::get('/orderm/{order_id}', [OrdermController::class, 'showPendingOrder'])->name('orderm.showPendingOrder');

    Route::get('/confirmorder', [ConfirmOrderController::class, 'showData'])->name('confirmorder.showData');

    Route::post('/confirmorder/finish', [ConfirmOrderController::class, 'finish'])->name('confirmorder.finish');

    Route::get('/modifyorder', [ModifyorderController::class, 'showData'])->name('modifyorder.showData');
    Route::post('/modifyorder/update/{id}', [ModifyorderController::class, 'updateData'])->name('modifyorder.updateData');

    Route::get('/cancelorder', [CancelorderController::class, 'showData'])->name('cancelorder.showData');
    // Route::post('/cancelorder/update/{id}', [CancelorderController::class, 'updateData'])->name('cancelorder.updateData');

    Route::post('/cancelorder/update/{id}', [CancelorderController::class, 'updateData'])->name('cancelorder.updateData');

    Route::get('/itemreceived', [ItemreceivedController::class, 'showData'])->name('itemreceived.showData');
    Route::post('/itemreceived/update', [ItemreceivedController::class, 'update'])->name('itemreceived.update');
    Route::post('/itemreceived/cancelorder', [ItemreceivedController::class, 'cancelorder'])->name('itemreceived.cancelorder');

    Route::get('/itemreceivededit', [ItemreceivededitController::class, 'showData'])->name('itemreceivededit.showData');
    Route::post('/itemreceivededit/finish', [ItemreceivededitController::class, 'finish'])->name('itemreceivededit.finish');

    Route::get('/itemaddbill', [ItemaddbillController::class, 'showData'])->name('itemaddbill.showData');
    Route::post('/itemaddbill/finish', [ItemaddbillController::class, 'finish'])->name('itemaddbill.finish');

    Route::get('/issuing', [IssuingController::class, 'showData'])->name('issuing.showData');
    Route::post('/issuing', [IssuingController::class, 'tempsaveData'])->name('issuing.tempsaveData');
    Route::post('/issuing/finish', [IssuingController::class, 'finishOrder'])->name('issuing.finishOrder');
    Route::post('/issuing/loan', [IssuingController::class, 'markLoan'])->name('issuing.markLoan');
    Route::get('/issuing/delete/{id}', [IssuingController::class, 'deleteData'])->name('issuing.deleteData');

    Route::get('/issuingloan', [IssuingLoanController::class, 'showData'])->name('issuingloan.showData');
    Route::post('/issuingloan/update', [IssuingLoanController::class, 'update'])->name('issuingLoan.update');
    Route::get('/issuingloan/delete/{id}', [IssuingLoanController::class, 'delete'])->name('issuingLoan.delete');
    Route::post('/issuingloan/finish', [IssuingLoanController::class, 'finish'])->name('issuingLoan.finish');

    Route::get('/issuingbookupdate', [IssuingbookupdateController::class, 'showData'])->name('issuingbookupdate.showData');
    Route::post('/issuingbookupdate/finish', [IssuingbookupdateController::class, 'finish'])->name('issuingbookupdate.finish');

    Route::get('/allmigration', [allmigrationController::class, 'showData'])->name('allmigration.showData');
    Route::post('/migration/group/import', [allmigrationController::class, 'importGroupExcel'])->name('group.excel.import');
    Route::post('/migration/import', [allmigrationController::class, 'importGeneral'])->name('allmigration.import');


});
Route::get('/download-report', [ReportController::class, 'downloadReport']);
