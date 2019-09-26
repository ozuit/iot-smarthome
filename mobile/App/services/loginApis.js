import { api } from './api';

export const login = (params) => {
    return api.post('/user/login', params);
}

export const fetchUserInfo = () => {
    return api.get('/me');
}