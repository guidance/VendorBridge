/**
 * VendorBridge Integration
 *
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     VendorBridge
 * @copyright   Copyright (c) 2015 Guidance Solutions (http://www.guidance.com)
 */
Validation.add('validate-cron-expression', 'The cron expression you entered is not valid.', function (v) {
    var parts = v.split(' ');
    var regex = /^(?:[1-9]?\d|\*)(?:(?:[\/-][1-9]?\d)|(?:,[1-9]?\d)+)?$/;
    for (var i = 0; i < parts.length; i++) {
        if (!regex.test(parts[i])) {
            return false;
        }
    }

    return (parts.length == 5);
});