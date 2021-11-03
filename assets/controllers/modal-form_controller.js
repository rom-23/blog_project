import {Controller} from 'stimulus';
import {Modal} from 'bootstrap';
import $ from 'jquery';

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
                swal('Your password has been updated.');
            },
            error: function (jqXHR, error, errorThrown) {
                if (jqXHR.status && jqXHR.status == 400) {
                    swal(jqXHR.responseText);
                } else if (jqXHR.status && jqXHR.status == 422) {
                    swal(errorThrown+', Both of new password must be the same');
                } else {
                    swal('Something went wrong...');
                }
            }
        });
        this.modal.hide();
    }

    modalHidden() {
        console.log('it was hidden!');
    }

}
