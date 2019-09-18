import React from 'react'
require('../../../css/Handle.css');
 /* *
   * 批量操作部分
   * */
var Handle= React.createClass({
      render: function(){   
          return(
               <div id="Handle">
                        <p>选中项操作:</p>
                        <a>删除商品</a>
                        <p><input type="checkbox" value=""></input></p>
                        <a>加入推荐批量更改</a>
               </div>
          )
      }
})
module.exports = Handle;