import ReactDOM from 'react-dom';
import {fetch} from 'whatwg-fetch';
let oLoad=ReactDOM.findDOMNode(document.getElementById("page-load"));
function request(pUrl,pType='get',data={}){
    showLoad();
    let config={},headers={},params='';
    if(pType.toLocaleLowerCase()==='file'){
        pType="post";
        if(data instanceof Object){
            params=new FormData();
            for(let key in data){
                params.append(key,data[key]);
            }
        }
        config={
            method:pType.toLocaleLowerCase(),
            body:params
        }
    }else if (pType.toLocaleLowerCase() ==="get"){
        config = {
            method: pType.toLocaleLowerCase()
        }
    }else{
        headers = {
            'Content-Type':'application/x-www-form-urlencoded'
        }
        if (data instanceof Object){
            for (let key in data){
                params+=`&${key}=${encodeURIComponent(data[key])}`;
            }
            params = params.slice(1)
        }
        config = {
            method: pType.toLocaleLowerCase(),
            headers,
            body:params
        }
    }
    return fetch(pUrl,config).then(res=> {
            hideLoad();
            return res.json();
        }
    );
}
function showLoad(){
    oLoad.style.display="block";
}
function hideLoad(){
    oLoad.style.display="none";
}
export {
    request
};