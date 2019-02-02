jQuery(function() {
    document.formvalidator.setHandler('greeting',
        function (value) {
            regex=/^[^\*]+$/;
            return regex.test(value);
        });
});