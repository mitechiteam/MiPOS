<?php

//home page

use App\Http\Controllers\API\AdjustProductStockController;
use App\Http\Controllers\API\BranchController;
use App\Http\Controllers\API\CashRegisterController;
use App\Http\Controllers\API\ContactController;
use App\Http\Controllers\API\CornJobLogController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\CustomerGroupController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\Api\EmailTemplateController;
use App\Http\Controllers\API\InviteController;
use App\Http\Controllers\API\InvoiceTemplateController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\ProductApiController;
use App\Http\Controllers\API\ProductAttributesController;
use App\Http\Controllers\API\ProductBrandsController;
use App\Http\Controllers\API\ProductCategoriesController;
use App\Http\Controllers\API\ProductGroupsController;
use App\Http\Controllers\API\ProductsController;
use App\Http\Controllers\API\ProductUnitsController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\RegisterLogController;
use App\Http\Controllers\API\ReportController;
use App\Http\Controllers\API\RestaurantTableController;
use App\Http\Controllers\API\RoleAssignController;
use App\Http\Controllers\api\RoleController;
use App\Http\Controllers\API\Sales\SaleApiController;
use App\Http\Controllers\API\SalesController;
use App\Http\Controllers\API\SalesShipmentController;
use App\Http\Controllers\API\SettingController;
use App\Http\Controllers\API\ShippingAreaController;
use App\Http\Controllers\API\SmsTemplateController;
use App\Http\Controllers\API\SupplierController;
use App\Http\Controllers\API\TaxController;
use App\Http\Controllers\API\TodoListController;
use App\Http\Controllers\API\UpdateController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InstallDemoDataController;
use App\Http\Controllers\Setup\EnvironmentController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;



Route::get('downloadSample', [ProductsController::class, 'downloadProductSample']);

// Auth Route

Route::get('/', [LoginController::class, 'showLoginForm'])
    ->name('login');
Route::get('/login', [LoginController::class, 'showLoginForm']);

Route::post('/', [LoginController::class, 'login']);
Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm']);

Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm']);


Route::post('/recover', [AuthController::class, 'recover']);
Route::post('/password/reset/{token}', [ResetPasswordController::class, 'reset']);
Route::get('/verify/{token}', [RegisterController::class, 'verifyUser']);
Route::get('accept/{token}', [InviteController::class, 'accept']);
Route::get('register/{token}', [RegisterController::class, 'regForm']);
Route::post('register/{token}', [InviteController::class, 'invitedRegistration']);
// for corn job
Route::get('/corn-job', [EmailTemplateController::class, 'callCornJob']);

// install demo data
Route::any('install-demo-data', [InstallDemoDataController::class, 'run'])
    ->name('install-demo-data');


// Auth middleware group Route
Route::group(['middleware' => 'auth'], function () {

    //logout route
    Route::get('/logout', [LoginController::class, 'logout']);

    //dashboard routes starts
    Route::get('/getAllDashboardData', [DashboardController::class, 'getAllData']);

    // dashboard controller routes ends
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboardgetdata', [DashboardController::class, 'getData']);

    // Profile Route

    Route::get('myprofile', [ProfileController::class, 'myindex']);

    Route::get('user-profile', [ProfileController::class, 'index']);

    Route::post('profile/{id}', [ProfileController::class, 'update']);

    Route::post('/updatePassword/{id}', [ProfileController::class, 'updatepassword']);

    Route::get('getDateFormat', [ProfileController::class, 'dateFormat']);

    // Email template Route
    Route::post('templatelist', [EmailTemplateController::class, 'index']);
    Route::get('/gettemplatecontent/{id}', [EmailTemplateController::class, 'show'])->middleware('permissions:can_edit_email_template');
    Route::post('/setcustomcontent/{id}', [EmailTemplateController::class, 'update'])->middleware('permissions:can_edit_email_template');
    Route::get('/knowDefaultRowSettings', [SettingController::class, 'knowDefaultRowSettings']);

    //Sms template Route
    Route::post('smstemplatelist', [SmsTemplateController::class, 'index']);
    Route::get('/getsmstemplatecontent/{id}', [SmsTemplateController::class, 'show'])->middleware('permissions:can_edit_sms_template');
    Route::post('/setsmscustomcontent/{id}', [SmsTemplateController::class, 'update'])->middleware('permissions:can_edit_sms_template');

    // Email Setting Route
    Route::get('/emailsetting', [SettingController::class, 'emailSettingForm']);
    Route::post('/emailsetting', [SettingController::class, 'emailSettingSave'])
        ->middleware('permissions:can_edit_email_setting');
    Route::get('/emailsettingdata', [SettingController::class, 'emailSettingData']);


    // Sms Setting Route

    Route::get('/getsmsdata', [SettingController::class, 'getSmsData']);
    Route::post('/sms-setting-update', [SettingController::class, 'smsSettingUpdate'])
        ->middleware('permissions:can_edit_sms_settings');

    // View Route
    Route::get('/settings', [SettingController::class, 'index']);

    // App Setting Route
    Route::post('/basic-setting', [SettingController::class, 'basicSettingSave'])
        ->middleware('permissions:can_edit_application_setting');
    Route::get('/basicsettingdata', [SettingController::class, 'basicSettingData']);
    Route::get('timezone', [ProfileController::class, 'getTimezone']);

    // Invoice Settings Route
    Route::post('invoice-setting-save', [SettingController::class, 'invoiceSettingsSave'])
        ->middleware('permissions:can_manage_invoice_setting');

    Route::get('invoice-settings', [SettingController::class, 'getInvoiceSettings'])
        ->middleware('permissions:can_manage_invoice_setting');
    Route::get('invoice-setting-data', [SettingController::class, 'invoiceSettingData']);
    Route::get('purchase-invoice-setting-data', [SettingController::class, 'purchaseInvoiceSettingData']);
    Route::post('purchase-invoice-setting-save',[SettingController::class, 'purchaseInvoiceSettingsSave'])
        ->middleware('permissions:can_manage_invoice_purchase_invoice_setting');

    // Invoice Template
    Route::post('invoice-templates', [InvoiceTemplateController::class, 'index']);
    Route::get('/get-invoice-template/{id}', [InvoiceTemplateController::class, 'show']);
    Route::get('/get-invoice-edit-data/{id}', [InvoiceTemplateController::class, 'getInvoiceEditData']);
    Route::post('/save-invoice-template/{id}', [InvoiceTemplateController::class, 'update']);
    Route::post('/add-invoice-template', [InvoiceTemplateController::class, 'store']);
    Route::get('/allInvoice', [InvoiceTemplateController::class, 'getAllInvoiceTemplate']);

    // Invite Route
    Route::get('/invite', [InviteController::class, 'invite']);
    Route::post('/invite', [InviteController::class, 'process'])
        ->middleware('permissions:can_manage_users');
    Route::get('/allroleid', [InviteController::class, 'getRoleId']);

    // Role Route
    Route::post('roles-list', [RoleController::class, 'getRolesList']);
    Route::get('/roletitle', [RoleController::class, 'allData']);
    Route::post('/addrole', [RoleController::class, 'store'])
        ->middleware('permissions:can_manage_roles');
    Route::post('/addrole/{id}', [RoleController::class, 'update'])
        ->middleware('permissions:can_manage_roles');
    Route::get('/rolepermissions/{id}', [RoleController::class, 'getRolePermissions']);
    Route::get('/rolepermissions/', [RoleController::class, 'getRoleWithout']);
    Route::post('/deleterole/{id}', [RoleController::class, 'delete'])
        ->middleware('permissions:can_manage_roles');
    Route::get('roles', [RoleController::class, 'index']);

    //Product setting route
    Route::get('/product-setting', [SettingController::class, 'productSetting']);
    Route::post('/product-setting-save', [SettingController::class, 'productSettingSave']);

    //Selling setting route
    Route::get('/sales-setting', [SettingController::class, 'salesSetting']);
    Route::post('/sales-setting-save', [SettingController::class, 'salesSettingSave']);

    //Notification Settings route should remove
    Route::get('/notification-setting', [SettingController::class, 'notificationSetting']);
    Route::post('/notification-setting-save', [SettingController::class, 'notificationSettingSave']);
    Route::post('/low-stock-notification-setting-save', [SettingController::class, 'lowStockNotificationSettingSave']);

    // 
    Route::get('/corn-log-last-obj', [CornJobLogController::class, 'getLastElement']);

    // Notification Route
    Route::get('notify', [NotificationController::class, 'index']);
    Route::post('/upnotify/{id}', [NotificationController::class, 'update']);
    Route::get('count', [NotificationController::class, 'count']);
    Route::post('countup/{id}', [NotificationController::class, 'countUp']);
    Route::get('booking/{id}', [NotificationController::class, 'singleView']);
    Route::get('notification', [NotificationController::class, 'allNoti']);
    Route::get('notifications', [NotificationController::class, 'reorder']);

    //all users
    Route::get('users', [UserController::class, 'allusers']);
    Route::post('/roleassign/{id}', [RoleAssignController::class, 'update']);
    Route::get('/get-user/{id}', [UserController::class, 'getRowUser']);
    Route::post('/enable-disable-user/{id}', [UserController::class, 'enableUser']);
    Route::post('/make-admin-user/{id}', [UserController::class, 'newAdminUser']);

    //product module
    Route::group(['prefix' => 'products', 'as' => 'products'], function () {
        // Index Page
        Route::get('/', [ProductsController::class, 'index']);

        Route::get('products', [ProductApiController::class, 'index']);
        // Products
        Route::post('products', [ProductsController::class, 'getProduct']);
        Route::post('store', [ProductsController::class, 'storeProduct'])
            ->middleware('permissions:can_manage_products');
        Route::post('delete/{id}', [ProductsController::class, 'deleteProduct'])
            ->middleware('permissions:can_manage_products');
        Route::get('all-supporting-data', [ProductsController::class, 'productSupportingData']);
        Route::get('edit-product/{id}', [ProductsController::class, 'getProductEditData']);
        Route::post('edit/{id}', [ProductsController::class, 'editProduct']);
        Route::get('/details/{id}', [ProductsController::class, 'productDetailsShow']);
        Route::get('getDetails/{id}', [ProductsController::class, 'getProductDetails']);
        Route::post('variantDetails/{id}', [ProductsController::class, 'getVariantDetails']);
        Route::post('/import', [ProductsController::class, 'importProduct'])
            ->middleware('permissions:can_manage_products');
        Route::post('/import-stock', [ProductsController::class, 'importOpeningStock'])
            ->middleware('permissions:can_manage_products');
        Route::get('/supporting-data', [ProductsController::class, 'getSupportingData']);
        Route::post('/adjust-stock', [ProductsController::class, 'adjustStockData']);
        Route::get('/search-product-for-stock-adjustment', [ProductsController::class, 'searchProductForStockAdjust']);

        // Variants
        Route::get('variant/{id}', [ProductsController::class, 'showVariant']);

        // Product Category
        Route::get('category', [ProductCategoriesController::class, 'index']);
        Route::post('categories', [ProductCategoriesController::class, 'getCategory']);
        Route::post('category/store', [ProductCategoriesController::class, 'storeCategory'])
            ->middleware('permissions:can_manage_categories');
        Route::post('category/delete/{id}', [ProductCategoriesController::class, 'deleteCategory'])
            ->middleware('permissions:can_manage_categories');
        Route::get('category/{id}', [ProductCategoriesController::class, 'showCategory']);
        Route::post('category/{id}', [ProductCategoriesController::class, 'updateCategory'])
            ->middleware('permissions:can_manage_categories');

        // Product Brand
        Route::get('brand', [ProductBrandsController::class, 'index']);
        Route::post('brands', [ProductBrandsController::class, 'getBrand']);
        Route::post('brand/store', [ProductBrandsController::class, 'storeBrand'])
            ->middleware('permissions:can_manage_brands');
        Route::post('brand/delete/{id}', [ProductBrandsController::class, 'deleteBrand']);
        Route::get('brand/{id}', [ProductBrandsController::class, 'showBrand']);
        Route::post('brand/{id}', [ProductBrandsController::class, 'updateBrand'])
            ->middleware('permissions:can_manage_brands');

        // Product Group
        Route::get('group', [ProductGroupsController::class, 'getGroup']);
        Route::post('groups', [ProductGroupsController::class, 'getAllGroup']);
        Route::post('group/store', [ProductGroupsController::class, 'storeGroup'])
            ->middleware('permissions:can_manage_groups');
        Route::post('group/delete/{id}', [ProductGroupsController::class, 'deleteGroup'])
            ->middleware('permissions:can_manage_groups');
        Route::get('group/{id}', [ProductGroupsController::class, 'showGroup']);
        Route::post('group/{id}', [ProductGroupsController::class, 'updateGroup'])
            ->middleware('permissions:can_manage_groups');
        Route::delete('group/delete/{id}', [ProductGroupsController::class, 'deleteGroup'])
            ->middleware('permissions:can_manage_groups');

        // Product Attribute
        Route::get('attribute', [ProductAttributesController::class, 'getAttribute']);
        Route::post('variant-attributes', [ProductAttributesController::class, 'getAttributeList']);
        Route::get('product-variant-attribute', [ProductAttributesController::class, 'getProductAttributeList']);
        Route::post('attribute/store', [ProductAttributesController::class, 'storeAttribute'])
            ->middleware('permissions:can_manage_variant_attribute');
        Route::post('attribute/delete/{id}', [ProductAttributesController::class, 'deleteAttribute'])
            ->middleware('permissions:can_manage_variant_attribute');
        Route::get('attribute/{id}', [ProductAttributesController::class, 'showAttribute']);
        Route::post('attribute/{id}', [ProductAttributesController::class, 'updateAttribute'])
            ->middleware('permissions:can_manage_variant_attribute');

        // Product Units
        Route::post('unit/store', [ProductUnitsController::class, 'store']);
        Route::post('units', [ProductUnitsController::class, 'getUnit']);
        Route::post('unit/{id}', [ProductUnitsController::class, 'update']);
        Route::get('unit/{id}', [ProductUnitsController::class, 'show']);
        Route::post('unit/delete/{id}', [ProductUnitsController::class, 'delete']);
    });

    //Sales
    Route::get('sales', [SalesController::class, 'index']);
    Route::post('sales-product', [SalesController::class, 'getProductNew']);
    Route::get('get-sales-product/{limit}/{offset}', [SaleApiController::class, 'index']);

    Route::post('get-return-orders', [SalesController::class, 'getReturnProduct']);
    Route::post('sales-returns-type-set', [SalesController::class, 'setSalesReturnsType']);
    Route::post('receive-type-set', [SalesController::class, 'setPurchaseReturnsType']);
    Route::post('/sales-list-getdata/{id}', [SalesController::class, 'salesListGetData']);
    Route::post('/sales/list/delete/{id}', [SalesController::class, 'saleListDelete']);
    Route::post('/sales/date/update/{id}', [SalesController::class, 'saleListUpdate']);

    //sales shipment list
    Route::post('/sales-shipment-getdata/{id}', [SalesShipmentController::class, 'salesListShipment']);
    Route::post('/shipping-order-status/{id}/{status}', [SalesShipmentController::class, 'setShippingStatus']);

    Route::post('sales-receiving-type-set', [SalesController::class, 'setSalesReceivingType']);
    Route::post('/store', [SalesController::class, 'salesStore'])
        ->middleware('permissions:can_manage_sales');
    Route::post('/continue-sale', [SalesController::class, 'salesStore'])
        ->middleware('permissions:can_manage_sales');
    Route::post('/purchase-store', [SalesController::class, 'salesStore'])
        ->middleware('permissions:can_manage_receives');
    Route::post('/continue-purchase', [SalesController::class, 'salesStore'])
        ->middleware('permissions:can_manage_receives');
    Route::post('/continue-sale-payments', [SalesController::class, 'getPaymentsAndDetails']);
    Route::get('/get-hold-orders', [SalesController::class, 'getHoldOrder']);
    Route::post('customers-list', [SalesController::class, 'customerList']);
    Route::post('sales-branch-set', [SalesController::class, 'setBranch']);
    Route::post('sales-cancel', [SalesController::class, 'salesCancel']);
    Route::get('/receives', [SalesController::class, 'orderReceive']);
    Route::get('/get-register-amount/{id}', [SalesController::class, 'getRegisterAmount']);
    Route::post('/save-due-amount', [SalesController::class, 'saveDueAmount']);
    Route::post('/offline-sales', [SalesController::class, 'offlineSalesStore']);

    // Customer sales sms

    Route::post('/customer-send-sms', [SalesController::class, 'customerSendSms']);

    //user tax-edit
    Route::post('/edit-tax/{id}', [UserController::class, 'editTax']);



    //tax
    Route::post('/taxlist', [TaxController::class, 'getData']);
    Route::get('/taxlist', [TaxController::class, 'taxGetDate']);
    Route::post('/addtax', [TaxController::class, 'store'])
        ->middleware('permissions:can_manage_tax_settings');
    Route::post('/edittax/{id}', [TaxController::class, 'update'])
        ->middleware('permissions:can_manage_tax_settings');
    Route::get('/edittax/{id}', [TaxController::class, 'getRowTax']);
    Route::post('/deletetax/{id}', [TaxController::class, 'deleteTax'])
        ->middleware('permissions:can_manage_tax_settings');


    //Shipping area
    Route::post('/arealist', [ShippingAreaController::class, 'areaListGetData']);
    Route::get('/arealist', [ShippingAreaController::class, 'areaListGetData']);
    Route::get('/get-areal-list', [ShippingAreaController::class, 'areaGetData']);
    Route::post('/add-shipping-area', [ShippingAreaController::class, 'store'])
        ->middleware('permissions:can_manage_shipping_area');

    Route::post('/delete-shipping-area/{id}', [ShippingAreaController::class, 'deleteShippingInfo'])
        ->middleware('permissions:can_see_shipping_area');

    Route::get('/edit-shipping-area/{id}', [ShippingAreaController::class, 'getRowShipping']);
    Route::post('/edit-shipping-area/{id}', [ShippingAreaController::class, 'update'])
        ->middleware('permissions:can_manage_shipping_area');

    //branches
    Route::get('/allBranches', [BranchController::class, 'getAllBranches']);
    Route::get('/branches', [BranchController::class, 'index']);
    Route::post('/branches', [BranchController::class, 'getBranchList']);
    Route::get('/branch-list', [BranchController::class, 'branchList']);
    Route::get('/restaurant-branch-list', [BranchController::class, 'restaurantBranchList']);
    Route::post('/addbranch', [BranchController::class, 'store'])
        ->middleware('permissions:can_manage_branches');
    Route::post('/editbranch/{id}', [BranchController::class, 'update'])
        ->middleware('permissions:can_manage_branches');
    Route::get('/edit-branch/{id}', [BranchController::class, 'getRowBranch']);
    Route::post('/delete-branch/{id}', [BranchController::class, 'deleteBranch'])
        ->middleware('permissions:can_manage_branches');
    Route::get('/branches-and-adjust-type', [BranchController::class, 'getBranchAndAdjustType']);
    //branch settings
    Route::get('/branch-settings-support-data', [SettingController::class, 'getDataForBranchSettings']);

    // Restaurant
    Route::post('get-table-list', [RestaurantTableController::class, 'getTableList']);
    Route::get('/tables', [RestaurantTableController::class, 'index']);
    Route::post('/addTable', [RestaurantTableController::class, 'store']);
    Route::post('/editTable/{id}', [RestaurantTableController::class, 'update']);
    Route::get('/edit-table/{id}', [RestaurantTableController::class, 'getRowTable']);
    Route::post('/delete-table/{id}', [RestaurantTableController::class, 'deleteTable']);

    // Adjust Stock
    Route::get('/adjust-stock-list', [AdjustProductStockController::class, 'getData']);
    Route::post('/adjust-stock-list', [AdjustProductStockController::class, 'getAdjustStockList']);
    Route::post('/add-adjust-stock', [AdjustProductStockController::class, 'store'])
        ->middleware('permissions:can_manage_adjust_stock');
    Route::post('/edit-adjust-stock/{id}', [AdjustProductStockController::class, 'update'])
        ->middleware('permissions:can_manage_adjust_stock');
    Route::post('/delete-adjust-stock/{id}', [AdjustProductStockController::class, 'deleteAdjustStockType'])
        ->middleware('permissions:can_manage_adjust_stock');
    Route::get('/adjust-stock-details/{id}', [AdjustProductStockController::class, 'getAdjustStockDetailsData']);


    //contacts
    Route::get('/contacts', [ContactController::class, 'index']);

    // customers
    Route::get('/customers', 'API\CustomerController@index');
    Route::get('/customers', [CustomerController::class, 'index']);
    Route::post('import-customer-contacts', [CustomerController::class, 'importCustomerContacts']);

    ///suppliers
    Route::post('import-supplier-contacts', [SupplierController::class, 'importSuppilerContacts']);
    Route::get('/suppliers', [SupplierController::class, 'index']);
    Route::post('supplier/store', [SupplierController::class, 'store']);
    Route::post('/supplier-list', [SupplierController::class, 'getSupplierData']);
    Route::get('/supplier-edit/{id}', [SupplierController::class, 'getData']);
    Route::get('/supplier/{id}', [SupplierController::class, 'getSupplierDetails']);
    Route::post('supplier/{id}', [SupplierController::class, 'editSupplierData']);
    Route::post('supplier/delete/{id}', [SupplierController::class, 'deleteSupplier']);
    Route::post('supplier-delivery-report/{id}', [SupplierController::class, 'getSupplierDeliveryRecords']);
    Route::post('/update-supplier-avatar/{id}', [SupplierController::class, 'updateAvatar']);

    //show customer list
    Route::post('/customer-list', [CustomerController::class, 'getCustomerList']);

    //save customer data
    Route::post('/customer/store', [CustomerController::class, 'store'])
        ->middleware('permissions:can_manage_customers');
    Route::post('/customer/{id}', [CustomerController::class, 'updateCustomer'])
        ->middleware('permissions:can_manage_customers');
    Route::post('/delete/{id}', [CustomerController::class, 'destroy'])
        ->middleware('permissions:can_manage_customers');
    Route::post('/customer/delete/{id}', [CustomerController::class, 'deleteCustomer'])
        ->middleware('permissions:can_manage_customers');
    Route::get('/customer/{id}', [CustomerController::class, 'getCustomerDetails']);
    Route::get('/customer-data/{id}', [CustomerController::class, 'getCustomerData']);
    Route::post('/update-customer-avatar/{id}', [CustomerController::class, 'updateAvatar']);

    // groups
    Route::get('/groups', [CustomerGroupController::class, 'index']);
    Route::post('/groups', [CustomerGroupController::class, 'getGroups']);
    Route::post('/group/store', [CustomerGroupController::class, 'store'])
        ->middleware('permissions:can_manage_customer_groups');
    Route::get('/groups/{id}', [CustomerGroupController::class, 'show']);
    Route::post('/group/delete/{id}', [CustomerGroupController::class, 'destroy'])
        ->middleware('permissions:can_manage_customer_groups');
    Route::post('/group/{id}', [CustomerGroupController::class, 'update'])
        ->middleware('permissions:can_manage_customer_groups');
    Route::get('/customer-groups', [CustomerGroupController::class, 'getCustomerGroups']);

    //payment
    Route::get('/paymentlist', [PaymentController::class, 'getData']);
    Route::post('/payment-list', [PaymentController::class, 'getPaymentList']);
    Route::post('/addpayment', [PaymentController::class, 'store'])
        ->middleware('permissions:can_manage_payment_settings');

    Route::post('/editpayment/{id}', [PaymentController::class, 'update'])
        ->middleware('permissions:can_manage_payment_settings');
    Route::post('/delete-payment/{id}', [PaymentController::class, 'deletePaymentMethod'])
        ->middleware('permissions:can_manage_payment_settings');

    Route::get('/payment-details/{id}', [PaymentController::class, 'getPaymentDetailsData']);
    Route::get('/invoice-logo', [PaymentController::class, 'getInvoiceLogo']);
    Route::get('/get-auto-invoice', [PaymentController::class, 'getAutoInvoice']);

    //users
    Route::post('/users-list', [UserController::class, 'getUsersList']);
    Route::get('/user/{id}', [UserController::class, 'userDetail']);
    Route::get('/userChartData/{id}', [UserController::class, 'getUser']);

    //sales register
    Route::get('/cash-registers', [CashRegisterController::class, 'getCashRegisterList']);
    Route::post('cash-registers', [CashRegisterController::class, 'index']);
    Route::post('cash-register-store', [CashRegisterController::class, 'store'])
        ->middleware('permissions:can_manage_cash_registers');
    Route::get('cash-register-show/{id}', [CashRegisterController::class, 'show']);
    Route::post('cash-register-update/{id}', [CashRegisterController::class, 'update'])
        ->middleware('permissions:can_manage_cash_registers');
    Route::post('delete-register/{id}', [CashRegisterController::class, 'deleteCashRegister'])
        ->middleware('permissions:can_manage_cash_registers');
    Route::post('cash-register-open-close', [CashRegisterController::class, 'cashRegisterLogs']);

    Route::post('/register-sales-info/{id}', [CashRegisterController::class, 'registerSalesInfo']);
    Route::get('/cash-register-total-sales-blance/{id}', [CashRegisterController::class, 'cashRegisterInfo']);

    //Keyboard shortcuts
    Route::post('shortcuts', [SettingController::class, 'storeKeyboardShortcutSettings']);
    Route::get('shortcut-setting-data/{id}', [SettingController::class, 'getShortcutSettings']);
    Route::post('tax-report', [ReportController::class, 'taxReports']);
    Route::post('profit-loss-report', [ReportController::class, 'profitLossReport']);

    // Report Route
    Route::get('reports', [ReportController::class, 'index']);
    Route::get('dateRangeAFormat', [ReportController::class, 'getdateRangeAFormat']);
    Route::post('customer-purchase-report/{id}', [ReportController::class, 'customerPurchaseReport']);
    Route::get('reports/ordersDetails/{id}', [ReportController::class, 'getOrdersDetails']);
    Route::get('/ordersDetails', [ReportController::class, 'getOrdersDetails']);
    Route::get('reports/order-details-and-invoice-template/{id}', [ReportController::class, 'getOrderDetailsWithInvoiceTemplate']);

    //sales
    Route::post('sales-report', [ReportController::class, 'salesReport']);
    Route::post('all-sales-details', [ReportController::class, 'allSalesDetails']);
    Route::post('sales-summary-report', [ReportController::class, 'salesSummaryReport']);
    Route::get('reports/sales/{id}', [ReportController::class, 'salesReportsDetails']);
    Route::post('reports/salesDetails/{id}', [ReportController::class, 'getSalesDetails']);
    Route::get('/sales-report-filter', [ReportController::class, 'getSalesReportFilterData']);
    Route::get('/sales-details-filter', [ReportController::class, 'getSalesDetailsFilterData']);
    Route::get('/sales-due-filter', [ReportController::class, 'getCustomerDueFilterData']);
    Route::post('/sales-and-purchase-report', [ReportController::class, 'salesAndPurchaseReport']);

    // adjustment stock
    Route::get('/adjustment-report-filter', [ReportController::class, 'getAdjustmentReportFilterData']);
    Route::post('adjust-stock-report', [ReportController::class, 'adjustStockReport']);
    Route::post('shipment-report', 'API\ReportController@shipmentReport');
    Route::post('shipment-report', [ReportController::class, 'shipmentReport']);

    //customer report
    Route::post('customer-summary-report', [ReportController::class, 'customerSummaryReport']);
    Route::get('/customer-report-filter', [ReportController::class, 'getCustomerReportFilterData']);

    //supplier report
    Route::post('supplier-summary-report', [ReportController::class, 'supplierSummaryReport']);

    //receiving
    Route::post('receiving-summary-report', [ReportController::class, 'receivingSummary']);
    Route::post('receiving-report', [ReportController::class, 'receivingReport']);
    Route::get('reports/receiving/{id}', [ReportController::class, 'receivingReportsDetails']);

    //register log
    Route::post('register-log-reports', [ReportController::class, 'registerLogReports']);
    Route::get('cash-register-for-filter', [ReportController::class, 'getCashRegisterFilterData']);

    //inventory
    Route::post('inventory-reports', [ReportController::class, 'inventoryReports']);
    Route::get('inventory-reports-filter', [ReportController::class, 'inventoryReportsFilter']); //new inventory filter

    //payment
    Route::post('payment-reports', [ReportController::class, 'paymentReport']);
    Route::get('payment-reports-filter', [ReportController::class, 'paymentReportFilter']); // new report filter
    Route::get('payment-summery-reports-filter', [ReportController::class, 'paymentSummaryReportFilter']);
    Route::post('payment-summary-reports', [ReportController::class, 'paymentSummary']);

    // Sales Chart
    Route::post('yearly-sales-chart', [ReportController::class, 'yearlySalesChart']);
    Route::get('branch-user', [ReportController::class, 'getBranchAndUser']);
    Route::get('available-stock-chart', [ReportController::class, 'availableStockChart']);

    Route::post('receiving-summary-reports', [ReportController::class, 'receivingSummary']);

    // cash register log
    Route::get('cash-register-logs', [RegisterLogController::class, 'index']);
    Route::post('save-cash-register', [RegisterLogController::class, 'saveRegisterLog']);

    // Updates Route
    Route::get('/gain-update', [UpdateController::class, 'applicationVersion']);
    Route::get('/update-version-list', [UpdateController::class, 'versionUpdateList']);
    Route::post('/install-new-version/{version}', [UpdateController::class, 'updateAction']);
    Route::get('/update-list', [UpdateController::class, 'updateAppUrl']);
    Route::get('/curl_get_contents', [UpdateController::class, 'curl_get_contents']);
    Route::get('/nexInstallVersion', [UpdateController::class, 'nexInstallVersion']);
    Route::get('/check-purchase-key', [UpdateController::class, 'checkPurchaseKey']);
    Route::post('/purchase-key-save', [SettingController::class, 'purchaseKeySave']);


    //Clear language cache
    Route::get('/clear-language-cache', function () {
        Artisan::call('cache:clear');
    });
    //product module
    Route::group(['prefix' => 'todo'], function () {
        Route::post('store', [TodoListController::class, 'store']);
        Route::post('update-status', [TodoListController::class, 'upDateStatus']);
        Route::post('delete', [TodoListController::class, 'deleteData']);
        Route::post('set-due-date', [TodoListController::class, 'setDueDate']);

        Route::post('list', [TodoListController::class, 'getTodoData']);
    });

});

// Localization
Route::get('/js/lang.js', function () {
    $strings = Cache()->rememberForever('lang.js', function () {
        $lang = config('app.locale');
        $files = glob(resource_path('lang/' . $lang . '/*.php'));
        $strings = [];
        foreach ($files as $file) {
            $name = basename($file, '.php');
            if ($name !== "lang") {
                $new_keys = require $file;
                $strings = array_merge($strings, $new_keys);
            }
        }
        return $strings;
    });
    header('Content-Type: text/javascript');
    echo ('window.i18n = ' . json_encode(array("lang" => $strings)) . ';');
    exit();
})->name('assets.lang');


Route::group(['prefix' => 'app'], function () {
    Route::get('environment', [EnvironmentController::class, 'index'])
        ->name('app.environment');

    Route::post('environment/database', [EnvironmentController::class, 'saveEnvironment'])
        ->name('app.environment.database');

    Route::get('environment/admin', [EnvironmentController::class, 'admin'])
        ->name('app.environment.admin');

    Route::post('environment/install', [EnvironmentController::class, 'store'])
        ->name('app.installer');
});
