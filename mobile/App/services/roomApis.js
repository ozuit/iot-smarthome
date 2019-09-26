import { api } from './api';

export const all = () => {
    return api.get('/room');
}
