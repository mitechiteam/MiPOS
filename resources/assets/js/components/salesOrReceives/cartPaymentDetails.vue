<template>
    <div class="modal-layout-content">
        <a href="#" class="position-absolute variant-modal-close-btn p-2 close" data-dismiss="modal" aria-label="Close"
           @click.prevent="closeModal">
            <i class="la la-close text-grey"></i>
        </a>
        <div class="row mx-0 modal-content-row">
            <!--Left payment details-->
            <div class="col-12 col-md-6 cart-border-right text-center">
                <div class="horizontal-scroll">
                    <h5 class="text-center mb-4">{{trans('lang.'+orderType)+' '+trans('lang.details')}}</h5>
                    <div v-if="logo =='default-logo.jpg'"></div>
                    <div class="invoiceLogo text-center" v-else>
                        <img class="text-center" :src="publicPath+'/uploads/logo/'+logo" height="50px" width="70px"
                             alt="">
                    </div>
                    <div>
                        <div class="text-center header-line-height">
                            <small class='text-center font-weight-bold'>{{ app_name }}</small>
                            <br>
                            <small class='text-center'>{{ dateFormats(finalCart.date) }}</small>
                            <br>
                            <small class='text-center'>{{trans('lang.'+orderType)+' '+trans('lang.receipt')}}</small>
                            <br>
                            <small class="text-left">{{ trans('lang.'+sold_by)}}: {{user.first_name + " "+
                                user.last_name}}
                            </small>
                            <br>
                            <small v-if="orderType == 'sales'">
                            <span v-if="salesOrReceivingType == 'customer'">
                                <span v-if="finalCart.customer.length === 0">{{ trans('lang.'+sold_to)}}: {{ trans('lang.walk_in_customer')}}</span>
                                <span v-else>
                                    {{ trans('lang.'+sold_to)}}: {{finalCart.customer.first_name + " " + finalCart.customer.last_name}}
                                </span>
                            </span>
                                <span v-else>
                                {{ trans('lang.'+sold_to)}}: {{finalCart.transferBranchName}}
                            </span>
                            </small>
                            <small v-else>
                            <span v-if="salesOrReceivingType == 'supplier'">
                                <span v-if="finalCart.customer.length === 0">{{ trans('lang.received_from')}}: {{ trans('lang.walk_in_supplier')}}</span>
                                <span v-else>
                                    {{ trans('lang.received_from')}}: {{finalCart.customer.first_name + " " + finalCart.customer.last_name}}
                                </span>
                            </span>
                                <span v-else>
                                {{ trans('lang.received_from')}}: {{finalCart.transferBranchName}}
                            </span>
                            </small>
                            <small class="text-left invoice-show" style="display: none">{{ trans('lang.invoice_id')
                                }}:{{
                                invoiceID }}
                            </small>
                        </div>
                        <div class="invoice-table">
                            <table class="table product-card-font" style="font-weight:500">
                                <thead class="border-top-0">
                                <tr>
                                    <th class="cart-summary-table text-left" v-if="finalCart.cart.length>1">{{
                                        trans('lang.items') }}
                                    </th>
                                    <th class="cart-summary-table text-left" v-else>{{ trans('lang.item') }}</th>
                                    <th class="cart-summary-table text-left">{{ trans('lang.qty') }}</th>
                                    <th class="cart-summary-table text-right">{{ trans('lang.price') }}</th>
                                    <th class="cart-summary-table text-right">{{ trans('lang.discount') }}</th>
                                    <th class="cart-summary-table text-right">{{ trans('lang.total') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="cartItem in finalCart.cart">
                                    <td class="cart-summary-table text-left">
                                        {{ cartItem.productTitle }}
                                        <br>
                                        <span v-if="cartItem.productTitle != cartItem.variantTitle && cartItem.variantTitle != 'default_variant' && cartItem.variantTitle != '' && cartItem.orderType != 'discount'">( {{ cartItem.variantTitle }} )</span>
                                        <p v-if="cartItem.cartItemNote!='' && cartItem.cartItemNote != null"
                                           class="cart-note pb-0 mb-0">
                                            {{ trans('lang.note') }}: <span>{{ cartItem.cartItemNote }}</span>
                                        </p>
                                    </td>
                                    <td class="cart-summary-table"
                                        v-if="cartItem.orderType != 'discount' && cartItem.orderType != 'shipment'">
                                        {{ numberFormatting(cartItem.quantity) }}
                                    </td>
                                    <td class="cart-summary-table" v-else></td>
                                    <td class="text-right cart-summary-table"
                                        v-if="cartItem.orderType != 'discount' && cartItem.orderType != 'shipment'">
                                        {{ numberFormat(cartItem.price) }}
                                    </td>
                                    <td class="cart-summary-table" v-else></td>
                                    <td class="text-right cart-summary-table" v-if="cartItem.discount >0">
                                        {{ decimalFormat(cartItem.discount) }}%
                                    </td>
                                    <td class="text-right cart-summary-table"
                                        v-else-if="cartItem.orderType != 'discount' && cartItem.orderType != 'shipment'">
                                        {{ decimalFormat('0.00') }}%
                                    </td>
                                    <td class="cart-summary-table" v-else></td>
                                    <td class="text-right cart-summary-table">
                                        {{ numberFormat(cartItem.calculatedPrice) }}
                                    </td>
                                </tr>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td class="cart-summary-table font-weight-bold text-left">{{ trans('lang.sub_total')
                                        }}
                                    </td>
                                    <td class="cart-summary-table"></td>
                                    <td class="cart-summary-table"></td>
                                    <td class="cart-summary-table"></td>
                                    <td class="text-right cart-summary-table">{{ numberFormat(finalCart.subTotal) }}
                                    </td>
                                </tr>
                                <tr v-if="finalCart.tax>0">
                                    <td class="cart-summary-table text-left">{{ trans('lang.item_tax') }}</td>
                                    <td class="cart-summary-table"></td>
                                    <td class="cart-summary-table"></td>
                                    <td class="cart-summary-table"></td>
                                    <td class="text-right cart-summary-table ">{{ numberFormat(finalCart.tax) }}</td>
                                </tr>
                                <tr>
                                    <td class="cart-summary-table font-weight-bold text-left">{{ trans('lang.total')
                                        }}
                                    </td>
                                    <td class="cart-summary-table"></td>
                                    <td class="cart-summary-table"></td>
                                    <td class="cart-summary-table"></td>
                                    <td class="text-right cart-summary-table ">{{ numberFormat(finalCart.grandTotal)
                                        }}
                                    </td>
                                </tr>
                                <tr v-if="payment.paymentName" v-for="payment in payments">
                                    <td class="cart-summary-table text-left">{{ payment.paymentName }}</td>
                                    <td class="cart-summary-table">
                                        <small class="font-weight-bold" v-if="payment.paid">{{ trans('lang.paid') }}<br>
                                            {{payment.paid}}
                                        </small>
                                    </td>
                                    <td class="cart-summary-table text-left" v-if="payment.paymentName">
                                        <small class="font-weight-bold" v-if="payment.exchange">
                                            {{trans('lang.exchange') }}
                                            <br> {{payment.exchange}}
                                        </small>
                                    </td>
                                    <td class="cart-summary-table">
                                        {{ numberFormat(payment.paid - payment.exchange) }}
                                    </td>
                                </tr>
                                <tr v-for="(paymentTypes, index) in paymentListData" 
                                    :key="`paymentListInvoceData-${index}`"
                                    v-if="paymentTypes.is_active">
                                    <td class="cart-summary-table text-left">{{paymentTypes.paymentName}}</td>
                                    <td class="cart-summary-table"></td>
                                    <td class="cart-summary-table"></td>
                                    <td class="cart-summary-table"></td>
                                    <td class="text-right cart-summary-table">{{numberFormat(paymentTypes.paid)}}</td>
                                </tr>
                                <tr v-if="exchangeValue>0">
                                    <td class="cart-summary-table text-left">{{ trans('lang.exchange') }}</td>
                                    <td class="cart-summary-table"></td>
                                    <td class="cart-summary-table"></td>
                                    <td class="cart-summary-table"></td>
                                    <td class="text-right cart-summary-table ">{{ numberFormat(exchangeValue) }}</td>
                                </tr>
                                <tr v-if="!printReceiptView">
                                    <td class="cart-summary-table font-weight-bold text-left" v-if="balance >= 0">
                                        {{ trans('lang.due') }}
                                    </td>
                                    <td class="cart-summary-table font-weight-bold text-left" v-else>{{
                                        trans('lang.change') }}
                                    </td>
                                    <td class="cart-summary-table"></td>
                                    <td class="cart-summary-table"></td>
                                    <td class="cart-summary-table"></td>
                                    <td class="text-right cart-summary-table">{{ numberFormat(absValue(balance)) }}</td>
                                </tr>
                                <tr v-if="finalCart.cartNote !=''">
                                    <td class="cart-summary-table" colspan="3">
                                        <small>{{ trans('lang.note') }}: {{ finalCart.cartNote }}</small>
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <a href="#" v-if="!isPaymentDone" @click.prevent="printReceiptBeforeDonePayment()"
                   class="px-2 btn-before-receipt">
                    <i class="la la-print pr-3"></i>
                    {{ trans('lang.print_received') }}
                </a>
            </div>

            <!--Right-->
            <div class="col-12 col-md-6" id="js-payment">
                <div v-if="!isPaymentDone || invoice_template == ''">
                    <div class="row mx-0 mb-4" id="js-payment-title">
                        <div class="col-6"><h5>{{ trans('lang.total') }}</h5></div>
                        <div class="col-6 text-right payment-amount"><h5> {{ numberFormat(finalCart.grandTotal) }} </h5>
                        </div>
                    </div>
                    <pre-loader v-if="!hidePaymentListGetLoader || invoice_template == ''"></pre-loader>
                    <div v-else>
                        <div class="payment-description" id="js-payment-description">
                            <div v-if="salesOrReceivingType !== 'internal-transfer'" class="row mx-0 mb-2"
                                 v-for="(paymentTypes, index) in paymentListData">
                                <div class="col-4 col-sm-6 col-md-5 col-lg-4 col-xl-4 mt-auto">
                                    <label>{{ paymentTypes.paymentName }}</label>
                                </div>
                                <div class="col-4 col-sm-5 col-md-6 col-lg-7 col-xl-7 pl-0">
                                    <label>{{numberFormat(paymentTypes.paid)}}</label>
                                </div>
                                <div class="col-1 mt-auto p-0 text-right">
                                    <a href="#"
                                       @click.prevent="clearPayment(index,paymentTypes.paymentID,paymentTypes.paid)">
                                        <i class="text-danger la la-trash"></i>
                                    </a>
                                </div>
                            </div>

                            <div v-if="salesOrReceivingType != 'internal-transfer'" class="form-group row ml-0">
                                <label class="col-4 col-sm-6 col-md-5 col-lg-4 col-form-label" :for="paid">{{paymentName}}</label>
                                <div class="col-4 col-sm-6 col-md-7 col-lg-8 col-xl-8 pl-0">
                                    <payment-input id="'paid'"
                                                   v-if="PaymentGauard"
                                                   :inputValue="decimalFormat(paymentValue)"
                                                   @input="getPaymentAmount">
                                    </payment-input>
                                </div>
                            </div>

                            <!--Sales note-->
                            <div v-if="orderType == 'sales'" class="form-group row ml-0">
                                <label class="col-4 col-sm-6 col-md-5 col-lg-4 col-form-label">
                                    {{trans('lang.note')}}
                                </label>
                                <div class="col-4 col-sm-6 col-md-7 col-lg-8 col-xl-8 pl-0">
                                    <textarea class="form-control"
                                              v-model="salesNote">
                                    </textarea>
                                </div>
                            </div>

                            <!--Shipment block starts-->
                            <div v-if='is_shipment_enable == 1 && orderType =="sales" && salesOrReceivingType == "customer" && salesOrReturnType=="sales" && is_connected'
                                 class="form-group row ml-0">
                                <label class="col-4 col-sm-6 col-md-5 col-lg-4 col-xl-4 col-form-label pt-0">
                                    {{ trans('lang.shipping')}}
                                </label>
                                <div class="col-4 col-sm-6 col-md-7 col-lg-8 col-xl-8 pl-0">
                                    <div class="d-flex align-items-center">
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio"
                                                   name="shipment"
                                                   class="custom-control-input"
                                                   id="shipment-yes"
                                                   checked="checked"
                                                   @click="addShipmentStatus(1)"
                                                   value="1"
                                                   v-model="addShipment"/>
                                            <label class="custom-control-label"
                                                   for="shipment-yes">
                                                {{ trans('lang.yes') }}
                                            </label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio"
                                                   name="shipment"
                                                   class="custom-control-input"
                                                   id="shipment-no"
                                                   @click="addShipmentStatus(0)"
                                                   value="0"
                                                   v-model="addShipment"/>
                                            <label class="custom-control-label"
                                                   for="shipment-no">
                                                {{ trans('lang.no') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-if="addShipmentInfo" class="">
                                <!--Select area start-->
                                <div class="form-group row ml-0">
                                    <label class="col-4 col-sm-6 col-md-5 col-lg-4 col-xl-4 col-form-label">
                                        {{ trans('lang.shipping_area') }}
                                    </label>
                                    <div class="col-4 col-sm-6 col-md-7 col-lg-8 col-xl-8 pl-0">
                                        <select v-model="shippingAreaId"
                                                v-validate="'required'"
                                                data-vv-as="shipping area"
                                                id="id"
                                                name="shipping_area"
                                                class="form-control"
                                                @change="setShippingPrice">
                                            <option value="" disabled selected>{{ trans('lang.choose_one') }}</option>
                                            <option v-for="(row, index) in shippingData" :value="row.id">
                                                {{ row.area }}
                                            </option>
                                        </select>

                                        <div class="heightError">
                                            <small class="text-danger" v-show="errors.has('shipping_area')">
                                                {{ errors.first('shipping_area') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <!--Select area end-->

                                <!--Price start-->
                                <div class="form-group row ml-0">
                                    <label class="col-4 col-sm-6 col-md-5 col-lg-4 col-xl-4 col-form-label">
                                        {{ trans('lang.price') }}
                                    </label>
                                    <div class="col-4 col-sm-6 col-md-7 col-lg-8 col-xl-8 pl-0">
                                        <input v-validate="'required'"
                                               name="price"
                                               type="text"
                                               class="form-control"
                                               v-model="shippingPrice"/>
                                        <div class="heightError">
                                            <small class="text-danger" v-show="errors.has('price')">
                                                {{ errors.first('price') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <!--Price end-->

                                <!--Address start-->
                                <div class="form-group row ml-0">
                                    <label class="col-4 col-sm-6 col-md-5 col-lg-4 col-xl-4 col-form-label">{{
                                        trans('lang.address') }}</label>
                                    <div class="col-4 col-sm-6 col-md-7 col-lg-8 col-xl-8 pl-0">
                                        <input v-validate="'required'"
                                               name="address"
                                               type="text"
                                               class="form-control"
                                               v-model="shippingAddress"
                                        />
                                        <div class="heightError">
                                            <small class="text-danger" v-show="errors.has('address')">
                                                {{ errors.first('address') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <!--Address end-->
                            </div>
                            <!--Shipment block ends-->
                        </div>


                        <div class="payment-action" id="js-payment-action">
                            <div class="row mx-0 mt-2 no-gutters">
                                <div class="col-12">
                                    <hr class="custom-margin">
                                    <div class="payment-group" style="overflow: hidden;">
                                        <span v-for="(paymentTypes, index) in paymentList">
                                           <button v-if="(salesOrReceivingType == 'customer' || salesOrReceivingType == 'supplier') && paymentTypes.type != 'credit'"
                                                   :id="paymentTypes.id"
                                                   class="btn app-color mobile-btn mr-1 mb-1"
                                                   :class="{activePayment: paymentName==paymentTypes.name}"
                                                   @click.prevent="setPayment(paymentTypes.id,paymentTypes.name,paymentTypes.status,paymentTypes.type)">
                                                    {{ paymentTypes.name }}
                                            </button>
                                            <button v-else-if="(salesOrReceivingType == 'customer' || salesOrReceivingType == 'supplier') && !isEmptyObj(finalCart.customer) && (paid >= 0 && noRoundingAmount >=0)"
                                                    :id="paymentTypes.id"
                                                    class="btn app-color mobile-btn mr-1 mb-1"
                                                    :class="{activePayment: paymentName==paymentTypes.name}"
                                                    @click.prevent="setPayment(paymentTypes.id,paymentTypes.name,paymentTypes.status,paymentTypes.type)">
                                                    {{ paymentTypes.name }}
                                            </button>
                                            <button v-else-if="salesOrReceivingType == 'internal'"
                                                    :id="paymentTypes.id"
                                                    class="btn app-color mobile-btn mr-1 mb-1"
                                                    :class="{activePayment: paymentName==paymentTypes.name}"
                                                    @click.prevent="setPayment(paymentTypes.id,paymentTypes.name,paymentTypes.status,paymentTypes.type)">
                                                    {{ paymentTypes.name }}
                                            </button>
                                        </span>
                                    </div>

                                    <!-- NB: Temporary unavailable feature,  When bank payment and card payment active -->

                                    <!--<div class="payment-group">

                                    class="payment-button"

                                       <span v-for="(paymentTypes, index) in paymentList">
                                           <button v-if="paymentTypes.type == 'card'"
                                                   :id="paymentTypes.id"
                                                   class="btn app-color mobile-btn m-1"
                                                   :class="{activePayment: paymentName==paymentTypes.name}"
                                                   @click.prevent="setPayment(paymentTypes.id,paymentTypes.name,paymentTypes.status,paymentTypes.type),openCardModal()">
                                                    {{ paymentTypes.name }}
                                            </button>
                                            <button v-else-if="paymentTypes.type == 'bank'"
                                                    :id="paymentTypes.id"
                                                    class="btn app-color mobile-btn m-1"
                                                    :class="{activePayment: paymentName==paymentTypes.name}"
                                                    @click.prevent="setPayment(paymentTypes.id,paymentTypes.name,paymentTypes.status,paymentTypes.type),openBankModal()">
                                                    {{ paymentTypes.name }}
                                            </button>
                                            <button v-else
                                                    :id="paymentTypes.id"
                                                    class="btn app-color mobile-btn m-1"
                                                    :class="{activePayment: paymentName==paymentTypes.name}"
                                                    @click.prevent="setPayment(paymentTypes.id,paymentTypes.name,paymentTypes.status,paymentTypes.type)">
                                                    {{ paymentTypes.name }}
                                            </button>
                                       </span>
                                    </div>-->
                                    <hr class="custom-margin">

                                    <!--shakib-->
                                    <span v-if="(salesOrReceivingType != 'internal-transfer') && balance==0 || paidAmount == finalCart.grandTotal">
                                        <button class="btn btn-block app-color payment-button"
                                                v-shortkey="donePaymentShortcut"
                                                :disabled="!isConnected && offline == 0"
                                                @shortkey="shortcutForDonePayment('donePayment')"
                                                @click.prevent="storeInvoice()">
                                                {{ trans('lang.done_payment') }}
                                        </button>
                                    </span>
                                    <span v-else-if=" salesOrReceivingType == 'internal-transfer'">
                                       <button class="btn btn-block app-color payment-button"
                                               v-shortkey="donePaymentShortcut"
                                               :disabled="!isConnected && offline == 0"
                                               @shortkey="shortcutForDonePayment('donePayment')"
                                               @click.prevent="storeInvoice()">
                                                {{ trans('lang.transfer') }}
                                        </button>
                                    </span>
                                    <span v-else>
                                        <button class="btn btn-block app-color payment-button"
                                                :disabled="!isConnected && offline == 0"
                                                @click.prevent="storeInvoice()">
                                                {{ trans('lang.add_payment') }}
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mx-0" v-else>
                    <div class="col-12 text-center">
                        <h4>{{ trans('lang.payment_received') }}</h4>
                    </div>
                    <a href="#" @click.prevent="printReceipt()" class="printReceiptButton">
                        <i class="la la-print pr-3"></i>
                        {{ trans('lang.print_received') }}
                    </a>

                    <div v-if="finalCart.customer.id && finalCart.customer.phone_number && !isSendSms &&  is_connected && autoSendSms == 0"
                         class="col-12 text-center">
                        <h4 v-if="orderType=='sales'">{{ trans('lang.send_sms_customer') }}</h4>
                        <h4 v-else>{{ trans('lang.send_sms_supplier') }}</h4>
                    </div>
                    <a v-if="finalCart.customer.id && finalCart.customer.phone_number && !isSendSms &&  is_connected && autoSendSms == 0"
                       href="#" @click.prevent="sendCustomerSms()" class="printReceiptButton">
                        <i class="la la-sms"></i>
                        {{ trans('lang.send_sms') }}
                    </a>
                </div>


            </div>

        </div>
        <invoice :printInvoice="printInvoice"
                 :HTMLcontent="HTMLcontent"
                 :invoiceID="invoiceID"
                 @resetGetInvoice="resetGetInvoice">
        </invoice>
        <print-receipt-component :print_invoice_before_done_payment="printInvoiceBeforeDonePayment"
                                 :sales_or_receiving_type="salesOrReceivingType"
                                 :transfer_branch_name="transferBranchName"
                                 :payment_list_data="paymentListData"
                                 :exchange_value="exchangeValue"
                                 :order_type="orderType"
                                 :final_cart="finalCart"
                                 :payments="payments"
                                 :sales_note = "salesNote"
                                 :sold_to="sold_to"
                                 :sold_by="sold_by"
                                 :balance="balance"
                                 :invoice_template="invoice_template"
                                 :user="user"
                                 :logo="logo"
                                 :shipping_area="shippingArea"
                                 :shipping_address="shippingAddress"
                                 :add_shipping_info="addShipmentInfo"
                                 @resetGetInvoiceBeforeDonePayment="resetGetInvoiceBeforeDonePayment">
        </print-receipt-component>
    </div>
</template>

<script>

    import axiosGetPost from '../../helper/axiosGetPostCommon';

    // import {
    //     setShipmentEnableDisable,
    //     getShipmentEnableDisable
    // } from './helper/salesComponentCommonMethod.js';

    import Product from "../settings/product/product";
    import Shipping from "../settings/Shipping/Shipping";

    export default {
        extends: axiosGetPost,

        props: [
            'selectedCashRegisterID',
            'finalCart',
            'user',
            'orderType',
            'salesOrReceivingType',
            'salesOrReturnType',
            'receiveOrReturnType',
            'orderID',
            'orderIdInternalTransfer',
            'orderIDInternal',
            'sold_to',
            'sold_by',
            'logo',
            'bankOrCardAmount',
            'calculateBank',
            'bankOrCardOptions',
            'donePaymentShortcut',
            'transferBranch',
            'transferBranchName',
            'paymentTypes',
            'autoInvoice',
            'invoice_template',
            'last_invoice_number',
            'invoice_prefix',
            'invoice_suffix',
            'selectedBranchID',
            'is_shipment_enable',
            'invoiceId',
            'is_cash_register_branch',
            'sales_default_invoice_template',
            'receives_default_invoice_template',
            'is_cash_register_used',
            'is_connected',
            'shipping_data',
            'add_shipping',
        ],
        data() {
            return {
                paymentList: [],
                isPaymentDone: false,
                invoice: [],
                PaymentGauard: true,
                payments: [],
                savedPayments: [],
                balance: this.finalCart.grandTotal,
                printReceiptView: false,
                invoiceID: '',
                autoInvoiceGenerate: false,
                donePaymentCheck: false,
                hidePaymentListGetLoader: null,
                exchangeValue: '',
                currentOrderId: '',
                paymentListData: [],
                paymentName: '',
                paymentId: '',
                paymentStatus: '',
                paid: '',
                noRoundingAmount: '',
                finalCartAmount: '',
                roundingDifference: 0,
                printInvoice: false,
                printInvoiceBeforeDonePayment: false,
                paymentType: '',
                paymentValue: '',
                paymentOptions: {},
                paidAmount: '',
                HTMLcontent: '',
                autoInvoiceStatus: '',
                highestInvoiceNumber: '',
                lastInvoiceNumber: '',
                isCashRegisterBranch: '',
                isConnected: true,
                isSendSms: false,

                addShipment: '',
                addShipmentInfo: '',
                shippingAreaId: '',
                shippingArea: '',
                shippingPrice: '',
                shippingAddress: '',
                isSubmited: false,
                isEmptyObj: (object) => {
                    if (_.isEmpty(object)) {
                        return true;
                    }
                },
                shippingData: [],
                shippingInfo: {},
                conditonForReturn : this.salesOrReturnType == 'returns' || this.receiveOrReturnType == 'returns',
                salesNote: "",

            }
        },
        created() {
            this.noRoundingAmount = this.finalCart.grandTotal;
            this.addShipmentInfo = this.add_shipping;
            if (this.addShipmentInfo) {
                this.addShipment = 1;
            } else this.addShipment = 0;
            this.isCashRegisterBranch = this.is_cash_register_branch;
            this.setPaymentListData();
            this.setAutoInvoiceStatus();
            if (this.orderID) {
                this.setSavedPayments();
            }
            this.currentOrderId = this.orderID;
            this.shippingInfo = {
                productID: null,
                variantID: null,
                taxID: null,
                orderType: "shipment",
                productTitle: "Shipment",
                price: 0,
                quantity: -1,
                calculatedPrice: 0,
                cartItemNote: "",
                showItemCollapse: false,
                variantTitle: "",
            };
        },
        watch: {
            calculateBank: function (newValue) {
                if (newValue) {
                    this.defaultPayment(this.bankOrCardAmount, this.bankOrCardOptions);
                }
            },
            is_connected: function (value) {
                this.isConnected = value;
            },
            finalCart: function (newValue) {
                if (newValue) {
                    this.paymentValue = this.finalCart.grandTotal;
                }

            },
            shippingPrice: function (value) {
                if (value == '') value = 0;
                else value = parseFloat(value);
                this.shippingInfo.price = value;
                this.shippingInfo.calculatedPrice = value;
                this.addShipmentStatus(1);
            },
            paid : 'preventCreditPayment',
        },
        mounted() {
            this.shippingData = this.shipping_data.map((area) => {
                return area;
            });

            let instance = this;
            instance.lastInvoiceNumber = parseInt(instance.last_invoice_number);

            // future use for bank and card payment
            // $('#bank-transfer-modal').on('hidden.bs.modal', function (e) {
            //     instance.selectDefaultMethod();
            // });
            // $('#card-payment-modal').on('hidden.bs.modal', function (e) {
            //     instance.selectDefaultMethod();
            // });
            $(window).resize(function () {
                instance.setPaymentDescHeight();
            });

        },
        beforeDestroy() {
            if (!this.donePaymentCheck) {
                this.storeInvoice('continue');
            }
        },
        methods: {
            setShippingPrice() {
                this.shippingPrice = this.shippingData.find(elem => elem.id == this.shippingAreaId).price.toFixed(2);
                this.shippingArea = this.shippingData.find(elem => elem.id == this.shippingAreaId).area;
            },
            closeModal() {
            },
            shortcutForDonePayment(value) {
                if (this.shortcutKeyInfo.donePayment.status == 1 && this.shortcutStatus == 1) {
                    this.storeInvoice();
                }
            },
            addShipmentStatus(value) {
                this.addShipment = value;
                if (this.addShipment == 1) {
                    this.addShipmentInfo = true;
                    this.$emit('addShipmentInfo', this.shippingInfo, true);
                }
                else {
                    this.addShipmentInfo = false;
                    this.$emit('addShipmentInfo', this.shippingInfo, false);
                };
            },
            getPaymentAmount(value) {
                this.paid = value;
                this.calculateBalance();
            },
            defaultPayment(amount, options) {
                this.paid = amount;
                this.paymentValue = amount;
                this.paymentOptions = options;
                this.calculateBalance();
                this.storeInvoice();
            },
            openBankModal() {
                this.$emit('amount', this.paid, '#bank-transfer-modal');
            },
            openCardModal() {
                this.$emit('amount', this.paid, '#card-payment-modal');
            },
            setAutoInvoiceStatus() {
                let instance = this;
                instance.autoInvoiceStatus = JSON.parse(instance.autoInvoice);
                if (instance.autoInvoiceStatus.setting_value == 1) {
                    instance.autoInvoiceGenerate = true;
                }
            },
            getAutoEmailReceive() {
                let instance = this;
                instance.axiosGet('/get-auto-email-receive', function (response) {
                        if (response.data.autoEmailReceive.setting_value == 1) {
                            instance.autoEmailReceive = true;
                        }
                    },
                    function (error) {

                    });
            },
            setPayment(id, name, status, type, check = true) {
                let instance = this;
                instance.PaymentGauard = false;
                this.paymentName = name;
                this.paymentId = id;
                this.paymentStatus = status;
                this.paymentType = type;
                this.paymentOptions = {};
                this.paid = parseFloat(this.rounding(this.noRoundingAmount)).toFixed(2);
                this.paymentValue = this.paid;
                this.roundingDifference = parseFloat(this.paid - this.noRoundingAmount);
                setTimeout(function () {
                    instance.PaymentGauard = true;
                });
                if (check) {
                    this.calculateBalance();
                }
            },
            selectDefaultMethod() {
                for (let i = 0; i < this.paymentList.length; i++) {
                    this.payments.push({
                        paymentID: this.paymentList[i].id,
                        paymentName: null,
                        paid: null,
                        exchange: null,
                    });
                    if (this.paymentList[i].is_default == 1) {
                        this.setPayment(this.paymentList[i].id, this.paymentList[i].name, this.paymentList[i].status, this.paymentList[i].type, false);
                    }
                }
            },
            clearPayment(index, payment_id, paid) {
                this.paymentListData.splice(index, 1);
                this.noRoundingAmount += parseFloat(paid);
                this.paid = this.rounding(this.noRoundingAmount).toFixed(2);
                this.paymentValue = this.paid;
                this.roundingDifference = this.paid - this.noRoundingAmount;
                this.calculateBalance();
            },
            checkPaymentSelected() {
                return _.filter(this.payments, ['paymentName', null]).length;
            },
            calculateBalance() {
                let data = this.getPaidAndExchangeAmount();
                if (this.salesOrReceivingType == 'internal-transfer') this.finalCart.grandTotal = this.paid;
                
                if(this.conditonForReturn) {
                    // this.exchangeValue = data.exchangedAmount > 0 ? Math.abs(data.exchangedAmount) - Math.abs(parseFloat(this.finalCart.grandTotal)) : data.exchangedAmount;  // return 
                    this.exchangeValue = data.exchangedAmount;  // return 
                }else{
                    this.exchangeValue = this.noRoundingAmount < 0 ? Math.abs(data.exchangedAmount) + Math.abs(parseFloat(this.noRoundingAmount)) : data.exchangedAmount;  // for salses or purchase
                }

                const lastIndex = this.paymentListData.length- 1;
                if (lastIndex >= 0) this.paymentListData[lastIndex].exchange = this.exchangeValue;

                this.paidAmount = data.paidAmount ? data.paidAmount : this.paid;
                this.balance = (this.finalCart.grandTotal + this.roundingDifference - data.paidAmount - (this.paid)).toFixed(2);
            },
            getPaidAndExchangeAmount(){
                 let paidAmount = 0,
                    exchangedAmount = 0,
                    condition = this.conditonForReturn;

                this.paymentListData.forEach(function (payment, index) {
                    if (payment.paid) {
                        paidAmount += parseFloat(payment.paid);

                        if(condition){ //return
                            // exchangedAmount += payment.paid > 0 ? parseFloat(payment.paid) : 0; 
                            if(payment.paid > 0){// return 
                                exchangedAmount += parseFloat(payment.paid);
                                payment.is_active = false;
                            }else{
                                payment.is_active = true;
                                exchangedAmount += 0;
                            }
                        }else{   //sales or purchase
                            // exchangedAmount += payment.paid < 0 ? parseFloat(payment.paid) : 0; // sales or purchase
                            if(payment.paid < 0){
                                exchangedAmount += parseFloat(payment.paid);
                                payment.is_active = false;
                            }else{
                                exchangedAmount += 0;
                                payment.is_active = true;
                            }
                        }
                    }
                });
                return {
                    paidAmount : paidAmount,
                    exchangedAmount : exchangedAmount,
                }
            },

            setDestroyCart(value) {
                this.$emit('setDestroyCart', value);
            },
            setPaymentListData() {
                let instance = this;
                instance.paymentList = JSON.parse(instance.paymentTypes);
                instance.selectDefaultPayment();
                instance.hidePaymentListGetLoader = true;
                instance.setPaymentDescHeight();
            },
            selectDefaultPayment() {
                let instance = this;
                for (let i = 0; i < instance.paymentList.length; i++) {
                    this.payments.push({
                        paymentID: instance.paymentList[i].id,
                        paymentName: null,
                        paid: null,
                        exchange: null,
                    });
                    if (instance.paymentList[i].is_default == 1) {
                        instance.setPayment(instance.paymentList[i].id, instance.paymentList[i].name, instance.paymentList[i].status, instance.paymentList[i].type);
                    }
                }
            },
            preventCreditPayment(){
                if(this.paid < 0 && this.paymentType == 'credit'){
                    this.paymentList.forEach((item, index, arr) => {
                        if (item.is_default == 1) {
                            this.setPayment(item.id, item.name, item.status, item.type);
                        }
                    });
                }
            },
            setSavedPayments() {
                let instance = this;

                if (navigator.onLine) {
                    instance.axiosGETorPOST(
                        {
                            url: '/continue-sale-payments', //set url
                            postData: {orderID: this.orderID} //set post data
                        },
                        (success, responseData) => { // callback after axios method call
                            if (success) //response after then function
                            {
                                instance.savedPayments = responseData;

                            }
                        });
                }
            },
            printReceipt() {
                this.printInvoice = true;
            },
            sendCustomerSms() {
                let instance = this;
                instance.inputFields = {
                    id: instance.finalCart.customer.id,
                    first_name: instance.finalCart.customer.first_name,
                    last_name: instance.finalCart.customer.last_name,
                    phone_number: instance.finalCart.customer.phone_number,
                    subTotal: instance.finalCart.subTotal,
                    invoiceId: instance.invoiceID,
                }
                instance.postDataMethod("/customer-send-sms", instance.inputFields);

            },
            postDataThenFunctionality(response) {
                this.isSendSms = true;
            },
            postDataCatchFunctionality(error) {
            },

            printReceiptBeforeDonePayment() {
                this.printInvoiceBeforeDonePayment = true;
            },
            storeInvoice(action = 'store') {

                if (this.addShipment == 1) {

                    //if shipment setting enabled
                    this.$validator.validateAll().then((result) => {
                        if (result) {
                            let cartItemsToStore = this.finalCart;
                            cartItemsToStore.shippingPrice = this.shippingPrice;
                            cartItemsToStore.shippingAreaId = this.shippingAreaId;
                            cartItemsToStore.shippingAreaSddress = this.shippingAddress;
                            this.storeInvoiceCart(action);
                        } else if (!result && action == 'continue') {
                            this.storeInvoiceCart(action);
                        }
                    });
                } else {
                    this.storeInvoiceCart(action);
                }
            },

            storeInvoiceCart(action) {
                let instance = this,
                    paidAmount = 0,
                    exchangedAmount = 0;
                                  
                this.paymentListData.push({
                    paid: this.paid,
                    paymentID: this.paymentId,
                    paymentName: this.paymentName,
                    paymentType: this.paymentType,
                    PaymentTime: moment().format('YYYY-MM-DD H:mm'),
                    options: this.paymentOptions,
                });
                this.paymentListData.forEach(function (payment, index, paymentListArray) {
                    if (payment.paid) {
                        paidAmount += parseFloat(payment.paid);
                        payment.exchange = 0 ;
                        if (paymentListArray.length - 1 == index) payment.exchange = instance.exchangeValue;
                    }
                });

                if ((this.balance == 0 || this.paidAmount == this.finalCart.grandTotal) || (this.paidAmount == 0 && this.salesOrReceivingType === "internal-transfer")) {

                    let cartItemsToStore = this.finalCart,
                        postURL;

                    cartItemsToStore.salesOrReturnType = this.salesOrReturnType;
                    cartItemsToStore.highest_invoice_number = this.lastInvoiceNumber;

                    if (cartItemsToStore.orderType == 'sales') {
                        postURL = '/store';
                    } else postURL = '/purchase-store';

                    if(this.conditonForReturn){
                        this.calculateBalance();
                    }
                    else{
                         this.getPaidAndExchangeAmount();
                    }
                    cartItemsToStore.payments = this.paymentListData;
                    cartItemsToStore.orderID = this.orderID;
                    cartItemsToStore.salesNote = this.salesNote;
                    cartItemsToStore.orderIdForInternalTransfer = this.orderIdForInternalTransfer;
                    cartItemsToStore.exchangeValue = this.exchangeValue;
                    cartItemsToStore.selectedBranchID = this.selectedBranchID;
                    cartItemsToStore.user = this.user;
                    cartItemsToStore.transferBranch = this.transferBranch;


                    if (this.isCashRegisterBranch === true) {
                        cartItemsToStore.cashRagisterId = this.selectedCashRegisterID;
                    }

                    cartItemsToStore.isCashRegisterBranch = this.isCashRegisterBranch;
                    cartItemsToStore.time = moment().format('YYYY-MM-DD h:mm A');

                    if (action == 'continue') {
                        cartItemsToStore.status = 'pending';

                        if (cartItemsToStore.orderType == 'sales') {
                            postURL = '/continue-sale';
                        } else postURL = '/continue-purchase';
                    }


                    instance.hidePaymentListGetLoader = false;
                    instance.isPaymentDone = false;


                    /*
                    * Done payment Section
                    *
                    * *********************************************************************************/

                    if (!navigator.onLine && this.offline == 1 && action != 'continue') {

                        /*
                        * Offline Done payment Section
                        * */

                        if (cartItemsToStore.orderID) {
                            cartItemsToStore.invoice_id = this.invoiceId;
                        } else {
                            if (this.invoiceId == null) {
                                cartItemsToStore.current_invoice_number = this.lastInvoiceNumber;
                                if (this.is_cash_register_used == 0) {
                                    cartItemsToStore.invoice_id = this.invoice_prefix + this.lastInvoiceNumber + '-' + '0' + '-' + this.user.id + this.invoice_suffix;
                                } else {
                                    cartItemsToStore.invoice_id = this.invoice_prefix + this.lastInvoiceNumber + '-' + this.selectedCashRegisterID + '-' + this.user.id + this.invoice_suffix;
                                }
                                this.lastInvoiceNumber = this.lastInvoiceNumber + 1;
                            } else {
                                cartItemsToStore.current_invoice_number = this.lastInvoiceNumber;
                                cartItemsToStore.invoice_id = this.invoiceId;
                            }
                        }
                        let localStorageData = localStorage.getItem('salesProduct');
                        if (localStorageData != null) {
                            let orderDetails = JSON.parse(localStorageData);
                            if (orderDetails.length > 0) {
                                orderDetails.forEach(function (orderHoldItem, index, array) {
                                    if (orderHoldItem.orderID == cartItemsToStore.orderID && orderHoldItem.orderID) {
                                        array.splice(index, 1);
                                    } else if (orderHoldItem.invoice_id == cartItemsToStore.invoice_id && orderHoldItem.orderID == null) {
                                        array.splice(index, 1);
                                    }
                                });
                            }
                            orderDetails.push(cartItemsToStore);
                            localStorage.setItem('salesProduct', JSON.stringify(orderDetails));
                        } else {
                            localStorage.setItem('salesProduct', JSON.stringify([cartItemsToStore]));
                        }
                        let instance = this;
                        instance.donePaymentCheck = true;
                        instance.$hub.$emit('setOrderID', null);
                        instance.isPaymentDone = true;

                        instance.generateOfflineInvoice(cartItemsToStore);

                        //To send the updated last invoice number to it mother component Sales or receives
                        instance.$emit('getUpdatedInvoice', instance.lastInvoiceNumber);
                        instance.printReceiptView = true;
                        instance.$emit('makePlaceOrderTrue', cartItemsToStore.tableId);
                        if (action == 'continue') {
                            instance.$emit('makeInvoiceIdNull', false);
                        } else {
                            instance.$emit('makeInvoiceIdNull', true);
                        }

                    } else if (navigator.onLine) {

                        /*
                        * Online Done payment Section
                        *
                        * */


                        this.lastInvoiceNumber = this.lastInvoiceNumber + 1;
                        cartItemsToStore.highest_invoice_number = this.lastInvoiceNumber;

                        this.isSubmited = true;

                        // cartItemsToStore.salesOrReturn = this.salesOrReturnType;

                        instance.axiosGETorPOST(
                            {
                                url: postURL, //set url
                                postData: cartItemsToStore, //set post data
                            },
                            (success, responseData) => {
                                // callback after axios method call
                                if (success) //response after then function
                                {
                                    if ("invoiceTemplate" in responseData) {
                                        instance.HTMLcontent = responseData.invoiceTemplate.data;
                                    }
                                    this.lastInvoiceNumber = responseData.lastInvoiceId;
                                    if (action == 'continue') {
                                        instance.$hub.$emit('setOrderID', responseData.orderID, responseData.lastInvoiceId, responseData.orderIdInternalTransfer,);
                                        instance.$emit('makeInvoiceIdNull', false);
                                    } else {
                                        instance.invoiceID = responseData.invoiceID;

                                        if ("checkAvailableQuantity" in responseData) {
                                            instance.$emit('makeInvoiceIdNull', false);
                                            for (let i = 0; i < responseData.message.length; i++) {
                                                let alertMessage = responseData.message[i];

                                                instance.showWarningAlertForOutOfStock(alertMessage);
                                            }
                                            $('#cart-payment-modal').modal('hide');
                                        } else {
                                            instance.$emit('makeInvoiceIdNull', true);
                                            instance.setDestroyCart(true);
                                        }
                                        instance.donePaymentCheck = true;
                                        instance.$hub.$emit('setOrderID', null, responseData.lastInvoiceId);

                                        if (instance.autoInvoiceGenerate == true) {
                                            $('#cart-payment-modal').modal('hide');
                                            instance.printReceipt();
                                        } else {
                                            instance.isPaymentDone = true;
                                            instance.printReceiptView = true;
                                        }
                                        instance.$emit('makePlaceOrderTrue', cartItemsToStore.tableId);
                                    }
                                }
                                instance.hidePaymentListGetLoader = true;
                            }
                        );
                    }
                } else {
                    instance.noRoundingAmount = parseFloat(instance.finalCart.grandTotal - paidAmount);
                    instance.paid = parseFloat(instance.rounding(instance.noRoundingAmount));
                    instance.paymentValue = instance.paid;
                    instance.roundingDifference = parseFloat(instance.paid - instance.noRoundingAmount);
                    instance.calculateBalance();
                }
            },
            generateOfflineInvoice(cartItemsToStore) {
                
                let instance = this;

                let invoiceLogo = this.publicPath + '/uploads/logo/' + this.logo,
                    logo = `<div>
                                <img class="invoice-logo" style="max-width: 200px; height: auto; margin: 0 auto;" src= "${invoiceLogo}" alt="Logo">
                            </div>`,
                    employeeName = cartItemsToStore.user.first_name + " " + cartItemsToStore.user.last_name,
                    itemDetails = instance.getInvoiceDetails(cartItemsToStore.cart),
                    paymentDetails = instance.makePaymentDetailsForInvoice(cartItemsToStore.payments),
                    subTotal = instance.numberFormat(cartItemsToStore.subTotal),
                    tax = instance.numberFormat(cartItemsToStore.tax),
                    total = instance.numberFormat(cartItemsToStore.grandTotal),
                    exchange = instance.numberFormat(cartItemsToStore.exchangeValue),
                    discount = instance.numberFormat(cartItemsToStore.overAllDiscount),
                    invoiceTemplate = this.invoice_template;

                let customerName,
                    customerPhone = '',
                    customerAddress = '',
                    tinNumber = '';

                if (this.orderType == 'sales') {
                    if (this.salesOrReceivingType == 'customer') {
                        customerName = cartItemsToStore.customer.length === 0 ? 'Walk In Customer' : `${cartItemsToStore.customer.first_name} ${cartItemsToStore.customer.last_name}`;
                        customerPhone = cartItemsToStore.customer.phone_number ? cartItemsToStore.customer.phone_number : '';
                        customerAddress = cartItemsToStore.customer.address ? cartItemsToStore.customer.address : '';
                        tinNumber = cartItemsToStore.customer.tin_number ? cartItemsToStore.customer.tin_number : '';

                    } else {
                        customerName = this.transferBranchName;
                    }
                } else {
                    if (this.salesOrReceivingType == 'supplier') {
                        customerName = cartItemsToStore.customer.length === 0 ? 'Walk In Supplier' : `${cartItemsToStore.customer.first_name} ${cartItemsToStore.customer.last_name}`;
                        tinNumber = cartItemsToStore.customer.tin_number ? cartItemsToStore.customer.tin_number : '';
                    } else {
                        customerName = this.transferBranchName;
                    }
                }

                let obj = {
                    '{app_name}': this.app_name,
                    '{app_logo}': '',
                    '{invoice_id}': '',
                    '{employee_name}': employeeName,
                    '{customer_name}': customerName,
                    '{supplier_name}': customerName,
                    '{date}': this.dateFormats(this.date),
                    '{time}': this.dateFormatsWithTime(this.date),
                    '{item_details}': itemDetails,
                    '{payment_details}': paymentDetails,
                    '{sub_total}': subTotal,
                    '{tax}': tax,
                    '{total}': total,
                    '{exchange}': exchange,
                    '{discount}': discount,
                    '{phone_number}': customerPhone,
                    '{address}': customerAddress,
                    '{tin}': tinNumber,
                    '{note}': this.salesNote,
                    '{barcode}': '',
                    '{shipment_info}': '',

                };
                
                if (this.is_cash_register_used == 0) {
                    this.invoiceID = this.invoiceId ? this.invoiceId : this.invoice_prefix + cartItemsToStore.current_invoice_number + '-' + '0' + '-' + this.user.id + this.invoice_suffix;
                    obj['{invoice_id}'] = this.invoiceID;
                } else {
                    this.invoiceID = this.invoiceId ? this.invoiceId : this.invoice_prefix + cartItemsToStore.current_invoice_number + '-' + this.selectedCashRegisterID + '-' + this.user.id + this.invoice_suffix;
                    obj['{invoice_id}'] = this.invoiceID;
                }

                if (this.addShipmentInfo) {
                    obj['{shipment_info}'] = this.shipping_address;
                }


                for (let [key, value] of Object.entries(obj)) {
                    invoiceTemplate = invoiceTemplate.replace(key, value);
                }

                this.HTMLcontent = invoiceTemplate;
            },
            getInvoiceDetails(itemDetails) {
                let row = "";
                for (let item of itemDetails) {
                    if (item.variantTitle == 'default_variant' || item.variantTitle == undefined) {
                        item.variantTitle = '';
                    }
                    if (item.discount == null) {
                        item.discount = '0.00';
                    }
                    let newRow = `<tr>
                            <td style="padding: 7px 0; text-align: left; border-bottom: 1px solid #bfbfbf; border-spacing: 0;">${item.productTitle} ${item.variantTitle}</td>
                            <td style="padding: 7px 0; text-align: right; border-bottom: 1px solid #bfbfbf; border-spacing: 0;">${this.numberFormatting(item.quantity)}</td>
                            <td style="padding: 7px 0; text-align: right; border-bottom: 1px solid #bfbfbf; border-spacing: 0;">${this.numberFormat(item.price)}</td>
                            <td style="padding: 7px 0; text-align: right; border-bottom: 1px solid #bfbfbf; border-spacing: 0;">${this.decimalFormat(item.discount)}%</td>
                            <td style="padding: 7px 0; text-align: right; border-bottom: 1px solid #bfbfbf; border-spacing: 0;">${this.numberFormat(item.calculatedPrice)}</td>
                        </tr>`;
                    row = row + newRow;
                }
                return row;
            },
            makePaymentDetailsForInvoice(paymentDetails) {
                let row = "";
                for (let item of paymentDetails) {
                    let newRow = `<tr style="text-align: left;">
                        <th style="padding: 7px 0;">${item.paymentName}</th>
                        <th style="padding: 7px 0;"></th>
                        <th style="padding: 7px 0;"></th>
                        <th style="padding: 7px 0;"></th>
                        <td style="padding: 7px 0; text-align: right;">${this.numberFormat(item.paid)}</td>
                    </tr>`;
                    row = row + newRow;
                }
                return row;
            },
            rounding(amount) {

                if (this.paymentStatus === 'near_half') {
                    return Math.round((amount) * 2).toFixed() / 2;

                } else if (this.paymentStatus === 'near_integer') {
                    return Math.round(amount);

                } else {
                    return amount;
                }
            },
            setPaymentDescHeight() {
                setTimeout(function () {
                    let totalHeight = $('#js-payment').height();
                    let paymentTitleHeight = $('#js-payment-title').height();
                    let paymentActionHeight = $('#js-payment-action').height();
                    let paymentDescHeight = totalHeight - (paymentTitleHeight + paymentActionHeight + 30);
                    $('#js-payment-description').height(paymentDescHeight);
                }, 1000);
            },
            resetGetInvoice(resetGetInvoice) {
                this.printInvoice = resetGetInvoice;
            },
            resetGetInvoiceBeforeDonePayment(resetGetInvoice) {
                this.printInvoiceBeforeDonePayment = resetGetInvoice;
            },
            absValue(value) {
                return Math.abs(value);
            },
        },
    }
</script>