import {Controller} from 'stimulus';
import {Modal} from 'bootstrap';
import $ from 'jquery';
const Swal = require('sweetalert2');

export default class extends Controller {
    static targets = ['modal', 'modalBody'];
    static values = {
        formUrl: String,
    }

    async openModal(event) {
        this.modalBodyTarget.innerHTML = 'Loading...';
        this.modal = new Modal(this.modalTarget);
        this.modal.show();
        this.modalBodyTarget.innerHTML = await $.ajax(this.formUrlValue);
    }

    async submitForm(event) {
        event.preventDefault();
        const $form = $(this.modalBodyTarget).find('form');
        await $.ajax({
            url: this.formUrlValue,
            method: $form.prop('method'),
            data: $form.serialize(),
            success: function (data) {
                Swal.fire({
                    icon: 'success',
                    text: 'Your password has been updated.',
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            error: function (jqXHR, error, errorThrown) {
                if (jqXHR.status && jqXHR.status == 400) {
                    Swal.fire({
                        timer: 2000,
                        position: 'top-center',
                        showConfirmButton: false,
                        showDenyButton: false,
                        showCancelButton: false,
                        icon: 'error',
                        text: jqXHR.responseText,
                    })
                } else if (jqXHR.status && jqXHR.status == 422) {
                    Swal.fire({
                        icon: 'error',
                        text: errorThrown+': Both of new passwords must be the same'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        text: 'Something went wrong...'
                    });
                }
            }
        });
        this.modal.hide();
    }

    modalHidden() {
        console.log('it was hidden!');
    }

}
