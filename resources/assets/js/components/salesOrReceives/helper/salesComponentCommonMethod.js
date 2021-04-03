export const subTotalAmount = function (salesOrReturnType, receiveOrReturnType, total, profit, tax, subTotal, cart, productTotalWithoutDiscount, isTaxExcluded) {
    total = 0;
    profit = 0;
    tax = 0;
    subTotal = 0;

    cart.forEach(function (cartItem) {
        productTotalWithoutDiscount += cartItem.price;

        let calculatedPriceForSub = 0;
        if (cartItem.quantity > 0) {
            //sales


            cartItem.calculatedPrice = cartItem.price * cartItem.quantity;

            if (cartItem.orderType != "discount" && cartItem.discount != undefined) {

                calculatedPriceForSub = cartItem.calculatedPrice - (cartItem.calculatedPrice * cartItem.discount / 100);

            } else if (cartItem.orderType != "discount" && cartItem.discount == undefined) {

                calculatedPriceForSub = cartItem.calculatedPrice;
            }

            if (cartItem.discount) {

                cartItem.calculatedPrice = cartItem.calculatedPrice - (cartItem.calculatedPrice * cartItem.discount / 100);

                tax += ((cartItem.calculatedPrice) * cartItem.productTaxPercentage) / 100;

            } else {

                tax += (cartItem.calculatedPrice * cartItem.productTaxPercentage) / 100;

            }


        } else if (salesOrReturnType == 'returns' || receiveOrReturnType == 'returns') {

            if (cartItem.orderType != "discount" && cartItem.discount != undefined) {

                calculatedPriceForSub = cartItem.calculatedPrice - (cartItem.calculatedPrice * cartItem.discount / 100);

            } else if (cartItem.orderType != "discount" && cartItem.discount == undefined) {

                calculatedPriceForSub = cartItem.calculatedPrice;
            }


            if (cartItem.discount) {

                cartItem.calculatedPrice = cartItem.calculatedPrice - (cartItem.calculatedPrice * cartItem.discount / 100);

                tax += ((cartItem.calculatedPrice) * cartItem.productTaxPercentage) / 100;

            } else {

                // cartItem.calculatedPrice = cartItem.price * (-1);
                tax += (cartItem.calculatedPrice * cartItem.productTaxPercentage) / 100;
            }

        }

        total += cartItem.calculatedPrice;
        subTotal += calculatedPriceForSub;

        if (cartItem.orderType !== 'discount') {
            if (cartItem.orderType !== 'shipment') {
                profit += cartItem.calculatedPrice - (cartItem.purchase_price * cartItem.quantity);
            }
            // if(isTaxExcluded) subTotal = Number((total + tax).toFixed(2));
            // else subTotal += calculatedPriceForSub;
        } else {
            if (cartItem.orderType !== 'shipment') {
                profit -= cartItem.price;
            }
        }
    });

    let grandTotal;
    if (isTaxExcluded) {
        grandTotal = Number((total + tax).toFixed(2));
    } else grandTotal = Number((total).toFixed(2));



    return {
        total: total,
        profit: profit,
        tax: tax,
        subTotal: subTotal,
        grandTotal: grandTotal,
        productTotalWithoutDiscount: productTotalWithoutDiscount
    }
}


/*
* setCartItemsToCookieOrDB
* */

export const cartItemsToCookie = function (flag = 0, object, appVersion) {
    let cookieName = appVersion + "-user-" + object.user.id + "-" + object.order_type + "-cart",
        cookieObject = {
            'cart': object.cart,
            'customer': object.selectedCustomer,
            'branch': object.selectedSearchBranch,
            'orderID': object.orderID,
            'orderIdInternalTransfer': object.orderIdInternalTransfer,
            'discount': object.discount,
            'overAllDiscount': object.overAllDiscount,
            'lastInvoiceNumber': object.lastInvoiceNumber,
            'addShipping': object.addShipping,
        };
    if (!window.$cookies.isKey(cookieName)) {
        window.$cookies.set(cookieName, cookieObject, "4m");
    }
    else {
        if (flag == 0) {
            let cookieValue = window.$cookies.get(cookieName);
            let cookieCart = cookieValue.cart;

            cookieCart.forEach(function (cookieCartItem, index, array) {
                if (cookieCartItem.showItemCollapse) {
                    array[index].showItemCollapse = false;
                }
            }),
                object.cart = cookieCart;
            object.selectedCustomer = cookieValue.customer;
            if (cookieValue.branch != undefined && object.salesOrReceivingType == 'internal') {

                if (cookieValue.branch.length == 0) {
                    object.isSelectedBranch = true;
                } else {
                    object.selectedSearchBranch = cookieValue.branch;
                    object.isSelectedBranch = false;
                }
            } else {
                object.isSelectedBranch = true;
            }
            object.discount = cookieValue.discount;
            object.newDiscount = cookieValue.discount;
            object.overAllDiscount = cookieValue.overAllDiscount;
            object.newOverAllDiscount = cookieValue.overAllDiscount;
            object.orderID = cookieValue.orderID;
            if (object.selectedCustomer.length == 1) {
                object.customerNotAdded = false;
            }
        } else {
            window.$cookies.set(cookieName, cookieObject, "4m");
        }
    }
    return {
        selectedCustomer: object.selectedCustomer,
        isSelectedBranch: object.isSelectedBranch,
        discount: object.discount,
        newDiscount: Number(object.newDiscount),
        overAllDiscount: Number(object.overAllDiscount),
        orderID: object.orderID,
        orderIdInternalTransfer: object.orderIdInternalTransfer,
        customerNotAdded: object.customerNotAdded,
        newOverAllDiscount: Number(object.newOverAllDiscount),
        cart: object.cart,
        selectedSearchBranch: object.selectedSearchBranch,
        addShipping: object.addShipping,
    }

}


/*
* Delete Cart From cookie
* */
export const deleteCartItemsFromCookieOrDB = function (user, order_type, appVersion) {
    window.$cookies.remove(appVersion + "-user-" + user.id + "-" + order_type + "-cart");
}

// export const setTaxExcludedIncludedCookie = function (value) {
//     window.$cookies.set("exclude-include", value, "2y");
// }
// export const getTaxExcludedIncludedCookie = function () {
//     const isNull = (value) => typeof value === "object" && !value
//
//     let value = window.$cookies.get("exclude-include");
//     if (isNull(value) || value === "true") return true;
//     return false;
// }


