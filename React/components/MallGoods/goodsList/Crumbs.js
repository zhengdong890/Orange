import React from 'react'
 /* *
   * 面包屑
   * */
var Crumbs= React.createClass({
      render: function(){   
          return(
         <div id='Crumbs'>
                 <a>后台管理中心</a>-<a>商品列表</a>
         </div>   
          )
      }
})
module.exports = Crumbs;