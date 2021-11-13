import {Controller} from 'stimulus';
import $ from 'jquery';

const Swal = require('sweetalert2');

export default class extends Controller {
    static targets = ['content'];
    static values = {
        formUrl: String,
    }

    async displayData(event) {
        const pp = document.querySelector('[data-model-target]');
        pp.innerHTML = await $.ajax(this.formUrlValue)
    }

}
