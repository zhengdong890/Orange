import React, { Component, PropTypes } from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/IntegratedSelect/selectList';
import thunk from 'redux-thunk';

class pageView extends React.Component{
	render(){	
        const {value , dispatch } = this.props;
        var page_config = value.Page;
        /*初次获取数据*/
        if(page_config.totalRows === ''){
        	dispatch(actions.getData(1,{'firstRow':0,'listRows':page_config.listRows}));
        }
        /*分页处理*/
        var totalPages = Math.ceil(page_config.totalRows/page_config.listRows);//总页数
        var nowPage    = page_config.nowPage;
        //当前页面大于总页面
        if(!totalPages && nowPage>totalPages){
          nowPage = totalPages;//当前页面设置为总页面
        }  
        var now_cool_page      = page_config.rollPage/2,
            now_cool_page_ceil = Math.ceil(now_cool_page);
        var pageData = {
        	    listRows : page_config.listRows,
        	    rollPage : page_config.rollPage,
        	    totalRows : page_config.totalRows,
                totalPages : totalPages,
                now_cool_page : now_cool_page,
                nowPage : nowPage,
                now_cool_page_ceil : now_cool_page_ceil
        };
        return (
            <div className='page'>
                <ul>    
                    <PageSelection data = {pageData} dispatch = {dispatch} />
                    <PageFirst data = {pageData} dispatch = {dispatch} />
                    <PagePrev data = {pageData} dispatch = {dispatch} />
                    <PageList data = {pageData} dispatch = {dispatch} ></PageList>		
                    <PageNext data = {pageData} dispatch = {dispatch} />
                    <PageEnd data = {pageData} dispatch = {dispatch} />
                    <PageTotal data = {pageData} dispatch = {dispatch} />
                    <PageJump data = {pageData} dispatch = {dispatch} />
                </ul>
             </div>
        )
	}	
}

/*首页*/
class PageFirst extends React.Component{
	render(){
	    var data          = this.props.data,
	        dispatch      = this.props.dispatch,
	        totalPages    = data.totalPages,
	        rollPage      = data.rollPage,
	        nowPage       = data.nowPage,
	        now_cool_page = data.now_cool_page,
	        parmers       = {
	    	    'firstRow':1, 
	    		'listRows':data.listRows
	        };
	    if(totalPages>rollPage&&(nowPage-now_cool_page)>=1){
	        return(
	            <a className='first' href='javascript:;' onClick={() => dispatch(actions.getData(1,parmers))}>首页</a>
	        )
	    }else{
	        return(
	            <a className='first'>首页</a>
	        )
	    }
	}	
}

/*末页*/
class PageEnd extends React.Component{
	render(){
	    var data          = this.props.data,
	        dispatch  = this.props.dispatch,
	        totalPages    = data.totalPages,
	        rollPage      = data.rollPage,
	        nowPage       = data.nowPage,
	        now_cool_page = data.now_cool_page,
	        parmers       = {
	    	    'firstRow':data.listRows * (totalPages - 1), 
	    		'listRows':data.listRows
	        };
	    if(totalPages>rollPage&&(nowPage+now_cool_page)<totalPages){
	        return(
	            <a className='end' href='javascript:;' onClick={() => dispatch(actions.getData(totalPages,parmers))}>末页</a> 
	        )
	    }else{
	        return(<a className='end'>末页</a>); 
	    }
	}	
}

/*上一页*/
class PagePrev extends React.Component{
	render(){
	    var data      = this.props.data,
	        dispatch  = this.props.dispatch,
	        prevPage  = data.nowPage - 1,
	        parmers   = {
	    	    'firstRow':data.listRows * (prevPage - 1),
	    		'listRows':data.listRows
	        };
	    if(prevPage>0){
	        return(
	            <a className='prev' href='javascript:;' onClick={() => dispatch(actions.getData(prevPage,parmers))}>上一页</a>
	        )                       
	    }else{
	        return(
	            <a className='prev'>上一页</a>
	        )
	    }
	}	
}

/*下一页*/
class PageNext extends React.Component{
	render(){
	    var data       = this.props.data,
	        dispatch   = this.props.dispatch,
	        nextPage   = data.nowPage + 1,
	        totalPages = data.totalPages,
	        parmers    = {
	    	    'firstRow':data.listRows * (nextPage - 1),
	    		'listRows':data.listRows
	        };
	    if(nextPage <= totalPages){
	        return(
	            <a className='next' href='javascript:;' onClick={() => dispatch(actions.getData(nextPage,parmers))}>下一页</a>
	        )                       
	    }else{
	        return(
	            <a className='next'>下一页</a>
	        )
	    } 
	}	
}

class PageSelection extends React.Component{
	pageListrowsChange(dispatch){
		var dom = this.refs['list_row'];
		var val = dom.value;
		dispatch(actions.pageListrowsChange(val , actions));
	}
	
	render(){
	    var data           = this.props.data,
	        dispatch       = this.props.dispatch,
	  	    listrow_config = [5,10,20,40,100],
	        listRows       = data.listRows,
	  	    option = listrow_config.map(function(v , index){
	            if(listRows == v){
	                return <option key = {index} value = {v}>{v}</option>;
	            }else{
	                return <option key = {index} value = {v}>{v}</option>;
	            }    	                 
		    });
	    return(
	        <select className='set_listRows' defaultValue = {listRows} ref = 'list_row' onChange = {this.pageListrowsChange.bind(this , dispatch)}>
	           {option}
	        </select>  
	    )
	}	
}

class PageTotal extends React.Component{
	render(){
	    var data       = this.props.data,
	  	    totalRows  = data.totalRows,
	  	    totalPages = data.totalPages; //总页数
	    return(
	        <p className='total'>共{totalPages}页{totalRows}条数据</p> 
	    ) 
	}	
}

class PageJump extends React.Component{
	pageJump(dispatch , data){
		var dom     = this.refs['jump'];
		var nowPage = parseInt(dom.value);
		var parmers = {
    	    'firstRow':data.listRows * (nowPage - 1),
    	    'listRows':data.listRows
    	};
		dispatch(actions.getData(nowPage,parmers))
	}
	
	render(){
		var data     = this.props.data,
		    dispatch = this.props.dispatch;
        return(
            <span>
                 <input name='jump_page' defaultValue = '1' ref = 'jump'></input>
                 <a href='javascript:;' className='jump' onClick= {this.pageJump.bind(this , dispatch , data)}>GO</a>
            </span>
        )
	}	
}

class PageList extends React.Component{
	render(){
        var data      = this.props.data,
            dispatch  = this.props.dispatch,
	        totalRows = data.totalRows,
	  	    rollPage  = data.rollPage,
	  	    nowPage   = data.nowPage,
	  	    listRows  = data.listRows,
	        firstRow  = data.firstRow,
	        totalPages= data.totalPages,
	        now_cool_page = data.now_cool_page,
	        now_cool_page_ceil = data.now_cool_page_ceil,
	        pageList = [];
        var page = 1;
	    for(var i = 1; i <= rollPage; i++){
	        if((nowPage - now_cool_page) <= 0 ){
	            page = i;
	        }else
	        //以当前页为标准 只显示rollPage页
	        if((nowPage + now_cool_page - 1) >= totalPages){
	            page = totalPages - rollPage + i;
	        }else{
	            page = nowPage - now_cool_page_ceil + i;
	        }  
	        if(page > 0 && page != nowPage){
	            //当前页数小于或者等于总页数才让其显示
	            if(page <= totalPages){
	            	var parmers    = {
	        	    	'firstRow':listRows * (page - 1),
	        	        'listRows':data.listRows
	        	    };
	                pageList.push(<PageNumber key = {i} parmers = {parmers} dispatch = {dispatch} page = {page}/>);                       
	            }else{
	                break;
	            }
	        }else{
	           	pageList.push(<a key={i} className='page_a now'>{page}</a>);      
	        }            	
	    }
	    return(
	        <span>
	             {pageList}
	        </span>
	    )
    }	
}

class PageNumber extends React.Component{
	render(){
        var parmers   = this.props.parmers,
            dispatch  = this.props.dispatch,
            page      = this.props.page;
	    return(
	        <a className='page_a' href='javascript:;' onClick={() => dispatch(actions.getData(page,parmers))}>{page}</a>
	    )
    }	
}

function mapStateToProps(state) {
   return {
      value: state
   }
}
export default connect(mapStateToProps)(pageView);