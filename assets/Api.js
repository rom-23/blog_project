import axios from 'axios';
import NProgress from 'nprogress';

class Api {

    constructor() {
        let service = axios.create({
            // method   : 'GET',
            // dataType : 'json',
            // // withCredentials: false,
            // // baseURL: 'https://some-domain.com/api/',
            // // timeout: 1000,
            headers : this.create_headers(),
            mode    : 'cors',
            cache   : 'default'
            // params  : {
            //     // ID: 12345
            // }
        });
        service.interceptors.request.use(
            (config) => {
                NProgress.start();
                console.log('Axios interceptor REQUEST OK');
                return config;
            },
            (error) => {
                console.log('Axios interceptor REQUEST ERROR');
                Promise.reject(error);
            }
        );
        service.interceptors.response.use(
            async (response) => {
                NProgress.done();
                console.log('RESPONSE INTERCEPTOR OK : ');
                // console.log(response.data);
                // console.log(response.status);
                // console.log(response.statusText);
                // console.log(response.headers);
                // console.log(response.config);
                return response;
            },
            async (error) => {
                console.log('RESPONSE ERROR INTERCEPTOR : ');
                // console.log(error.message);
                // console.log(error.request);
                // console.log(error.config);
                // console.log(error.response.data.message);
                await Promise.reject(error);
            }
        );
        axios.defaults.withCredentials = true;
        this.service = service;
    }

    create_headers() {
        return new Headers({
            'csrf'                             : 'token',
            'Accept'                           : 'application/json',
            'Content-Type'                     : 'application/json',
            'Access-Control-Allow-Credentials' : true,
            'Access-Control-Allow-Origin'      : '*'
        });
    }

    get(path, callback) {
        return this.service.get(path).then(
            (response) => {
                return callback(response);
            }
        ).catch(error => {
            return callback(error);
        });
    }

    delete(path, payload, callback) {
        return this.service.request({
            method       : 'DELETE',
            url          : path,
            responseType : 'json',
            data         : payload
        }).then((response) => {
            return callback(response);
        });
    }

    patch(path, payload, callback) {
        return this.service.request({
            method       : 'PATCH',
            url          : path,
            responseType : 'json',
            data         : payload
        }).then((response) => {
            return callback(response);
        });
    }

    put(path, payload, callback) {
        return this.service.request({
            method       : 'PUT',
            url          : path,
            responseType : 'json',
            data         : payload
        }).then((response) => {
            return callback(response);
        });
    }

    post(path, payload, callback) {
        return this.service.request({
            method       : 'POST',
            url          : path,
            responseType : 'json',
            data         : payload
        }).then((response) => {
            return callback(response);
        });
    }
}

export default new Api;
