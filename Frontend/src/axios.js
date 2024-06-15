import axios from 'axios';
const instance = axios.create({
    baseURL: 'http://localhost:4081/api'
});


export default instance;