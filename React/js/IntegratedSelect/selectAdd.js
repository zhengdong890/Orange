class CashClass{
	constructor() {
        this.selectData  = {
            title  : '',
            desc   : '',
            area   : '',
            status : '0'
        };        
    }	
	
	getConfig(){
	    return {
	    	selectData : this.selectData 
	    }    	
	}
	
	
	getData(){
		return{				
			selectData : this.selectData
	    } 
	}
}
var cash = new CashClass();
export default cash;