const env = {
    test: 'test', product: 'product', local: 'local',
};
const API_URL = {
    local: 'http://api.iot.test/api/v1',
    test: '',
    product: '',
};
const currentEnv = env.local;

export const BASE_API_URL = API_URL[currentEnv];
export const USER_TOKEN = 'USER_TOKEN';
