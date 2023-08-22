window.displayDiscountsOverride = window.displayDiscounts;
window.displayDiscounts = function (attributeId) {
    window.displayDiscountsOverride();
    var currentAttrPrice = window.gm_omniprice_attr_prices[attributeId];
    if (!currentAttrPrice) {
        currentAttrPrice = window.gm_omniprice_attr_prices[0];
    }
    if (currentAttrPrice) {
        $('#buy_block .gm_omniprice').show();
        $('#buy_block .gm_omniprice_lowest').text(currentAttrPrice);
    } else {
        $('#buy_block .gm_omniprice').hide();
    }
};