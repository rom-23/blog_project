import './styles/app.scss';
import './bootstrap.js';
import './plugins/tom-select';
import './plugins/fetch';
import './plugins/prototype-form';
import Swal from 'sweetalert2';

import { Application } from 'stimulus';
import { definitionsFromContext } from 'stimulus/webpack-helpers';
const application = Application.start();
const context = require.context('./controllers', true, /_controller\.js$/);
application.load(definitionsFromContext(context));
