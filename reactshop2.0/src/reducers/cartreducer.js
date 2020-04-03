let cartData={
    aCartData:localStorage['cartData']!==undefined?JSON.parse(localStorage['cartData']):[],
    total:localStorage['total']!==undefined?parseFloat(localStorage['total']):0,
    freight:localStorage['freight']!==undefined?parseFloat(localStorage['freight']):0
}

function cartReducer(state=cartData,action){
    let data={};
    switch (action.type){
        case "addCart":
            data=addCart(state, action.data);
            return Object.assign({},state, data);
        case "delItem":
            data=delItem(state, action.data);
            return Object.assign({},state, data);
        case "checkItem":
            data = checkItem(state, action.data);
            return Object.assign({},state, data);
        case "allItem":
            data = setAllChecked(state, action.data);
            return Object.assign({},state, data);
        case "incAmount":
            data = incAmount(state, action.data);
            return Object.assign({},state, data);
        case "decAmount":
            data = decAmount(state, action.data);
            return Object.assign({},state, data);
        case "changeAmount":
            data = changeAmount(state, action.data);
            return Object.assign({},state, data);
        case "clearCart":
            data = clearCart(state);
            return Object.assign({},state, data);
        default:
            return state;
    }
}
//添加商品
function addCart(state,action){
    let bSameItem=false;
    if (state.aCartData.length>0){
        //购物车里有相同的商品数量加1
        for (let key in state.aCartData){
            if (state.aCartData[key].gid===action.gid && JSON.stringify(state.aCartData[key].attrs)===JSON.stringify(action.attrs)){
                state.aCartData[key].amount+=parseInt(action.amount);
                bSameItem=true;
                break;
            }
        }
    }
    //购物车里没有相同是数据增加购物车商品
    if (!bSameItem){
        state.aCartData.push(action);
    }

    setTotal(state);
    setFreight(state);

    localStorage['cartData']=JSON.stringify(state.aCartData);
    return state;
}
//删除商品
function delItem(state,action) {
    state.aCartData.splice(action.index,1);
    setTotal(state);
    setFreight(state);
    localStorage['cartData']=JSON.stringify(state.aCartData);
    return state;
}
//重新计算总价
function setTotal(state){
    let total=0;
    for (let key in state.aCartData){
        if (state.aCartData[key].checked){
            total+=parseFloat(state.aCartData[key].price)*parseInt(state.aCartData[key].amount);
        }
    }
    state.total=parseFloat(total.toFixed(2));
    localStorage['total']=state.total;
}
//计算运费
function setFreight(state){
    let aFreight=[];
    for (let key in state.aCartData){
        if (state.aCartData[key].checked){
            aFreight.push(state.aCartData[key].freight);
        }
    }
    state.freight=aFreight.length>0?parseFloat(Math.max.apply(null,aFreight).toFixed(2)):0;
    localStorage['freight']=state.freight;
}
//选择商品
function checkItem(state,action) {
    state.aCartData[action.index].checked =action.checked;
    setTotal(state);
    setFreight(state);
    localStorage['cartData']=JSON.stringify(state.aCartData);
    return state;
}
//全选商品
function setAllChecked(state,action) {
    if (action.checked ){
        for (let key in state.aCartData){
            state.aCartData[key].checked = true;
        }
    } else{
        for (let key in state.aCartData){
            state.aCartData[key].checked = false;
        }
    }
    setTotal(state);
    setFreight(state);
    localStorage['cartData']=JSON.stringify(state.aCartData);
    return state;
}
//增加数量
function incAmount(state,action){
    state.aCartData[action.index].amount+=1;
    setTotal(state);
    localStorage['cartData']=JSON.stringify(state.aCartData);
    return state;

}
//减少数量
function decAmount(state,action){
    if (state.aCartData[action.index].amount>1){
        state.aCartData[action.index].amount-=1;
        setTotal(state);
        localStorage['cartData']=JSON.stringify(state.aCartData);
    }
    return state;
}

//改变数量
function changeAmount(state,action) {
    state.aCartData[action.index].amount=action.amount;
    setTotal(state);
    localStorage['cartData']=JSON.stringify(state.aCartData);
    return state;
}

//清空购物车
function clearCart(state){
    localStorage.removeItem("cartData");
    localStorage.removeItem("total");
    localStorage.removeItem("freight");
    state.aCartData=[];
    state.total = 0;
    state.freight = 0;
    return state;
}
export default cartReducer;
