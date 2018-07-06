define(
    [
        'jquery',
        'Magento_Ui/js/modal/modal'
    ],
    function($) {
        "use strict";
        //creating jquery widget
        $.widget('Vendor.modalForm', {
            options: {
                modalForm: '#modal-form',
                modalButton: '.open-modal-form',
                iframeId: '#megga-login-iframe'
            },
            _create: function() {
                this.options.modalOption = this._getModalOptions();
                this._bind();
            },
            _getModalOptions: function() {
                /**
                 * Modal options
                 */
                var options = {
                    type: 'popup',
                    responsive: true,
                    //title: 'Sample Title',
                    buttons: []
                };

                return options;
            },
            _bind: function(){
                var modalOption = this.options.modalOption;
                var modalForm = this.options.modalForm;
                var meggaIframe = this.options.iframeId;

                $(document).on('click', this.options.modalButton,  function(){
                    var src = $(meggaIframe).attr('data-src');
                    $(meggaIframe).attr('src', src);
                    //Initialize modal
                    $(modalForm).modal(modalOption);
                    //open modal
                    $(modalForm).trigger('openModal');
                });
            }
        });

        return $.Vendor.modalForm;
    }
);
