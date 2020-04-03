import React from 'react';
import {connect} from "react-redux";
import { Modal} from 'antd-mobile';
import config from '../../../assets/js/conf/config.js';
import {safeAuth,lazyImg,setScrollTop} from '../../../assets/js/utils/util.js';
import UpRefresh from '../../../assets/js/libs/uprefresh.js';
import {request} from '../../../assets/js/libs/request.js';
import SubHeaderComponent from '../../../components/header/subheader';
import Css from '../../../assets/css/user/myfav/index.css';
class  MyFav extends React.Component{
    constructor(props){
        super(props);
        safeAuth(props);
        this.state = {
            goods:[]
        }
        this.oUpRefresh=null;
        this.curPage=1;
        this.maxPage=0;
        this.offsetBottom=100;
    }
    componentDidMount(){
        setScrollTop();
        this.oUpRefresh=new UpRefresh();
        this.getData();
    }

    componentWillUnmount(){
        this.oUpRefresh.uneventSrcoll();
        this.setState=(state,callback)=>{
            return;
        }
    }
    getData(){
        let url=config.baseUrl+"/api/user/fav/index?uid="+this.props.state.user.uid+"&token="+config.token+"&page="+this.curPage;
        request(url).then(res=>{
            if (res.code===200){
                this.setState({goods:res.data},()=>{
                    lazyImg();
                });
                this.maxPage=res.pageinfo.pagenum;
                this.getScrollPage();
            }
        });
    }
    getScrollPage(){
        this.oUpRefresh.init({"curPage":this.curPage,"maxPage":this.maxPage,"offsetBottom":this.offsetBottom},curPage=>{
            let url=config.baseUrl+"/api/user/fav/index?uid="+this.props.state.user.uid+"&token="+config.token+"&page="+curPage;
            request(url).then((res)=>{
                if (res.code===200){
                    if (res.data.length>0){
                        let goods=this.state.goods;
                        for (let i=0;i<res.data.length;i++){
                            goods.push(res.data[i]);
                        }
                        this.setState({goods:goods},()=>{
                            lazyImg();
                        });
                    }
                }
            });
        });
    }
    pushPage(url){
        this.props.history.push(config.path+url);
    }
    delGoods(fid,index){
        Modal.alert('', '确认要取消关注吗？', [
            { text: '取消', onPress: () => {}, style: 'default' },
            { text: '确认', onPress: () => {
                    let url=config.baseUrl+'/api/user/fav/del?uid='+this.props.state.user.uid+'&fid='+fid+'&token='+config.token;
                    request(url,"post").then(res=>{
                        if (res.code===200){
                            let goods=this.state.goods;
                            goods.splice(index, 1)
                            this.setState({goods:goods},()=>{
                                lazyImg();
                            });
                        }
                    });
                }
            }
        ]);
    }
    render(){
        return(
            <div className={Css['page']}>
                <SubHeaderComponent title="我的收藏"></SubHeaderComponent>
                <div className={Css['main']}>
                    {
                        this.state.goods.length>0?
                            this.state.goods.map((item,index)=>{
                                return (
                                    <div className={Css['goods-list']} key={index}>
                                        <div className={Css['image']}>
                                            <img data-echo={item.image} src={require("../../../assets/images/common/lazyImg.jpg")} alt=""/>
                                        </div>
                                        <div className={Css['title']}>{item.title}</div>
                                        <div className={Css['price']}>¥{item.price}</div>
                                        <div className={Css['btn-wrap']}>
                                            <div className={Css['btn']} onClick={this.pushPage.bind(this, 'goods/details/item?gid='+item.gid)}>购买</div>
                                            <div className={Css['btn']} onClick={this.delGoods.bind(this, item.fid,index)}>删除</div>
                                        </div>
                                    </div>
                                )
                            })
                        :<div className="null-item">您还没有收藏商品！</div>
                    }
                </div>
            </div>
        );
    }
}
export default connect((state)=>{
    return{
        state:state
    }
})(MyFav)
