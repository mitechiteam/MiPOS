<template>
    <div style="display:none">
        <div
                id="cart-print-area"
                style="padding: 20px !important; color: #444d55; font-family: 'Lato', sans-serif;">
            <span v-html="invoiceTemplate"></span>
        </div>
    </div>
</template>

<script>

    import axiosGetPost from "../../helper/axiosGetPostCommon";

    export default {
        extends: axiosGetPost,

        props: [
            "print_invoice_before_done_payment",
            "sales_or_receiving_type",
            "transfer_branch_name",
            "payment_list_data",
            "exchange_value",
            "order_type",
            "final_cart",
            "payments",
            "sales_note",
            "sold_to",
            "sold_by",
            "balance",
            "user",
            "logo",
            "invoice_template",
            "invoiceId",
            "shipping_area",
            "shipping_address",
            "add_shipping_info",
        ],
        data() {
            return {
                invoiceTemplate: "",
                soldBy: "",
                itemDetails: "",
                paymentDetails: "",
                note: '',
            };
        },
        watch: {
            print_invoice_before_done_payment: function (newVal) {
                if (newVal) {
                    this.printReceiptBeforeDonePayment();
                }
            },
            final_cart: function (newVal) {
                if (newVal) {
                    this.printInvoiceGenerate();
                }
            },
            shipping_address: 'printInvoiceGenerate',
            exchange_value: 'printInvoiceGenerate',
            sales_note: 'printInvoiceGenerate',
            payment_list_data: 'printInvoiceGenerate',
        },
        created() {
            this.printInvoiceGenerate();
        },

        methods: {
            printReceiptBeforeDonePayment() {
                $("#cart-print-area").printThis({
                    importCSS: true,
                    importStyle: true,
                    printContainer: true,
                    header: null
                });
                this.$emit("resetGetInvoiceBeforeDonePayment", false);
            },
            printInvoiceGenerate() {

                let instance = this;
                this.invoiceTemplate = this.invoice_template;
                this.soldBy = this.user.first_name + " " + this.user.last_name;

                this.itemDetails = this.getInvoiceDetails(this.final_cart.cart);

                let customerName,
                    customerPhone = '',
                    customerAddress = '',
                    tinNumber = '';

                if (instance.order_type == "sales") {
                    if (instance.sales_or_receiving_type == "customer") {
                        if (instance.final_cart.customer.length === 0) {
                            customerName = instance.trans("lang.walk_in_customer");
                        } else {
                            customerName = `${instance.final_cart.customer.first_name} ${instance.final_cart.customer.last_name}`;
                            customerPhone = instance.final_cart.customer.phone_number ? instance.final_cart.customer.phone_number : '';
                            customerAddress = instance.final_cart.customer.address ? instance.final_cart.customer.address : '';
                            tinNumber = instance.final_cart.customer.tin_number ? instance.final_cart.customer.tin_number : '';
                        }
                    } else {
                        customerName = instance.final_cart.transferBranchName;
                    }
                } else {
                    if (instance.sales_or_receiving_type == "supplier") {
                        customerName =
                            instance.final_cart.customer.length === 0
                                ? instance.trans("lang.walk_in_supplier")
                                : `${instance.final_cart.customer.first_name} ${instance.final_cart.customer.last_name}`;
                        tinNumber = instance.final_cart.customer.tin_number ? instance.final_cart.customer.tin_number : '';
                    } else {
                        customerName = instance.final_cart.transferBranchName;
                    }
                }

                let invoiceLogo = this.publicPath + "/uploads/logo/" + this.logo,
                    logo = `<div>
                                <img class="invoice-logo" style="max-width: 200px; height: auto; margin: 0 auto;" src= "${invoiceLogo}" alt="Logo">
                            </div>`;

                let obj = {
                    "{app_logo}": logo,
                    "{app_name}": this.app_name,
                    "{invoice_id}": "",
                    "{table_name}": "",
                    "{employee_name}": this.soldBy,
                    "{customer_name}": customerName,
                    "{supplier_name}": customerName,
                    "{date}": this.dateFormats(this.date),
                    "{time}": this.dateFormatsWithTime(this.date),
                    "{item_details}": this.itemDetails,
                    "{sub_total}": this.numberFormat(this.final_cart.subTotal),
                    "{tax}": this.numberFormat(this.final_cart.tax),
                    "{total}": this.numberFormat(this.final_cart.grandTotal),
                    "{discount}": this.numberFormat(this.final_cart.discount),
                    "{phone_number}": customerPhone,
                    "{address}": customerAddress,
                    '{tin}': tinNumber,
                    "{barcode}": "",
                    "{shipment_info}": "",
                };


                this.sales_note ? obj['{note}'] = this.sales_note : obj['{note}'] = "";

                if (this.payment_list_data) {
                    //If not Restaurant

                    obj['{payment_details}'] = this.getPaymentDetails();
                    obj['{exchange}'] = this.numberFormat(this.exchange_value);

                } else {
                    obj['{payment_details}'] = "";
                    obj['{exchange}'] = "";
                }


                this.add_shipping_info == true ? obj['{shipment_info}'] = this.shipping_address : obj['{shipment_info}'] = "";

                for (let [key, value] of Object.entries(obj)) {
                    this.invoiceTemplate = this.invoiceTemplate.replace(key, value);
                }

                this.invoiceTemplate = this.invoiceTemplate;
            },
            getInvoiceDetails(itemDetails) {
                let row = "";
                for (let item of itemDetails) {
                    if (
                        item.variantTitle == "default_variant" ||
                        item.variantTitle == undefined
                    ) {
                        item.variantTitle = "";
                    }
                    if (item.discount == null) {
                        item.discount = "0.00";
                    }
                    const title = item.cartItemNote ? `<td style="padding: 7px 0; text-align: left; border-bottom: 1px solid #bfbfbf; border-spacing: 0;">
                                ${item.productTitle} ${item.variantTitle} <br><p style="font-size: 80%; line-height: 12px;color: #6b6161;">Note: ${item.cartItemNote}</p></td>`
                                : `<td style="padding: 7px 0; text-align: left; border-bottom: 1px solid #bfbfbf; border-spacing: 0;">
                                ${item.productTitle} ${item.variantTitle}</td>`;

                    let newRow = `<tr>
                            ${title}
                            <td style="padding: 7px 0; text-align: right; border-bottom: 1px solid #bfbfbf; border-spacing: 0;">${this.numberFormatting(
                        item.quantity
                    )}</td>
                            <td style="padding: 7px 0; text-align: right; border-bottom: 1px solid #bfbfbf; border-spacing: 0;">${this.numberFormat(
                        item.price
                    )}</td>
                            <td style="padding: 7px 0; text-align: right; border-bottom: 1px solid #bfbfbf; border-spacing: 0;">${this.decimalFormat(
                        item.discount
                    )}%</td>
                            <td style="padding: 7px 0; text-align: right; border-bottom: 1px solid #bfbfbf; border-spacing: 0;">${this.numberFormat(
                        item.calculatedPrice
                    )}</td>
                        </tr>`;
                    row = row + newRow;
                }
                return row;
            },

            getPaymentDetails() {
                let output = "";

                if (this.payment_list_data.length > 0) {
                    for (let item of this.payment_list_data) {
                        if (item.is_active) {
                            output += `<tr style="text-align: left;">
                                                <th style="padding: 7px 0;">${item.paymentName}</th>
                                                <th style="padding: 7px 0;"></th>
                                                <th style="padding: 7px 0;"></th>
                                                <th style="padding: 7px 0;"></th>
                                                <td style="padding: 7px 0; text-align: right;">${this.numberFormat(item.paid)}</td>
                                        </tr>`;
                        }
                    }
                }
                return output;
            }
        }
    };
</script>

<style scoped>
    @media print {
        * {
            color: #000000 !important;
        }
    }
</style>