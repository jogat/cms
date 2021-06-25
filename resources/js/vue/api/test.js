

export default {
    get(){
        return axios.get('/session/data').then(rtn => rtn.data);
    }
}
