 const React = require('../Public/js/react.min');
 /* *
   * 面包屑
   * */
var Form_submit= React.createClass({
      render: function(){   
                 return(
    	                    <table cellspacing='10' className='table'>
                                    <tr>
                                           <td><span>商品名称:</span></td>
                                           <td><input type='text' name='goods_name'></input></td>
                                    </tr>
                                    <tr>
                                           <td valign='top'><span>商品关键字:</span></td>
                                           <td><input type='text' name='goods_keyword'></input></td>
                                    </tr>
                                    <tr>
                                           <td valign='top'><span>商品货号:</span></td>
                                           <td><input type='text' name='goods_sn'></input><p>(不输入将自动生成)</p></td>
                                    </tr>
                                    <tr>
                                           <td valign='top'><span>商品款号:</span></td>
                                           <td><input type='text' name='goods_code'></input></td>
                                    </tr> 
                                    <tr><td><span>所属分类:</span></td>
                                          <td>
                                              <select name='cat_id'>
                                                   <option value='0'>请选择...</option>
                                                       <option value=''></option>
                                              </select>
                                          </td>
                                     </tr>
                                    <tr>
                                           <td><span>商品原价:</span></td>
                                           <td><input type='text' name='goods_price1'></input></td>
                                    </tr>
                                    <tr>
                                           <td><span>商品价格:</span></td>
                                           <td><input type='text' name='goods_price'></input></td>
                                    </tr>
                                    <tr>
                                           <td><span>赠送积分:</span></td>
                                           <td><input type='text' name='goods_credit'></input></td>
                                    </tr>
                                    <tr>
                                           <td><span>库存:</span></td>
                                           <td><input type='text' name='goods_number'></input></td>
                                    </tr>
                                    <tr>
                                           <td><span>排序:</span></td>
                                           <td><input type='text' name='sort' value=''></input></td>
                                    </tr>
                                    <tr>
                                           <td><span>是否上架:</span></td>
                                           <td>
                                              <input type='radio' name='is_show' value='y' checked='checked' className='isshow'></input><p>是</p>
                                              <input type='radio' name='is_show' className='isshow' value='n'></input><p>否</p>
                                           </td>
                                    </tr>
                                    <tr>
                                           <td><span>加入推荐:</span></td>
                                           <td>
                                                 <input type='checkbox' name='goods_model[]' value='' className='checkbox' ></input><p></p> 
                                           </td>
                                    </tr>
                                    <tr>
                                           <td valign='top'><span>描述:</span></td>
                                           <td>
                                             <textarea name='goods_describe'></textarea>
                                           </td>
                                    </tr>
                                    <tr>
                                           <td valign='top'><span>上传商品缩略图片:</span></td>
                                           <td>
                                               <div className='img'>
                                               <img />
                                               <a className='btn_addimg'>点击添加<input type='file' name='goods_thumb'></input></a>                                             
                                               </div> 
                                           </td>
                                    </tr>
                                    <tr>
                                           <td valign='top'><span>上传商品图片:</span></td>
                                           <td>
                                               <div className='img'>
                                                  <img />
                                                  <a className='btn_addimg'>点击添加<input type='file' name='goods_img'></input></a>                                             
                                               </div> 
                                           </td>
                                    </tr>
                                    <tr><td></td><td><button>确认添加</button></td></tr>  
                          </table>
                 )
      }
})
module.exports = Form_submit;